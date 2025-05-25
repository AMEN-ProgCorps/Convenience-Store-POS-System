function showOrders(label,labelbtn){
    const pending = document.getElementById('pending_orders');
    const completed = document.getElementById('complete_orders');
    const cancelled = document.getElementById('cancelled_orders');
    labelbtn.classList.toggle('active')
    if(label === 'pending'){
        pending.classList.toggle('show');
    }
    else if(label === 'complete'){
        completed.classList.toggle('show');
    }
    else if(label === 'cancelled'){
        cancelled.classList.toggle('show');
    }
}
function itemGoes(order_id) {
    fetch(`php/employee/get_order_items.php?order_id=${order_id}`)
        .then(res => res.json())
        .then(items => {
            // Render items in the order-details-container
        });
}
function updateOrderStatus(order_id, action) {
    fetch('php/employee/update_order_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `order_id=${order_id}&action=${action}`
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            // Refresh the order lists or update UI
        }
    });
}