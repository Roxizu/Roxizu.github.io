<?php
require_once 'conexion.php';
require_once 'session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['emailuser'] ?? '';
    $contra = $_POST['contrauser'] ?? '';

    // Check in clients table
    $sql_cliente = "SELECT * FROM datos WHERE email = ? AND contra = ?";
    $stmt = $conn->prepare($sql_cliente);
    $stmt->bind_param("ss", $email, $contra);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        iniciarSesion($email, 'cliente');
    }

    echo "<script>
    alert('Email o contraseña incorrectos');
    window.location.href = '../login 2.html';
</script>";


}

$conn->close();
?>
