// Search functionality for the product list in page1.php
window.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-bar-container input[type="text"]');
    if (!searchInput) return;
    let categoryHiddenBySearch = false;
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim().toLowerCase();
        // Always get the latest product cards in case DOM changes
        const productCards = document.querySelectorAll('.center-bar-body-main-container .item-card');
        productCards.forEach(card => {
            const name = card.querySelector('.item-name')?.textContent.toLowerCase() || '';
            if (query === '' || name.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
        // Hide category if search is active, show if cleared
        if (query !== '' && !categoryHiddenBySearch) {
            toggleCategory();
            categoryHiddenBySearch = true;
        } else if (query === '' && categoryHiddenBySearch) {
            toggleCategory();
            categoryHiddenBySearch = false;
        }
    });
});
function toggleCategory() {
    const category = document.querySelector('.category-toggle');
    const category_body = document.querySelector('.center-bar-category-container');
    category.classList.toggle('active');
    if (!category_body.classList.contains('active')) {
        category_body.classList.add('active');
    } else {
        category_body.classList.add('exiting');
        setTimeout(() => {
            category_body.classList.remove('active', 'exiting');
        }, 400); // Match the CSS animation duration
    }
}
