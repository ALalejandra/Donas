<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Nombre Proveedor";

// Incluir el archivo de conexión a la base de datos
include('cone.php');

// Consulta SQL para obtener los detalles de órdenes del usuario actual
$sql = "SELECT
            d.IdDetalle,
            d.IdOrden,
            d.IdProducto,
            d.Cantidad,
            d.PrecioUnitario,
            p.NombreProd,
            p.CodigoB,
            p.FecCad,
            p.Precio
        FROM `detallesordenes` AS d
        JOIN `productos` AS p ON p.IdProd = d.IdProducto
        JOIN `ordenes` AS o ON d.IdOrden = o.IdOrden
        JOIN `usuarios` AS u ON o.IdUsu = u.IdUsu
        WHERE u.Usu = ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Bind de parámetros
    $stmt->bind_param("s", $usuario);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Obtener resultados
    $result = $stmt->get_result();
    
    // Cerrar declaración
    $stmt->close();
} else {
    // Manejar errores de preparación de consulta
    error_log("Error al preparar la consulta: " . $conn->error);
}

// Cerrar conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesqq.css">
	<script type="text/javascript">(function () {
var ldk = document.createElement('script');
ldk.type = 'text/javascript';
ldk.async = true;
ldk.src = 'https://s.cliengo.com/weboptimizer/6681d2218b43c13fa257176d/6681d2228b43c13fa2571770.js?platform=dashboard';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ldk, s); })();
</script>

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
        <h1>Detalles de Órdenes</h1>
        <div id="productosContainer">
            <table>
                <thead>
                    <tr>
                        <th>IdDetalle</th>
                        <th>IdOrden</th>
                        <th>IdProducto</th>
                        <th>Cantidad</th>
                        <th>PrecioUnitario</th>
                        <th>NombreProd</th>
                        <th>CodigoB</th>
                        <th>FecCad</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Verificar si hay resultados
                    if ($result && $result->num_rows > 0) {
                        // Iterar sobre cada fila de resultados
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['IdDetalle']); ?></td>
                                <td><?php echo htmlspecialchars($row['IdOrden']); ?></td>
                                <td><?php echo htmlspecialchars($row['IdProducto']); ?></td>
                                <td><?php echo htmlspecialchars($row['Cantidad']); ?></td>
                                <td><?php echo htmlspecialchars($row['PrecioUnitario']); ?></td>
                                <td><?php echo htmlspecialchars($row['NombreProd']); ?></td>
                                <td><?php echo htmlspecialchars($row['CodigoB']); ?></td>
                                <td><?php echo htmlspecialchars($row['FecCad']); ?></td>
                                <td><?php echo htmlspecialchars($row['Precio']); ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        // Mostrar mensaje si no hay resultados
                        echo "<tr><td colspan='9'>No se encontraron detalles de órdenes para el usuario.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>&copy;2024 Last Bite. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
