document.addEventListener('DOMContentLoaded', function () {
    // Initialize search functionality
    const searchInput = document.getElementById('staff-search');
    searchInput.addEventListener('input', filterStaff);

    // Initialize filter functionality
    const filterOptions = document.querySelectorAll('.filter-option');
    filterOptions.forEach(option => {
        option.addEventListener('click', function () {
            filterOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            filterStaff();
        });
    });

    // Initialize create staff button
    const createStaffBtn = document.querySelector('.create-staff-btn');
    createStaffBtn.addEventListener('click', function () {
        showStaffForm();
    });

    // Close form when clicking outside
    document.addEventListener('click', function (event) {
        const cartBar = document.querySelector('.cart-bar');
        const staffForm = document.querySelector('.staff-form');
        if (cartBar.classList.contains('active') &&
            !staffForm.contains(event.target) &&
            !event.target.classList.contains('staff-box') &&
            !event.target.classList.contains('create-staff-btn')) {
            closeForm();
        }
    });
});

function filterStaff() {
    const searchTerm = document.getElementById('staff-search').value.toLowerCase();
    const activeRole = document.querySelector('.filter-option.active').dataset.role;
    const staffBoxes = document.querySelectorAll('.staff-box');

    staffBoxes.forEach(box => {
        const name = box.querySelector('.staff-name').textContent.toLowerCase();
        const role = box.dataset.role;
        const matchesSearch = name.includes(searchTerm);
        const matchesRole = activeRole === 'all' || role === activeRole;

        box.style.display = matchesSearch && matchesRole ? 'block' : 'none';
    });

    // Show no results message if needed
    const visibleStaff = document.querySelectorAll('.staff-box[style="display: block"]');
    const container = document.querySelector('.staff-container');
    const noResults = container.querySelector('.no-results');

    if (visibleStaff.length === 0) {
        if (!noResults) {
            const message = document.createElement('div');
            message.className = 'no-results';
            message.textContent = 'No staff members found';
            container.appendChild(message);
        }
    } else if (noResults) {
        noResults.remove();
    }
}

function showStaffForm(employee = null) {
    const form = document.getElementById('staff-form');
    const formTitle = document.querySelector('.form-title');
    const deleteBtn = document.querySelector('.delete-btn');
    const passwordField = document.getElementById('password');
    const cartBar = document.querySelector('.cart-bar');

    // Reset form and any previous error messages
    form.reset();
    clearFormErrors();

    if (employee) {
        formTitle.textContent = 'Edit Staff';
        document.getElementById('employee-id').value = employee.employee_id;
        document.getElementById('staff-name').value = employee.name;
        document.getElementById('role').value = employee.role;
        document.getElementById('store-name').value = employee.store_name;

        // Make password optional for editing
        passwordField.removeAttribute('required');
        passwordField.placeholder = 'Leave blank to keep current password';

        deleteBtn.style.display = 'block';
    } else {
        formTitle.textContent = 'Create New Staff';
        document.getElementById('employee-id').value = '';
        passwordField.setAttribute('required', 'required');
        passwordField.placeholder = 'Enter password';
        deleteBtn.style.display = 'none';
    }

    cartBar.classList.add('active');
}

function closeForm() {
    const cartBar = document.querySelector('.cart-bar');
    cartBar.classList.remove('active');
    clearFormErrors();
}

function clearFormErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(error => error.remove());
}

function showError(inputElement, message) {
    clearError(inputElement);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '12px';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    inputElement.parentNode.appendChild(errorDiv);
}

function clearError(inputElement) {
    const existingError = inputElement.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
}

function saveStaff(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const employeeId = document.getElementById('employee-id').value;
    const url = employeeId ? '../../php/api/update_staff.php' : '../../php/api/add_staff.php';

    // Clear any existing errors
    clearFormErrors();

    fetch(url, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                if (data.errors) {
                    // Show specific errors for each field
                    Object.keys(data.errors).forEach(field => {
                        const inputElement = document.getElementById(field);
                        if (inputElement) {
                            showError(inputElement, data.errors[field]);
                        }
                    });
                } else {
                    alert(data.error || 'Error saving staff member');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving staff member');
        });
}

function deleteStaff(employeeId) {
    if (confirm('Are you sure you want to delete this staff member? This action cannot be undone.')) {
        fetch('../../php/api/delete_staff.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ employee_id: employeeId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error deleting staff member');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting staff member');
            });
    }
} 