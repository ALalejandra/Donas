<?php
include 'cone.php';

$nombre = $_POST['nombre'];
$contacto = $_POST['contacto'];
$usuario = $_POST['usuario'];
$contras = $_POST['contras'];
$activo = $_POST['activo'];

// Hashear la contraseña antes de insertarla en la base de datos
$contras_hashed = password_hash($contras, PASSWORD_DEFAULT);

$sql = "INSERT INTO proveedores (Nombre, Contacto, Usuario, Contras, Activo) VALUES ('$nombre', '$contacto', '$usuario', '$contras_hashed', '$activo')";

if ($conn->query($sql) === TRUE) {
    // Redirige a ConsultaProveedores.html
    header('Location: ConsultaProveedores.html');
    exit; // Asegura que el script se detenga después de la redirección
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
