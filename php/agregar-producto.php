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
$nombre = $_POST['product-name'] ?? null;
$descripcion = $_POST['product-description'] ?? null;
$categoria = $_POST['product-category'] ?? null;
$marca = $_POST['product-brand'] ?? null;
$precio_regular = $_POST['product-price'] ?? null;
$precio_oferta = $_POST['product-sale-price'] ?? null;
$stock = $_POST['product-stock'] ?? null;
$sku = $_POST['product-sku'] ?? null;
$estado = $_POST['estado'] ?? null;

// Validar que los campos obligatorios estén llenos
if (
    empty($nombre) || empty($descripcion) || empty($categoria) || empty($marca) ||
    empty($precio_regular) || empty($stock) || empty($sku) || empty($estado)
) {

    die("No se han llenado todos los campos obligatorios. <a href='../agregar-producto.html'>Volver</a>");
}

// Procesar imágenes
$imagen_principal = '';
if (isset($_FILES['image-upload-input'])) {
    $target_dir = "../img/productos/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if ($_FILES['image-upload-input']['name'][0] != '') {
        $imagen_principal = uploadImage(
            $_FILES['image-upload-input']['tmp_name'][0],
            $_FILES['image-upload-input']['name'][0],
            $target_dir
        );
    }
}

// Función para subir imágenes
function uploadImage($tmpName, $fileName, $targetDir)
{
    $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;

    if (move_uploaded_file($tmpName, $targetFile)) {
        return $newFileName;
    }
    return '';
}

// Obtener valores adicionales
$es_nuevo = isset($_POST['new']) ? 1 : 0;
$es_bestseller = isset($_POST['bestseller']) ? 1 : 0;
$es_edicion_limitada = isset($_POST['limited']) ? 1 : 0;
$garantia = $_POST['garantia'] ?? null;
$etiquetas = $_POST['product-tags'] ?? null;

// Modificar la consulta SQL para incluir los nuevos campos
$stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, categoria, marca, precio_regular, precio_oferta, stock, sku, estado, imagen_principal, es_nuevo, es_mas_vendido, es_edicion_limitada, garantia, etiquetas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt) {
    $stmt->bind_param(
        "ssssddisssiiiss",
        $nombre,
        $descripcion,
        $categoria,
        $marca,
        $precio_regular,
        $precio_oferta,
        $stock,
        $sku,
        $estado,
        $imagen_principal,
        $es_nuevo,
        $es_bestseller,
        $es_edicion_limitada,
        $garantia,
        $etiquetas
    );

    if ($stmt->execute()) {
        echo '
        <script>
            alert("Producto agregado exitosamente");
       
            location.href = "../perfil-empresa.html";
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
