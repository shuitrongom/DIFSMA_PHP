<?php
/**
 * Feature: dif-cms-php-migration, Property 16: round-trip del footer en todas las páginas
 *
 * Validates: Requirements 14.3
 *
 * Para cualquier configuración guardada en `footer_config`, get_footer_config()
 * retorna los mismos valores — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Extrae la lógica de consulta del footer de includes/footer.php en una función testable.
 * Consulta footer_config WHERE id=1 y retorna el array de valores del footer.
 * Si no existe registro, retorna los valores predeterminados.
 */
function get_footer_config(PDO $pdo): array
{
    $defaults = [
        'texto_inst'    => 'Sistema Municipal DIF San Mateo Atenco, comprometido con el bienestar de las familias.',
        'horario'       => 'Horario de lunes a viernes de 8:00 a 16:00 horas',
        'direccion'     => 'Mariano Matamoros 310, Bo. La Concepción, San Mateo Atenco.',
        'telefono'      => '722 970 77 86',
        'email'         => 'presidencia@difsanmateoatenco.gob.mx',
        'url_facebook'  => 'https://facebook.com/DifSanMateoAtenco/',
        'url_twitter'   => 'https://twitter.com/DIFSMA',
        'url_instagram' => 'https://www.instagram.com/difsma',
    ];

    try {
        $stmt = $pdo->prepare('SELECT * FROM footer_config WHERE id = 1 LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            foreach ($defaults as $key => $default) {
                if (!empty($row[$key])) {
                    $defaults[$key] = $row[$key];
                }
            }
        }
    } catch (\PDOException $e) {
        // En caso de error de DB, se usan los valores predeterminados
    }

    return $defaults;
}

class Property16FooterRoundTripTest extends TestCase
{
    private PDO $pdo;

