<?php
/**
 * admin/avisos_privacidad.php — CRUD para Avisos de Privacidad
 * Gestiona: texto del aviso + botones con PDF
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';
$pdo = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';
    if (!csrf_validate($token)) {
        $_SESSION['flash_message']='Token CSRF inválido.'; $_SESSION['flash_type']='danger';
        header('Location: avisos_privacidad.php'); exit;
    }

    // UPDATE TEXT
    if ($action==='update_texto') {
        $texto=trim($_POST['texto_aviso']??'');
        if ($texto==='') { $_SESSION['flash_message']='El texto no puede estar vacío.'; $_SESSION['flash_type']='warning'; header('Location: avisos_privacidad.php'); exit; }
        try {
            $s=$pdo->query('SELECT id FROM avisos_privacidad_config LIMIT 1'); $row=$s->fetch();
            if ($row) { $pdo->prepare('UPDATE avisos_privacidad_config SET texto_aviso=? WHERE id=?')->execute([$texto,$row['id']]); }
            else { $pdo->prepare('INSERT INTO avisos_privacidad_config (texto_aviso) VALUES (?)')->execute([$texto]); }
            $_SESSION['flash_message']='Texto actualizado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header('Location: avisos_privacidad.php'); exit;
    }

    // ADD BUTTON
    if ($action==='add_boton') {
        $titulo=trim($_POST['titulo']??'');
        if ($titulo==='') { $_SESSION['flash_message']='Ingrese un título.'; $_SESSION['flash_type']='warning'; header('Location: avisos_privacidad.php'); exit; }
        $pdfPath=null;
        if (isset($_FILES['pdf'])&&$_FILES['pdf']['error']!==UPLOAD_ERR_NO_FILE) {
            $upload=handle_upload($_FILES['pdf'],'pdf');
            if (!$upload['success']) { $_SESSION['flash_message']=$upload['error']; $_SESSION['flash_type']='danger'; header('Location: avisos_privacidad.php'); exit; }
            $pdfPath=$upload['path'];
        }
        try {
            $s=$pdo->query('SELECT COALESCE(MAX(orden),0)+1 FROM avisos_privacidad'); $ord=(int)$s->fetchColumn();
            $pdo->prepare('INSERT INTO avisos_privacidad (titulo,pdf_path,orden) VALUES (?,?,?)')->execute([$titulo,$pdfPath,$ord]);
            $_SESSION['flash_message']='Botón agregado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header('Location: avisos_privacidad.php'); exit;
    }

    // EDIT BUTTON
    if ($action==='edit_boton') {
        $id=(int)($_POST['boton_id']??0); $titulo=trim($_POST['titulo']??'');
        if ($id<=0||$titulo==='') { $_SESSION['flash_message']='Datos inválidos.'; $_SESSION['flash_type']='warning'; header('Location: avisos_privacidad.php'); exit; }
        try { $pdo->prepare('UPDATE avisos_privacidad SET titulo=? WHERE id=?')->execute([$titulo,$id]);
            $_SESSION['flash_message']='Botón actualizado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header('Location: avisos_privacidad.php'); exit;
    }

    // UPLOAD PDF
    if ($action==='upload_pdf') {
        $id=(int)($_POST['boton_id']??0);
        if ($id<=0||!isset($_FILES['pdf'])||$_FILES['pdf']['error']===UPLOAD_ERR_NO_FILE) { $_SESSION['flash_message']='Seleccione un PDF.'; $_SESSION['flash_type']='warning'; header('Location: avisos_privacidad.php'); exit; }
        $upload=handle_upload($_FILES['pdf'],'pdf');
        if (!$upload['success']) { $_SESSION['flash_message']=$upload['error']; $_SESSION['flash_type']='danger'; header('Location: avisos_privacidad.php'); exit; }
        try { $s=$pdo->prepare('SELECT pdf_path FROM avisos_privacidad WHERE id=?'); $s->execute([$id]); $old=$s->fetchColumn();
            if ($old&&file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('UPDATE avisos_privacidad SET pdf_path=? WHERE id=?')->execute([$upload['path'],$id]);
            $_SESSION['flash_message']='PDF subido.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header('Location: avisos_privacidad.php'); exit;
    }

    // DELETE PDF
    if ($action==='delete_pdf') {
        $id=(int)($_POST['boton_id']??0);
        try { $s=$pdo->prepare('SELECT pdf_path FROM avisos_privacidad WHERE id=?'); $s->execute([$id]); $old=$s->fetchColumn();
            if ($old&&file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('UPDATE avisos_privacidad SET pdf_path=NULL WHERE id=?')->execute([$id]);
            $_SESSION['flash_message']='PDF eliminado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header('Location: avisos_privacidad.php'); exit;
    }

    // DELETE BUTTON
    if ($action==='delete_boton') {
        $id=(int)($_POST['boton_id']??0);
        try { $s=$pdo->prepare('SELECT pdf_path FROM avisos_privacidad WHERE id=?'); $s->execute([$id]); $old=$s->fetchColumn();
            if ($old&&file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('DELETE FROM avisos_privacidad WHERE id=?')->execute([$id]);
            $_SESSION['flash_message']='Botón eliminado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header('Location: avisos_privacidad.php'); exit;
    }
}

// ── Consultar datos ──
$config = null; $botones = [];
try {
    $config = $pdo->query('SELECT * FROM avisos_privacidad_config LIMIT 1')->fetch();
    $botones = $pdo->query('SELECT * FROM avisos_privacidad ORDER BY orden ASC')->fetchAll();
} catch(PDOException $e) { if(defined('APP_DEBUG')&&APP_DEBUG) error_log('avisos: '.$e->getMessage()); }

$flashMessage=$_SESSION['flash_message']??''; $flashType=$_SESSION['flash_type']??'';
unset($_SESSION['flash_message'],$_SESSION['flash_type']);
$token=csrf_token();

?><!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Avisos de Privacidad — Admin DIF</title>
<link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/admin.css?v=5">
</head><body><div class="d-flex">
<?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
<div class="main-content">
<nav class="navbar navbar-light bg-white shadow-sm px-3">
<button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
<span class="navbar-brand mb-0 h6">Avisos de Privacidad</span>
<a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
</nav>
<div class="container-fluid p-4">
<?php if($flashMessage):?><div class="alert alert-<?=htmlspecialchars($flashType)?> alert-dismissible fade show"><?=htmlspecialchars($flashMessage)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif;?>

<div class="row g-4">
<!-- Texto del aviso -->
<div class="col-12">
<div class="card mb-4">
<div class="card-header bg-primary text-white"><i class="bi bi-pencil-square me-1"></i> Texto del Aviso de Privacidad</div>
<div class="card-body">
<form method="POST">
<input type="hidden" name="action" value="update_texto">
<input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>">
<div class="mb-3">
<textarea class="form-control" name="texto_aviso" rows="5" required><?=$config?htmlspecialchars($config['texto_aviso']):''?></textarea>
</div>
<button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar texto</button>
</form>
</div></div></div>

<!-- Agregar botón -->
<div class="col-lg-5">
<div class="card">
<div class="card-header bg-success text-white"><i class="bi bi-plus-circle me-1"></i> Agregar botón</div>
<div class="card-body">
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="add_boton">
<input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>">
<div class="mb-3"><label class="form-label">Título del botón</label><input type="text" name="titulo" class="form-control" required placeholder="Ej: Aviso de Privacidad Integral..."></div>
<div class="mb-3"><label class="form-label">PDF (máx. 20 MB, opcional)</label><input type="file" name="pdf" class="form-control" accept=".pdf"></div>
<button type="submit" class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i> Agregar</button>
</form>
</div></div></div>

<!-- Lista de botones -->
<div class="col-lg-7">
<div class="card">
<div class="card-header"><i class="bi bi-list-ul me-1"></i> Botones actuales <span class="badge bg-secondary ms-1"><?=count($botones)?></span></div>
<div class="card-body p-0">
<?php if(empty($botones)):?>
<div class="text-center text-muted py-4"><p class="mb-0">No hay botones. Use el formulario para agregar.</p></div>
<?php else:?>
<div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead class="table-light"><tr><th>#</th><th>Título</th><th>PDF</th><th style="width:200px">Acciones</th></tr></thead><tbody>
<?php foreach($botones as $i=>$b):?>
<tr><td><?=$i+1?></td><td><?=htmlspecialchars($b['titulo'])?></td>
<td><?php if(!empty($b['pdf_path'])):?><span class="badge bg-success">Sí</span><?php else:?><span class="badge bg-secondary">No</span><?php endif;?></td>
<td>
<button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#eB<?=(int)$b['id']?>"><i class="bi bi-pencil"></i></button>
<?php if(empty($b['pdf_path'])):?>
<button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#uP<?=(int)$b['id']?>"><i class="bi bi-upload"></i></button>
<?php else:?>
<a href="../<?=htmlspecialchars($b['pdf_path'])?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
<button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#uP<?=(int)$b['id']?>"><i class="bi bi-arrow-repeat"></i></button>
<form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar PDF?')"><input type="hidden" name="action" value="delete_pdf"><input type="hidden" name="boton_id" value="<?=(int)$b['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-x"></i></button></form>
<?php endif;?>
<form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar botón?')"><input type="hidden" name="action" value="delete_boton"><input type="hidden" name="boton_id" value="<?=(int)$b['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
</td></tr>
<!-- Modal editar -->
<div class="modal fade" id="eB<?=(int)$b['id']?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form method="POST"><input type="hidden" name="action" value="edit_boton"><input type="hidden" name="boton_id" value="<?=(int)$b['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><div class="modal-header"><h5 class="modal-title">Editar botón</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label">Título</label><input type="text" name="titulo" class="form-control" value="<?=htmlspecialchars($b['titulo'])?>" required></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning">Guardar</button></div></form></div></div></div>
<!-- Modal subir PDF -->
<div class="modal fade" id="uP<?=(int)$b['id']?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form method="POST" enctype="multipart/form-data"><input type="hidden" name="action" value="upload_pdf"><input type="hidden" name="boton_id" value="<?=(int)$b['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><div class="modal-header"><h5 class="modal-title"><?=empty($b['pdf_path'])?'Subir':'Reemplazar'?> PDF</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="file" name="pdf" class="form-control" accept=".pdf" required></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Subir</button></div></form></div></div></div>
<?php endforeach;?></tbody></table></div>
<?php endif;?>
</div></div></div>
</div>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/upload-progress.js?v=12"></script>
<script>
const sidebar=document.getElementById('sidebar');
if(window.innerWidth<=768)sidebar.classList.add('collapsed');
document.getElementById('toggleSidebar').addEventListener('click',function(){sidebar.classList.toggle('collapsed');});
var cb=document.getElementById('closeSidebar');if(cb)cb.addEventListener('click',function(){sidebar.classList.add('collapsed');});
</script></body></html>
