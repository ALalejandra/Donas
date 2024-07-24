<?php
session_start();
include 'cone.php';

// Verificar si el usuario está autenticado
$usuario = $_SESSION['usuario'] ?? null;

if ($usuario) {
    // Consulta para obtener los datos del proveedor actual
    $stmt = $conn->prepare("SELECT IdProveedores, Nombre, Contacto, Usuario, Activo FROM proveedores WHERE usuario = ?");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta de proveedores: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("s", $usuario);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $proveedores = array();
        
        while ($row = $result->fetch_assoc()) {
            $proveedores[] = $row;
        }

        echo json_encode($proveedores);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al ejecutar la consulta de proveedores: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
}

$conn->close();
?>
