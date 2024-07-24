<?php
header('Content-Type: application/json');
include 'cone.php';

// Calcular la fecha actual y la fecha dentro de 5 días
$currentDate = date('Y-m-d');
$fiveDaysLater = date('Y-m-d', strtotime('+5 days'));

// Consulta SQL para seleccionar productos próximos a caducar en 5 días
$sql = "SELECT NombreProd, FecCad FROM productos WHERE FecCad BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $currentDate, $fiveDaysLater);

// Ejecutar la consulta
$stmt->execute();
$result = $stmt->get_result();

$productosVentasFlash = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productosVentasFlash[] = $row;
    }
}

$stmt->close();
$conn->close();

// Codificar los resultados como JSON para enviarlos al cliente
echo json_encode($productosVentasFlash);
?>
