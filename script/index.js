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