<?php
// Incluir el archivo de conexión a la base de datos
include('cone.php');

// Verificar que se haya recibido una solicitud POST con JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    // Leer los datos JSON enviados en la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar que se haya recibido el IdUsu
    $IdUsu = isset($data['IdUsu']) ? $data['IdUsu'] : '';

    error_log("Insert Order - IdUsu recibido: " . $IdUsu);

    // Verificar que se recibió un IdUsu válido
    if ($IdUsu) {
        error_log("Insert Order - IdUsu es válido.");

        // Preparar la consulta para insertar en la tabla ordenes
        $sql_insert_order = "INSERT INTO ordenes (IdOrden, IdUsu, Fecha, estatus) VALUES (DEFAULT, ?, NOW(), ?)";
        $stmt_insert_order = $conn->prepare($sql_insert_order);
        if (!$stmt_insert_order) {
            http_response_code(500); // Error interno del servidor
            error_log("Error al preparar la consulta para insertar la orden: " . $conn->error);
            echo "Error al preparar la consulta para insertar la orden: " . $conn->error;
            exit();
        }
        // Parámetros a bindear
        $estatus = 'pendiente'; // Valor por defecto para el estatus de la orden
        $stmt_insert_order->bind_param("is", $IdUsu, $estatus);

        // Ejecutar la consulta para insertar la orden
        if ($stmt_insert_order->execute()) {
            // Obtener el ID de la última inserción
            $IdOrden = $conn->insert_id;
            http_response_code(200); // OK
            error_log("Orden insertada correctamente 1. IdUsu: " . $IdUsu . " IdOrden: " . $IdOrden);
            echo "Orden insertada correctamente. IdOrden: " . $IdOrden;

            // Actualizar el estatus de la orden a "Pagado"
            $sql_update_order = "UPDATE ordenes SET estatus = 'Pagado' WHERE IdOrden = ?";
            $stmt_update_order = $conn->prepare($sql_update_order);
            if (!$stmt_update_order) {
                error_log("Error al preparar la consulta para actualizar la orden: " . $conn->error);
            } else {
                $stmt_update_order->bind_param("i", $IdOrden);
                if ($stmt_update_order->execute()) {
                    error_log("Orden actualizada a 'Pagado' 2. IdOrden: " . $IdOrden);
				} else {
                    error_log("Error al actualizar la orden: " . $stmt_update_order->error);
                }
                $stmt_update_order->close();
            }

        } else {
            http_response_code(500); // Error interno del servidor
            error_log("Error al insertar la orden: " . $stmt_insert_order->error);
            echo "Error al insertar la orden: " . $stmt_insert_order->error;
        }

        // Cerrar el statement
        $stmt_insert_order->close();
    } else {
        http_response_code(400); // Solicitud incorrecta
        error_log("No se recibió el IdUsu válido.");
        echo "No se recibió el IdUsu válido.";
    }
} else {
    http_response_code(400); // Solicitud incorrecta
    error_log("Solicitud incorrecta. Se esperaba una solicitud POST con JSON.");
    echo "Solicitud incorrecta. Se esperaba una solicitud POST con JSON.";
}

// Cerrar la conexión
$conn->close();
?>



















































