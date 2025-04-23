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
    window.location.href = `editar-producto.html?id=${id}`;
}

function confirmarEliminar(id) {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'block';
    
    document.getElementById('confirmDelete').onclick = function() {
        eliminarProducto(id);
        modal.style.display = 'none';
    };
}

function eliminarProducto(id) {
    fetch('php/eliminar-producto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            cargarProductos();
        } else {
            alert('Error al eliminar el producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el producto');
    });
}