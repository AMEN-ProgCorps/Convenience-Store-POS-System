function processOrder(orderId, action) {
    if (!confirm(`Are you sure you want to ${action} this order?`)) {
        return;
    }

    fetch('../../php/api/process_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_id: orderId,
            action: action
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const message = document.createElement('div');
                message.className = 'alert alert-success';
                message.textContent = data.message;
                document.querySelector('.center-bar').prepend(message);

                // Remove message after 3 seconds
                setTimeout(() => {
                    message.remove();
                }, 3000);

                // Refresh orders list
                loadOrders();
            } else {
                alert(data.error || `Error ${action}ing order`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Error ${action}ing order`);
        });
}

function approveOrder(orderId) {
    processOrder(orderId, 'approve');
}

function declineOrder(orderId) {
    processOrder(orderId, 'decline');
} 