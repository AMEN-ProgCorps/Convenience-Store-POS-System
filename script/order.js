// --- Order Arrangement and Filter Functionality ---
let currentOrderArrangement = 'Highest';
let currentOrderFilter = 'Date';


function orderArrangement(type) {
    currentOrderArrangement = type;
    // Update active class
    document.querySelectorAll('.order-header-filter .type').forEach(el => {
        // Compare by data attribute or text content
        const elType = el.getAttribute('onclick')?.includes('Highest') ? 'Highest' :
                       el.getAttribute('onclick')?.includes('Lowest') ? 'Lowest' : el.textContent.trim();
        el.classList.toggle('active', elType === type);
    });
    renderOrderBoxes();
}

function orderFilter(type) {
    currentOrderFilter = type;
    // Update active class
    document.querySelectorAll('.order-header-filter .options').forEach(el => {
        el.classList.toggle('active', el.textContent.trim() === type);
    });
    renderOrderBoxes();
}

function getOrderDataFromDOM() {
    // Parse order data from DOM for sorting/filtering
    const orderBoxes = Array.from(document.querySelectorAll('.order-box'));
    return orderBoxes.map(box => {
        const orderId = box.id.replace('order_', '');
        const dateText = box.querySelector('.date-purchased')?.textContent.match(/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/);
        const orderDate = dateText ? new Date(dateText[0]) : new Date(0);
        const totalText = box.querySelector('.order_totalis')?.textContent.match(/P([\d,.]+)/);
        const totalAmount = totalText ? parseFloat(totalText[1].replace(/,/g, '')) : 0;
        const qtyText = box.querySelector('.order_totalis')?.textContent.match(/Total (\d+) item/);
        const totalQty = qtyText ? parseInt(qtyText[1]) : 0;
        const statusText = box.querySelector('.order-status')?.textContent.replace('Status:', '').trim() || '';
        return {
            box,
            orderId,
            orderDate,
            totalAmount,
            totalQty,
            statusText
        };
    });
}

function renderOrderBoxes() {
    const container = document.querySelector('.order-container');
    if (!container) return;
    const header = container.querySelector('.order-header');
    const orderData = getOrderDataFromDOM();


    // Arrangement: Highest/Lowest (by total amount)
    if (currentOrderArrangement === 'Highest') {
        orderData.sort((a, b) => b.totalAmount - a.totalAmount);
    } else if (currentOrderArrangement === 'Lowest') {
        orderData.sort((a, b) => a.totalAmount - b.totalAmount);
    }
    // Filter
    if (currentOrderFilter === 'Date') {
        orderData.sort((a, b) => b.orderDate - a.orderDate);
    } else if (currentOrderFilter === 'Quantity') {
        orderData.sort((a, b) => b.totalQty - a.totalQty);
    } else if (currentOrderFilter === 'Ammount') {
        orderData.sort((a, b) => b.totalAmount - a.totalAmount);
    } else if (currentOrderFilter === 'Status') {
        orderData.sort((a, b) => a.statusText.localeCompare(b.statusText));
    }

    // Remove all order-boxes
    container.querySelectorAll('.order-box').forEach(box => box.remove());

    // Re-append in new order
    orderData.forEach(data => container.appendChild(data.box));

    // Re-append header if needed
    if (header && container.firstChild !== header) {
        container.insertBefore(header, container.firstChild);
    }
}

// Attach filter/arrangement functions to window for inline onclick
window.orderArrangement = orderArrangement;
window.orderFilter = orderFilter;
// Toggle order items: show only one by default, show all on toggle
function toggleOrderItem(e) {
    // Find the order-footer that triggered the event
    let target = e && e.target ? e.target : null;
    if (!target) return;
    // Find the closest order-box
    let orderBox = target.closest('.order-box');
    if (!orderBox) return;
    let mainBox = orderBox.querySelector('.main-box');
    if (!mainBox) return;
    // Get all order-item divs
    let items = mainBox.querySelectorAll('.order-item');
    // If already expanded, collapse to show only the first
    if (orderBox.classList.contains('expanded')) {
        items.forEach((item, idx) => {
            item.style.display = idx === 0 ? '' : 'none';
        });
        orderBox.classList.remove('expanded');
        target.textContent = 'View Item/s ▼';
    } else {
        items.forEach(item => item.style.display = '');
        orderBox.classList.add('expanded');
        target.textContent = 'Hide Item/s ▲';
    }
}

