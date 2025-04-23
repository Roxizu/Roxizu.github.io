<?php
// Conexión con la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "formulario";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Error de conexión: ' . $conn->connect_error]));
}

// Obtener el ID del producto a eliminar
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if ($id === null) {
    die(json_encode(['success' => false, 'error' => 'ID no proporcionado']));
}

// Eliminar el producto
$stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);

$response = ['success' => false];

if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['error'] = 'Error al eliminar el producto: ' . $stmt->error;
}

$stmt->close();
$conn->close();

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);