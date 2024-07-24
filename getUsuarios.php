<?php
include 'cone.php';

$sql = "SELECT IdUsu, Usu, Nombre, App, Apm, Correo, FecNam, FecIng, Activo FROM usuarios";
$result = mysqli_query($conn, $sql);

$usuarios = array();

while ($row = mysqli_fetch_assoc($result)) {
    $usuarios[] = $row;
}

echo json_encode($usuarios);

mysqli_close($conn);
?>
