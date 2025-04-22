document.addEventListener('DOMContentLoaded', function() {
    fetch('php/check_session.php')
        .then(response => response.json())
        .then(data => {
            const userIconContainer = document.querySelector('.user-icon').parentElement;
            
            if (data.logged_in && data.rol) {
               
                    userIconContainer.href = 'php/logout.php';
                    userIconContainer.innerHTML = `
                        <div class="user-icon">
                            <span style="font-size: 14px;">Cerrar Sesión</span>
                        </div>`;
               
            }
        })
        .catch(error => console.error('Error:', error));
});