<?php
// updateProducto.php

// Mostrar todos los errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurar la respuesta como JSON
header('Content-Type: application/json');

// Incluir el archivo de conexión a la base de datos
require_once 'cone.php';

// Verificar si se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de producto no válido']);
    exit;
}

$productId = $_GET['id'];

// Validar y procesar los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $NombreProd = $_POST['NombreProd'] ?? '';
    $CodigoB = $_POST['CodigoB'] ?? '';
    $FecCad = $_POST['FecCad'] ?? '';
    $Precio = $_POST['Precio'] ?? '';
    $Stock = $_POST['Stock'] ?? '';
    $Id_Categoria = $_POST['Id_Categoria'] ?? '';
    $Activo = isset($_POST['Activo']) ? '1' : '0';

    // Ejemplo de actualización en la base de datos
    try {
        $stmt = $conn->prepare("UPDATE productos SET NombreProd=?, CodigoB=?, FecCad=?, Precio=?, Stock=?, Id_Categoria=?, Activo=? WHERE IdProd=?");
        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Error preparando la consulta']);
            exit;
        }
        $stmt->bind_param("sssdiisi", $NombreProd, $CodigoB, $FecCad, $Precio, $Stock, $Id_Categoria, $Activo, $productId);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el producto']);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no válido']);
}
?>