// On page load, collapse all order-boxes to show only the first item
window.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.order-box .main-box').forEach(mainBox => {
        let items = mainBox.querySelectorAll('.order-item');
        items.forEach((item, idx) => {
            item.style.display = idx === 0 ? '' : 'none';
        });
    });
    // Attach event listeners to all .ampler buttons
    document.querySelectorAll('.order-footer .ampler').forEach(btn => {
        btn.onclick = toggleOrderItem;
    });
});

// Sample data for demonstration
const sampleOrderItems = [
    {
        id: 1,
        name: 'Coke 1.5L',
        quantity: 2,
        price: 65.00,
        image: '', // Add image URL if available
    },
    {
        id: 2,
        name: 'Piattos',
        quantity: 1,
        price: 32.00,
        image: '',
    },
];

function renderOrderBox(items) {
    const orderBox = document.getElementById('orderBox');
    const orderFooter = document.getElementById('orderFooter');
    orderBox.innerHTML = '';
    let total = 0;
    items.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'order-item';

        // Image
        const imgDiv = document.createElement('div');
        imgDiv.className = 'order-image';
        if (item.image) {
            imgDiv.style.backgroundImage = `url(${item.image})`;
            imgDiv.style.backgroundSize = 'cover';
        }
        itemDiv.appendChild(imgDiv);

        // Contents
        const contentsDiv = document.createElement('div');
        contentsDiv.className = 'contents';
        const label = document.createElement('div');
        label.className = 'contents-label';
        label.textContent = item.name;
        const quantity = document.createElement('div');
        quantity.className = 'contents-quantity';
        quantity.innerHTML = `Qty: <input type="number" min="1" value="${item.quantity}" data-id="${item.id}" class="order-qty-input" style="width:50px;">`;
        contentsDiv.appendChild(label);
        contentsDiv.appendChild(quantity);
        itemDiv.appendChild(contentsDiv);

        // Price
        const priceDiv = document.createElement('div');
        priceDiv.className = 'order-price';
        priceDiv.textContent = `₱${(item.price * item.quantity).toFixed(2)}`;
        itemDiv.appendChild(priceDiv);

        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.textContent = 'Remove';
        removeBtn.className = 'order-remove-btn';
        removeBtn.setAttribute('data-id', item.id);
        priceDiv.appendChild(removeBtn);

        orderBox.appendChild(itemDiv);
        total += item.price * item.quantity;
    });
    // Add footer back
    orderBox.appendChild(orderFooter);
    document.getElementById('orderTotal').textContent = `₱${total.toFixed(2)}`;
}

// Event delegation for quantity change and remove
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('order-qty-input')) {
        const id = parseInt(e.target.getAttribute('data-id'));
        const newQty = parseInt(e.target.value);
        const item = sampleOrderItems.find(i => i.id === id);
        if (item && newQty > 0) {
            item.quantity = newQty;
            renderOrderBox(sampleOrderItems);
        }
    }
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('order-remove-btn')) {
        const id = parseInt(e.target.getAttribute('data-id'));
        const idx = sampleOrderItems.findIndex(i => i.id === id);
        if (idx !== -1) {
            sampleOrderItems.splice(idx, 1);
            renderOrderBox(sampleOrderItems);
        }
    }
});

// Initial render on page load
window.addEventListener('DOMContentLoaded', function() {
    renderOrderBox(sampleOrderItems);
});
