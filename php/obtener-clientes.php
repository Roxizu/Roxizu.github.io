<?php
require_once 'conexion.php';

// Asegurarnos de que no haya salida antes del JSON
header('Content-Type: application/json');

try {
    // Modificar la consulta para usar la tabla datos en lugar de login
    $sql = "SELECT id, nombre, apellido, email, fecha FROM datos WHERE role = 'cliente'";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $clientes = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $clientes[] = array(
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'email' => $row['email'],
                'fecha' => $row['fecha']
            );
        }
    }

    echo json_encode($clientes);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => $e->getMessage()));
}

$conn->close();
?>