<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Asegúrate de que la ruta sea correcta según tu estructura de archivos

// Recibe los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$correoUsuario = isset($data["correoUsuario"]) ? $data["correoUsuario"] : "correo@example.com";
$details = isset($data["details"]) ? $data["details"] : [];
$carrito = isset($data["carrito"]) ? $data["carrito"] : [];

// Configuración de PHPMailer
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
    $mail->addAddress($correoUsuario); // Añade el correo del usuario registrado

    // Generar el contenido del carrito en HTML
    $carritoHtml = '';
    if (!empty($carrito)) {
        $carritoHtml .= '<h3>Detalles del Carrito:</h3>';
        $carritoHtml .= '<ul>';
        foreach ($carrito as $index => $producto) {
            $carritoHtml .= '<li>';
            $carritoHtml .= '<strong>Nombre:</strong> ' . htmlspecialchars($producto['NombreProd']) . '<br>';
            $carritoHtml .= '<strong>Fecha de Caducidad:</strong> ' . htmlspecialchars($producto['FecCad']) . '<br>';
            $carritoHtml .= '<strong>Id Producto:</strong> ' . htmlspecialchars($producto['IdProd']) . '<br>';
            $carritoHtml .= '<strong>Código de Barras:</strong> ' . htmlspecialchars($producto['CodigoB']) . '<br>';
            $carritoHtml .= '<strong>Precio:</strong> $' . htmlspecialchars($producto['Precio']) . '<br>';

            $imagePath = 'uploads/' . $producto['Imagen']; // Asegúrate de que la ruta sea correcta
            $cid = 'image' . $index;
            $mail->addEmbeddedImage($imagePath, $cid);

            $carritoHtml .= '<strong>Imagen:</strong><br><img src="cid:' . $cid . '" alt="' . htmlspecialchars($producto['NombreProd']) . '" width="100" height="200"><br>';
            $carritoHtml .= '</li>';
        }
        $carritoHtml .= '</ul>';
    } else {
        $carritoHtml .= '<p>El carrito está vacío.</p>';
    }

    $mail->isHTML(true);
    $mail->Subject = 'Pago exitoso';
    $mail->Body    = '<p>¡Gracias por tu compra en Last Bite!</p>
                      <p>Tu pago ha sido procesado exitosamente con PayPal.</p>
                      <p>Los productos adquiridos son los siguientes.</p>' . $carritoHtml . '
                      <p>¡Esperamos verte pronto!</p>
                      <p>Saludos,</p>
                      <p>El equipo de Last Bite</p>';
    $mail->send();
    http_response_code(200);
    echo json_encode(array("message" => "Correo electrónico enviado correctamente."));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Error al enviar el correo electrónico: {$mail->ErrorInfo}"));
}
?>
