<?php
session_start();

$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Nombre Proveedor";

// Función para conectar a la base de datos
include('cone.php');

// Consulta para obtener IdUsu
$sql_id_usu = "SELECT IdUsu FROM usuarios WHERE Usu = ?";
$stmt_id_usu = $conn->prepare($sql_id_usu);
$stmt_id_usu->bind_param("s", $usuario);
$stmt_id_usu->execute();
$stmt_id_usu->bind_result($IdUsu);
$stmt_id_usu->fetch();
$stmt_id_usu->close();

// Verificar si IdUsu se obtuvo correctamente
if (!isset($IdUsu)) {
    // Manejar caso donde no se encuentra el IdUsu
    error_log("No se encontró IdUsu para el usuario: " . $usuario);
    exit(); // Puedes redirigir o mostrar un mensaje de error adecuado
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pago</title>
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
        <h3>Tu Pago fue exitoso, aquí encontrarás los detalles de tu Compra</h3>
        <h3>Productos Comprados</h3>
        <div id="productosContainer">
            <?php foreach ($_SESSION['carrito'] as $producto): ?>
                <div class="producto-item">
                    <h4><?php echo htmlspecialchars($producto['NombreProd']); ?></h4>
                    <p>Fecha de Caducidad: <?php echo htmlspecialchars($producto['FecCad']); ?></p>
                    <p>Id Producto: <?php echo htmlspecialchars($producto['IdProd']); ?></p>
                    <p>Código de Barras: <?php echo htmlspecialchars($producto['CodigoB']); ?></p>
                    <p>Precio: <?php echo htmlspecialchars($producto['Precio']); ?></p>
					<img src="uploads/<?php echo htmlspecialchars($producto['Imagen']); ?>" alt="Imagen del producto">
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        // Consulta para obtener IdOrden
        $sql_id_orden = "SELECT IdOrden FROM ordenes WHERE IdUsu = ? ORDER BY IdOrden DESC LIMIT 1";
        $stmt_id_orden = $conn->prepare($sql_id_orden);
        $stmt_id_orden->bind_param("i", $IdUsu);
        $stmt_id_orden->execute();
        $stmt_id_orden->bind_result($IdOrden);
        $stmt_id_orden->fetch();
        $stmt_id_orden->close();

        // Calcular el monto total del carrito
        function calcularMontoTotal($carrito) {
            $montoTotal = 0;
            foreach ($carrito as $producto) {
                $montoTotal += is_numeric($producto['Precio']) ? intval($producto['Precio']) : 0;
            }
            return $montoTotal;
        }

        // Verificar si $_SESSION['carrito'] está definido y no está vacío
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            $montoTotal = calcularMontoTotal($_SESSION['carrito']);
        } else {
            $montoTotal = 0; // Otra lógica que desees aplicar si no hay productos en el carrito
        }

        // Actualizar el stock en la base de datos
        foreach ($_SESSION['carrito'] as $producto) {
            $idProducto = $producto['IdProd'];
            $cantidadComprada = 1; // Ajusta esto según tu estructura de datos de carrito
            $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE IdProd = ?";
            $stmt_update_stock = $conn->prepare($sql_update_stock);
            if ($stmt_update_stock) {
                $stmt_update_stock->bind_param("ii", $cantidadComprada, $idProducto);
                if ($stmt_update_stock->execute()) {
                    // Éxito al actualizar el stock
                    error_log("Stock actualizado correctamente para el producto IdProd: " . $idProducto);
                } else {
                    // Error al ejecutar la actualización
                    error_log("Error al actualizar el stock para el producto IdProd: " . $idProducto . ", Error: " . $stmt_update_stock->error);
                }
                $stmt_update_stock->close();
            } else {
                // Error al preparar la consulta
                error_log("Error al preparar la consulta para actualizar el stock: " . $conn->error);
            }
        }

        // Insertar en la tabla transacciones
        $tipoTransaccion = "Paypal";
        $fechaTransaccion = date('Y-m-d H:i:s'); // Fecha actual del sistema

        $sql_insert_transaccion = "INSERT INTO transacciones (Tipo, Fecha, Cantidad, IdOrden) VALUES (?, ?, ?, ?)";
        $stmt_insert_transaccion = $conn->prepare($sql_insert_transaccion);
        if ($stmt_insert_transaccion) {
            $stmt_insert_transaccion->bind_param("ssdi", $tipoTransaccion, $fechaTransaccion, $montoTotal, $IdOrden);
            if ($stmt_insert_transaccion->execute()) {
                // Éxito al insertar la transacción
                error_log("Transacción insertada correctamente. IdOrden: " . $IdOrden);
                
                // Actualizar el estatus de la orden a "Pagado"
                $sql_update_order = "UPDATE ordenes SET Estatus = 'Pagado' WHERE IdOrden = ?";
                $stmt_update_order = $conn->prepare($sql_update_order);
                if ($stmt_update_order) {
                    $stmt_update_order->bind_param("i", $IdOrden);
                    if ($stmt_update_order->execute()) {
                        // Éxito al actualizar el estatus de la orden
                        error_log("Estatus de la orden actualizado correctamente. IdOrden: " . $IdOrden);
                    } else {
                        // Error al ejecutar la actualización
                        error_log("Error al actualizar el estatus de la orden. Error: " . $stmt_update_order->error);
                    }
                    $stmt_update_order->close();
                } else {
                    // Error al preparar la consulta
                    error_log("Error al preparar la consulta para actualizar el estatus de la orden: " . $conn->error);
                }

                // Insertar en la tabla detalle_transacciones
                foreach ($_SESSION['carrito'] as $producto) {
                    $idProducto = $producto['IdProd'];
                    $cantidadComprada = 1; // Ajusta esto según tu estructura de datos de carrito
                    $precioUnitario = $producto['Precio'];
                    $sql_insert_detalle = "INSERT INTO detallesordenes (IdOrden, IdProducto, Cantidad, PrecioUnitario) VALUES (?, ?, ?, ?)";
                    $stmt_insert_detalle = $conn->prepare($sql_insert_detalle);
                    if ($stmt_insert_detalle) {
                        $stmt_insert_detalle->bind_param("iiid", $IdOrden, $idProducto, $cantidadComprada, $precioUnitario);
                        if ($stmt_insert_detalle->execute()) {
                            // Éxito al insertar el detalle de la transacción
                            error_log("Detalle de transacción insertado correctamente. IdOrden: " . $IdOrden . ", IdProducto: " . $idProducto);
                        } else {
                            // Error al ejecutar la inserción
                            error_log("Error al insertar el detalle de la transacción. Error: " . $stmt_insert_detalle->error);
                        }
                        $stmt_insert_detalle->close();
                    } else {
                        // Error al preparar la consulta
                        error_log("Error al preparar la consulta para insertar el detalle de la transacción: " . $conn->error);
                    }
                }

                // Vaciar el carrito después de completar la compra
                $_SESSION['carrito'] = array();

            } else {
                // Error al ejecutar la inserción
                error_log("Error al insertar la transacción. Error: " . $stmt_insert_transaccion->error);
            }
            $stmt_insert_transaccion->close();
        } else {
            // Error al preparar la consulta
            error_log("Error al preparar la consulta para insertar la transacción: " . $conn->error);
        }
        ?>
        <h3>Monto Total: $<?php echo number_format($montoTotal, 2); ?></h3>
        <h3>Tu número de orden es: <?php echo htmlspecialchars($IdOrden); ?></h3>

        <p>¡Gracias por tu compra en nuestra tienda!</p>
    </main>

    <footer>
        <p>&copy;2024 Last Bite. Todos los derechos reservados.</p>
    </footer>

    <?php
    $conn->close();
    ?>
</body>
</html>
