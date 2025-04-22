document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('.purchase-step');
    const indicators = document.querySelectorAll('.step-indicator');
    const nextBtn = document.querySelector('.next-btn');
    const confirmBtn = document.querySelector('.confirm-btn');
    const creditCardRadio = document.getElementById('credit-card');
    const paypalRadio = document.getElementById('paypal');
    const cardDetails = document.getElementById('card-details');
    const paypalDetails = document.getElementById('paypal-details');
    
    // Get stored values
    const quantity = parseInt(localStorage.getItem('purchaseQuantity')) || 1;
    const basePrice = 300;
    const shipping = 50;
    const subtotal = basePrice * quantity;
    const total = subtotal + shipping;

    // Update all price displays
    const quantityElement = document.querySelector('.quantity');
    const priceElement = document.querySelector('.price');
    const subtotalElement = document.querySelector('.summary-row:first-child span:last-child');
    const totalElement = document.querySelector('.summary-row.total span:last-child');

    if (quantityElement) quantityElement.textContent = `Cantidad: ${quantity}`;
    if (priceElement) priceElement.textContent = `RD$${subtotal}`;
    if (subtotalElement) subtotalElement.textContent = `RD$${subtotal}`;
    if (totalElement) totalElement.textContent = `RD$${total}`;

    function togglePaymentMethod() {
        if (creditCardRadio.checked) {
            cardDetails.style.display = 'block';
            paypalDetails.style.display = 'none';
            nextBtn.textContent = 'Revisar Compra';
        } else {
            cardDetails.style.display = 'none';
            paypalDetails.style.display = 'block';
            nextBtn.textContent = 'Pagar con PayPal';
        }
    }

    creditCardRadio.addEventListener('change', togglePaymentMethod);
    paypalRadio.addEventListener('change', togglePaymentMethod);

    nextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (paypalRadio.checked) {
            window.location.href = 'https://www.paypal.com/signin';
        } else {
            steps[0].classList.remove('active');
            steps[1].classList.add('active');
            indicators[0].classList.remove('active');
            indicators[1].classList.add('active');
        }
    });

    // Add confirmation button handler
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            // Store final purchase details with proper parsing
            const purchaseQuantity = parseInt(localStorage.getItem('purchaseQuantity')) || 1;
            const purchaseTotal = subtotal + shipping;
            
            localStorage.setItem('finalQuantity', purchaseQuantity.toString());
            localStorage.setItem('finalTotal', purchaseTotal.toString());
            localStorage.setItem('finalSubtotal', subtotal.toString());
            
            window.location.href = 'confirmacion.html';
        });
    }

    // Initial state
    togglePaymentMethod();
});