<?php
include 'cone.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Escapar el ID para evitar inyección de SQL
    $id = mysqli_real_escape_string($conn, $id);

    // Consultar los datos del usuario por ID, excluyendo la contraseña
    $sql = "SELECT IdUsu, Usu, Nombre, App, Apm, Correo, FecNam, Activo FROM usuarios WHERE IdUsu = '$id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_assoc($result);
        echo json_encode($usuario);
    } else {
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
}

mysqli_close($conn);
?>
