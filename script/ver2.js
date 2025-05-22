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
            location.replace('page2.php');
        }, 500);
    } else if (area === 'items') {
        orderButton.classList.remove('active');
        itemsButton.classList.add('active');
        orderContainer.classList.add('exiting');
        setTimeout(() => {
            orderContainer.classList.remove('active', 'exiting');
            location.replace('page1.php');
        }, 500);
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
    resetShownumber();
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
    const inputDelay = itemCard.querySelector('.total_input'); // Target the total input within the specific item-card
    if (placeholderButton) {
        placeholderButton.classList.add('exiting');
        setTimeout(() => {
            placeholderButton.classList.remove('active', 'exiting'); // Hide the add-to-order button
            actualInput.classList.add('active'); // Show the input container
            setTimeout(() => {
                inputDelay.classList.add('late'); // Add 'late' class after showing
                setTimeout(() => {
                    inputDelay.classList.add('flex'); // Add 'flex' class after showing
                }, 1400); // Match the animation duration
            }, 900); // Match the CSS animation duration

            // Remove 'late' and 'flex' classes from other item-cards when clicking a new one
            const allItemCards = document.querySelectorAll('.item-card .total_input');
            allItemCards.forEach((input) => {
                if (input !== inputDelay) {
                    input.classList.remove('late', 'flex');
                }
            });
        }, 500); // Match the animation duration
    } else {
        console.error('Placeholder button not found in this item-card!');
    }
}

function removeCartToggle() {
    const activeCard = document.querySelector('.item-card.active');
    const cartBody = document.querySelector('.cart-top-body');
    const noItemPlaceholder = cartBody.querySelector('p');

    if (activeCard) {
        const shownumberElement = activeCard.querySelector('.shownumber');
        const quantity = parseInt(shownumberElement.textContent);

        if (quantity > 0) {
            const itemId = activeCard.getAttribute('id');
            let cartItem = cartBody.querySelector(`.cart-top-body-product[data-id="${itemId}"]`);

            // If the item is not already in the cart, create it
            if (!cartItem) {
                cartItem = document.createElement('div');
                cartItem.classList.add('cart-top-body-product');
                cartItem.setAttribute('data-id', itemId);

                // Clone item details
                const itemImage = activeCard.querySelector('.item-image').cloneNode(true);
                const itemName = activeCard.querySelector('.item-name').textContent;
                const itemPrice = activeCard.querySelector('.item-price').textContent;

                cartItem.innerHTML = `
                    <div class="cart-top-body-product-image">${itemImage.outerHTML}</div>
                    <div class="cart-top-body-product-label">
                        <div class="cart-top-body-product-label-name">${itemName}</div>
                        <div class="cart-top-body-product-label-details">
                            <div class="cart-top-body-product-label-details-price">${itemPrice}</div>
                            <div class="cart-top-body-product-label-details-quantity">${quantity}</div>
                        </div>
                    </div>
                    <div class="cart-top-body-product-total_price">₱${(parseFloat(itemPrice.replace('₱', '')) * quantity).toFixed(2)}</div>
                `;

                cartBody.appendChild(cartItem);
            } else {
                // Update the quantity and total price if the item already exists in the cart
                const quantityElement = cartItem.querySelector('.cart-top-body-product-label-details-quantity');
                const totalPriceElement = cartItem.querySelector('.cart-top-body-product-total_price');
                const itemPrice = parseFloat(activeCard.querySelector('.item-price').textContent.replace('₱', ''));

                const newQuantity = parseInt(quantityElement.textContent) + quantity;
                quantityElement.textContent = newQuantity;
                totalPriceElement.textContent = `₱${(itemPrice * newQuantity).toFixed(2)}`;
            }

            // Remove the "no item present" placeholder if it exists
            if (noItemPlaceholder) {
                noItemPlaceholder.remove();
            }
        }

        // Reset the active card
        activeCard.classList.remove('active');
        const placeholderButton = activeCard.querySelector('.add-to-order');
        const actualInput = activeCard.querySelector('.total-input-container');
        const inputDelay = activeCard.querySelector('.total_input');

        if (placeholderButton) {
            placeholderButton.classList.add('active');
        }
        if (actualInput) {
            actualInput.classList.remove('active');
        }
        if (inputDelay) {
            inputDelay.classList.remove('flex');
            inputDelay.classList.add('late');
        }
    }

    // If no items are in the cart, show the "no item present" placeholder
    if (!cartBody.querySelector('.cart-top-body-product')) {
        const placeholder = document.createElement('p');
        placeholder.textContent = 'No item present';
        cartBody.appendChild(placeholder);
    }
}

// Reset shownumber to zero for all item-cards
function resetShownumber() {
    document.querySelectorAll('.item-card .shownumber').forEach(shownumber => {
        shownumber.textContent = '0'; // Reset the value to zero
    });
}
// Add event listeners for subtract and add buttons with stock cap
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.total_input-subtract').forEach(button => {
        button.addEventListener('click', () => {
            const shownumber = button.nextElementSibling;
            let currentValue = parseInt(shownumber.textContent);
            if (currentValue > 0) {
                shownumber.textContent = currentValue - 1;
            }
        });
    });

    document.querySelectorAll('.total_input-add').forEach(button => {
        button.addEventListener('click', () => {
            const shownumber = button.previousElementSibling;
            let currentValue = parseInt(shownumber.textContent);
            // Find the parent item-card to get the stock
            const itemCard = button.closest('.item-card');
            let maxStock = 9999;
            if (itemCard && itemCard.hasAttribute('data-stock')) {
                maxStock = parseInt(itemCard.getAttribute('data-stock'));
            }
            if (currentValue < maxStock) {
                shownumber.textContent = currentValue + 1;
            }
        });
    });
});