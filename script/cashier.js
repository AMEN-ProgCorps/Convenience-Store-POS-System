function showOrders(label, labelbtn) {

    // Show the selected list
    const targetList = document.getElementById(`${label}_orders`);
    if (targetList) {
        targetList.classList.toggle('show');
    }
}

function itemGoes(order_id) {
    fetch(`../../php/employee/get_order_items.php?order_id=${order_id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert('Error loading order details: ' + data.error);
                return;
            }

            const detailsBody = document.querySelector('.details-body');
            const oiBody = detailsBody.querySelector('.oi-body');
            oiBody.innerHTML = ''; // Clear existing items

            let totalAmount = 0;

            // Add each item to the order details
            data.forEach(item => {
                const itemBox = document.createElement('div');
                itemBox.className = 'item-box';
                itemBox.setAttribute('id', item.product_id);

                itemBox.innerHTML = `
                    <div class="item_name">ID: <span>${item.product_id}</span> - <span>${item.name}</span></div>
                    <div class="itemb-lock">
                        <div class="item_quantity">
                            <label for="">Quantity:</label>
                            <button onclick="updateQuantity(${item.product_id}, -1)">-</button>
                            <label>${item.quantity}</label>
                            <button onclick="updateQuantity(${item.product_id}, 1)">+</button>
                        </div>
                    </div>
                    <div class="item_price">P${item.total_amount.toFixed(2)}</div>
                    <div class="remove-item" onclick="removeItem(${item.product_id})" title="Remove Item">
                        <i class="fa-solid fa-trash-can"></i>
                    </div>
                `;

                oiBody.appendChild(itemBox);
                totalAmount += item.total_amount;
            });

            // Update order ID and total amount
            detailsBody.querySelector('.oi-id .oi-id-label').textContent = `Order #${order_id}`;
            detailsBody.querySelector('.oi-total .oi-total-label span').textContent = ` P${totalAmount.toFixed(2)}`;

            // Get payment type from the order item
            const paymentType = document.querySelector(`.order-item[order="${order_id}"] .oid:nth-child(3) span`).textContent;

            // Show payment section based on order's payment type
            const paymentSection = detailsBody.querySelector('.oi-payment');
            if (paymentType.toLowerCase() === 'cash') {
                paymentSection.innerHTML = `
                    <label>Cash Payment</label>
                    <input type="number" id="cash-payment" placeholder="Enter cash amount" required min="${totalAmount}" step="0.01">
                `;
            } else {
                paymentSection.innerHTML = `
                    <label>Payment Type: ${paymentType}</label>
                `;
            }

            // Set up buttons
            const buttonSection = detailsBody.querySelector('.oi-button');
            buttonSection.innerHTML = `
                <button class="cancel" onclick="updateOrderStatus('${order_id}', 'decline')">DECLINE</button>
                <button class="approve" onclick="updateOrderStatus('${order_id}', 'approve')">APPROVE</button>
            `;

            // Show the cart bar
            document.querySelector('.cart-bar').classList.add('active');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading order details');
        });
}

// Store original quantities and modified quantities
let originalQuantities = {};
let modifiedQuantities = {};
let originalPrices = {};

// Add a set to track removed items
let removedItems = new Set();

function updateQuantity(productId, change) {
    const itemBox = document.querySelector(`.item-box[id="${productId}"]`);
    if (!itemBox) return;

    // Get the quantity label and current quantity
    const quantityLabel = itemBox.querySelector('.item_quantity label:nth-child(3)');
    let currentQuantity = parseInt(quantityLabel.textContent);

    // Store original quantity if not already stored
    if (!(productId in originalQuantities)) {
        originalQuantities[productId] = currentQuantity;
        originalPrices[productId] = parseFloat(itemBox.querySelector('.item_price').textContent.replace('P', ''));
    }

    // Calculate new quantity
    const newQuantity = currentQuantity + change;
    if (newQuantity < 1) return; // Prevent negative quantities

    // Update the quantity label
    quantityLabel.textContent = newQuantity;
    modifiedQuantities[productId] = newQuantity;

    // Update the item's total price
    const unitPrice = originalPrices[productId] / originalQuantities[productId];
    const newTotal = unitPrice * newQuantity;
    itemBox.querySelector('.item_price').textContent = `P${newTotal.toFixed(2)}`;

    // Update total amount
    updateTotalAmount();
}

function updateTotalAmount() {
    let total = 0;
    document.querySelectorAll('.item-box').forEach(box => {
        const price = parseFloat(box.querySelector('.item_price').textContent.replace('P', ''));
        total += price;
    });
    document.querySelector('.oi-total .oi-total-label span').textContent = ` P${total.toFixed(2)}`;
}

