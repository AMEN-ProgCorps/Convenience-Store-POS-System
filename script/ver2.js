function toggleMenu(area) {
    const itemsContainer = document.querySelector('.items-container');
    const orderContainer = document.querySelector('.order-container');
    const itemsButton = document.querySelector('.items-tab');
    const orderButton = document.querySelector('.order-tab');

    if (area === 'order') {
        itemsButton.classList.remove('active');
        orderButton.classList.add('active');
        itemsContainer.classList.add('exiting');
        toggleCart();
        setTimeout(() => {
            itemsContainer.classList.remove('active', 'exiting');
            location.replace('../html/account.html');
        }, 500);
    } else if (area === 'items') {
        orderButton.classList.remove('active');
        itemsButton.classList.add('active');
        orderContainer.classList.add('exiting');
        setTimeout(() => {
            orderContainer.classList.remove('active', 'exiting');
            location.replace('../html/items.html');
        }, 500);
    }
}
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

function toggleCart(){
    const cart_btn = document.querySelector('.cart_toggle');
    const cart_body = document.querySelector('.cart-bar');
    cart_btn.classList.toggle('active');
    if (!cart_body.classList.contains('active')) {
        cart_body.classList.remove('exit');
        cart_body.classList.add('active');
        setTimeout(() => {
            cart_body.style.display = 'block';
        }, 500); // Match the CSS animation duration
    } else {
        cart_body.classList.remove('active');
        cart_body.classList.add('exit');
        setTimeout(() => {
            cart_body.style.display = 'none';
        }, 400); // Match the CSS animation duration
    }


}

function togglePayment() {
    const cash = document.querySelector('.cash-box');
    const ewallet = document.querySelector('.ewallet-box');
    cash.classList.toggle('active');
    ewallet.classList.toggle('active');
}

function addCartToggle(itemCard) {
    // Deactivate any currently active item-card
    const activeCard = document.querySelector('.item-card.active');
    if (activeCard && activeCard !== itemCard) {
        activeCard.classList.remove('active');

        // Reset the add-to-order button of the previously active card
        const previousPlaceholderButton = activeCard.querySelector('.add-to-order');
        const previousInputContainer = activeCard.querySelector('.total-input-container');
        if (previousPlaceholderButton) {
            previousPlaceholderButton.classList.add('active'); // Restore the active state
        }
        if (previousInputContainer) {
            previousInputContainer.classList.remove('active'); // Hide the input container
        }
    }

    // Activate the clicked item-card
    itemCard.classList.add('active');

    const placeholderButton = itemCard.querySelector('.add-to-order'); // Target the button within the specific item-card
    const actualInput = itemCard.querySelector('.total-input-container'); // Target the input within the specific item-card
    if (placeholderButton) {
        placeholderButton.classList.add('exiting');
        setTimeout(() => {
            placeholderButton.classList.remove('active', 'exiting'); // Hide the add-to-order button
            actualInput.classList.add('active'); // Show the input container
        }, 500); // Match the animation duration
    } else {
        console.error('Placeholder button not found in this item-card!');
    }
}