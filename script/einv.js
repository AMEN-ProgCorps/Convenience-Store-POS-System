document.addEventListener('DOMContentLoaded', function () {
    // Load initial data
    loadInventoryStats();
    loadInventoryCharts();
    loadInventoryRecords();

    // Refresh data every 5 minutes
    setInterval(() => {
        loadInventoryStats();
        loadInventoryCharts();
        loadInventoryRecords();
    }, 300000);
});

function loadInventoryStats() {
    fetch('../../php/api/get_inventory_stats.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-items').textContent = data.total_items.toLocaleString();
            document.getElementById('total-value').textContent = '₱' + data.total_value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('low-stock').textContent = data.low_stock_items.toLocaleString();
        })
        .catch(error => console.error('Error loading inventory stats:', error));
}

function loadInventoryCharts() {
    // Load Popular Items Chart
    fetch('../../php/api/get_popular_items.php')
        .then(response => response.json())
        .then(data => {
            createPopularItemsChart(data);
        })
        .catch(error => console.error('Error loading popular items:', error));

    // Load Category Sales Chart
    fetch('../../php/api/get_category_sales.php')
        .then(response => response.json())
        .then(data => {
            createCategorySalesChart(data);
        })
        .catch(error => console.error('Error loading category sales:', error));

    // Load Reduction Rate Chart
    fetch('../../php/api/get_reduction_rate.php')
        .then(response => response.json())
        .then(data => {
            createReductionRateChart(data);
        })
        .catch(error => console.error('Error loading reduction rate:', error));
}

