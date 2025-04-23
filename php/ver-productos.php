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

// Manejar la eliminación de productos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
    $id = $_POST["delete_id"];
    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
    exit();
}

// Obtener todos los productos
$sql = "SELECT * FROM productos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Productos - Electrónica S.M.R</title>
    <link rel="stylesheet" href="../css/ver-productos.css">
</head>
<body>
    <nav class="navbar">
        <img src="../img/logo de Electronica S.M.R Banner Horizontal.png" alt="Electrónica S.M.R Logo" class="logo-img">
    </nav>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                </svg>
                Productos Registrados
            </h1>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar productos...">
            <select id="categoryFilter" class="filter-select">
                <option value="">Todas las categorías</option>
                <option value="consolas">Consolas</option>
                <option value="accesorios-consolas">Accesorios para consolas</option>
                <option value="componentes-pc">Componentes PC</option>
                <option value="smartphones">Smartphones y Tablets</option>
            </select>
        </div>

        <div class="products-table">
            <table id="productTable">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Marca</th>
                        <th>Precio Regular</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr data-id='" . $row['id'] . "'>";
                            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['categoria']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
                            echo "<td>RD$" . htmlspecialchars($row['precio_regular']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                            echo "<td><span class='status-badge " . 
                                ($row['estado'] == 'Activo' ? 'status-active' : 
                                ($row['estado'] == 'Inactivo' ? 'status-inactive' : 'status-coming')) . 
                                "'>" . htmlspecialchars($row['estado']) . "</span></td>";
                            echo "<td class='actions'>
                                    <button class='btn btn-edit' onclick='editProduct(" . $row['id'] . ")'>Editar</button>
                                    <button class='btn btn-delete' onclick='deleteProduct(" . $row['id'] . ")'>Eliminar</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align: center;'>No hay productos registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal de confirmación -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirmar Eliminación</h2>
            <p>¿Estás seguro de que deseas eliminar este producto?</p>
            <div class="modal-buttons">
                <button id="confirmDelete" class="btn btn-delete">Eliminar</button>
                <button id="cancelDelete" class="btn">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        let productIdToDelete = null;

        function deleteProduct(id) {
            productIdToDelete = id;
            document.getElementById('deleteModal').style.display = 'block';
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (productIdToDelete) {
                const formData = new FormData();
                formData.append('delete_id', productIdToDelete);

                fetch('ver-productos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = document.querySelector(`tr[data-id="${productIdToDelete}"]`);
                        if (row) row.remove();
                    } else {
                        alert('Error al eliminar el producto');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el producto');
                })
                .finally(() => {
                    document.getElementById('deleteModal').style.display = 'none';
                    productIdToDelete = null;
                });
            }
        });

        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteModal').style.display = 'none';
            productIdToDelete = null;
        });

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                modal.style.display = 'none';
                productIdToDelete = null;
            }
        };

        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#productTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });

        // Filtrado por categoría
        document.getElementById('categoryFilter').addEventListener('change', function(e) {
            const filterValue = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#productTable tbody tr');
            
            rows.forEach(row => {
                const category = row.children[1].textContent.toLowerCase();
                row.style.display = !filterValue || category === filterValue ? '' : 'none';
            });
        });

        function editProduct(id) {
            // Redirigir a la página de edición
            window.location.href = `editar-producto.php?id=${id}`;
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>