<?php
// Conexión con la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "formulario";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]));
}

// Consultar todos los productos
$sql = "SELECT id, nombre, descripcion, categoria, marca, precio_regular, precio_oferta, stock, sku, estado, imagen_principal FROM productos";
$result = $conn->query($sql);

$productos = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

// Cerrar la conexión
$conn->close();

// Devolver los productos en formato JSON
header('Content-Type: application/json');
echo json_encode($productos);