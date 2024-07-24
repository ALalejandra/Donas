<?php
session_start();
$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Nombre Proveedor";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="stylesxx.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><?php echo htmlspecialchars($usuario); ?></li> <!-- Mostrar el nombre del proveedor -->
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
                
                <li><a href="logout.php">Cierre de sesión</a></li>
            </ul>
        </nav>
    </header>

    <h1>Agregar Producto</h1>
    <form id="addProductForm" method="POST" action="insertPProductop.php">
        <div>
            <label for="NombreProd">Nombre del Producto:</label>
            <input type="text" id="NombreProd" name="NombreProd" placeholder="Ingresa el Nombre del producto" required>
        </div>
        <div>
            <label for="CodigoB">Código de Barras:</label>
            <input type="text" id="CodigoB" name="CodigoB" placeholder="Ingresa el Codigo de Barras" required>
        </div>
        <div>
            <label for="FecCad">Fecha de Caducidad:</label>
            <input type="date" id="FecCad" name="FecCad" required>
        </div>
        <div>
            <label for="Precio">Precio:</label>
            <input type="text" id="Precio" name="Precio" min="0" step="0.01" placeholder="Ingresa el precio por pieza" required>
        </div>
        <div>
            <label for="Stock">Stock:</label>
            <input type="number" id="Stock" name="Stock" placeholder="Ingresa el Numero de stock" required>
        </div>
        <div>
            <label for="Id_Categoria">ID de Categoría:</label>
            <input type="text" id="Id_Categoria" name="Id_Categoria" placeholder="Ingresa el codigo de la categoria" required>
        </div>
		
        <button type="submit">Agregar Producto</button>
    </form>
    
    <footer>
        <p>&copy; 2024 Tu Empresa. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
