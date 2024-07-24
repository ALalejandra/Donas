<?php
include 'cone.php';

$id = $_GET['id'];

$sql = "DELETE FROM usuarios WHERE IdUsu = '$id'";

if (mysqli_query($conn, $sql)) {
    echo 'success';
} else {
    echo 'error';
}

mysqli_close($conn);
?>
