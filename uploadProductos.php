<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
include 'cone.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    foreach ($data as $producto) {
        $nombreProducto = $producto['Nombre del Producto'] ?? null;
        $codigoBarras = $producto['Código de Barras'] ?? null;
        $fechaCaducidad = $producto['Fecha de Caducidad'] ?? null;
        $costo = $producto['Costo'] ?? null;
        $piezas = $producto['Piezas'] ?? null;
        $categoria = $producto['Categoria'] ?? null;
        $activo = $producto['Activo'] ?? null;

        // Verificar si los valores son correctos
        if (is_null($nombreProducto) || is_null($codigoBarras) || is_null($fechaCaducidad) || is_null($costo) || is_null($piezas) || is_null($categoria) || is_null($activo)) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos de producto', 'producto' => $producto]);
            exit;
        }

        // Preparar la declaración SQL para evitar inyección SQL
        $stmt = $conn->prepare("INSERT INTO productos (NombreProd, CodigoB, FecCad, Precio, Stock, Id_Categoria, Activo) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Verificar si la preparación de la declaración fue exitosa
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Error en la preparación del statement: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param("sssdiss", $nombreProducto, $codigoBarras, $fechaCaducidad, $costo, $piezas, $categoria, $activo);

        // Ejecutar la declaración
        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Error al insertar el producto: ' . $stmt->error]);
            $stmt->close();
            $conn->close();
            exit;
        }

        $stmt->close();
    }

    $conn->close();
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos']);
}
?>
