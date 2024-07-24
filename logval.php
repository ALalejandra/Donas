
<?php
// Incluye el archivo de conexión a la base de datos
include 'cone.php';

// Incluye PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Función para generar OTP
function generarOTP() {
    $longitudOTP = 6; // Longitud del OTP
    return rand(pow(10, $longitudOTP-1), pow(10, $longitudOTP)-1); // Genera el OTP
}

// Verifica si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibe los datos del formulario
    $usuario = $_POST['Usu'];
    $contraseña = $_POST['Contra'];
    $nombre = $_POST['Nombre'];
    $apellidoPaterno = $_POST['App'];
    $apellidoMaterno = $_POST['Apm'];
    $correo = $_POST['Correo'];
    $fechaNacimiento = $_POST['FecNam'];

    // Escapa los datos para evitar inyección de SQL
    $usuario = mysqli_real_escape_string($conn, $usuario);
    $contraseña = mysqli_real_escape_string($conn, $contraseña);
    $nombre = mysqli_real_escape_string($conn, $nombre);
    $apellidoPaterno = mysqli_real_escape_string($conn, $apellidoPaterno);
    $apellidoMaterno = mysqli_real_escape_string($conn, $apellidoMaterno);
    $correo = mysqli_real_escape_string($conn, $correo);
    $fechaNacimiento = mysqli_real_escape_string($conn, $fechaNacimiento);

    // Verifica si el usuario ya existe
    $checkUserQuery = "SELECT Usu FROM usuarios WHERE Usu = '$usuario'";
    $result = mysqli_query($conn, $checkUserQuery);

    if (mysqli_num_rows($result) > 0) {
        // Si el usuario ya existe, redirecciona a Soynuevo.html con un mensaje de error
        header("Location: Soynuevo.html?error=duplicate_user");
        exit(); // Termina el script después de la redirección
    } else {
        // Genera OTP
        $otp = generarOTP();

        // Hash de la contraseña
        $hashContraseña = md5($contraseña);

        // Consulta SQL para insertar los datos en la tabla de usuarios
        $sql = "INSERT INTO usuarios (IdUsu, Usu, Contra, Nombre, App, Apm, Correo, FecNam, FecIng, Activo, OTP) 
                VALUES (NULL, '$usuario', '$hashContraseña', '$nombre', '$apellidoPaterno', '$apellidoMaterno', '$correo', '$fechaNacimiento', NOW(), 0, '$otp')";

        // Ejecuta la consulta
        if (mysqli_query($conn, $sql)) {
            // Envío de correo de confirmación
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64'; // Opcional: Puedes configurar la codificación a base64 para asegurar que todos los caracteres sean manejados correctamente

            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  // Cambia esto si usas otro proveedor de correo
                $mail->SMTPAuth = true;
                $mail->Username = 'lastbite.oficial@gmail.com'; // Cambia esto por tu dirección de correo
                $mail->Password = 'lednwwkgqopoucvu'; // Cambia esto por tu contraseña
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Configuración del correo
                $mail->setFrom('lastbite.oficial@gmail.com', 'Last Bite');
                $mail->addAddress($correo, $nombre); // Añade el correo del usuario registrado

                $mail->isHTML(true);
                $mail->Subject = 'Confirmación de registro en Last Bite';
                 $mail->Body    = '<p>¡Gracias por registrarte en Last Bite!</p>
                      <p>Te damos la bienvenida. Para activar tu cuenta, necesitas ingresar el siguiente código de verificación:</p>
                      <p><strong>Código de verificación:</strong> ' . $otp . '</p>
                      <p>¡Esperamos verte pronto en Last Bite!</p>
                      <p>Saludos.</p>
                      <p>El equipo de Last Bite</p>';
                $mail->send();
                echo 'Correo de confirmación enviado correctamente.';
                
                // Redirecciona a la página de verificación de OTP
                header("Location: verificar_otp.php?usuario=$usuario");
                exit(); // Termina el script después de la redirección
            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
            }

        } else {
            // Si hay un error en la consulta, muestra un mensaje de error
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// Cierra la conexión a la base de datos
mysqli_close($conn);
?>
