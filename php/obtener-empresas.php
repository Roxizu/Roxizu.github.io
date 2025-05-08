<?php
require_once 'conexion.php';

header('Content-Type: application/json');

// Eliminar cualquier salida de texto antes del JSON
// No imprimir mensajes de conexión exitosa

$sql = "SELECT id, nombre_empresa, RNC, email, telefono, direccion FROM login WHERE role = 'empresa'";
$result = $conn->query($sql);

$empresas = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $empresas[] = $row;
    }
}

echo json_encode($empresas);

$conn->close();
?>