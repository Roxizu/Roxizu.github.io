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
$telefono = $_POST['telefonouser'] ?? null;
$email = $_POST['emailuser'] ?? null;
$direccion = $_POST['direccionuser'] ?? null;
$fecha = $_POST['fechauser'] ?? null;

// Validar que todos los campos estén llenos
if (empty($nombre) || empty($telefono) || empty($email) || empty($direccion) || empty($fecha)) {
    die("No se han llenado todos los campos. <a href='jobfinal.html'>Volver</a>");
}

// Validar el formato del correo electrónico
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Correo electrónico no válido. <a href='jobfinal.html'>Volver</a>");
}



// Insertar datos en la base de datos utilizando consultas preparadas
$stmt = $conn->prepare(query: "INSERT INTO datos (nombre, telefono, email, direccion, fecha) VALUES (?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("sssss", $nombre, $telefono, $email, $direccion, $fecha);

    if ($stmt->execute()) {
        echo '
        <script>
            alert("Registro exitoso");
            location.href = "index.html";
        </script>
        ';
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
