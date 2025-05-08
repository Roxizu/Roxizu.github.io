<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = isset($data['id']) ? intval($data['id']) : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM login WHERE id = ? AND role = 'cliente'");
    $stmt->bind_param("i", $id);
    
    $response = array();
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $conn->error;
    }
    
    echo json_encode($response);
    
    $stmt->close();
}

$conn->close();
?>