    /** Campos de footer_config que se prueban en el round-trip */
    private array $fields = [
        'texto_inst',
        'horario',
        'direccion',
        'telefono',
        'email',
        'url_facebook',
        'url_twitter',
        'url_instagram',
    ];

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('
            CREATE TABLE footer_config (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                texto_inst TEXT,
                horario VARCHAR(200),
                direccion TEXT,
                telefono VARCHAR(50),
                email VARCHAR(200),
                url_facebook VARCHAR(500),
                url_twitter VARCHAR(500),
                url_instagram VARCHAR(500),
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
    }

    /**
     * Genera un string aleatorio no vacío para usar como valor de campo.
     */
    private function randomFieldValue(): string
    {
        $length = random_int(5, 80);
        return bin2hex(random_bytes((int) ceil($length / 2)));
    }

    /**
     * Genera un array aleatorio con valores para todos los campos del footer.
     */
    private function randomFooterData(): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            $data[$field] = $this->randomFieldValue();
        }
        return $data;
    }

    /**
     * Inserta o reemplaza el registro id=1 en footer_config con los datos dados.
     */
    private function saveFooterConfig(array $data): void
    {
        // Eliminar registro anterior si existe
        $this->pdo->exec('DELETE FROM footer_config WHERE id = 1');

        $stmt = $this->pdo->prepare('
            INSERT INTO footer_config
                (id, texto_inst, horario, direccion, telefono, email,
                 url_facebook, url_twitter, url_instagram)
            VALUES
                (1, :texto_inst, :horario, :direccion, :telefono, :email,
                 :url_facebook, :url_twitter, :url_instagram)
        ');
        $stmt->execute([
            ':texto_inst'    => $data['texto_inst'],
            ':horario'       => $data['horario'],
            ':direccion'     => $data['direccion'],
            ':telefono'      => $data['telefono'],
            ':email'         => $data['email'],
            ':url_facebook'  => $data['url_facebook'],
            ':url_twitter'   => $data['url_twitter'],
            ':url_instagram' => $data['url_instagram'],
        ]);
    }

    /**
     * **Validates: Requirements 14.3**
     *
     * Property 16: Para cualquier configuración guardada en footer_config,
     * get_footer_config() retorna los mismos valores — 100 iteraciones.
     */
    public function testProperty16_FooterRoundTrip(): void
    {
        $failures = 0;
        $failureDetails = [];

        for ($i = 0; $i < 100; $i++) {
            $data = $this->randomFooterData();
            $this->saveFooterConfig($data);

            $result = get_footer_config($this->pdo);

            foreach ($this->fields as $field) {
                if ($result[$field] !== $data[$field]) {
                    $failures++;
                    $failureDetails[] = "Iteration {$i}: field '{$field}' expected '{$data[$field]}', got '{$result[$field]}'";
                    break;
                }
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 16 failed in {$failures}/100 iterations.\n" . implode("\n", array_slice($failureDetails, 0, 5))
        );
    }

    /**
     * **Validates: Requirements 14.3**
     *
     * Verifica que todos los campos del footer se recuperan correctamente
     * campo por campo con valores conocidos.
     */
    public function testAllFooterFieldsAreReturnedCorrectly(): void
    {
        $data = [
            'texto_inst'    => 'Institución de bienestar social municipal.',
            'horario'       => 'Lunes a viernes 9:00 a 15:00 horas',
            'direccion'     => 'Calle Principal 123, Centro',
            'telefono'      => '722 123 45 67',
            'email'         => 'contacto@dif.gob.mx',
            'url_facebook'  => 'https://facebook.com/dif',
            'url_twitter'   => 'https://twitter.com/dif',
            'url_instagram' => 'https://instagram.com/dif',
        ];

        $this->saveFooterConfig($data);
        $result = get_footer_config($this->pdo);

        foreach ($this->fields as $field) {
            $this->assertSame(
                $data[$field],
                $result[$field],
                "Field '{$field}' was not returned correctly."
            );
        }
    }

    /**
     * **Validates: Requirements 14.3**
     *
     * Cuando no existe registro en footer_config, se usan los valores predeterminados.
     */
    public function testDefaultsUsedWhenNoRecordExists(): void
    {
        // No insertar ningún registro
        $result = get_footer_config($this->pdo);

        $this->assertNotEmpty($result['texto_inst'], 'Default texto_inst should not be empty.');
        $this->assertNotEmpty($result['horario'], 'Default horario should not be empty.');
        $this->assertNotEmpty($result['direccion'], 'Default direccion should not be empty.');
        $this->assertNotEmpty($result['telefono'], 'Default telefono should not be empty.');
        $this->assertNotEmpty($result['email'], 'Default email should not be empty.');
        $this->assertNotEmpty($result['url_facebook'], 'Default url_facebook should not be empty.');
        $this->assertNotEmpty($result['url_twitter'], 'Default url_twitter should not be empty.');
        $this->assertNotEmpty($result['url_instagram'], 'Default url_instagram should not be empty.');
    }

    /**
     * **Validates: Requirements 14.3**
     *
     * Verifica que actualizar el registro id=1 se refleja en la siguiente llamada
     * a get_footer_config() — simula el comportamiento de "todas las páginas
     * renderizan el footer con los valores actualizados".
     */
    public function testUpdatedConfigIsReflectedImmediately(): void
    {
        $original = $this->randomFooterData();
        $this->saveFooterConfig($original);

        $first = get_footer_config($this->pdo);
        foreach ($this->fields as $field) {
            $this->assertSame($original[$field], $first[$field], "First read: field '{$field}' mismatch.");
        }

        // Actualizar con nuevos valores
        $updated = $this->randomFooterData();
        $this->saveFooterConfig($updated);

        $second = get_footer_config($this->pdo);
        foreach ($this->fields as $field) {
            $this->assertSame($updated[$field], $second[$field], "Second read after update: field '{$field}' mismatch.");
        }
    }

    /**
     * **Validates: Requirements 14.3**
     *
     * Verifica que campos vacíos en la DB no sobreescriben los valores predeterminados
     * (comportamiento de includes/footer.php: solo sobreescribe si !empty($row[$key])).
     */
    public function testEmptyDbFieldsFallBackToDefaults(): void
    {
        // Insertar registro con todos los campos vacíos
        $this->pdo->exec('DELETE FROM footer_config WHERE id = 1');
        $this->pdo->exec("
            INSERT INTO footer_config (id, texto_inst, horario, direccion, telefono, email,
                url_facebook, url_twitter, url_instagram)
            VALUES (1, '', '', '', '', '', '', '', '')
        ");

        $result = get_footer_config($this->pdo);

        // Con campos vacíos, deben usarse los valores predeterminados
        $this->assertNotEmpty($result['texto_inst'], 'Empty DB field should fall back to default texto_inst.');
        $this->assertNotEmpty($result['email'], 'Empty DB field should fall back to default email.');
        $this->assertNotEmpty($result['url_facebook'], 'Empty DB field should fall back to default url_facebook.');
    }
}
