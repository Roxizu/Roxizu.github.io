function checkPermissions() {
    fetch('php/check_session.php')
        .then(response => response.json())
        .then(data => {
            const addProductBtn = document.querySelector('.add-product-btn');
            if (addProductBtn) {
                if (!data.logged_in || data.rol !== 'empresa') {
                    addProductBtn.style.display = 'none';
                }
            }
        });
}

document.addEventListener('DOMContentLoaded', checkPermissions);