<?php
require_once 'conexion.php';
require_once 'session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $contra = $_POST['contra'] ?? '';

    // Check in business table
    $sql_empresa = "SELECT * FROM login WHERE email = ? AND contra = ?";
    $stmt = $conn->prepare($sql_empresa);
    $stmt->bind_param("ss", $email, $contra);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        iniciarSesion($email, 'empresa');
    } else {
        // Fixed: Redirect to login1.html instead of login 2.html
        echo "<script>
            alert('Email o contraseña incorrectos');
            window.location.href = '../login1.html';
        </script>";
    }
}

$conn->close();
?>
