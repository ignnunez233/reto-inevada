<?php
declare(strict_types=1);
require 'db.php';
header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

try {
    // --- CRUD VEHÍCULOS ---
    if ($action === 'get_vehiculos') {
        $stmt = $pdo->query("SELECT * FROM vehiculos");
        echo json_encode($stmt->fetchAll());
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
        echo json_encode($stmt->fetchAll());
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
