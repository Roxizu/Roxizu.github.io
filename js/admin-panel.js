document.addEventListener('DOMContentLoaded', function() {
    // Manejo de pestañas
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remover clase active de todos los botones y contenidos
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Activar la pestaña seleccionada
            btn.classList.add('active');
            const tabId = btn.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');

            // Cargar datos según la pestaña
            if (tabId === 'empresas') {
                cargarEmpresas();
            } else if (tabId === 'clientes') {
                cargarClientes();
            }
        });
    });

    // Cargar datos iniciales
    cargarEmpresas();

    // Función para cargar empresas
    // Definir las funciones globalmente
    function cargarEmpresas() {
        fetch('php/obtener-empresas.php')
            .then(response => response.json())
            .then(empresas => {
                const tbody = document.querySelector('#empresasTable tbody');
                tbody.innerHTML = '';
                
                // Obtener el total de ventas del localStorage
                const totalVentas = parseFloat(localStorage.getItem('totalVentas')) || 0;

                empresas.forEach(empresa => {
                    // Calcular el 5% de las ganancias
                    const porcentaje = 5;
                    const ganancias = totalVentas * (porcentaje / 100);

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${empresa.nombre_empresa || empresa.nombre_empre || ''}</td>
                        <td>${empresa.RNC || empresa.rnc || ''}</td>
                        <td>${empresa.email || ''}</td>
                        <td>${empresa.telefono || ''}</td>
                        <td>${empresa.direccion || ''}</td>
                        <td>RD$${ganancias.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td>${porcentaje}%</td>
                        <td>
                            <button onclick="editarEmpresa(${empresa.id})" class="btn-editar">Editar</button>
                            <button onclick="eliminarEmpresa(${empresa.id})" class="btn-eliminar">Eliminar</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => console.error('Error:', error));
    }

    // Definir cargarClientes como función global
    // Definir las funciones globalmente primero
    // Definir las funciones globalmente
    function cargarClientes() {
        fetch('php/obtener-clientes.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(clientes => {
                console.log('Datos de clientes recibidos:', clientes);
                const tbody = document.querySelector('#clientesTable tbody');
                tbody.innerHTML = '';
    
                if (!clientes || clientes.length === 0) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = '<td colspan="5" style="text-align: center;">No hay clientes registrados</td>';
                    tbody.appendChild(tr);
                    return;
                }
    
                clientes.forEach(cliente => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${cliente.nombre || ''}</td>
                        <td>${cliente.apellido || ''}</td>
                        <td>${cliente.email || ''}</td>
                        <td>${cliente.fecha || ''}</td>
                        <td>
                            <button onclick="editarCliente(${cliente.id})" class="btn-editar">Editar</button>
                            <button onclick="eliminarCliente(${cliente.id})" class="btn-eliminar">Eliminar</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => {
                console.error('Error detallado:', error);
                const tbody = document.querySelector('#clientesTable tbody');
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Error al cargar los clientes</td></tr>';
            });
    }
    // Agregar carga inicial de clientes cuando se hace clic en la pestaña
    document.querySelector('[data-tab="clientes"]').addEventListener('click', () => {
        cargarClientes();
    });

    // Cargar datos iniciales
    cargarEmpresas();
});

// Funciones para manejar acciones de empresas
function editarEmpresa(id) {
    // Implementar lógica de edición
    console.log('Editar empresa:', id);
}

function eliminarEmpresa(id) {
    if (confirm('¿Está seguro de que desea eliminar esta empresa?')) {
        fetch('php/eliminar-empresa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cargarEmpresas();
            } else {
                alert('Error al eliminar la empresa');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function eliminarCliente(id) {
    if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
        fetch('php/eliminar-cliente.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cargarClientes();
            } else {
                alert('Error al eliminar el cliente');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
// Funciones para manejar acciones de clientes
function editarCliente(id) {
    // Implementar lógica de edición
    console.log('Editar cliente:', id);
}

function buscarEmpresa() {
    const searchTerm = document.getElementById('searchEmpresa').value.toLowerCase();
    const rows = document.querySelectorAll('#empresasTable tbody tr');
    
    rows.forEach(row => {
        const rnc = row.children[1].textContent.toLowerCase();
        row.style.display = rnc.includes(searchTerm) ? '' : 'none';
    });
}

function buscarCliente() {
    const searchTerm = document.getElementById('searchCliente').value.toLowerCase();
    const rows = document.querySelectorAll('#clientesTable tbody tr');
    
    rows.forEach(row => {
        const email = row.children[2].textContent.toLowerCase();
        row.style.display = email.includes(searchTerm) ? '' : 'none';
    });
}

// Agregar event listeners para búsqueda en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchEmpresa').addEventListener('input', buscarEmpresa);
    document.getElementById('searchCliente').addEventListener('input', buscarCliente);
});