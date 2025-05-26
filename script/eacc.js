document.addEventListener('DOMContentLoaded', function () {
    // Filter functionality
    const filterOptions = document.querySelectorAll('.filter-option');
    filterOptions.forEach(option => {
        option.addEventListener('click', function () {
            filterOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            filterAccounts(this.dataset.type);
        });
    });

    // Search functionality
    const searchInput = document.getElementById('account-search');
    const searchButton = document.getElementById('search-button');

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        const accountBoxes = document.querySelectorAll('.account-box');
        const currentFilter = document.querySelector('.filter-option.active').dataset.type;

        accountBoxes.forEach(box => {
            const id = box.querySelector('.account-id').textContent.toLowerCase();
            const name = box.querySelector('.account-name').textContent.toLowerCase();
            const type = box.dataset.type;
            const matches = (id.includes(searchTerm) || name.includes(searchTerm));
            const filterMatches = (currentFilter === 'all' || currentFilter === type);

            box.style.display = (matches && filterMatches) ? 'block' : 'none';
        });
    }

    searchInput.addEventListener('input', performSearch);
    searchButton.addEventListener('click', performSearch);

    // Create Account button
    const createAccountBtn = document.querySelector('.create-account-btn');
    createAccountBtn.addEventListener('click', function () {
        showAccountForm();
    });

    // Account type toggle in form
    const typeOptions = document.querySelectorAll('.type-option');
    typeOptions.forEach(option => {
        option.addEventListener('click', function () {
            typeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            toggleFormFields(this.dataset.type);
        });
    });
});

function filterAccounts(type) {
    const accountBoxes = document.querySelectorAll('.account-box');
    accountBoxes.forEach(box => {
        if (type === 'all' || box.dataset.type === type) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    });
}

function showAccountForm(accountData = null) {
    const cartBar = document.querySelector('.cart-bar');
    cartBar.classList.add('active');

    // If accountData is provided, we're editing an existing account
    const isEditing = !!accountData;
    const formTitle = document.querySelector('.form-title');
    formTitle.textContent = isEditing ? 'Edit Account' : 'Create New Account';

    // Show/hide delete button
    const deleteBtn = document.querySelector('.delete-btn');
    deleteBtn.style.display = isEditing ? 'block' : 'none';

    // Reset form
    document.getElementById('account-form').reset();
    document.getElementById('account-id').value = '';

    // Get type options container and options
    const typeToggle = document.querySelector('.account-type-toggle');
    const customerOption = document.querySelector('.type-option[data-type="customer"]');
    const employeeOption = document.querySelector('.type-option[data-type="employee"]');

    if (isEditing) {
        // Hide type toggle when editing
        typeToggle.classList.add('editing');

        // Set account type based on ID prefix (C for customer, E for employee)
        const type = accountData.id.startsWith('C') ? 'customer' : 'employee';

        // Show appropriate fields
        toggleFormFields(type);

        // Fill form with account data
        document.getElementById('account-id').value = accountData.id;
        document.getElementById('account-name').value = accountData.name || '';
        document.getElementById('password').required = false;
        document.getElementById('password').placeholder = 'Leave blank to keep current password';

        if (type === 'customer') {
            document.getElementById('phone-number').value = accountData.phone_number || '';
            document.getElementById('email').value = accountData.email || '';
        } else {
            document.getElementById('role').value = accountData.role || 'Cashier';
            document.getElementById('store-name').value = accountData.store_name || '';
        }
    } else {
        // Show type toggle for new accounts
        typeToggle.classList.remove('editing');

        // Default to customer type for new accounts
        customerOption.classList.add('active');
        employeeOption.classList.remove('active');
        toggleFormFields('customer');
        document.getElementById('password').required = true;
        document.getElementById('password').placeholder = 'Enter password';
    }
}

function toggleFormFields(type) {
    const customerFields = document.getElementById('customer-fields');
    const employeeFields = document.getElementById('employee-fields');

    if (type === 'customer') {
        customerFields.style.display = 'block';
        employeeFields.style.display = 'none';
    } else {
        customerFields.style.display = 'none';
        employeeFields.style.display = 'block';
    }
}

function saveAccount(event) {
    event.preventDefault();
    const form = document.getElementById('account-form');
    const formData = new FormData(form);
    const accountType = document.querySelector('.type-option.active').dataset.type;

    // Add account type to form data
    formData.append('type', accountType);

    // Send to backend
    fetch('../../php/api/manage_account.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Account saved successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving account');
        });
}

function deleteAccount(accountId, accountType) {
    if (confirm('Are you sure you want to delete this account?')) {
        fetch('../../php/api/delete_account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: accountId,
                type: accountType
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Account deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting account');
            });
    }
}

function closeForm() {
    document.querySelector('.cart-bar').classList.remove('active');
}
