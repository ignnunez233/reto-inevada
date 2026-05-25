<?php
declare(strict_types=1);
require 'db.php';
/**
 * Sanitiza de manera recursiva un arreglo de datos contra ataques XSS.
 * Convierte caracteres especiales en entidades HTML seguras.
 */
function sanitize_xss(array $data): array {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $data[$key] = sanitize_xss($value);
        } elseif (is_string($value)) {
            // ENT_QUOTES asegura que se escapen comillas simples y dobles
            $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    return $data;
}
header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

try {
    // --- CRUD VEHÍCULOS ---
    if ($action === 'get_vehiculos') {
        $stmt = $pdo->query("SELECT * FROM vehiculos");
        $resultados = $stmt->fetchAll();
        // Aplicamos sanitización antes de codificar en JSON
        echo json_encode(sanitize_xss($resultados));
    }
    
    elseif ($action === 'create_vehiculo') {
        // Limpiamos espacios y pasamos a mayúsculas
        $patente = strtoupper(trim($_POST['patente'] ?? ''));
        $modelo = trim($_POST['modelo'] ?? '');

        // Validación patentes: El último carácter debe ser un número entre 0 y 9
        // El símbolo '$' indica el final de la cadena de texto
        if (!preg_match('/[0-9]$/', $patente)) {
            echo json_encode(['error' => 'La patente es inválida. Debe terminar estrictamente en un número (0-9).']);
            exit; // Detiene la ejecución inmediatamente para no insertar en la BD
        }

        // Si pasa la validación, procedemos con la inserción segura
        $stmt = $pdo->prepare("INSERT INTO vehiculos (patente, modelo) VALUES (?, ?)");
        $stmt->execute([$patente, $modelo]);
        echo json_encode(['success' => true]);
    }
    
    elseif ($action === 'delete_vehiculo') {
        $stmt = $pdo->prepare("DELETE FROM vehiculos WHERE patente = ?");
        $stmt->execute([$_POST['patente']]);
        echo json_encode(['success' => true]);
    }

    // --- CRUD DEUDAS ---
    elseif ($action === 'get_deudas') {
        $stmt = $pdo->query("SELECT * FROM deudas");
        $resultados = $stmt->fetchAll();
        // Aplicamos sanitización antes de codificar en JSON
        echo json_encode(sanitize_xss($resultados));
    }

    elseif ($action === 'create_deuda') {
        $stmt = $pdo->prepare("INSERT INTO deudas (patente, monto, motivo, fecha_emision) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['patente'], $_POST['monto'], $_POST['motivo'], $_POST['fecha_emision']]);
        echo json_encode(['success' => true]);
    }
    
    else {
        echo json_encode(['error' => 'Acción no válida']);
    }
} catch (Exception $e) {
    // En producción, esto se guarda en el archivo de logs del servidor (error_log)
    error_log("CRITICAL ERROR: " . $e->getMessage());
    
    // Al cliente (frontend) solo le devolvemos un mensaje genérico por seguridad
    http_response_code(500);
    echo json_encode(['error' => 'Ocurrió un error interno en el servidor. Inténtelo más tarde.']);
}
?>
