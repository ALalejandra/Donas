<?php
require_once('cone.php');

// Inicializar el array de respuesta
$response = array();

// Comprobar si el parámetro 'id' está presente en la solicitud
if (!isset($_GET['id'])) {
    $response = array(
        "status" => "error",
        "message" => "ID del producto no especificado"
    );
} else {
    $productId = $_GET['id'];

    // Prepara la consulta para eliminar el producto
    $sql = "DELETE FROM productos WHERE IdProd = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $response = array(
            "status" => "error",
            "message" => "Error al preparar la consulta: " . $conn->error
        );
    } else {
        $stmt->bind_param("i", $productId); // "i" indica que es un entero

        if ($stmt->execute()) {
            $response = array(
                "status" => "success",
                "message" => "Producto eliminado correctamente"
            );
        } else {
            $response = array(
                "status" => "error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            );
        }

        $stmt->close();
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
