<?php
include 'cone.php'; // Incluir el archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y escapar los datos del formulario
    $nombreProd = mysqli_real_escape_string($conn, $_POST['NombreProd']);
    $codigoB = mysqli_real_escape_string($conn, $_POST['CodigoB']);
    $fecCad = mysqli_real_escape_string($conn, $_POST['FecCad']);
    $precio = mysqli_real_escape_string($conn, $_POST['Precio']);
    $stock = mysqli_real_escape_string($conn, $_POST['Stock']);
    $idCategoria = mysqli_real_escape_string($conn, $_POST['Id_Categoria']);
    $activo = mysqli_real_escape_string($conn, $_POST['Activo']);
    $idProveedores = mysqli_real_escape_string($conn, $_POST['IdProveedores']);

    // Asegurar que los datos numéricos sean tratados adecuadamente
    $precio = floatval($precio); // Convertir a flotante
    $stock = intval($stock); // Convertir a entero

    // Manejar la subida de la imagen
    if (isset($_FILES['Imagen']) && $_FILES['Imagen']['error'] == 0) {
        $imagen = $_FILES['Imagen']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["Imagen"]["name"]);

        // Verificar si el archivo es una imagen real
        $check = getimagesize($_FILES["Imagen"]["tmp_name"]);
        if ($check === false) {
            echo "El archivo no es una imagen.";
            exit();
        }

        // Verificar si se pudo mover la imagen al directorio de destino
        if (move_uploaded_file($_FILES["Imagen"]["tmp_name"], $target_file)) {
            // Preparar la consulta SQL para insertar el producto
            $sql = "INSERT INTO productos (NombreProd, CodigoB, FecCad, Precio, Stock, Id_Categoria, Activo, IdProveedores, Imagen) 
                    VALUES ('$nombreProd', '$codigoB', '$fecCad', $precio, $stock, '$idCategoria', '$activo', '$idProveedores', '$imagen')";

            // Ejecutar la consulta y verificar si fue exitosa
            if (mysqli_query($conn, $sql)) {
                // Redirigir al usuario a una página de confirmación o a la lista de productos
                header('Location: Productos.html');
                exit(); // Asegurar que el script se detenga después de redirigir
            } else {
                // Si hay un error en la consulta, mostrar el mensaje de error
                echo "Error al agregar el producto: " . mysqli_error($conn);
            }
        } else {
            echo "Error al subir la imagen.";
        }
    } else {
        // Mostrar un mensaje de error adecuado si no se cargó correctamente el archivo
        echo "Error al cargar la imagen: " . $_FILES['Imagen']['error'];
    }
}

// Cerrar la conexión a la base de datos al finalizar
mysqli_close($conn);
?>
