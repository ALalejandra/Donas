<?php
require_once('cone.php');

if ($_SERVER["REQUEST_METHOD"] == "PUT" && isset($_GET['id']) && isset($_GET['activo'])) {
    // Obtén el ID del proveedor y el estado activo desde la URL
    $idProveedor = $_GET['id'];
    $activo = $_GET['activo'];

    // Prepara la consulta SQL para actualizar el estado activo del proveedor
    $sql = "UPDATE proveedores SET Activo = ? WHERE IdProveedores = ?";

    // Prepara y ejecuta la declaración usando una consulta preparada para seguridad
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $activo, $idProveedor); // "ii" indica que son enteros
    if ($stmt->execute()) {
        // Si la actualización tiene éxito, devuelve un estado 200 (OK)
        http_response_code(200);
        echo json_encode(array("message" => "Estado del proveedor actualizado correctamente."));
    } else {
        // Si hay un error, devuelve un estado 500 (Error interno del servidor)
        http_response_code(500);
        echo json_encode(array("message" => "Error al cambiar el estado del proveedor: " . $conn->error));
    }

    $stmt->close();
} else {
    // Si no se recibe una solicitud PUT válida, devuelve un estado 405 (Método no permitido)
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido o ID de proveedor o estado activo no especificados."));
}

$conn->close();
?>
