// Place Order functionality and receipt display
document.addEventListener('DOMContentLoaded', function () {
    const placeOrderBtn = document.querySelector('.order-button');
    if (!placeOrderBtn) return;

    placeOrderBtn.addEventListener('click', async function (e) {
        e.preventDefault();
        // Gather cart items
        const cartItems = Array.from(document.querySelectorAll('.cart-top-body .cart-top-body-product'));
        if (cartItems.length === 0) {
            alert('Cart is empty!');
            return;
        }
        // Get user id from a data attribute or fallback to C101
        let userId = 'C101';
        if (window.sessionUserId) userId = window.sessionUserId;

        // Get payment type
        let paymentType = 'cash';
        if (userId !== 'C101') {
            paymentType = window.currentPaymentType || 'cash';
        }

        // Get discount info
        let discountId = null;
        let discountPercent = 0;
        const selectedDiscount = document.querySelector('.dbox.selected');
        if (selectedDiscount) {
            discountId = selectedDiscount.getAttribute('data-id');
            discountPercent = parseFloat(selectedDiscount.getAttribute('data-percent')) || 0;
        }

        // Get pricing
        const subTotal = parseFloat((document.querySelector('.t2.total-box-label-price')?.textContent || '0').replace(/[^\d.\-]/g, '')) || 0;
        const discountAmount = parseFloat((document.querySelector('.t4.total-box-label-price')?.textContent || '0').replace(/[^\d.\-]/g, '')) || 0;
        const tax = parseFloat((document.querySelector('.t6.total-box-label-price')?.textContent || '0').replace(/[^\d.\-]/g, '')) || 0;
        const total = parseFloat((document.querySelector('.out7 .total-box-label-price')?.textContent || '0').replace(/[^\d.\-]/g, '')) || 0;

        // Prepare cart data
        const cart = cartItems.map(item => {
            const productId = item.getAttribute('product');
            const quantity = parseInt(item.querySelector('.cart-top-body-product-label-details-quantity').textContent);
            const unitPrice = parseFloat(item.querySelector('.cart-top-body-product-label-details-price').textContent.replace(/[^\d.\-]/g, '')) / quantity;
            const total = unitPrice * quantity;

            return {
                product_id: productId,
                quantity: quantity,
                unit_price: unitPrice,
                total: total
            };
        });

        // Send order to server
        try {
            const response = await fetch('../../php/api/place_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart: cart,
                    discount_id: discountId,
                    discount_percent: discountPercent,
                    payment_type: paymentType,
                    sub_total: subTotal,
                    discount_amount: discountAmount,
                    tax: tax,
                    total: total
                })
            });

            const data = await response.json();
            if (data.error) {
                throw new Error(data.error);
            }

            // Clear cart and reset pricing displays
            document.querySelector('.cart-top-body').innerHTML = '';
            // Reset all price displays to 0
            document.querySelectorAll('.t2.total-box-label-price').forEach(el => el.textContent = '₱0.00');
            document.querySelectorAll('.t4.total-box-label-price').forEach(el => el.textContent = '-₱0.00');
            document.querySelectorAll('.t6.total-box-label-price').forEach(el => el.textContent = '₱0.00');
            document.querySelectorAll('.out7 .total-box-label-price').forEach(el => el.textContent = '₱0.00');

            // Show receipt
            showReceipt(data.receipt);
            alert('Order placed successfully!');

        } catch (error) {
            console.error('Error:', error);
            alert('Error placing order: ' + error.message);
        }
    });
});

function showReceipt(receipt) {
    // Format receipt text using sample_qrcode_layout.txt style
    let receiptText = '===============================\n';
    receiptText += `Order_id: ${receipt.order_id}\n`;
    receiptText += 'Product:\n';
    receipt.cart.forEach(item => {
        if (item.name && item.name.trim() !== '' && item.quantity && item.quantity > 0) {
            receiptText += `${item.quantity} - ${item.name}\n`;
        }
    });
    receiptText += '\n---------------------------\n';
    receiptText += `sub-total: ${receipt.sub_total}\n`;
    if (receipt.discount && receipt.discount > 0) {
        receiptText += `Discount: ${receipt.discount}`;
        if (receipt.discount_percent) receiptText += ` (${receipt.discount_percent}%)`;
        receiptText += '\n';
    }
    receiptText += `Tax: 0.12\n`;
    receiptText += `Total : ${receipt.total}\n`;
    receiptText += `type: ${receipt.payment_type}\n`;
    receiptText += 'Pls Proceed to the Cashier!\n';
    receiptText += '===============================\n';

    // Show in #receipt .receipt-contents p
    const receiptDiv = document.getElementById('receipt');
    if (receiptDiv) {
        receiptDiv.style.display = 'flex';
        const p = receiptDiv.querySelector('.receipt-contents p');
        if (p) p.textContent = receiptText;
        // Optionally, generate a QR code (placeholder)
        let qr = receiptDiv.querySelector('.receipt-contents .qrcode');
        if (!qr) {
            qr = document.createElement('div');
            qr.className = 'qrcode';
            receiptDiv.querySelector('.receipt-contents').appendChild(qr);
        }
        // Use a free API for QR code (for demo)
        qr.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(receiptText)}" alt="QR Code"/>`;
    }
}

function closeReceipt() {
    const receiptDiv = document.getElementById('receipt');
    if (receiptDiv) receiptDiv.style.display = 'none';
}
