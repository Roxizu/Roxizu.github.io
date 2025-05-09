function checkPermissions() {
    fetch('php/check_session.php')
        .then(response => response.json())
        .then(data => {
            const addProductBtn = document.querySelector('.add-product-btn');
            const viewProductsBtn = document.querySelector('.view-products-btn');
            const salesBtn = document.querySelector('.sales-btn');



            if (addProductBtn) {
                if (!data.logged_in || data.rol !== 'empresa') {
                    addProductBtn.style.display = 'none';
                }
            }
            
            if (viewProductsBtn) {
                if (!data.logged_in || data.rol !== 'empresa') {
                    viewProductsBtn.style.display = 'none';
                }
            }

            if (salesBtn) {
                if (!data.logged_in || data.rol !== 'empresa') {
                    salesBtn.style.display = 'none';
                }
            }
        });
}

document.addEventListener('DOMContentLoaded', checkPermissions);