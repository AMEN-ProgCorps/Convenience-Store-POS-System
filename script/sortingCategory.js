function sortCategory(category) {
    const categoryList = document.querySelectorAll('.category');
    const itemCards = document.querySelectorAll('.item-card');

    // Switch to the clicked category
    categoryList.forEach(cat => {
        const categoryName = cat.querySelector('.category-label-name').textContent.trim().toLowerCase();
        if (categoryName === category.trim().toLowerCase()) {
            cat.classList.add('active');
            cat.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            cat.classList.remove('active');
        }
    });

    // Filter items based on the selected category
    itemCards.forEach(card => {
        const itemCategory = card.getAttribute('category').trim().toLowerCase();
        if (category.trim().toLowerCase() === 'all' || itemCategory === category.trim().toLowerCase()) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}   