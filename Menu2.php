<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Nombre Proveedor";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones</title>
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
                <li><a href="Chat.php">Compras</a></li>
                <li><a href="logout.php">Cierre de sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Carrusel de imágenes -->
        <div class="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="Nito.Png" alt="Image 1">
                </div>
                <div class="carousel-item">
                    <img src="Donas.png" alt="Image 2">
                </div>
                <div class="carousel-item">
                    <img src="Game.png" alt="Image 3">
                </div>
                <div class="carousel-item">
                    <img src="Fames.png" alt="Image 4">
                </div>
                <div class="carousel-item">
                    <img src="Falletas.png" alt="Image 5">
                </div>
            </div>
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>

        <!-- Botón de ventas flash -->
        <div class="flash-sale-container">
            <button class="flash-sale-btn" id="flashSaleBtn">Ventas Flash</button>
        </div>

        <!-- Botones cuadrados -->
        <div class="square-buttons-container">
            <button class="square-btn" onclick="cargarProductos(1)">Galletas</button>
            <button class="square-btn" onclick="cargarProductos(2)">Chocolates</button>
            <button class="square-btn" onclick="cargarProductos(3)">Gelatinas</button>
        </div>

        <!-- Caja de texto para buscar (inicialmente oculta) -->
        <div class="search-container" id="searchContainer">
            <input type="text" id="searchInput" placeholder="Buscar...">
        </div>

        <div id="productosContainer">
            <!-- Este div se llenará dinámicamente con los productos cargados -->
        </div>
    </main>

    <footer>
        <p>&copy;2024 Last Bite. Todos los derechos reservados.</p>
    </footer>

    <!-- Script para resaltar el enlace activo y controlar el carrusel -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Función para cargar productos próximos a caducar al hacer clic en el botón
            document.getElementById('flashSaleBtn').addEventListener('click', function() {
                fetch('productosProximosCaducar.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            alert('Productos próximos a caducar en 5 días:');
                            data.forEach(producto => {
                                alert(`Nombre: ${producto.NombreProd}, Fecha de Caducidad: ${producto.FecCad}, Id Producto: ${producto.IdProd}, Codigo Barras: ${producto.CodigoB}, Precio: ${producto.Precio}`);
                                // Aquí podrías implementar lógica para mostrar los productos en la interfaz gráfica
                            });
                        } else {
                            alert('No hay productos próximos a caducar en 5 días.');
                        }
                    })
                    .catch(error => console.error('Error al obtener productos:', error));
            });

            // Variables del carrusel
            let slideIndex = 0;

            // Función para mostrar la diapositiva actual
            function showSlides(n) {
                let slides = document.getElementsByClassName("carousel-item");
                if (n >= slides.length) { slideIndex = 0; }
                if (n < 0) { slideIndex = slides.length - 1; }
                for (let i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                slides[slideIndex].style.display = "block";
            }

            // Función para cambiar de diapositiva
            function plusSlides(n) {
                slideIndex += n;
                showSlides(slideIndex);
            }

            // Inicializar el carrusel de imágenes
            showSlides(slideIndex);

            // Cambiar diapositiva automáticamente cada 5 segundos
            setInterval(function() {
                slideIndex++;
                showSlides(slideIndex);
            }, 5000);
        });

        function cargarProductos(categoria) {
            fetch(`productosPorCategoria.php?categoria=${categoria}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la petición HTTP: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Mostrar la caja de búsqueda
                    document.getElementById('searchContainer').style.display = 'block';

                    // Limpiar el contenedor de productos
                    document.getElementById('productosContainer').innerHTML = '';

                    // Mostrar los productos obtenidos
                    if (data.length > 0) {
                        data.forEach(producto => {
                            // Construir la estructura HTML para cada producto
                            let productoHTML = `
                                <div class="producto">
                                    <h3>${producto.NombreProd}</h3>
                                    <p>Fecha de Caducidad: ${producto.FecCad}</p>
                                    <p>Id Producto: ${producto.IdProd}</p>
                                    <p>Codigo de Barras: ${producto.CodigoB}</p>
                                    <p>Precio: ${producto.Precio}</p>
                                    <img src="uploads/${producto.Imagen}" alt="${producto.NombreProd}">
                                    <form method="POST" action="UCarrito.php">
                                        <input type="hidden" name="NombreProd" value="${producto.NombreProd}">
                                        <input type="hidden" name="FecCad" value="${producto.FecCad}">
                                        <input type="hidden" name="IdProd" value="${producto.IdProd}">
                                        <input type="hidden" name="CodigoB" value="${producto.CodigoB}">
                                        <input type="hidden" name="Precio" value="${producto.Precio}">
                                        <input type="hidden" name="Imagen" value="${producto.Imagen}">
                                        <button type="submit" name="action" value="agregar">Agregar</button>
                                    </form>
                                </div>
                            `;

                            // Agregar el producto al contenedor
                            document.getElementById('productosContainer').innerHTML += productoHTML;
                        });
                    } else {
                        // Mostrar mensaje si no hay productos
                        document.getElementById('productosContainer').innerHTML = '<p>No hay productos disponibles en esta categoría.</p>';
                    }
                })
                .catch(error => console.error('Error al obtener productos:', error));
        }

        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const products = document.querySelectorAll('#productosContainer .producto');

            products.forEach(product => {
                const text = product.textContent.toLowerCase();
                product.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
