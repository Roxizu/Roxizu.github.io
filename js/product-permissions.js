document.addEventListener('DOMContentLoaded', function() {
    fetch('php/check_session.php')  // Fixed path to check_session.php
        .then(response => response.json())
        .then(data => {
            const buyButton = document.querySelector('.buy-now-btn');
            const quantitySelector = document.querySelector('.quantity-selector');
            const addProductBtn = document.querySelector('.add-product-btn');
            
            // Default state - no access
            if (buyButton) {
                buyButton.disabled = true;
                buyButton.style.backgroundColor = '#ccc';
                buyButton.style.cursor = 'not-allowed';
                buyButton.innerHTML = 'Inicie sesión para comprar';
            }
            if (quantitySelector) {
                quantitySelector.style.display = 'none';
            }
            if (addProductBtn) {
                addProductBtn.style.display = 'none';
            }

            // Check if user is logged in and has a role
            if (data.logged_in && data.rol) {
                
                    // Default state - no access
            if (buyButton) {
                buyButton.disabled = true;
                buyButton.style.backgroundColor = '#ccc';
                buyButton.style.cursor = 'not-allowed';
                buyButton.innerHTML = 'Inicie sesión para comprar';
            }
            if (quantitySelector) {
                quantitySelector.style.display = 'none';
            }
            if (addProductBtn) {
                addProductBtn.style.display = 'none';
            }

               
                if (data.rol === 'cliente') {
                    if (buyButton) {
                        buyButton.disabled = false;
                        buyButton.style.backgroundColor = '';
                        buyButton.style.cursor = 'pointer';
                        buyButton.innerHTML = 'Comprar ahora';
                    }
                    if (quantitySelector) {
                        quantitySelector.style.display = 'block';
                    }
                }
                if (data.rol === 'empresa') {
                    if (addProductBtn) {
                        addProductBtn.style.display = 'block';
                    }

                    if (buyButton) {
                        buyButton.disabled = false;
                        buyButton.style.backgroundColor = '';
                        buyButton.style.cursor = 'pointer';
                        buyButton.innerHTML = 'Comprar ahora';
                    }
                    if (quantitySelector) {
                        quantitySelector.style.display = 'block';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error checking session:', error);
        });
});