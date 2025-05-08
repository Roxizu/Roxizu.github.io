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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $categoria = $_POST['categoria'] ?? null;
    $precio = $_POST['precio_regular'] ?? null;
    $stock = $_POST['stock'] ?? null;
    $estado = $_POST['estado'] ?? null;

    if (!$id || !$nombre || !$categoria || !$precio || !$stock || !$estado) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
        exit;
    }

    $imagen_nombre = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $imagen = $_FILES['imagen'];
        $imagen_nombre = uniqid() . '_' . $imagen['name'];
        $ruta_destino = '../img/productos/' . $imagen_nombre;
        
        if (!move_uploaded_file($imagen['tmp_name'], $ruta_destino)) {
            echo json_encode(['success' => false, 'message' => 'Error al subir la imagen']);
            exit;
        }
    }

    try {
        $sql = "UPDATE productos SET 
                nombre = ?, 
                categoria = ?, 
                precio_regular = ?, 
                stock = ?, 
                estado = ?";
        $params = [$nombre, $categoria, $precio, $stock, $estado];
        
        if ($imagen_nombre) {
            $sql .= ", imagen_principal = ?";
            $params[] = $imagen_nombre;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $conn->prepare($sql);
        $tipos = str_repeat('s', count($params));
        $stmt->bind_param($tipos, ...$params);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el producto: ' . $stmt->error]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
}

$conn->close();
?>