function updateOrderStatus(orderId, action) {
    if (action === 'approve') {
        // Get the payment type from the order details
        const paymentType = document.querySelector('.order-item[order="' + orderId + '"] .oid:nth-child(3) span').textContent;

        if (paymentType.toLowerCase() === 'cash') {
            // Get the cash payment input
            const cashPayment = parseFloat(document.getElementById('cash-payment').value);
            const totalAmount = parseFloat(document.querySelector('.oi-total .oi-total-label span').textContent.replace('P', '').trim());

            // Validate cash payment
            if (!cashPayment || isNaN(cashPayment)) {
                alert('Please enter the cash payment amount');
                return;
            }

            if (cashPayment < totalAmount) {
                alert('Insufficient payment amount');
                return;
            }

            // Calculate balance
            const balance = cashPayment - totalAmount;

            // Show balance alert with bold text
            alert(`Transaction completed!\nThe balance after transaction is \u{1D5EF}${balance.toFixed(2)}\u{1D5EF}`);
        }

        // If quantities were modified or items were removed, update the database
        if (Object.keys(modifiedQuantities).length > 0 || removedItems.size > 0) {
            // First update product quantities and removed items
            fetch('../../php/employee/update_quantities.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    quantities: modifiedQuantities,
                    original_quantities: originalQuantities,
                    removed_items: Array.from(removedItems)
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error updating quantities: ' + data.error);
                        return;
                    }
                    // If quantities updated successfully, proceed with order approval
                    completeOrderUpdate(orderId, 'completed');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating quantities');
                });
        } else {
            // If no modifications, just update order status
            completeOrderUpdate(orderId, 'completed');
        }
    } else if (action === 'decline') {
        // For decline, just update the order status
        completeOrderUpdate(orderId, 'cancelled');
    }
}

function completeOrderUpdate(orderId, status) {
    fetch('../../php/employee/update_order_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_id: orderId,
            status: status
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error updating order: ' + data.error);
                return;
            }
            // Reset all tracking variables
            originalQuantities = {};
            modifiedQuantities = {};
            originalPrices = {};
            removedItems.clear();
            // Refresh the page to show updated orders
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
        });
}

function checkItem(order_id) {
    fetch(`../../php/employee/get_order_items.php?order_id=${order_id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert('Error loading order details: ' + data.error);
                return;
            }

            const detailsBody = document.querySelector('.details-body');
            const oiBody = detailsBody.querySelector('.oi-body');
            oiBody.innerHTML = ''; // Clear existing items

            let totalAmount = 0;

            // Add each item to the order details
            data.forEach(item => {
                const itemBox = document.createElement('div');
                itemBox.className = 'item-box';
                itemBox.setAttribute('id', item.product_id);

                itemBox.innerHTML = `
                    <div class="item_name">ID: <span>${item.product_id}</span> - <span>${item.name}</span></div>
                    <div class="itemb-lock">
                        <div class="item_quantity">
                            <label>Quantity:</label>
                            <label>${item.quantity}</label>
                        </div>
                    </div>
                    <div class="item_price">P${item.total_amount.toFixed(2)}</div>
                `;

                oiBody.appendChild(itemBox);
                totalAmount += item.total_amount;
            });

            // Update order ID and total amount
            detailsBody.querySelector('.oi-id .oi-id-label').textContent = `Order #${order_id}`;
            detailsBody.querySelector('.oi-total .oi-total-label span').textContent = ` P${totalAmount.toFixed(2)}`;

            // Clear payment section since this is just for viewing
            const paymentSection = detailsBody.querySelector('.oi-payment');
            paymentSection.innerHTML = '';

            // Clear button section since this is just for viewing
            const buttonSection = detailsBody.querySelector('.oi-button');
            buttonSection.innerHTML = '';

            // Show the cart bar
            document.querySelector('.cart-bar').classList.add('active');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading order details');
        });
}

function removeItem(productId) {
    const itemBox = document.querySelector(`.item-box[id="${productId}"]`);
    if (!itemBox) return;

    // Store original quantity if not already stored
    if (!(productId in originalQuantities)) {
        originalQuantities[productId] = parseInt(itemBox.querySelector('.item_quantity label:nth-child(3)').textContent);
        originalPrices[productId] = parseFloat(itemBox.querySelector('.item_price').textContent.replace('P', ''));
    }

    // Add to removed items set
    removedItems.add(productId);

    // Remove the item box with animation
    itemBox.style.transition = 'all 0.3s ease';
    itemBox.style.transform = 'translateX(100%)';
    itemBox.style.opacity = '0';

    setTimeout(() => {
        itemBox.remove();
        // Update total amount
        updateTotalAmount();
    }, 300);
}