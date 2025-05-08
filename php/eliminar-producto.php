<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$dbname = "formulario";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

$id = $_POST['id'] ?? null;

if ($id === null) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Producto eliminado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

