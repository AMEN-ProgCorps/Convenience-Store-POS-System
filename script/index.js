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
