<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Usar $conn en lugar de $conexion
    if (!isset($conn)) {
        die("Error: No se pudo establecer la conexión con la base de datos");
    }

    // Verificar credenciales del administrador
    $sql = "SELECT * FROM administradores WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_username'] = $username;

 
    
        header('Location: ../admin-panel.html');
        exit;
    } else {
        echo "<script>
                alert('Usuario o contraseña incorrectos');
                window.location.href = '../admin-login.html';
              </script>";
    }
}
?>