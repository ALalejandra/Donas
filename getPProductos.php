<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'cone.php';
session_start();

// Obtener el nombre del usuario en sesión
$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "";

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
    CASE WHEN p.FecCad < ? THEN 'Caducado' ELSE 'No caducado' END AS Caducado
FROM productos p
INNER JOIN categoria c ON p.Id_Categoria = c.IdCategoria
WHERE 
    p.IdProveedores = (
        SELECT IdProveedores 
        FROM proveedores 
        WHERE Usuario = ?
    )
    AND p.Activo > 0
ORDER BY p.FecCad ASC;
"; // Ordenar por fecha de caducidad ascendente

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ss", $currentDate, $usuario); // Vincular parámetros correctamente
    $stmt->execute();
    $result = $stmt->get_result();

    $productos = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }

    echo json_encode($productos);

    $stmt->close();
} else {
    echo "Error al preparar la consulta: " . $conn->error;
}

mysqli_close($conn);
?>
