<?php
session_start();
$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Nombre Proveedor";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Proveedores</title>
    <link rel="stylesheet" href="stylesxx.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><?php echo htmlspecialchars($usuario); ?></li>
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

    <main>
        <h1>Consulta de Proveedores</h1>
        <table id="proveedoresTable">
            <thead>
                <tr>
                    <th>ID Proveedor</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Usuario</th>
                    <th>Activo</th>
                    <th>Editar</th>
                    <th>Eliminar</th>					
                </tr>
            </thead>
            <tbody>
                <!-- Los datos de los proveedores se cargarán aquí dinámicamente -->
            </tbody>
        </table>
    </main>

    <footer>
        <p>&copy;2024 Last Bite. Todos los derechos reservados.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function loadProveedores() {
                fetch('getPProveedores.php')
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.querySelector("#proveedoresTable tbody");
                        tableBody.innerHTML = '';

                        data.forEach(proveedor => {
                            const row = document.createElement('tr');

                            // Añadir las celdas de datos
                            Object.keys(proveedor).forEach(key => {
                                const cell = document.createElement('td');
                                cell.textContent = proveedor[key];
                                cell.setAttribute('data-label', key);
                                row.appendChild(cell);
                            });

                            // Añadir botón de editar en celda separada
                            const editCell = document.createElement('td');
                            const editButton = document.createElement('button');
                            editButton.textContent = 'Editar';
                            editButton.classList.add('btn-edit');
                            editButton.addEventListener('click', () => {
                                window.location.href = `editarPProveedor.php?id=${proveedor.IdProveedores}`;
                            });
                            editCell.appendChild(editButton);
                            row.appendChild(editCell);

                            // Añadir botón de eliminar en celda separada
                            const deleteCell = document.createElement('td');
                            const deleteButton = document.createElement('button');
                            deleteButton.textContent = 'Eliminar';
                            deleteButton.classList.add('btn-delete');
                            deleteButton.addEventListener('click', () => {
                                const confirmDelete = confirm('¿Estás seguro de eliminar este proveedor?');
                                if (confirmDelete) {
                                    fetch(`eliminarPProveedor.php?id=${proveedor.IdProveedores}`, {
                                        method: 'DELETE'
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Error al eliminar el proveedor.');
                                        }
                                        // Recargar la lista de proveedores después de eliminar
                                        loadProveedores();
                                    })
                                    .catch(error => console.error('Error:', error));
                                }
                            });
                            deleteCell.appendChild(deleteButton);
                            row.appendChild(deleteCell);

                            // Añadir la fila a la tabla
                            tableBody.appendChild(row);
                        });
                    })
                    .catch(error => console.error('Error al cargar proveedores:', error));
            }

            loadProveedores();
        });
    </script>
</body>
</html>
