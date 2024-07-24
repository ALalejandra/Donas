<?php
// Incluir el archivo de conexión
require_once 'cone.php';

// Obtener la categoría desde el parámetro GET
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Inicializar el array para almacenar los productos
$productos = array();

// Obtener la fecha actual del sistema
$fecha_actual = date('Y-m-d');

// Consultar productos según la categoría y filtrar por fecha de caducidad
if ($categoria === '1') {
    $sql = "SELECT IdProd, NombreProd, CodigoB, FecCad, Precio, Imagen FROM productos WHERE Id_Categoria = 1 AND FecCad > '$fecha_actual'";
} else if ($categoria === '2') {
    $sql = "SELECT IdProd, NombreProd, CodigoB, FecCad, Precio, Imagen FROM productos WHERE Id_Categoria = 2 AND FecCad > '$fecha_actual'";
} else if ($categoria === '3') {
    $sql = "SELECT IdProd, NombreProd, CodigoB, FecCad, Precio, Imagen FROM productos WHERE Id_Categoria = 3 AND FecCad > '$fecha_actual'";
} else {
    // Devolver un JSON con mensaje de error si la categoría no es válida
    $error_message = "Categoría no válida";
    echo json_encode(array('error' => $error_message));
    exit; // Terminar la ejecución del script
}

// Ejecutar la consulta SQL
$result = $conn->query($sql);

// Verificar si se encontraron resultados
if ($result && $result->num_rows > 0) {
    // Iterar sobre los resultados y almacenar en el array de productos
    while ($row = $result->fetch_assoc()) {
        $productos[] = array(
            "NombreProd" => $row["NombreProd"],
            "FecCad" => $row["FecCad"],
            "IdProd" => $row["IdProd"],
            "CodigoB" => $row["CodigoB"],
            "Precio" => $row["Precio"],
            "Imagen" => $row["Imagen"]
        );
    }

    // Devolver los productos en formato JSON
    echo json_encode($productos);
} else {
    // Devolver un JSON con mensaje de error si no se encontraron productos
    $error_message = "No se encontraron productos para la categoría: $categoria";
    echo json_encode(array('error' => $error_message));
}

// Cerrar la conexión
$conn->close();
?>
