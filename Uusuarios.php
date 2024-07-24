<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirigir a la página de inicio de sesión si no está autenticado
    exit();
}

$usuario = $_SESSION['usuario'];

// Función para conectar a la base de datos
include('cone.php');

// Variables para almacenar mensajes de éxito o error
$msg = '';
$error = '';

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $contra_actual = $_POST['contra_actual'];
    $contra_nueva = $_POST['contra_nueva'];
    $contra_confirmar = $_POST['contra_confirmar'];
    $correo = $_POST['correo'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Consulta para obtener la contraseña actual del usuario
    $sql_select_pass = "SELECT Contra FROM usuarios WHERE Usu = ?";
    $stmt_select_pass = $conn->prepare($sql_select_pass);
    $stmt_select_pass->bind_param("s", $usuario);
    $stmt_select_pass->execute();
    $stmt_select_pass->bind_result($contra_bd);
    $stmt_select_pass->fetch();
    $stmt_select_pass->close();

    // Verificar si la contraseña actual coincide
    if (!empty($contra_actual)) {
        if (md5($contra_actual) != $contra_bd) {
            $error = "La contraseña actual no es válida.";
        }
    }

    // Validar y procesar cambios si la contraseña actual es correcta
    if (empty($error)) {
        // Preparar la consulta para actualizar los datos del usuario
        $sql_update = "UPDATE usuarios SET Correo = ?, Activo = ?";

        // Actualizar la contraseña si se proporcionó una nueva contraseña
        if (!empty($contra_nueva) && ($contra_nueva == $contra_confirmar)) {
            $sql_update .= ", Contra = ?";
            $contra_nueva_md5 = md5($contra_nueva);
        }

        $sql_update .= " WHERE Usu = ?";
        $stmt_update = $conn->prepare($sql_update);

        // Bindear parámetros
        if (!empty($contra_nueva) && ($contra_nueva == $contra_confirmar)) {
            $stmt_update->bind_param("siss", $correo, $activo, $contra_nueva_md5, $usuario);
        } else {
            $stmt_update->bind_param("sis", $correo, $activo, $usuario);
        }

        // Ejecutar consulta
        if ($stmt_update->execute()) {
            $msg = "Información actualizada correctamente.";
        } else {
            $error = "Error al actualizar la información: " . $stmt_update->error;
        }

        $stmt_update->close();
    }
}

// Consulta para obtener los datos actuales del usuario
$sql_select_user = "SELECT Usu, Correo, Activo FROM usuarios WHERE Usu = ?";
$stmt_select_user = $conn->prepare($sql_select_user);
$stmt_select_user->bind_param("s", $usuario);
$stmt_select_user->execute();
$stmt_select_user->bind_result($usu, $correo, $activo);
$stmt_select_user->fetch();
$stmt_select_user->close();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="stylesqq.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><?php echo htmlspecialchars($usuario); ?></li>
                <li><a href="Menu2.php">Promociones</a></li>
                <li><a href="Uusuarios.php">Usuarios</a></li>
                <li><a href="UCarrito.php">Carrito</a></li>
                <li><a href="Chat.php">Chat</a></li>
                <li><a href="logout.php">Cierre de sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Editar Información de Usuario</h1>
        <?php if (!empty($msg)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($msg); ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usu); ?>" disabled>
            </div>
            <div>
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
            </div>
            <div>
                <label for="activo">Activo:</label>
                <input type="checkbox" id="activo" name="activo" value="1" <?php if ($activo == 1) echo "checked"; ?>>
            </div>
            <div>
                <label for="contra_actual">Contraseña Actual:</label>
                <input type="password" id="contra_actual" name="contra_actual">
            </div>
            <div>
                <label for="contra_nueva">Nueva Contraseña:</label>
                <input type="password" id="contra_nueva" name="contra_nueva">
            </div>
            <div>
                <label for="contra_confirmar">Confirmar Contraseña:</label>
                <input type="password" id="contra_confirmar" name="contra_confirmar">
            </div>
            <div>
                <button type="submit">Guardar Cambios</button>
            </div>
        </form>
    </main>

    <footer>
        <p>&copy;2024 Last Bite. Todos los derechos reservados.</p>
    </footer>

    <?php
    $conn->close();
    ?>
</body>
</html>
