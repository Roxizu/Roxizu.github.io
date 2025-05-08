<?php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "formulario";

$conn = new mysqli(hostname: $host, username: $user, password: $password, database: $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
