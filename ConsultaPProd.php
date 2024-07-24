<?php
session_start();
$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Nombre Proveedor";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Productos</title>
    <link rel="stylesheet" href="stylesxx.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
        <h1>Consulta de Productos</h1>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Buscar productos...">
            <button id="downloadInventoryButton">Inventario</button>
        </div>
        <div class="search-container">
            <button id="downloadExcelButton">Descargar Excel</button>
            <label for="fileInput" id="fileInputLabel">Seleccionar archivo</label>
            <input type="file" id="fileInput" accept=".xlsx, .xls">
        </div>
        <table id="productosTable">
            <thead>
                <tr>
                    <th>ID Producto</th>
                    <th>Nombre del Producto</th>
                    <th>Código de Barras</th>
                    <th>Fecha de Caducidad</th>
                    <th>Costo</th>
                    <th>Piezas</th>
                    <th>Categoria</th>
                    <th>Activo</th>
                    <th>Proveedor</th>
                    <th>Caducado</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos de los productos se cargarán aquí dinámicamente -->
            </tbody>
        </table>
    </main>

    <footer>
        <p>&copy;2024 Last Bite. Todos los derechos reservados.</p>
    </footer>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        function loadProductos() {
            fetch('getPProductos.php')
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(text) });
                    }
                    return response.json();
                })
                .then(data => {
                    const tableBody = document.querySelector("#productosTable tbody");
                    tableBody.innerHTML = '';

                    data.forEach(producto => {
                        const row = document.createElement('tr');

                        for (const key in producto) {
                            const cell = document.createElement('td');
                            cell.textContent = producto[key];
                            cell.setAttribute('data-label', key);
                            row.appendChild(cell);
                        }

                        if (producto.Caducado === 'Caducado') {
                            row.classList.add('Caducado');
                        }

                        const editCell = document.createElement('td');
                        const editButton = document.createElement('button');
                        editButton.textContent = 'Editar';
                        editButton.className = 'btn-edit';
                        editButton.onclick = () => editProducto(producto.IdProd);
                        editCell.appendChild(editButton);
                        row.appendChild(editCell);

                       

                        tableBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar productos:', error);
                    fetch('getPProductos.php')
                        .then(response => response.text())
                        .then(data => console.log('Contenido de la respuesta:', data));
                });
        }

        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#productosTable tbody tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let match = false;

                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(filter)) {
                        match = true;
                    }
                });

                row.style.display = match ? '' : 'none';
            });
        });

        function editProducto(id) {
            window.location.href = 'editPProductoForm.php?id=' + id;
        }

       

        document.getElementById('downloadExcelButton').addEventListener('click', function() {
            downloadExcelTemplate();
        });

        document.getElementById('fileInput').addEventListener('change', function(event) {
            handleFileUpload(event.target.files[0]);
        });

        document.getElementById('downloadInventoryButton').addEventListener('click', function() {
            downloadInventory();
        });

        function downloadExcelTemplate() {
    const ws = XLSX.utils.json_to_sheet([
        {
            "Nombre del Producto": "",
            "Código de Barras": "",
            "Fecha de Caducidad": "YYYY-MM-DD",
            "Costo": "",
            "Piezas": "",
            "Categoria": "",
        }
    ]);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Plantilla');
    XLSX.writeFile(wb, 'plantilla_productos.xlsx');
}



function handleFileUpload(file) {
    const reader = new FileReader();
    reader.onload = function(event) {
        const data = new Uint8Array(event.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        const json = XLSX.utils.sheet_to_json(worksheet);

        const proveedorId = "<?php echo $_SESSION['usuario']; ?>";

        json.forEach(producto => {
            producto.IdProveedores = proveedorId;
        });

        fetch('uploadPProductos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(json)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadProductos();
                alert('Productos cargados correctamente.');
            } else {
                console.error('Error al cargar productos:', data.message);
                if (data.producto) {
                    console.error('Producto con error:', data.producto);
                }
                alert('Error al cargar productos: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error al procesar la solicitud:', error);
            alert('Error al procesar la solicitud. Ver la consola para más detalles.');
        });
    };
    reader.readAsArrayBuffer(file);
}

        function downloadInventory() {
            fetch('getPProductos.php')
                .then(response => response.json())
                .then(data => {
                    const ws = XLSX.utils.json_to_sheet(data);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Inventario');
                    XLSX.writeFile(wb, 'inventario_productos.xlsx');
                });
        }

        loadProductos();
    });
    </script>
</body>
</html>
