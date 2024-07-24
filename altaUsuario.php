<?php
include 'cone.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['Usu'];
    $contraseña = $_POST['Contra']; // La contraseña ya estará en hash MD5
    $nombre = $_POST['Nombre'];
    $apellidoPaterno = $_POST['App'];
    $apellidoMaterno = $_POST['Apm'];
    $correo = $_POST['Correo'];
    $fechaNacimiento = $_POST['FecNam'];
    $activo = $_POST['Activo'];

    // Escapar los datos para evitar inyección de SQL
    $usuario = mysqli_real_escape_string($conn, $usuario);
    $contraseña = mysqli_real_escape_string($conn, $contraseña);
    $nombre = mysqli_real_escape_string($conn, $nombre);
    $apellidoPaterno = mysqli_real_escape_string($conn, $apellidoPaterno);
    $apellidoMaterno = mysqli_real_escape_string($conn, $apellidoMaterno);
    $correo = mysqli_real_escape_string($conn, $correo);
    $fechaNacimiento = mysqli_real_escape_string($conn, $fechaNacimiento);
    $activo = mysqli_real_escape_string($conn, $activo);

    // Insertar los datos del usuario en la base de datos
    $sql = "INSERT INTO usuarios (Usu, Contra, Nombre, App, Apm, Correo, FecNam, Activo) 
            VALUES ('$usuario', '$contraseña', '$nombre', '$apellidoPaterno', '$apellidoMaterno', '$correo', '$fechaNacimiento', '$activo')";

    if (mysqli_query($conn, $sql)) {
        header('Location: Usuarios.html');
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
