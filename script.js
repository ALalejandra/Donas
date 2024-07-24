<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <form class="login-form" id="registration-form" action="#" method="post">
        <h2>Registro de Usuario</h2>
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="apellido-paterno">Apellido Paterno:</label>
        <input type="text" id="apellido-paterno" name="apellido-paterno" required>

        <label for="apellido-materno">Apellido Materno:</label>
        <input type="text" id="apellido-materno" name="apellido-materno" required>

        <label for="fecha-nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha-nacimiento" name="fecha-nacimiento" required>

        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" required>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required>

        <button type="submit">Registrarse</button>
    </form>

    <div class="image-container">
        <img src="https://via.placeholder.com/300" alt="Placeholder Image">
    </div>
</div>

</body>
</html>
