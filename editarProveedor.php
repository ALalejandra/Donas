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
    $nombre = $_POST['nombre'];
    $contacto = $_POST['contacto'];
    $usuario = $_POST['usuario'];
    $activo = $_POST['activo'];

    // Actualizar datos del proveedor
    $sql = "UPDATE proveedores SET Nombre='$nombre', Contacto='$contacto', Usuario='$usuario', Activo='$activo'";

    // Si la contraseña no está vacía, actualizarla también
    if (!empty($contras)) {
        $hashedPassword = password_hash($contras, PASSWORD_DEFAULT);
        $sql .= ", Contras='$hashedPassword'";
    }

    $sql .= " WHERE IdProveedores=$idProveedor";

    if ($conn->query($sql) === TRUE) {
        // Redirigir a la página de consulta de proveedores después de la actualización
        header('Location: ConsultaProveedores.html');
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
                <li><a href="Menu1.html" class="active">Promociones</a></li>
                <li>
                    <a href="Productos.html">Productos</a>
                    <ul>
                        <li><a href="ConsultaProd.html">Consultar Productos</a></li>
                    </ul>
                </li>
                <li>
                    <a href="altaProveedores.html">Proveedores</a>
                    <ul>
                        <li><a href="ConsultaProveedores.html">Consultar Proveedores</a></li>
                    </ul>
                </li>
                <li>
                    <a href="Usuarios.html">Adm</a>
                    <ul>
                        <li><a href="ConsultaProd.html">Consultar Adm</a></li>
                    </ul>
                </li>
                <li>
                    <a href="Productos.html">Clientes</a>
                    <ul>
                        <li><a href="ConsultaProd.html">Consultar Clientes</a></li>
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

            <label for="nombre">Nombre del Proveedor:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>

            <label for="contacto">Contacto del Proveedor:</label>
            <input type="text" id="contacto" name="contacto" value="<?php echo $contacto; ?>" required>

            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" value="<?php echo $usuario; ?>" required>

            <label for="activo">Activo:</label>
            <select id="activo" name="activo" required>
                <option value="2" <?php if ($activo == 2) echo 'selected'; ?>>Sí</option>
                <option value="4" <?php if ($activo == 4) echo 'selected'; ?>>No</option>
            </select>

            <input type="submit" value="Guardar Cambios">
        </form>
    </main>

    <footer>
        <p>&copy;2024 Last Bite. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
