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
}

function showStaffForm(employee = null) {
    const form = document.getElementById('staff-form');
    const formTitle = document.querySelector('.form-title');
    const deleteBtn = document.querySelector('.delete-btn');
    const passwordField = document.getElementById('password');
    const cartBar = document.querySelector('.cart-bar');

    // Reset form
    form.reset();

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
        passwordField.placeholder = '';
        deleteBtn.style.display = 'none';
    }

    cartBar.classList.add('active');
}

function closeForm() {
    document.querySelector('.cart-bar').classList.remove('active');
}

function saveStaff(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const employeeId = document.getElementById('employee-id').value;
    const url = employeeId ? '../../php/api/update_staff.php' : '../../php/api/add_staff.php';

    fetch(url, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the page to show updated data
                location.reload();
            } else {
                alert(data.error || 'Error saving staff member');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving staff member');
        });
}

function deleteStaff(employeeId) {
    if (confirm('Are you sure you want to delete this staff member?')) {
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