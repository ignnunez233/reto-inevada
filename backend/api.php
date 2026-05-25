<?php
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
        // Las variables se pasan a execute() para evitar Inyección SQL nativamente
        $stmt = $pdo->prepare("INSERT INTO vehiculos (patente, modelo) VALUES (?, ?)");
        $stmt->execute([$_POST['patente'], $_POST['modelo']]);
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
    echo json_encode(['error' => $e->getMessage()]);
}
?>