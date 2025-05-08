document.addEventListener('DOMContentLoaded', function() {
    cargarProductos();

    // Configurar búsqueda y filtros
    document.getElementById('searchInput').addEventListener('input', filtrarProductos);
    document.getElementById('statusFilter').addEventListener('change', filtrarProductos);

    // Configurar modal de eliminación
    const modal = document.getElementById('deleteModal');
    document.getElementById('cancelDelete').addEventListener('click', () => {
        modal.style.display = 'none';
    });
});

// En la función que genera las filas de la tabla
function cargarProductos() {
    fetch('php/obtener-productos.php')
        .then(response => response.json())
        .then(productos => {
            mostrarProductos(productos);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function mostrarProductos(productos) {
    const tbody = document.getElementById('productsTableBody');
    tbody.innerHTML = '';

    productos.forEach(producto => {
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', producto.id); // Agregar el ID como atributo
        tr.innerHTML = `
            <td><img src="img/productos/${producto.imagen_principal}" class="product-image" alt="${producto.nombre}"></td>
            <td>${producto.nombre}</td>
            <td>${producto.categoria}</td>
            <td>$${producto.precio_regular}</td>
            <td>${producto.stock}</td>
            <td><span class="status-badge status-${producto.estado.toLowerCase()}">${producto.estado}</span></td>
            <td class="actions">
                <button class="btn btn-edit" onclick="editarProducto(${producto.id})">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn btn-delete" onclick="confirmarEliminar(${producto.id})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function filtrarProductos() {
    const busqueda = document.getElementById('searchInput').value.toLowerCase();
    const estado = document.getElementById('statusFilter').value;
    const filas = document.querySelectorAll('#productsTableBody tr');

    filas.forEach(fila => {
        const nombre = fila.children[1].textContent.toLowerCase();
        const estadoProducto = fila.querySelector('.status-badge').textContent.toLowerCase();
        
        const coincideBusqueda = nombre.includes(busqueda);
        const coincideEstado = estado === 'todos' || estadoProducto === estado;

        fila.style.display = coincideBusqueda && coincideEstado ? '' : 'none';
    });
}

function editarProducto(id) {
    const fila = document.querySelector(`tr[data-id="${id}"]`);
    const nombre = fila.querySelector('td:nth-child(2)').textContent;
    const categoria = fila.querySelector('td:nth-child(3)').textContent;
    const precio = fila.querySelector('td:nth-child(4)').textContent.replace('$', '').trim();
    const stock = fila.querySelector('td:nth-child(5)').textContent;
    const estado = fila.querySelector('.status-badge').textContent.toLowerCase();
    const imagenSrc = fila.querySelector('td:nth-child(1) img').src;

    document.getElementById('editName').value = nombre;
    document.getElementById('editCategory').value = categoria;
    document.getElementById('editPrice').value = parseFloat(precio);
    document.getElementById('editStock').value = stock;
    document.getElementById('editStatus').value = estado;
    
    const currentImage = document.getElementById('currentProductImage');
    currentImage.src = imagenSrc;
    currentImage.style.display = 'block';
    
    document.getElementById('previewImg').style.display = 'none';
    document.getElementById('editImage').value = '';

    const editModal = document.getElementById('editModal');
    editModal.style.display = 'block';
    editModal.dataset.productId = id;
}

// Eliminar la segunda definición de closeEditModal()
function closeEditModal() {
    const editModal = document.getElementById('editModal');
    editModal.style.display = 'none';
    // Limpiar el formulario
    document.getElementById('editProductForm').reset();
    document.getElementById('previewImg').src = '';
    document.getElementById('currentProductImage').src = '';
}

// Mantener solo un event listener para el formulario
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productId = document.getElementById('editModal').dataset.productId;
    const formData = new FormData();
    
    formData.append('id', productId);
    formData.append('nombre', document.getElementById('editName').value);
    formData.append('categoria', document.getElementById('editCategory').value);
    formData.append('precio_regular', document.getElementById('editPrice').value);
    formData.append('stock', document.getElementById('editStock').value);
    formData.append('estado', document.getElementById('editStatus').value.toLowerCase());
    
    const imageFile = document.getElementById('editImage').files[0];
    if (imageFile) {
        formData.append('imagen_principal', imageFile);
    }

    const saveButton = document.querySelector('.btn-save');
    saveButton.disabled = true;
    saveButton.textContent = 'Guardando...';

    fetch('php/actualizar-producto.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Producto actualizado correctamente');
            closeEditModal();
            location.reload();
        } else {
            throw new Error(data.message || 'Error al actualizar el producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message);
    })
    .finally(() => {
        saveButton.disabled = false;
        saveButton.textContent = 'Guardar';
    });
});

// Un único event listener para window.onclick
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');
    if (event.target === editModal) {
        closeEditModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productId = document.getElementById('editModal').dataset.productId;
    const formData = new FormData(this);
    formData.append('id', productId);

    fetch('php/actualizar-producto.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Error al actualizar el producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el producto');
    })
    .finally(() => {
        closeEditModal();
    });
});

// Cerrar modal cuando se hace clic fuera de él
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');
    if (event.target === editModal) {
        closeEditModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
}

function previewImage(input) {
    const preview = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}


function confirmarEliminar(id) {
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.style.display = 'block';
    deleteModal.dataset.productId = id;
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    const deleteModal = document.getElementById('deleteModal');
    const productId = deleteModal.dataset.productId;
    
    if (!productId) {
        alert('Error: No se encontró el ID del producto');
        return;
    }

    const formData = new FormData();
    formData.append('id', productId);
    
    fetch('php/eliminar-producto.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Producto eliminado exitosamente');
            location.reload();
        } else {
            throw new Error(data.message || 'Error al eliminar el producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el producto: ' + error.message);
    })
    .finally(() => {
        deleteModal.style.display = 'none';
    });
});

// Agregar el event listener para el botón de cancelar
document.getElementById('cancelDelete').addEventListener('click', function() {
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.style.display = 'none';
});