<?php
/**
 * admin/cuenta_publica.php — CRUD para Cuenta Pública
 * Estructura: Bloques (año) → Títulos (módulos) → Conceptos (con PDF)
 */
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload_handler.php';
require_once __DIR__ . '/../includes/db.php';
$pdo = get_db();
$bloqueId = isset($_GET['bloque_id']) ? (int)$_GET['bloque_id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token  = $_POST['csrf_token'] ?? '';
    if (!csrf_validate($token)) {
        $_SESSION['flash_message']='Token CSRF inválido.'; $_SESSION['flash_type']='danger';
        header('Location: cuenta_publica.php'.($bloqueId>0?"?bloque_id={$bloqueId}":'')); exit;
    }
    if ($action==='create_block') {
        $anio=trim($_POST['anio']??'');
        if (empty($anio)||!preg_match('/^\d{4}$/',$anio)) { $_SESSION['flash_message']='Año inválido.'; $_SESSION['flash_type']='warning'; header('Location: cuenta_publica.php'); exit; }
        if ((int)$anio > (int)date('Y')) { $_SESSION['flash_message']='El año no puede ser mayor al año en curso ('.date('Y').').'; $_SESSION['flash_type']='warning'; header('Location: cuenta_publica.php'); exit; }
        try {
            $s=$pdo->prepare('SELECT id FROM cp_bloques WHERE anio=?'); $s->execute([$anio]);
            if ($s->fetch()) { $_SESSION['flash_message']="Ya existe bloque {$anio}."; $_SESSION['flash_type']='warning'; header('Location: cuenta_publica.php'); exit; }
            $s=$pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM cp_bloques'); $s->execute(); $ord=(int)$s->fetchColumn();
            $pdo->prepare('INSERT INTO cp_bloques (anio,orden) VALUES (?,?)')->execute([$anio,$ord]);
            $_SESSION['flash_message']="Bloque {$anio} creado."; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header('Location: cuenta_publica.php'); exit;
    }
    if ($action==='delete_block') {
        $id=(int)($_POST['id']??0);
        try {
            $sp=$pdo->prepare('SELECT c.pdf_path FROM cp_conceptos c INNER JOIN cp_titulos t ON c.titulo_id=t.id WHERE t.bloque_id=? AND c.pdf_path IS NOT NULL'); $sp->execute([$id]); $pdfs=$sp->fetchAll();
            $pdo->prepare('DELETE FROM cp_bloques WHERE id=?')->execute([$id]);
            foreach($pdfs as $p){$f=BASE_PATH.'/'.$p['pdf_path'];if(file_exists($f))unlink($f);}
            $_SESSION['flash_message']='Bloque eliminado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header('Location: cuenta_publica.php'); exit;
    }
    if ($action==='add_titulo') {
        $bId=(int)($_POST['bloque_id']??0); $nombre=trim($_POST['nombre']??'');
        if ($bId<=0||$nombre==='') { $_SESSION['flash_message']='Ingrese un nombre.'; $_SESSION['flash_type']='warning'; header("Location: cuenta_publica.php?bloque_id={$bId}"); exit; }
        try { $s=$pdo->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM cp_titulos WHERE bloque_id=?'); $s->execute([$bId]); $ord=(int)$s->fetchColumn();
            $pdo->prepare('INSERT INTO cp_titulos (bloque_id,nombre,orden) VALUES (?,?,?)')->execute([$bId,$nombre,$ord]);
            $_SESSION['flash_message']='Título agregado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header("Location: cuenta_publica.php?bloque_id={$bId}"); exit;
    }
    if ($action==='edit_titulo') {
        $bId=(int)($_POST['bloque_id']??0); $tId=(int)($_POST['titulo_id']??0); $nombre=trim($_POST['nombre']??'');
        if ($tId<=0||$nombre==='') { $_SESSION['flash_message']='Datos inválidos.'; $_SESSION['flash_type']='warning'; header("Location: cuenta_publica.php?bloque_id={$bId}"); exit; }
        try { $pdo->prepare('UPDATE cp_titulos SET nombre=? WHERE id=? AND bloque_id=?')->execute([$nombre,$tId,$bId]); $_SESSION['flash_message']='Título actualizado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header("Location: cuenta_publica.php?bloque_id={$bId}"); exit;
    }
    if ($action==='delete_titulo') {
        $bId=(int)($_POST['bloque_id']??0); $tId=(int)($_POST['titulo_id']??0);
        try { $sp=$pdo->prepare('SELECT pdf_path FROM cp_conceptos WHERE titulo_id=? AND pdf_path IS NOT NULL'); $sp->execute([$tId]); $pdfs=$sp->fetchAll();
            $pdo->prepare('DELETE FROM cp_titulos WHERE id=? AND bloque_id=?')->execute([$tId,$bId]);
            foreach($pdfs as $p){$f=BASE_PATH.'/'.$p['pdf_path'];if(file_exists($f))unlink($f);}
            $_SESSION['flash_message']='Título eliminado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header("Location: cuenta_publica.php?bloque_id={$bId}"); exit;
    }
    if ($action==='add_concepto') {
        $bId=(int)($_POST['bloque_id']??0); $tId=(int)($_POST['titulo_id']??0); $nombre=trim($_POST['nombre']??'');
        if ($tId<=0||$nombre==='') { $_SESSION['flash_message']='Ingrese un nombre.'; $_SESSION['flash_type']='warning'; header("Location: cuenta_publica.php?bloque_id={$bId}"); exit; }
        $pdfPath=null;
        if (isset($_FILES['pdf'])&&$_FILES['pdf']['error']!==UPLOAD_ERR_NO_FILE) {
            $upload=handle_upload($_FILES['pdf'],'pdf');
            if (!$upload['success']) { $_SESSION['flash_message']=$upload['error']; $_SESSION['flash_type']='danger'; header("Location: cuenta_publica.php?bloque_id={$bId}"); exit; }
            $pdfPath=$upload['path'];
        }
        try { $s=$pdo->prepare('SELECT COALESCE(MAX(numero),0)+1 FROM cp_conceptos WHERE titulo_id=?'); $s->execute([$tId]); $num=(int)$s->fetchColumn();
            $pdo->prepare('INSERT INTO cp_conceptos (titulo_id,numero,nombre,pdf_path,orden) VALUES (?,?,?,?,?)')->execute([$tId,$num,$nombre,$pdfPath,$num]);
            $_SESSION['flash_message']='Concepto agregado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header("Location: cuenta_publica.php?bloque_id={$bId}"); exit;
    }
    if ($action==='edit_concepto') {
        $bId=(int)($_POST['bloque_id']??0); $cId=(int)($_POST['concepto_id']??0); $nombre=trim($_POST['nombre']??'');
        if ($cId<=0||$nombre==='') { $_SESSION['flash_message']='Datos inválidos.'; $_SESSION['flash_type']='warning'; header("Location: cuenta_publica.php?bloque_id={$bId}"); exit; }
        try { $pdo->prepare('UPDATE cp_conceptos SET nombre=? WHERE id=?')->execute([$nombre,$cId]); $_SESSION['flash_message']='Concepto actualizado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header("Location: cuenta_publica.php?bloque_id={$bId}"); exit;
    }
    if ($action==='upload_pdf') {
        $bId=(int)($_POST['bloque_id']??0); $cId=(int)($_POST['concepto_id']??0);
        if ($cId<=0||!isset($_FILES['pdf'])||$_FILES['pdf']['error']===UPLOAD_ERR_NO_FILE) { $_SESSION['flash_message']='Seleccione un PDF.'; $_SESSION['flash_type']='warning'; header("Location: cuenta_publica.php?bloque_id={$bId}"); exit; }
        $upload=handle_upload($_FILES['pdf'],'pdf');
        if (!$upload['success']) { $_SESSION['flash_message']=$upload['error']; $_SESSION['flash_type']='danger'; header("Location: cuenta_publica.php?bloque_id={$bId}"); exit; }
        try { $s=$pdo->prepare('SELECT pdf_path FROM cp_conceptos WHERE id=?'); $s->execute([$cId]); $old=$s->fetchColumn();
            if ($old&&file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('UPDATE cp_conceptos SET pdf_path=? WHERE id=?')->execute([$upload['path'],$cId]);
            $_SESSION['flash_message']='PDF subido.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header("Location: cuenta_publica.php?bloque_id={$bId}"); exit;
    }
    if ($action==='delete_pdf') {
        $bId=(int)($_POST['bloque_id']??0); $cId=(int)($_POST['concepto_id']??0);
        try { $s=$pdo->prepare('SELECT pdf_path FROM cp_conceptos WHERE id=?'); $s->execute([$cId]); $old=$s->fetchColumn();
            if ($old&&file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('UPDATE cp_conceptos SET pdf_path=NULL WHERE id=?')->execute([$cId]);
            $_SESSION['flash_message']='PDF eliminado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header("Location: cuenta_publica.php?bloque_id={$bId}"); exit;
    }
    if ($action==='delete_concepto') {
        $bId=(int)($_POST['bloque_id']??0); $cId=(int)($_POST['concepto_id']??0); $tId=(int)($_POST['titulo_id']??0);
        try { $s=$pdo->prepare('SELECT pdf_path FROM cp_conceptos WHERE id=?'); $s->execute([$cId]); $old=$s->fetchColumn();
            if ($old&&file_exists(BASE_PATH.'/'.$old)) unlink(BASE_PATH.'/'.$old);
            $pdo->prepare('DELETE FROM cp_conceptos WHERE id=?')->execute([$cId]);
            $s=$pdo->prepare('SELECT id FROM cp_conceptos WHERE titulo_id=? ORDER BY orden ASC'); $s->execute([$tId]);
            $n=1; $u=$pdo->prepare('UPDATE cp_conceptos SET numero=?,orden=? WHERE id=?');
            foreach($s->fetchAll() as $r){$u->execute([$n,$n,$r['id']]);$n++;}
            $_SESSION['flash_message']='Concepto eliminado.'; $_SESSION['flash_type']='success';
        } catch(PDOException $e) { $_SESSION['flash_message']=(defined('APP_DEBUG')&&APP_DEBUG)?$e->getMessage():'Error.'; $_SESSION['flash_type']='danger'; }
        header("Location: cuenta_publica.php?bloque_id={$bId}"); exit;
    }
}
$currentBloque=null; $titulos=[]; $conceptosMap=[]; $bloques=[];
try {
    if ($bloqueId>0) {
        $s=$pdo->prepare('SELECT * FROM cp_bloques WHERE id=?'); $s->execute([$bloqueId]); $currentBloque=$s->fetch();
        if ($currentBloque) {
            $s=$pdo->prepare('SELECT * FROM cp_titulos WHERE bloque_id=? ORDER BY orden ASC'); $s->execute([$bloqueId]); $titulos=$s->fetchAll();
            $tIds=array_column($titulos,'id');
            if (!empty($tIds)) { $in=implode(',',array_fill(0,count($tIds),'?'));
                $s=$pdo->prepare("SELECT * FROM cp_conceptos WHERE titulo_id IN ({$in}) ORDER BY orden ASC"); $s->execute($tIds);
                while($r=$s->fetch()){$conceptosMap[(int)$r['titulo_id']][]=$r;}
            }
        }
    } else {
        $s=$pdo->query('SELECT b.*, (SELECT COUNT(*) FROM cp_titulos t WHERE t.bloque_id=b.id) AS num_titulos, (SELECT COUNT(*) FROM cp_conceptos c INNER JOIN cp_titulos t2 ON c.titulo_id=t2.id WHERE t2.bloque_id=b.id) AS num_conceptos FROM cp_bloques b ORDER BY b.anio DESC');
        $bloques=$s->fetchAll();
    }
} catch(PDOException $e) { if(defined('APP_DEBUG')&&APP_DEBUG) error_log('CP: '.$e->getMessage()); }
$flashMessage=$_SESSION['flash_message']??''; $flashType=$_SESSION['flash_type']??'';
unset($_SESSION['flash_message'],$_SESSION['flash_type']);
$token=csrf_token();
?><!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Cuenta Pública — Admin DIF</title>
<link rel="icon" href="../img/favicon-32x32.png" sizes="35x35">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/admin.css?v=3">
</head><body><div class="d-flex">
<?php require_once __DIR__ . '/sidebar_sections.php'; render_admin_sidebar($sidebar_groups, $current_admin_file); ?>
<div class="main-content">
<nav class="navbar navbar-light bg-white shadow-sm px-3">
<button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
<span class="navbar-brand mb-0 h6"><?php if($currentBloque):?><a href="cuenta_publica.php" class="text-decoration-none text-muted">Cuenta Pública</a> <i class="bi bi-chevron-right mx-1 small"></i> <?=htmlspecialchars($currentBloque['anio'])?><?php else:?>Cuenta Pública — Bloques por Año<?php endif;?></span>
<a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-box-arrow-right"></i> Salir</a>
</nav>
<div class="container-fluid p-4">
<?php if($flashMessage):?><div class="alert alert-<?=htmlspecialchars($flashType)?> alert-dismissible fade show"><?=htmlspecialchars($flashMessage)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif;?>
<?php if($bloqueId>0 && $currentBloque): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
<h5 class="mb-0"><i class="bi bi-calendar-event me-1"></i> Cuenta Pública <?=htmlspecialchars($currentBloque['anio'])?></h5>
<button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteBlockModal"><i class="bi bi-trash me-1"></i> Eliminar bloque</button>
</div>
<div class="card mb-4"><div class="card-header bg-primary text-white"><i class="bi bi-plus-circle me-1"></i> Agregar Título / Módulo</div>
<div class="card-body"><form method="POST" class="row g-2 align-items-end">
<input type="hidden" name="action" value="add_titulo"><input type="hidden" name="bloque_id" value="<?=$bloqueId?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>">
<div class="col-md-9"><label class="form-label">Nombre del título</label><input type="text" name="nombre" class="form-control" placeholder="Ej: CUENTA PÚBLICA <?=htmlspecialchars($currentBloque['anio'])?> MODULO 1 DISCIPLINA FINANCIERA" required></div>
<div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i> Agregar</button></div>
</form></div></div>
<?php if(empty($titulos)):?><div class="text-center text-muted py-4"><i class="bi bi-folder2-open" style="font-size:2rem;"></i><p class="mt-2">No hay títulos/módulos aún.</p></div>
<?php else: foreach($titulos as $titulo): $tC=$conceptosMap[(int)$titulo['id']]??[]; ?>
<div class="card mb-4">
<div class="card-header d-flex justify-content-between align-items-center" style="background-color:#7b2d8e;color:#fff;">
<span><i class="bi bi-bookmark-fill me-1"></i> <?=htmlspecialchars($titulo['nombre'])?></span>
<div><button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#eTM<?=(int)$titulo['id']?>"><i class="bi bi-pencil"></i></button>
<button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#dTM<?=(int)$titulo['id']?>"><i class="bi bi-trash"></i></button></div>
</div>
<div class="card-body">
<form method="POST" enctype="multipart/form-data" class="row g-2 align-items-end mb-3">
<input type="hidden" name="action" value="add_concepto"><input type="hidden" name="bloque_id" value="<?=$bloqueId?>"><input type="hidden" name="titulo_id" value="<?=(int)$titulo['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>">
<div class="col-md-5"><label class="form-label small">Concepto</label><input type="text" name="nombre" class="form-control form-control-sm" required></div>
<div class="col-md-4"><label class="form-label small">PDF (opcional)</label><input type="file" name="pdf" class="form-control form-control-sm" accept=".pdf"></div>
<div class="col-md-3"><button type="submit" class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-plus-lg me-1"></i> Agregar</button></div>
</form>
<?php if(empty($tC)):?><p class="text-muted small mb-0">Sin conceptos.</p>
<?php else:?><div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0">
<thead class="table-light"><tr><th style="width:50px">#</th><th>Concepto</th><th style="width:80px">PDF</th><th style="width:200px">Acciones</th></tr></thead><tbody>
<?php foreach($tC as $c):?>
<tr><td><?=(int)$c['numero']?></td><td><?=htmlspecialchars($c['nombre'])?></td>
<td><?php if(!empty($c['pdf_path'])):?><span class="badge bg-success">Sí</span><?php else:?><span class="badge bg-secondary">No</span><?php endif;?></td>
<td>
<button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#eC<?=(int)$c['id']?>"><i class="bi bi-pencil"></i></button>
<?php if(empty($c['pdf_path'])):?><button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#uP<?=(int)$c['id']?>"><i class="bi bi-upload"></i></button>
<?php else:?><button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#uP<?=(int)$c['id']?>"><i class="bi bi-arrow-repeat"></i></button>
<form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar PDF?')"><input type="hidden" name="action" value="delete_pdf"><input type="hidden" name="bloque_id" value="<?=$bloqueId?>"><input type="hidden" name="concepto_id" value="<?=(int)$c['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-x"></i></button></form>
<?php endif;?>
<form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar concepto?')"><input type="hidden" name="action" value="delete_concepto"><input type="hidden" name="bloque_id" value="<?=$bloqueId?>"><input type="hidden" name="concepto_id" value="<?=(int)$c['id']?>"><input type="hidden" name="titulo_id" value="<?=(int)$titulo['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
</td></tr>
<div class="modal fade" id="eC<?=(int)$c['id']?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form method="POST"><input type="hidden" name="action" value="edit_concepto"><input type="hidden" name="bloque_id" value="<?=$bloqueId?>"><input type="hidden" name="concepto_id" value="<?=(int)$c['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><div class="modal-header"><h5 class="modal-title">Editar concepto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" value="<?=htmlspecialchars($c['nombre'])?>" required></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning">Guardar</button></div></form></div></div></div>
<div class="modal fade" id="uP<?=(int)$c['id']?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form method="POST" enctype="multipart/form-data"><input type="hidden" name="action" value="upload_pdf"><input type="hidden" name="bloque_id" value="<?=$bloqueId?>"><input type="hidden" name="concepto_id" value="<?=(int)$c['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><div class="modal-header"><h5 class="modal-title"><?=empty($c['pdf_path'])?'Subir':'Reemplazar'?> PDF</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="file" name="pdf" class="form-control" accept=".pdf" required></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Subir</button></div></form></div></div></div>
<?php endforeach;?></tbody></table></div><?php endif;?>
</div></div>
<div class="modal fade" id="eTM<?=(int)$titulo['id']?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form method="POST"><input type="hidden" name="action" value="edit_titulo"><input type="hidden" name="bloque_id" value="<?=$bloqueId?>"><input type="hidden" name="titulo_id" value="<?=(int)$titulo['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><div class="modal-header"><h5 class="modal-title">Editar título</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" value="<?=htmlspecialchars($titulo['nombre'])?>" required></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning">Guardar</button></div></form></div></div></div>
<div class="modal fade" id="dTM<?=(int)$titulo['id']?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form method="POST"><input type="hidden" name="action" value="delete_titulo"><input type="hidden" name="bloque_id" value="<?=$bloqueId?>"><input type="hidden" name="titulo_id" value="<?=(int)$titulo['id']?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><div class="modal-header"><h5 class="modal-title text-danger">Eliminar título</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>¿Eliminar <strong><?=htmlspecialchars($titulo['nombre'])?></strong> y todos sus conceptos?</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Eliminar</button></div></form></div></div></div>
<?php endforeach; endif;?>
<div class="modal fade" id="deleteBlockModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form method="POST"><input type="hidden" name="action" value="delete_block"><input type="hidden" name="id" value="<?=$bloqueId?>"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>"><div class="modal-header"><h5 class="modal-title text-danger">Eliminar bloque</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>¿Eliminar bloque <strong><?=htmlspecialchars($currentBloque['anio'])?></strong> y todo su contenido?</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Eliminar</button></div></form></div></div></div>
<?php else: ?>
<div class="row g-4">
<div class="col-lg-4"><div class="card">
<div class="card-header bg-primary text-white"><i class="bi bi-plus-circle me-1"></i> Crear bloque por año</div>
<div class="card-body"><form method="POST">
<input type="hidden" name="action" value="create_block"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token)?>">
<div class="mb-3"><label class="form-label">Año</label><input type="number" name="anio" class="form-control" min="2000" max="<?=date('Y')?>" placeholder="<?=date('Y')?>" required></div>
<button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i> Crear bloque</button>
</form></div></div></div>
<div class="col-lg-8"><div class="card">
<div class="card-header"><i class="bi bi-cash-stack me-1"></i> Bloques Cuenta Pública <span class="badge bg-secondary ms-1"><?=count($bloques)?></span></div>
<div class="card-body p-0">
<?php if(empty($bloques)):?>
<div class="text-center text-muted py-4"><i class="bi bi-folder2-open" style="font-size:2rem;"></i><p class="mt-2 mb-0">No hay bloques. Use el formulario para crear uno.</p></div>
<?php else:?>
<div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead class="table-light"><tr><th>Año</th><th>Títulos</th><th>Conceptos</th><th style="width:120px">Acciones</th></tr></thead><tbody>
<?php foreach($bloques as $b):?>
<tr><td><strong><?=htmlspecialchars($b['anio'])?></strong></td>
<td><span class="badge bg-info"><?=(int)$b['num_titulos']?></span></td>
<td><span class="badge bg-secondary"><?=(int)$b['num_conceptos']?></span></td>
<td><a href="cuenta_publica.php?bloque_id=<?=(int)$b['id']?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i> Ver</a></td></tr>
<?php endforeach;?>
</tbody></table></div>
<?php endif;?>
</div></div></div></div>
<?php endif;?>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar=document.getElementById('sidebar');
if(window.innerWidth<=768)sidebar.classList.add('collapsed');
document.getElementById('toggleSidebar').addEventListener('click',function(){sidebar.classList.toggle('collapsed');});
var cb=document.getElementById('closeSidebar');if(cb)cb.addEventListener('click',function(){sidebar.classList.add('collapsed');});
</script></body></html>
