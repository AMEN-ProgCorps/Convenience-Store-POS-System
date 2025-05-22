function toggleDiscount() {
    const discount_view = document.getElementById('discount-body');
    discount_view.classList.toggle('active');
}

// Discount selection logic
document.addEventListener('DOMContentLoaded', function() {
    let selectedDiscount = null;
    let selectedDiscountPercent = 0;
    let selectedDiscountLabel = null;
    const discountBoxes = document.querySelectorAll('.dbox');
    const acceptBtn = document.querySelector('.discount_footer .accept');
    const discountActive = document.querySelector('.discount-active');

    function updateCartPricing() {
        // Calculate subtotal from cart
        let subtotal = 0;
        document.querySelectorAll('.cart-top-body-product').forEach(item => {
            const price = item.querySelector('.cart-top-body-product-label-details-price');
            const qty = item.querySelector('.cart-top-body-product-label-details-quantity');
            if (price && qty) {
                const p = parseFloat(price.textContent.replace(/[^\d.]/g, ''));
                const q = parseInt(qty.textContent);
                if (!isNaN(p) && !isNaN(q)) {
                    subtotal += p * q;
                }
            }
        });

        // If subtotal is not a valid number (empty cart), set all to 0
        if (!isFinite(subtotal) || subtotal <= 0) {
            subtotal = 0;
        }

        // Get discount percent
        let discountPercent = selectedDiscountPercent || 0;
        let discountAmount = subtotal * (discountPercent / 100);
        if (!isFinite(discountAmount) || subtotal === 0) discountAmount = 0;
        let afterDiscount = subtotal - discountAmount;
        if (!isFinite(afterDiscount) || subtotal === 0) afterDiscount = 0;
        let tax = afterDiscount * 0.12;
        if (!isFinite(tax) || subtotal === 0) tax = 0;
        let total = afterDiscount + tax;
        if (!isFinite(total) || subtotal === 0) total = 0;

        // Update all matching UI elements (in case there are multiple)
        document.querySelectorAll('.t2.total-box-label-price').forEach(el => el.textContent = '₱' + subtotal.toFixed(2));
        document.querySelectorAll('.t4.total-box-label-price').forEach(el => el.textContent = '-₱' + discountAmount.toFixed(2));
        document.querySelectorAll('.t6.total-box-label-price').forEach(el => el.textContent = '₱' + tax.toFixed(2));
        document.querySelectorAll('.out7 .total-box-label-price').forEach(el => el.textContent = '₱' + total.toFixed(2));
    }

    discountBoxes.forEach(box => {
        box.addEventListener('click', function() {
            // Don't allow selection if warning is present
            const warningBox = box.querySelector('.dbox-warning');
            if (warningBox && warningBox.style.display === 'flex') return;
            discountBoxes.forEach(b => b.classList.remove('selected'));
            box.classList.add('selected');
            selectedDiscount = box.getAttribute('data-id');
            selectedDiscountPercent = parseFloat(box.getAttribute('data-percent')) || 0;
            selectedDiscountLabel = box.querySelector('.dbox-label').textContent;
        });
    });

    if (acceptBtn) {
        acceptBtn.addEventListener('click', function() {
            if (selectedDiscount && discountActive) {
                discountActive.textContent = selectedDiscountLabel;
            }
            updateCartPricing();
            toggleDiscount();
        });
    }

    // Recalculate pricing when cart changes
    document.querySelectorAll('.add_cart, .total_input-add, .total_input-subtract').forEach(btn => {
        btn.addEventListener('click', function() {
            setTimeout(updateCartPricing, 100); // Wait for DOM update
        });
    });
    // Also update on page load
    updateCartPricing();
});