<?php
header('Content-Type: application/json');

// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el archivo de conexión
include 'cone.php';

// Leer los datos JSON enviados en la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Imprimir el JSON en la consola
error_log('Datos recibidos: ' . print_r($data, true));

if ($data) {
    // Obtener el usuario de la sesión
    session_start();
    $usuario = $_SESSION['usuario'] ?? null;

    if ($usuario) {
        // Consultar el ID del proveedor basado en el usuario
        $stmt = $conn->prepare("SELECT IdProveedores FROM proveedores WHERE usuario = ?");
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta del proveedor: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param("s", $usuario);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $idProveedor = $row['IdProveedores'];

                foreach ($data as $producto) {
                    $nombreProducto = $producto['Nombre del Producto'] ?? null;
                    $codigoBarras = $producto['Código de Barras'] ?? null;
                    $fechaCaducidad = $producto['Fecha de Caducidad'] ?? null;
                    $costo = $producto['Costo'] ?? null;
                    $piezas = $producto['Piezas'] ?? null;
                    $categoria = $producto['Categoria'] ?? null;
                    $activo = 0;  // Establecer el valor de "Activo" en 0

                    // Verificar si los valores son correctos
                    if (is_null($nombreProducto) || is_null($codigoBarras) || is_null($fechaCaducidad) || is_null($costo) || is_null($piezas) || is_null($categoria)) {
                        echo json_encode(['status' => 'error', 'message' => 'Faltan datos de producto', 'producto' => $producto]);
                        exit;
                    }

                    // Preparar la declaración SQL para evitar inyección SQL
                    $stmtInsert = $conn->prepare("INSERT INTO productos (NombreProd, CodigoB, FecCad, Precio, Stock, Id_Categoria, Activo, IdProveedores) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                    // Verificar si la preparación de la declaración fue exitosa
                    if (!$stmtInsert) {
                        echo json_encode(['status' => 'error', 'message' => 'Error en la preparación del statement: ' . $conn->error]);
                        exit;
                    }

                    $stmtInsert->bind_param("sssdissi", $nombreProducto, $codigoBarras, $fechaCaducidad, $costo, $piezas, $categoria, $activo, $idProveedor);

                    // Ejecutar la declaración
                    if (!$stmtInsert->execute()) {
                        echo json_encode(['status' => 'error', 'message' => 'Error al insertar el producto: ' . $stmtInsert->error, 'producto' => $producto]);
                        $stmtInsert->close();
                        $conn->close();
                        exit();
                    }

                    $stmtInsert->close();
                }

                $conn->close();
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Proveedor no encontrado']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error en la ejecución de la consulta del proveedor: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado en la sesión']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos']);
}
?>
