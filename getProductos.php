<?php
include 'cone.php';

// Obtener la fecha actual en formato YYYY-MM-DD
$currentDate = date('Y-m-d');

// Consultar los datos de la tabla productos
$sql = "SELECT 
            p.IdProd,
            p.NombreProd,
            p.CodigoB,
            p.FecCad,
            p.Precio,
            p.Stock,
            c.Nombre AS Categoria,
            p.Activo,
			p.IdProveedores,
            CASE WHEN p.FecCad < '$currentDate' THEN 'Caducado' ELSE 'No caducado' END AS Caducado
        FROM productos p
        INNER JOIN categoria c ON p.Id_Categoria = c.IdCategoria
        ORDER BY p.FecCad ASC"; // Ordenar por fecha de caducidad ascendente

$result = mysqli_query($conn, $sql);

$productos = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }
}

echo json_encode($productos);

mysqli_close($conn);
?>
