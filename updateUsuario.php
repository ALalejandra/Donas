<?php
include 'cone.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['IdUsu'];
    $usuario = $_POST['Usu'];
    $nombre = $_POST['Nombre'];
    $apellidoPaterno = $_POST['App'];
    $apellidoMaterno = $_POST['Apm'];
    $correo = $_POST['Correo'];
    $fechaNacimiento = $_POST['FecNam'];
    $activo = $_POST['Activo'];

    // Escapar los datos para evitar inyección de SQL
    $usuario = mysqli_real_escape_string($conn, $usuario);
    $nombre = mysqli_real_escape_string($conn, $nombre);
    $apellidoPaterno = mysqli_real_escape_string($conn, $apellidoPaterno);
    $apellidoMaterno = mysqli_real_escape_string($conn, $apellidoMaterno);
    $correo = mysqli_real_escape_string($conn, $correo);
    $fechaNacimiento = mysqli_real_escape_string($conn, $fechaNacimiento);
    $activo = mysqli_real_escape_string($conn, $activo);


    // Actualizar los datos del usuario en la base de datos
    $sql = "UPDATE usuarios SET 
                Usu = '$usuario', 
                Nombre = '$nombre', 
                App = '$apellidoPaterno', 
                Apm = '$apellidoMaterno', 
                Correo = '$correo', 
                FecNam = '$fechaNacimiento', 
                Activo = '$activo'
            WHERE IdUsu = '$id'";

    if (mysqli_query($conn, $sql)) {
        header('Location: Usuarios.html');
    } else {
        echo 'error';
    }
}

mysqli_close($conn);
?>
