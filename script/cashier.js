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
                    <div class="remove-item" onclick="removeItem(${item.product_id})">
                        <i class="fa-solid fa-trash-can"></i>
                    </div>
                `;

                oiBody.appendChild(itemBox);
                totalAmount += item.total_amount;
            });

            // Update order ID and total amount
            detailsBody.querySelector('.oi-id .oi-id-label').textContent = `Order #${order_id}`;
            detailsBody.querySelector('.oi-total .oi-total-label span').textContent = ` P${totalAmount.toFixed(2)}`;

            // Show payment section based on order's payment type
            const paymentSection = detailsBody.querySelector('.oi-payment');
            paymentSection.innerHTML = `
                <label>Cash Payment</label>
                <input type="number" id="cash-payment" placeholder="Enter cash amount">
            `;

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

function updateOrderStatus(order_id, action) {
    // Show loading state
    const buttonSection = document.querySelector('.oi-button');
    const originalButtons = buttonSection.innerHTML;
    buttonSection.innerHTML = '<div style="text-align: center;">Processing...</div>';

    const formData = new FormData();
    formData.append('order_id', order_id);
    formData.append('action', action);

    fetch('../../php/employee/update_order_status.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                // Show success message
                buttonSection.innerHTML = `
                <div style="text-align: center; color: green;">
                    Order ${action === 'approve' ? 'approved' : 'declined'} successfully!
                    <br>
                    ${action === 'approve' ? 'Inventory updated.' : 'Stock levels restored.'}
                </div>`;

                // Refresh the page after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                // Show error and restore buttons
                alert('Error: ' + (result.error || 'Unknown error occurred'));
                buttonSection.innerHTML = originalButtons;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
            buttonSection.innerHTML = originalButtons;
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