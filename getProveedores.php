<?php
header('Content-Type: application/json');
include 'cone.php';

$sql = "SELECT IdProveedores, Nombre, Contacto, Usuario, Activo FROM proveedores";
$result = $conn->query($sql);

$proveedores = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $proveedores[] = $row;
    }
}

echo json_encode($proveedores);

$conn->close();
?>
