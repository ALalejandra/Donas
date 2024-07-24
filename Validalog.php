<?php
include('cone.php');

// Verificación del envío de formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = $_POST['Usu'];
    $password = $_POST['Contra'];

    // Hashear la contraseña ingresada con MD5
    $hashedPassword = md5($password);

    // Preparar la consulta para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usu = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el usuario existe en la tabla usuarios
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Comparar la contraseña hasheada con la almacenada
        if ($hashedPassword === $row['Contra']) {
            session_start();

            // Verificar el estado de "Activo" en usuarios
            if ($row['Activo'] == 0) {
                // Inicio de sesión, redirigir a verificar OTP
                $_SESSION["usuario"] = $usuario;
                echo "<script>window.location.href = 'verificar_otp.php';</script>";
                exit();
            } elseif ($row['Activo'] == 1) {
                // Activo = 1, redirigir a menu2.php
                $_SESSION["usuario"] = $usuario;
                echo "<script>window.location.href = 'Menu2.php';</script>";
                exit();
            } elseif ($row['Activo'] == 3) {
                // Activo = 3, redirigir a Menu1.html
                echo "<script>window.location.href = 'Menu1.html';</script>";
                exit();
            } else {
                // Otro valor de Activo en usuarios, mostrar alerta
                echo "<script>alert('No Activo en usuarios');</script>";
                echo "<script>window.location.href = 'index.html#Login';</script>";
                exit();
            }
        } else {
            // Contraseña incorrecta, mostrar alerta
            echo "<script>alert('Contraseña incorrecta');</script>";
            echo "<script>window.location.href = 'index.html#Login';</script>";
            exit();
        }
    } else {
        // El usuario no fue encontrado en usuarios, buscar en proveedores
        $stmt = $conn->prepare("SELECT * FROM proveedores WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Comparar la contraseña hasheada con la almacenada
            if ($hashedPassword === $row['Contras']) {
                session_start();

                // Verificar el estado de "Activo" en proveedores
                if ($row['Activo'] == 2) {
                    // Activo = 2, redirigir a menu3.php
                    $_SESSION["usuario"] = $usuario;
                    echo "<script>window.location.href = 'menu3.php';</script>";
                    exit();
                } else {
                    // Otro valor de Activo en proveedores, mostrar alerta
                    echo "<script>alert('No Activo en proveedores');</script>";
                    echo "<script>window.location.href = 'index.html#Login';</script>";
                    exit();
                }
            } else {
                // Contraseña incorrecta para proveedores, mostrar alerta
                echo "<script>alert('Contraseña incorrecta para proveedores');</script>";
                echo "<script>window.location.href = 'index.html#Login';</script>";
                exit();
            }
        } else {
            // Usuario no encontrado en proveedores, mostrar alerta
            echo "<script>alert('No activo');</script>";
            echo "<script>window.location.href = 'index.html#Login';</script>";
            exit();
        }
    }
} else {
    // Redirige a UsuAlta2.html si la solicitud no es POST.
    echo "<script>alert('No se ha enviado el formulario');</script>";
    echo "<script>window.location.href = 'index.html#Login';</script>";
    exit();
}
?>








//<?php
//include('cone.php');

// Verificación del envío de formulario.
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  //  $usuario = $_POST['Usu'];
   // $password = $_POST['Contra'];

    // Hashear la contraseña ingresada con MD5
   // $hashedPassword = md5($password);

    // Preparar la consulta para evitar inyecciones SQL
  //  $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usu = ?");
  //  $stmt->bind_param("s", $usuario);
  //  $stmt->execute();
  //  $result = $stmt->get_result();

    // Verificar si el usuario existe en la tabla usuarios
   // if ($result->num_rows > 0) {
   //     $row = $result->fetch_assoc();

        // Comparar la contraseña hasheada con la almacenada
   //     if ($hashedPassword === $row['Contra']) {
   //         session_start();

            // Verificar el estado de "Activo" en usuarios
   //         if ($row['Activo'] == 0) {
                // Inicio de sesión, redirigir a verificar OTP
   //             $_SESSION["usuario"] = $usuario;
   //             header("Location: verificar_otp.php");
   //             exit();
   //         } elseif ($row['Activo'] == 1) {
                // Activo = 1, redirigir a menu2.html
   //             header("Location: menu2.html");
   //             exit();
   //         } elseif ($row['Activo'] == 3) {
                // Activo = 3, redirigir a Menu1.html
   //             header("Location: Menu1.html");
   //             exit();
     //       } else {
             // Otro valor de Activo en usuarios, redirigir a log.html
//                header("Location: log.html");
//                exit();
  //          }
    //    } else {
            // Contraseña incorrecta.
      //      header("Location: UsuAltas1.html");
        //    exit();
       // }
//    } else {
        // El usuario no fue encontrado en usuarios, buscar en proveedores
//        $stmt = $conn->prepare("SELECT * FROM proveedores WHERE usuario = ?");
 //       $stmt->bind_param("s", $usuario);
  //      $stmt->execute();
   //     $result = $stmt->get_result();

//        if ($result->num_rows > 0) {
//            $row = $result->fetch_assoc();

            // Comparar la contraseña hasheada con la almacenada
//            if ($hashedPassword === $row['Contras']) {
 //               session_start();

                // Verificar el estado de "Activo" en proveedores
//                if ($row['Activo'] == 2) {
                    // Activo = 1, redirigir a menu2.html
//                    $_SESSION["usuario"] = $usuario;
 //                   header("Location: menu3.html");
  //                  exit();
   //             } else {
                    // Otro valor de Activo en proveedores, redirigir a log.html
  //                  header("Location: log.html");
 //                   exit();
 //               }
 //           } else {
                // Contraseña incorrecta para proveedores.
 //               header("Location: UsuAltas1.html");
 //               exit();
 //           }
 //       } else {
            // Usuario no encontrado en proveedores.
 //           header("Location: UsuAltas1.html");
 //           exit();
 //       }
 //   }
//} else {
    // Redirige a UsuAlta2.html si la solicitud no es POST.
  //  header("Location: UsuAlta2.html");
 //   exit();
//}
//?>


