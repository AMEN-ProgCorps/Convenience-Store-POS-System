// Due to new design this function is not used anymore
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        // Remove active class from all tabs 
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        // Add active class to the clicked tab
        if (tab.classList.contains('active')) return;
        tab.classList.add('active');

        // Handle content container animations
        const activeContainer = document.querySelector('.content-container.active');
        if (activeContainer) {
            activeContainer.classList.add('exiting');
            setTimeout(() => {
                activeContainer.classList.remove('active', 'exiting');
            }, 500); // Match the CSS transition duration
        }

        // Show the target content container
        const targetId = tab.getAttribute('data-target');
        const targetContainer = document.getElementById(targetId);
        setTimeout(() => {
            targetContainer.classList.add('active');
        }, 500); // bug Found kaya pala di nagana need nito

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

function toggleCart() {
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

function selectPaymentType(type) {
    // Remove active class from all payment buttons
    document.querySelectorAll('.payment-button').forEach(btn => {
        if (btn.getAttribute('data-type') === type) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Update the current payment type
    window.currentPaymentType = type;
}