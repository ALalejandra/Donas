<?php
include 'cone.php'; // Incluir el archivo de conexión a la base de datos
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y escapar los datos del formulario
    $nombreProd = mysqli_real_escape_string($conn, $_POST['NombreProd']);
    $codigoB = mysqli_real_escape_string($conn, $_POST['CodigoB']);
    $fecCad = mysqli_real_escape_string($conn, $_POST['FecCad']);
    $precio = mysqli_real_escape_string($conn, $_POST['Precio']);
    $stock = mysqli_real_escape_string($conn, $_POST['Stock']);
    $idCategoria = mysqli_real_escape_string($conn, $_POST['Id_Categoria']);
    $usuario = isset($_SESSION["usuario"]) ? mysqli_real_escape_string($conn, $_SESSION["usuario"]) : null;

    if (!$usuario) {
        echo "Error: Usuario no identificado.";
        exit();
    }

    // Asegurar que los datos numéricos sean tratados adecuadamente
    $precio = floatval($precio); // Convertir a flotante
    $stock = intval($stock); // Convertir a entero

    // Obtener el IdProveedores desde la tabla proveedores
    $sqlProveedor = "SELECT IdProveedores FROM proveedores WHERE Usuario = ?";
    $stmt = $conn->prepare($sqlProveedor);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idProveedores = $row['IdProveedores'];

        // Preparar la consulta SQL para insertar el producto
        $sql = "INSERT INTO productos (NombreProd, CodigoB, FecCad, Precio, Stock, Id_Categoria, IdProveedores, Activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)"; // Asegurar que el producto esté inactivo inicialmente

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdisi", $nombreProd, $codigoB, $fecCad, $precio, $stock, $idCategoria, $idProveedores);

        // Ejecutar la consulta y verificar si fue exitosa
        if ($stmt->execute()) {
            // Mostrar un mensaje de confirmación antes de redirigir
            echo "<script>
                alert('Producto en espera de autorización');
                window.location.href = 'ConsultaPProd.php';
            </script>";
            exit(); // Asegurar que el script se detenga después de redirigir
        } else {
            // Si hay un error en la consulta, mostrar el mensaje de error
            echo "Error al agregar el producto: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: Proveedor no encontrado.";
    }
}

// Cerrar la conexión a la base de datos al finalizar
mysqli_close($conn);
?>
