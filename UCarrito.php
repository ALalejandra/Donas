<?php
include('cone.php');

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Nombre Proveedor";

// Consulta para obtener el correo del usuario
$sql = "SELECT correo, IdUsu FROM usuarios WHERE Usu = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->bind_result($correoUsuario, $IdUsu);
$stmt->fetch();
$stmt->close();

// Si no se encontró el correo del usuario, se puede usar un valor predeterminado o manejarlo según tu lógica de negocio
$correoUsuario = isset($correoUsuario) ? $correoUsuario : "correo@example.com";

// Verificar si se ha enviado la acción de agregar o eliminar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'agregar') {
        $producto = [
            'NombreProd' => $_POST['NombreProd'],
            'FecCad' => $_POST['FecCad'],
            'IdProd' => $_POST['IdProd'],
            'CodigoB' => $_POST['CodigoB'],
            'Precio' => $_POST['Precio'],
            'Imagen' => $_POST['Imagen'] // Asegúrate de que 'Imagen' sea la URL completa de la imagen
        ];

        // Agregar el producto al carrito de compras
        $_SESSION['carrito'][] = $producto;

        // Registro en el log de servidor
        error_log("Producto agregado al carrito: " . json_encode($producto));
    } elseif ($_POST['action'] === 'eliminar') {
        // Obtener el índice del producto a eliminar
        $indice = isset($_POST['indice']) ? $_POST['indice'] : null;
        
        // Verificar si el índice es válido y el producto existe en el carrito
        if ($indice !== null && isset($_SESSION['carrito'][$indice])) {
            // Eliminar el producto del carrito usando el índice
            unset($_SESSION['carrito'][$indice]);
            // Reindexar el array para evitar índices vacíos
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);

            // Registro en el log de servidor
            error_log("Producto eliminado del carrito. Índice: " . $indice);
        }
    }
}

// Función para calcular el monto total del carrito
function calcularMontoTotal($carrito) {
    $montoTotal = 0;
    foreach ($carrito as $producto) {
        $montoTotal += is_numeric($producto['Precio']) ? floatval($producto['Precio']) : 0;
    }
    return $montoTotal;
}

// Configuración de PayPal
$clientId = 'AVGPUX_DvtajdBwbuzqduW6VyXaa6ctVcrKcCvMYLeHcKCiHuP3m4oUyDUIq5LOvwVtRPKNa1eOc_eYn';
$clientSecret = 'ENXepJEyGNpp9J-idHgPKjOmx6NZz_SmdvbC7Ie2Z64YeLoKbdmC1aGGywuUF9yKksYgfBrrVFFI4g8K';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras con PayPal</title>
    <link rel="stylesheet" href="stylesq.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><?php echo htmlspecialchars($usuario); ?></li> <!-- Mostrar el nombre del proveedor -->
                <li><a href="Menu2.php">Promociones</a></li>
                <li><a href="Uusuarios.php">Usuarios</a></li>
                <li><a href="UCarrito.php">Carrito</a></li>
                <li><a href="Chat.php">Chat</a></li>
                <li><a href="logout.php">Cierre de sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Carrito de Compras</h1>

        <?php
        // Verificar si $_SESSION['carrito'] está definido y no vacío
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            // Calcular y mostrar el monto total
            $montoTotal = calcularMontoTotal($_SESSION['carrito']);
            ?>
            <p>Monto Total: $<?php echo number_format($montoTotal, 2); ?></p>

            <!-- Botón para pagar con PayPal -->
            <div id="paypal-button-container"></div>

            <h2>Detalles del Carrito</h2>
            <div id="carritoContainer">
                <div class="row">
                    <?php foreach ($_SESSION['carrito'] as $indice => $producto): ?>
                        <div class="col">
                            <div class="carrito-item">
                                <h3><?php echo htmlspecialchars($producto['NombreProd']); ?></h3>
                                <p>Fecha de Caducidad: <?php echo htmlspecialchars($producto['FecCad']); ?></p>
                                <p>Id Producto: <?php echo htmlspecialchars($producto['IdProd']); ?></p>
                                <p>Codigo de Barras: <?php echo htmlspecialchars($producto['CodigoB']); ?></p>
                                <p>Precio: <?php echo htmlspecialchars($producto['Precio']); ?></p>
								<img src="uploads/<?php echo htmlspecialchars($producto['Imagen']); ?>" alt="Imagen del producto">

                                <form method="post">
                                    <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                                    <button type="submit" name="action" value="eliminar">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php } else { ?>
            <p>El carrito está vacío.</p>
        <?php } ?>
    </main>

    <footer>
        <p>&copy;2024 Last Bite. Todos los derechos reservados.</p>
    </footer>

    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $clientId; ?>&currency=USD"></script>
    <script>
        // Obtener IdUsu del servidor PHP
        var IdUsu = '<?php echo $IdUsu; ?>';

        paypal.Buttons({
            createOrder: function(data, actions) {
                // Calcular el monto total a pagar
                var montoTotal = <?php echo json_encode($montoTotal); ?>;
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: montoTotal.toFixed(2)
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Enviar correo electrónico al usuario
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "send_payment_email.php", true);
                    xhr.setRequestHeader("Content-Type", "application/json");
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            console.log("Correo electrónico enviado correctamente.");
                        } else {
                            console.error("Error al enviar el correo electrónico.");
                        }
                    };
                    // Pasar el correo del usuario y los productos del carrito como parte de los datos
                    var dataToSend = {
                        details: details,
                        correoUsuario: '<?php echo $correoUsuario; ?>',
                        carrito: <?php echo json_encode($_SESSION['carrito']); ?>
                    };
                    xhr.send(JSON.stringify(dataToSend));

                    // Insertar los detalles de la orden en la tabla ordenes
                    var xhrInsertOrder = new XMLHttpRequest();
                    xhrInsertOrder.open("POST", "insert_order.php", true);
                    xhrInsertOrder.setRequestHeader("Content-Type", "application/json");
                    xhr.onload = function() {
                        if (xhrInsertOrder.status === 200) {
                            console.log("Orden insertada correctamente en la tabla ordenes.");
                        } else {
                            console.error("Error al insertar la orden en la tabla ordenes.");
                        }
                    };
                    // Datos a enviar a insert_order.php
                    var dataOrder = {
                        IdUsu: IdUsu, // Aquí se envía IdUsu
                        Fecha: new Date().toISOString().slice(0, 19).replace('T', ' '), // Fecha actual del sistema
                        estatus: 'pendiente' // Valor por defecto para el estatus de la orden
                    };
                    xhrInsertOrder.send(JSON.stringify(dataOrder));

                    alert('Transacción completada por ' + details.purchase_units[0].amount.value);
                   window.location.href = 'confirmacion.php?IdUsu=<?php echo urlencode($IdUsu); ?>';
                
			   });
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