function createPopularItemsChart(data) {
    const ctx = document.getElementById('popular-items-chart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.name),
            datasets: [{
                label: 'Units Sold Today',
                data: data.map(item => item.quantity),
                backgroundColor: '#0A401E',
                borderColor: '#0A401E',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function createCategorySalesChart(data) {
    const ctx = document.getElementById('category-sales-chart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.map(item => item.name),
            datasets: [{
                data: data.map(item => item.total_sold),
                backgroundColor: [
                    '#0A401E',
                    '#156F3C',
                    '#45B07F',
                    '#88D4AB',
                    '#B7E4C7',
                    '#D8F3DC'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Sales by Category This Week'
                },
                legend: {
                    display: true,
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${context.raw} units (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function createReductionRateChart(data) {
    const ctx = document.getElementById('reduction-rate-chart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Reduced', 'In Stock'],
            datasets: [{
                data: [data.reduction_percentage, 100 - data.reduction_percentage],
                backgroundColor: ['#dc3545', '#0A401E']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: data.reduction_percentage + '% Reduction'
                }
            }
        }
    });
}

function loadInventoryRecords() {
    // Load Reductions
    fetch('../../php/api/get_inventory_records.php?type=reduction')
        .then(response => response.json())
        .then(data => {
            displayRecords('reductions-container', data, false);
        })
        .catch(error => console.error('Error loading reductions:', error));

    // Load Additions
    fetch('../../php/api/get_inventory_records.php?type=addition')
        .then(response => response.json())
        .then(data => {
            displayRecords('additions-container', data, true);
        })
        .catch(error => console.error('Error loading additions:', error));
}

function displayRecords(containerId, records, isAddition) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    records.forEach(record => {
        const recordElement = document.createElement('div');
        recordElement.className = 'record-item';

        // Format the date
        const date = new Date(record.change_date);
        const formattedDate = date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        recordElement.innerHTML = `
            <div class="record-details">
                <div class="record-product">${record.product_name}</div>
                <div class="record-info">
                    ${record.employee_name} • ${formattedDate}
                </div>
            </div>
            <div class="record-quantity ${isAddition ? 'quantity-added' : 'quantity-reduced'}">
                ${isAddition ? '+' : '-'}${Math.abs(record.quantity_change)}
            </div>
        `;
        container.appendChild(recordElement);
    });

    if (records.length === 0) {
        container.innerHTML = '<div class="loading">No records found</div>';
    }
}

// Form Management Functions
function showForm(type) {
    const productForm = document.getElementById('product-form');
    const discountForm = document.getElementById('discount-form');
    const categoryForm = document.getElementById('category-form');
    const tabs = document.querySelectorAll('.management-tab');

    // Hide all forms
    productForm.classList.remove('active');
    discountForm.classList.remove('active');
    categoryForm.classList.remove('active');
    tabs.forEach(tab => tab.classList.remove('active'));

    // Show selected form
    if (type === 'product') {
        productForm.classList.add('active');
        tabs[0].classList.add('active');
    } else if (type === 'discount') {
        discountForm.classList.add('active');
        tabs[1].classList.add('active');
    } else if (type === 'category') {
        categoryForm.classList.add('active');
        tabs[2].classList.add('active');
    }
}

function switchProductForm(type) {
    const newForm = document.getElementById('add-product-form');
    const existingForm = document.getElementById('update-product-form');
    const buttons = document.querySelectorAll('.type-btn');

    if (type === 'new') {
        newForm.classList.add('active');
        existingForm.classList.remove('active');
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
    } else {
        newForm.classList.remove('active');
        existingForm.classList.add('active');
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');
        loadExistingProducts();
    }
}

function loadExistingProducts() {
    fetch('../../php/api/get_products.php')
        .then(response => response.json())
        .then(products => {
            const select = document.getElementById('existing-product');
            select.innerHTML = '<option value="">Select Product</option>';
            products.forEach(product => {
                select.innerHTML += `
                    <option value="${product.product_id}" 
                            data-stock="${product.stock_level}"
                            data-category="${product.category_id}">
                        ${product.name} (${product.category_name})
                    </option>`;
            });
        })
        .catch(error => console.error('Error loading products:', error));
}

function loadProductDetails(productId) {
    if (!productId) {
        document.getElementById('current-stock').value = '';
        return;
    }

    const option = document.querySelector(`#existing-product option[value="${productId}"]`);
    document.getElementById('current-stock').value = option.dataset.stock;
}

function handleProductSubmit(event, type) {
    event.preventDefault();

    const form = type === 'new' ? document.getElementById('add-product-form') : document.getElementById('update-product-form');
    const formData = new FormData(form);
    formData.append('type', type);

    const errorElement = type === 'new' ? document.getElementById('product-error') : document.getElementById('update-error');
    const successElement = type === 'new' ? document.getElementById('product-success') : document.getElementById('update-success');

    fetch('../../php/api/update_inventory_records.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successElement.textContent = data.message;
                errorElement.textContent = '';
                form.reset();

                // Refresh the data without page reload
                loadInventoryStats();
                loadInventoryCharts();
                loadInventoryRecords();
                loadExistingProducts(); // Refresh product list in update form

                // Clear success message after 3 seconds
                setTimeout(() => {
                    successElement.textContent = '';
                }, 3000);
            } else {
                errorElement.textContent = data.message;
                successElement.textContent = '';
            }
        })
        .catch(error => {
            errorElement.textContent = 'An error occurred. Please try again.';
            successElement.textContent = '';
            console.error('Error:', error);
        });
}

function handleDiscountSubmit(event) {
    event.preventDefault();

    const form = document.getElementById('add-discount-form');
    const formData = new FormData(form);

    const errorElement = document.getElementById('discount-error');
    const successElement = document.getElementById('discount-success');

    fetch('../../php/api/add_discount.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successElement.textContent = data.message;
                errorElement.textContent = '';
                form.reset();

                // Refresh relevant data
                loadInventoryStats();

                // Clear success message after 3 seconds
                setTimeout(() => {
                    successElement.textContent = '';
                }, 3000);
            } else {
                errorElement.textContent = data.message;
                successElement.textContent = '';
            }
        })
        .catch(error => {
            errorElement.textContent = 'An error occurred. Please try again.';
            successElement.textContent = '';
            console.error('Error:', error);
        });
}

// Set minimum date for discount validity
document.addEventListener('DOMContentLoaded', function () {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('valid-from').min = today;
    document.getElementById('valid-till').min = today;
});

function handleCategorySubmit(event) {
    event.preventDefault();

    const form = document.getElementById('add-category-form');
    const formData = new FormData(form);

    const errorElement = document.getElementById('category-error');
    const successElement = document.getElementById('category-success');

    fetch('../../php/api/add_category.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successElement.textContent = data.message;
                errorElement.textContent = '';
                form.reset();

                // Refresh categories in all dropdowns
                refreshCategories();

                // Clear success message after 3 seconds
                setTimeout(() => {
                    successElement.textContent = '';
                }, 3000);
            } else {
                errorElement.textContent = data.message;
                successElement.textContent = '';
            }
        })
        .catch(error => {
            errorElement.textContent = 'An error occurred. Please try again.';
            successElement.textContent = '';
            console.error('Error:', error);
        });
}

function deleteCategory(categoryId) {
    if (!confirm('Are you sure you want to delete this category?')) {
        return;
    }

    fetch('../../php/api/delete_category.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ category_id: categoryId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh categories without page reload
                refreshCategories();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
            console.error('Error:', error);
        });
}

function refreshCategories() {
    fetch('../../php/api/get_categories.php')
        .then(response => response.json())
        .then(data => {
            const container = document.querySelector('.categories-container');
            container.innerHTML = '';

            data.forEach(category => {
                const categoryElement = document.createElement('div');
                categoryElement.className = 'category-item';
                categoryElement.innerHTML = `
                    <span>${category.name}</span>
                    <button class="delete-category" onclick="deleteCategory(${category.category_id})">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                container.appendChild(categoryElement);
            });

            // Also update the category select in the product form
            const categorySelect = document.getElementById('category');
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            data.forEach(category => {
                categorySelect.innerHTML += `
                    <option value="${category.category_id}">${category.name}</option>
                `;
            });
        })
        .catch(error => {
            console.error('Error refreshing categories:', error);
        });
}
