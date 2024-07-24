<?php
include 'cone.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $idProveedor = $_GET['id'];

    // Obtener datos del proveedor a editar
    $sql = "SELECT * FROM proveedores WHERE IdProveedores = $idProveedor";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre = $row['Nombre'];
        $contacto = $row['Contacto'];
        $usuario = $row['Usuario'];
        $activo = $row['Activo'];
    } else {
        echo "No se encontró el proveedor.";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idProveedor = $_POST['id'];
    $contacto = $_POST['contacto'];
    

    // Actualizar datos del proveedor
    $sql = "UPDATE proveedores SET Contacto='$contacto'";

    // Si la contraseña no está vacía, actualizarla también
    if (!empty($contras)) {
        $hashedPassword = password_hash($contras, PASSWORD_DEFAULT);
        $sql .= ", Contras='$hashedPassword'";
    }

    $sql .= " WHERE IdProveedores=$idProveedor";

    if ($conn->query($sql) === TRUE) {
        // Redirigir a la página de consulta de proveedores después de la actualización
        header('Location: ConsultaPProveedores.php');
        exit;
    } else {
        echo "Error al actualizar el proveedor: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor</title>
    <link rel="stylesheet" href="stylesxx.css">
</head>
<body>
    <header>
        <nav>
           <ul>
                <li><?php echo htmlspecialchars($usuario); ?></li>
                <li><a href="Menu3.php" class="active">Promociones</a></li>
                <li>
                    <a href="PProductos.php">Productos</a>
                    <ul>
                        <li><a href="ConsultaPProd.php">Consultar Productos</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">Proveedores</a>
                    <ul>
                        <li><a href="ConsultaPProveedores.php">Consultar Proveedores</a></li>
                    </ul>
                </li>
                <li><a href="#">Cierre de sesión</a></li>

            </ul>
        </nav>
    </header>

    <main>
        <h1>Editar Proveedor</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $idProveedor; ?>">

            <label for="contacto">Contacto del Proveedor:</label>
            <input type="text" id="contacto" name="contacto" value="<?php echo $contacto; ?>" required>

            <input type="submit" value="Guardar Cambios">
        </form>
    </main>

    <footer>
        <p>&copy;2024 Last Bite. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
