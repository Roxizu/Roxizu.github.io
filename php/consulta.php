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

// Variables para manejar las acciones 
$accion = $_POST['accion'] ?? null; 
$id = $_POST['id'] ?? null; 
 
// Acciones condicionales 
if ($accion === "eliminar") { 
    // Eliminar el registro 
    $query = "DELETE FROM datos WHERE id = ?"; 
    $stmt = $conn->prepare($query); 
    if ($stmt) { 
        $stmt->bind_param("i", $id); 
        if ($stmt->execute()) { 
            echo "Registro eliminado correctamente. <a href='jobfinal.html'>Volver</a>"; 
        } else { 
            echo "Error al eliminar el registro: " . $conn->error; 
        } 
        $stmt->close(); 
    } 
 
 
     
} elseif ($accion === "editar") { 
    // Mostrar formulario para editar 
    $query = "SELECT * FROM datos WHERE id = ?"; 
    $stmt = $conn->prepare($query); 
    if ($stmt) { 
        $stmt->bind_param("i", $id); 
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $row = $result->fetch_assoc(); 
 
        echo "<form action='' method='POST'> 
                <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'> 
                <input type='hidden' name='accion' value='guardar_edicion'> 
                <label>Nombre: <input type='text' name='nombre' value='" . htmlspecialchars($row['nombre']) 
. "'></label><br> 
                <label>Teléfono: <input type='text' name='telefono' value='" . 
htmlspecialchars($row['telefono']) . "'></label><br> 
                <label>Email: <input type='text' name='email' value='" . htmlspecialchars($row['email']) . 
"'></label><br> 
                <label>Dirección: <input type='text' name='direccion' value='" . 
htmlspecialchars($row['direccion']) . "'></label><br> 
                <label>Fecha de Nacimiento: <input type='date' name='fecha' value='" . 
htmlspecialchars($row['fecha']) . "'></label><br> 
                <button type='submit'>Guardar</button> 
              </form>"; 
    } 
} elseif ($accion === "guardar_edicion") { 
    // Guardar los cambios realizados al editar 
    $nombre = $_POST['nombre']; 
    $telefono = $_POST['telefono']; 
    $email = $_POST['email']; 
    $direccion = $_POST['direccion']; 
    $fecha = $_POST['fecha']; 
 
    $query = "UPDATE datos SET nombre = ?, telefono = ?, email = ?, direccion = ?, fecha_nacimiento = ? 
WHERE id = ?"; 
    $stmt = $conn->prepare($query); 
    if ($stmt) { 
        $stmt->bind_param("sssssi", $nombre, $telefono, $email, $direccion, $fecha, $id); 
        if ($stmt->execute()) { 
            echo "Registro actualizado correctamente. <a href='index.html'>Volver</a>"; 
        } else { 
            echo "Error al actualizar el registro: " . $conn->error; 
        } 
        $stmt->close(); 
    } 
} else { 
    // Mostrar formulario de búsqueda y resultados 
    $criterio = $_POST['criterio'] ?? null; 
    $valor = $_POST['valor'] ?? null; 
 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($criterio) && !empty($valor)) { 
        // Validar el formato según el criterio 
        if ($criterio === "email" && !filter_var($valor, FILTER_VALIDATE_EMAIL)) { 
            die("El formato del correo electrónico no es válido. <a href='index.html'>Volver</a>"); 
        } elseif ($criterio === "telefono" && !preg_match("/^\d{10}$/", $valor)) { 
            die("El formato del teléfono no es válido. Debe contener exactamente 10 dígitos. <a 
href='index.html'>Volver</a>"); 
        } 
 
        // Consulta a la base de datos 
        $query = ""; 
        if ($criterio === "email") { 
            $query = "SELECT * FROM datos WHERE email = ?"; 
        } elseif ($criterio === "telefono") { 
            $query = "SELECT * FROM datos WHERE telefono = ?"; 
        } 
 
        $stmt = $conn->prepare($query); 
        if ($stmt) { 
            $stmt->bind_param("s", $valor); 
            $stmt->execute(); 
            $result = $stmt->get_result(); 
 
            if ($result->num_rows > 0) { 
                echo "<h2>Resultados encontrados:</h2>"; 
                echo "<table border='1'>"; 
                echo "<tr><th>Nombres</th><th>Teléfono</th><th>Email</th><th>Dirección</th><th>Fecha</th><th>Acciones</th></tr>"; 
 
                while ($row = $result->fetch_assoc()) { 
                    echo "<tr> 
                            <td>" . htmlspecialchars($row['nombre']) . "</td> 
                            <td>" . htmlspecialchars($row['telefono']) . "</td> 
                            <td>" . htmlspecialchars($row['email']) . "</td> 
                            <td>" . htmlspecialchars($row['direccion']) . "</td> 
                            <td>" . htmlspecialchars($row['fecha']) . "</td> 
                            <td> 
                                <form action='' method='POST' style='display:inline;'> 
                                    <input type='hidden' name='id' value='" . $row['id'] . "'> 
                                    <button type='submit' name='accion' value='editar'>Editar</button> 
                                </form> 
                                <form action='' method='POST' style='display:inline;'> 
                                    <input type='hidden' name='id' value='" . $row['id'] . "'> 
                                    <button type='submit' name='accion' value='eliminar' onclick='return 
confirm(\"¿Estás seguro de eliminar este registro?\");'>Eliminar</button> 
                                </form> 
                            </td> 
                          </tr>"; 
                } 
                echo "</table>"; 
            } else { 
                echo "No se encontraron resultados para el criterio seleccionado. <a 
href='jobfinal.html'>Volver</a>"; 
            } 
        } 
    } else { 
        // Mostrar el formulario de búsqueda 
        echo "<form action='' method='POST'> 
                <label for='criterio'>Buscar por:</label> 
                <select name='criterio' id='criterio'> 
                    <option value='email'>Email</option> 
                    <option value='telefono'>Teléfono</option> 
                </select> 
                <label for='valor'>Valor:</label> 
                <input type='text' name='valor' id='valor' required> 
                <button type='submit'>Buscar</button> 
              </form>"; 
    } 
} 
 
// Cerrar la conexión 
$conn->close(); 
?>