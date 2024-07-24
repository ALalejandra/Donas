// actualizar_estatus.php

<?php
// Incluir el archivo de conexión a la base de datos
include('cone.php');

// Obtener el IdOrden pasado como argumento
$IdOrden = isset($argv[1]) ? intval($argv[1]) : 0;

if ($IdOrden > 0) {
    // Preparar la consulta para actualizar el estatus de la orden
    $sql_update_order = "UPDATE ordenes SET estatus = 'Pagado' WHERE IdOrden = ?";
    $stmt_update_order = $conn->prepare($sql_update_order);
    if (!$stmt_update_order) {
        error_log("Error al preparar la consulta para actualizar la orden: " . $conn->error);
    } else {
        $stmt_update_order->bind_param("i", $IdOrden);
        if ($stmt_update_order->execute()) {
            error_log("Orden actualizada a 'Pagado'. IdOrden: " . $IdOrden);
        } else {
            error_log("Error al actualizar la orden: " . $stmt_update_order->error);
        }
        $stmt_update_order->close();
    }
} else {
    error_log("IdOrden no válido recibido.");
}

// Cerrar la conexión
$conn->close();
?>
