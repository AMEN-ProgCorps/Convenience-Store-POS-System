<?php
include '../import/database.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ToMart</title>
        <link rel="icon" href="../../style/ToMart_Logo.png" type="image/x-icon">
        <link rel="stylesheet" href="../../style/index.css">
        <link rel="stylesheet" href="../../style/eacc.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="../../script/lan.js" defer></script>
        <script src="../../script/eacc.js" defer></script>
    </head>
    <body>
        <div class="body_container">
            <div class="side-bar"><!--Side bar for tabs-->
                <div class="logo-container">
                    <div class="logo-icon"></div>
                    <div class="logo-label">ToMart</div> 
                </div>
                <div class="username-container">
                        Hello! <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Employee'; ?>
                        <?php if (isset($_SESSION['user_role'])): ?>
                            <span style="font-size:0.9em;color:#0A401E;">(<?php echo htmlspecialchars($_SESSION['user_role']); ?>)</span>
                        <?php endif; ?>
                </div>
                <div class="tab-container">
                <?php $role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : ''; ?>
                <?php if ($role === 'Admin'): ?>
                    <div class="items-tab tab" onclick="window.location.href='Einv.php'">
                        <div class="tab-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="tab-label">Inventory</div>
                    </div>
                    <div class="items-tab tab" onclick="window.location.href='Esear.php'">
                        <div class="tab-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="tab-label">Search Items</div>
                    </div>
                    <div class="order-tab tab" onclick="window.location.href='Ecash.php'">
                        <div class="tab-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div class="tab-label">Cashier</div>
                    </div>
                    <div class="items-tab tab active">
                        <div class="tab-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="tab-label">Accounts</div>
                    </div>
                    <div class="items-tab tab" onclick="window.location.href='Estaf.php'">
                        <div class="tab-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="tab-label">Staff</div>
                    </div>
                <?php else: ?>
                    <!-- Not admin: bounce to previous page or access_portal.php -->
                    <?php
                        header('Location: ../access_portal.php');
                        exit;
                    ?>
                <?php endif; ?>
                    <div class="logout-tab tab" onclick="window.location.href='../access_portal.php?logout=1';">
                        <div class="tab-icon"><i class="fa-solid fa-circle-user"></i></div>
                        <div class="tab-label">Logout</div>
                    </div>
                </div>
            </div>
            <div class="center-bar"><!--Main bar for for stuffs-->
                <div class="account-header">
                    <div class="search-container">
                        <input type="text" id="account-search" placeholder="Search accounts...">
                        <button id="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="filter-container">
                        <div class="filter-toggle">
                            <div class="filter-option active" data-type="all">All</div>
                            <div class="filter-option" data-type="customer">Customer</div>
                            <div class="filter-option" data-type="employee">Employee</div>
                        </div>
                        <button class="create-account-btn">Create Account</button>
                    </div>
                </div>
                
                <div class="account-container">
                    <?php
                    // Fetch all accounts using the view
                    $sql = "SELECT * FROM all_accounts ORDER BY id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    
                    while ($account = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $type = strpos($account['id'], 'C') === 0 ? 'customer' : 'employee';
                        echo '<div class="account-box" data-type="' . $type . '" onclick="showAccountForm(' . htmlspecialchars(json_encode($account)) . ')">';
                        echo '<div class="account-id">' . htmlspecialchars($account['id']) . '</div>';
                        echo '<div class="account-name">' . htmlspecialchars($account['name']) . '</div>';
                        if ($type === 'employee' && $account['role']) {
                            echo '<div class="account-role">' . htmlspecialchars($account['role']) . '</div>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            <div class="cart-bar"><!--Side bar for Account Details-->
                <div class="account-form">
                    <div class="form-header">
                        <div class="form-title">Create New Account</div>
                        <button onclick="closeForm()" class="close-btn">×</button>
                    </div>
                    
                    <form id="account-form" onsubmit="saveAccount(event)">
                        <div class="account-type-toggle">
                            <div class="type-option active" data-type="customer">Customer</div>
                            <div class="type-option" data-type="employee">Employee</div>
                        </div>

                        <div class="form-group">
                            <label for="account-name">Name</label>
                            <input type="text" id="account-name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <div id="customer-fields">
                            <div class="form-group">
                                <label for="phone-number">Phone Number</label>
                                <input type="tel" id="phone-number" name="phone_number">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email">
                            </div>
                        </div>

                        <div id="employee-fields" style="display: none;">
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select id="role" name="role">
                                    <option value="Cashier">Cashier</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="store-name">Store Name</label>
                                <input type="text" id="store-name" name="store_name">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="save-btn">Save</button>
                            <button type="button" class="cancel-btn" onclick="closeForm()">Cancel</button>
                            <button type="button" class="delete-btn" onclick="deleteAccount(document.getElementById('account-id').value, document.querySelector('.type-option.active').dataset.type)" style="display: none;">Delete</button>
                        </div>
                        
                        <input type="hidden" id="account-id" name="id">
                    </form>
                </div>
            </div> 
        </div>
    </body>
</html>