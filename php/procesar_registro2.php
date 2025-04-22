<?php
// Conexión con la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "formulario";

$conn = new mysqli(hostname: $host, username: $user, password: $password, database: $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


// Obtener los valores del formulario
$nombre = $_POST['nombreuser'] ?? null;
$apellido = $_POST['apellidouser'] ?? null;
$email = $_POST['emailuser'] ?? null;
$contra = $_POST['contrauser'] ?? null;
$fecha = $_POST['fechauser'] ?? null;
$role = 'cliente'; // Add default role for regular users

// Validar que todos los campos estén llenos
if (empty($nombre) || empty($apellido) || empty($email) || empty($contra) || empty($fecha)) {
    die("No se han llenado todos los campos. <a href='cliente.html'>Volver</a>");
}

// Validar el formato del correo electrónico
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Correo electrónico no válido. <a href='cliente.html'>Volver</a>");
}

// Verificar si el correo ya existe en ambas tablas
$check_email = $conn->prepare("SELECT email FROM datos WHERE email = ? UNION SELECT email FROM login WHERE email = ?");
$check_email->bind_param("ss", $email, $email);
$check_email->execute();
$result = $check_email->get_result();

if ($result->num_rows > 0) {
    echo "<script>
        alert('Este correo electrónico ya está registrado. Por favor, utilice otro.');
        window.location.href = '../cliente.html';
    </script>";
    exit;
}

// Insertar datos en la base de datos utilizando consultas preparadas
$stmt = $conn->prepare("INSERT INTO datos (nombre, apellido, email, contra, fecha, role) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("ssssss", $nombre, $apellido, $email, $contra, $fecha, $role);

    if ($stmt->execute()) {
        // Start session and store user data
        session_start();
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['role'] = 'cliente';
        $_SESSION['nombre'] = $nombre;
        
        echo '
        <script>
            alert("Registro exitoso");
            window.location.href = "../login 2.html";
        </script>
        ';
        exit;
    } else {
        echo "Error al guardar los datos: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error en la consulta: " . $conn->error;
}

// Cerrar la conexión
$conn->close();

?>
