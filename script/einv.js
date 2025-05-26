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
            labels: data.map(category => category.name),
            datasets: [{
                data: data.map(category => category.sales),
                backgroundColor: [
                    '#0A401E',
                    '#156835',
                    '#1F914C',
                    '#29BA63',
                    '#33E37A'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
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

        let discountInfo = '';
        if (record.discount_id) {
            discountInfo = `<div class="record-discount">Discount: ${record.discount_percentage}%</div>`;
        }

        recordElement.innerHTML = `
            <div class="record-details">
                <div class="record-product">${record.product_name}</div>
                <div class="record-info">
                    ${record.employee_name} • ${new Date(record.change_date).toLocaleString()}
                    ${discountInfo}
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

function handleProductSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    // Reset messages
    document.getElementById('product-error').style.display = 'none';
    document.getElementById('product-success').style.display = 'none';

    fetch('../../php/api/add_product.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('product-success').textContent = 'Product added successfully!';
                document.getElementById('product-success').style.display = 'block';
                form.reset();

                // Refresh inventory data
                loadInventoryStats();
                loadInventoryRecords();
            } else {
                document.getElementById('product-error').textContent = data.error || 'Error adding product';
                document.getElementById('product-error').style.display = 'block';
            }
        })
        .catch(error => {
            document.getElementById('product-error').textContent = 'Error adding product';
            document.getElementById('product-error').style.display = 'block';
        });
}

function handleDiscountSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    // Reset messages
    document.getElementById('discount-error').style.display = 'none';
    document.getElementById('discount-success').style.display = 'none';

    // Validate dates
    const validFrom = new Date(formData.get('valid_from'));
    const validTill = new Date(formData.get('valid_till'));

    if (validTill <= validFrom) {
        document.getElementById('discount-error').textContent = 'End date must be after start date';
        document.getElementById('discount-error').style.display = 'block';
        return;
    }

    fetch('../../php/api/add_discount.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('discount-success').textContent = 'Discount added successfully!';
                document.getElementById('discount-success').style.display = 'block';
                form.reset();
            } else {
                document.getElementById('discount-error').textContent = data.error || 'Error adding discount';
                document.getElementById('discount-error').style.display = 'block';
            }
        })
        .catch(error => {
            document.getElementById('discount-error').textContent = 'Error adding discount';
            document.getElementById('discount-error').style.display = 'block';
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
    const form = event.target;
    const formData = new FormData(form);

    // Reset messages
    document.getElementById('category-error').style.display = 'none';
    document.getElementById('category-success').style.display = 'none';

    fetch('../../php/api/add_category.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('category-success').textContent = 'Category added successfully!';
                document.getElementById('category-success').style.display = 'block';
                form.reset();

                // Refresh the categories list
                refreshCategories();
            } else {
                document.getElementById('category-error').textContent = data.error || 'Error adding category';
                document.getElementById('category-error').style.display = 'block';
            }
        })
        .catch(error => {
            document.getElementById('category-error').textContent = 'Error adding category';
            document.getElementById('category-error').style.display = 'block';
        });
}

function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category? This will affect all products in this category.')) {
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
                    // Refresh the categories list
                    refreshCategories();
                } else {
                    alert(data.error || 'Error deleting category');
                }
            })
            .catch(error => {
                alert('Error deleting category');
            });
    }
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
