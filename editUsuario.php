<?php
include 'cone.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['IdUsu'];
    $usuario = $_POST['Usu'];
    $contraseña = $_POST['Contra'];
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

    // Encriptar la contraseña
    $hashContraseña = md5($contraseña);

    // Actualizar los datos del usuario en la base de datos
    $sql = "UPDATE usuarios SET 
                Usu = '$usuario', 
                Contra = '$hashContraseña', 
                Nombre = '$nombre', 
                App = '$apellidoPaterno', 
                Apm = '$apellidoMaterno', 
                Correo = '$correo', 
                FecNam = '$fechaNacimiento', 
                Activo = '$activo'
            WHERE IdUsu = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo 'success';
    } else {
        echo 'error';
    }
}

mysqli_close($conn);
?>
