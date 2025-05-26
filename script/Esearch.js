document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const itemsContainer = document.querySelector('.items-box-container');

    // Function to load and display items
    function loadItems(searchQuery = '') {
        fetch(`../api/search_items.php?query=${encodeURIComponent(searchQuery)}`)
            .then(response => response.json())
            .then(items => {
                itemsContainer.innerHTML = ''; // Clear existing items

                if (items.length === 0) {
                    itemsContainer.innerHTML = '<div class="no-results">No items found</div>';
                    return;
                }

                items.forEach(item => {
                    const itemBox = document.createElement('div');
                    itemBox.className = 'item-box';
                    itemBox.innerHTML = `
                        <div class="item-id">ID: ${item.id}</div>
                        <div class="item-name">${item.name}</div>
                        <div class="item-category">${item.category || 'Uncategorized'}</div>
                        <div class="item-details">
                            <div class="item-quantity">Stock: ${item.quantity}</div>
                            <div class="item-amount">₱${item.amount}</div>
                        </div>
                    `;
                    itemsContainer.appendChild(itemBox);
                });
            })
            .catch(error => {
                console.error('Error loading items:', error);
                itemsContainer.innerHTML = '<div class="error">Error loading items</div>';
            });
    }

    // Load items on page load
    loadItems();

    // Search functionality
    function handleSearch() {
        const searchQuery = searchInput.value.trim();
        loadItems(searchQuery);
    }

    // Event listeners
    searchButton.addEventListener('click', handleSearch);

    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });

    // Debounce function for search input
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Add debounced search on input
    const debouncedSearch = debounce(() => handleSearch(), 300);
    searchInput.addEventListener('input', debouncedSearch);
});
