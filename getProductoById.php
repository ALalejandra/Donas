<?php
// getProductoById.php

// Incluir el archivo de conexión a la base de datos
include 'cone.php';

// Configurar la respuesta como JSON
header('Content-Type: application/json');

// Obtener el ID del producto desde la solicitud GET
$productId = $_GET['id'];

// Consulta SQL para obtener el producto por su ID
$sql = "SELECT * FROM productos WHERE IdProd = $productId";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $producto = $result->fetch_assoc();
    $producto['FecCad'] = ($producto['FecCad'] == '0000-00-00') ? '' : $producto['FecCad']; // Manejar fecha inválida
    echo json_encode($producto);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
}

// Cerrar conexión a la base de datos
$conn->close();
?>
