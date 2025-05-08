<?php
// Conexión con la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "formulario";
$conn = new mysqli($host, $user, $password, $dbname);
// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
// Obtener los valores del formulario
$nombre_empresa = $_POST['nombre_empreuser'] ?? null;
$nombre = $_POST['nombreuser'] ?? null;
$direccion = $_POST['direccionuser'] ?? null;
$telefono= $_POST['telefonouser'] ?? null;
$email = $_POST['emailuser'] ?? null;
$RNC = $_POST['rncuser'] ?? null;
$contra = $_POST['passworduser'] ?? null;
$role = 'empresa'; 

// Validar que todos los campos estén llenos
if (empty($nombre_empresa) || empty($direccion) || empty($telefono) || empty($email) || empty($RNC) || empty($contra)  ) {
    die("No se han llenado todos los campos. <a href='http://localhost:8080/formulario/empresa.html'>Volver</a>");
}
// Validar el formato del correo electrónico
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Correo electrónico no válido. <a href='http://localhost:8080/formulario/empresa.html'>Volver</a>");
}


$check_email = $conn->prepare("SELECT email FROM datos WHERE email = ? UNION SELECT email FROM login WHERE email = ?");
$check_email->bind_param("ss", $email, $email);
$check_email->execute();
$result = $check_email->get_result();

if ($result->num_rows > 0) {
    echo "<script>
        alert('Este correo electrónico ya está registrado. Por favor, utilice otro.');
        window.location.href = '../empresa.html';
    </script>";
    exit;
}

// Modificar la consulta para incluir campos adicionales necesarios para el panel de administrador
$stmt = $conn->prepare("INSERT INTO login (
    nombre_empresa, 
    nombre, 
    direccion, 
    telefono, 
    email, 
    RNC, 
    contra, 
    role,
    fecha_registro,
    estado,
    porcentaje_comision
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'activo', 5.00)");

if ($stmt) {
    $stmt->bind_param("ssssssss", 
        $nombre_empresa, 
        $nombre, 
        $direccion, 
        $telefono, 
        $email, 
        $RNC, 
        $contra, 
        $role
    );
   
    if ($stmt->execute()) {
       
 

        // Start session and store user data
        session_start();
        $_SESSION['user_id'] = $empresa_id;
        $_SESSION['role'] = 'empresa';
        $_SESSION['nombre_empresa'] = $nombre_empresa;

        echo '
        <script>
            alert("Registro exitoso");
            window.location.href = "../login1.html";
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
