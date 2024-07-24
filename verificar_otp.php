<?php
// Incluye el archivo de conexión a la base de datos
include 'cone.php';

// Variable para almacenar el mensaje de error
$errorMessage = '';

// Verifica si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibe el OTP ingresado por el usuario
    $otpIngresado = $_POST['otp'];

    // Escapa los datos para evitar inyección de SQL
    $otpIngresado = mysqli_real_escape_string($conn, $otpIngresado);

    // Consulta SQL para verificar si el OTP ingresado es válido
    $consultaOTP = "SELECT * FROM usuarios WHERE OTP = '$otpIngresado'";
    $resultado = mysqli_query($conn, $consultaOTP);

    if (mysqli_num_rows($resultado) > 0) {
        // Si el OTP es válido, actualiza el estado del usuario como activo
        $fila = mysqli_fetch_assoc($resultado);
        $idUsuario = $fila['IdUsu'];

        $actualizarEstado = "UPDATE usuarios SET Activo = 1 WHERE IdUsu = $idUsuario";
        if (mysqli_query($conn, $actualizarEstado)) {
            // No redireccionamos aquí, solo establecemos un mensaje de éxito
            $successMessage = "¡Cuenta activada correctamente! Redireccionando al inicio...";
            // Luego, utilizamos JavaScript para redireccionar
            echo "<script>window.location.href = 'log.html';</script>";
            exit();
        } else {
            $errorMessage = "Error al activar la cuenta: " . mysqli_error($conn);
        }
    } else {
        // Si el OTP no es válido, muestra un mensaje de error en la variable
        $errorMessage = "Código OTP incorrecto. Inténtalo de nuevo.";
    }
}

// Cierra la conexión a la base de datos
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar OTP</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Función para mostrar una alerta si hay un mensaje de error
        <?php if (!empty($errorMessage)): ?>
            setTimeout(function() {
                alert("<?php echo $errorMessage; ?>");
            }, 100);
        <?php endif; ?>
    </script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.html#inicio">Inicio</a></li>
                <li><a href="index.html#historia">Historia</a></li>
                <li><a href="index.html#ubicacion">Ubicación</a></li>
                <li><a href="index.html#menu">Menú</a></li>
                <li><a href="index.html#Objetivos">Objetivos</a></li>
                <li><a href="index.html#Misión">Misión</a></li>
                <li><a href="index.html#Visión">Visión</a></li>
                <li><a href="log.html">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section id="verificar-otp" class="login-section">
            <div class="container">
                <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <h2>Verificar codigo OTP</h2>
                    <label for="otp">Ingresa tu codigo OTP:</label>
                    <input type="text" id="otp" name="otp" maxlength="6" required placeholder="Ingresa el código OTP">
                    <button type="submit">Verificar OTP</button>
                </form>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Last Bite. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
