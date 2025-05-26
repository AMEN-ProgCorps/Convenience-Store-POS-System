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
        <link rel="stylesheet" href="../../style/Estaf.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="../../script/lan.js" defer></script>
        <script src="../../script/Estaf.js" defer></script>
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
                <?php
                    $role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
                    if ($role !== 'Admin' && $role !== 'Manager') {
                        header('Location: ../access_portal.php');
                        exit;
                    }
                ?>
                <?php if ($role === 'Admin' || $role === 'Manager'): ?>
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
                    <?php if ($role === 'Admin'): ?>
                    <div class="items-tab tab" onclick="window.location.href='Eacc.php'">
                        <div class="tab-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="tab-label">Accounts</div>
                    </div>
                    <?php endif; ?>
                    <div class="items-tab tab active">
                        <div class="tab-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="tab-label">Staff</div>
                    </div>
                <?php endif; ?>
                    <div class="logout-tab tab" onclick="window.location.href='../access_portal.php?logout=1';">
                        <div class="tab-icon"><i class="fa-solid fa-circle-user"></i></div>
                        <div class="tab-label">Logout</div>
                    </div>
                </div>
            </div>
            <div class="center-bar"><!--Main bar for for stuffs-->
                <div class="staff-header">
                    <div class="search-container">
                        <input type="text" id="staff-search" placeholder="Search staff...">
                        <button id="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="filter-container">
                        <div class="filter-toggle">
                            <div class="filter-option active" data-role="all">All</div>
                            <div class="filter-option" data-role="Cashier">Cashier</div>
                            <div class="filter-option" data-role="Manager">Manager</div>
                        </div>
                        <button class="create-staff-btn">Create Staff Account</button>
                    </div>
                </div>
                
                <div class="staff-container">
                    <?php
                    // Fetch all employee accounts
                    $sql = "SELECT * FROM employee_accounts WHERE role != 'Admin' ORDER BY employee_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    
                    while ($employee = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="staff-box" data-role="' . htmlspecialchars($employee['role']) . '" onclick="showStaffForm(' . htmlspecialchars(json_encode($employee)) . ')">';
                        echo '<div class="staff-id">' . htmlspecialchars($employee['employee_id']) . '</div>';
                        echo '<div class="staff-name">' . htmlspecialchars($employee['name']) . '</div>';
                        echo '<div class="staff-role">' . htmlspecialchars($employee['role']) . '</div>';
                        echo '<div class="staff-store">' . htmlspecialchars($employee['store_name']) . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            <div class="cart-bar"><!--Side bar for Staff Details-->
                <div class="staff-form">
                    <div class="form-header">
                        <div class="form-title">Create New Staff</div>
                        <button onclick="closeForm()" class="close-btn">×</button>
                    </div>
                    
                    <form id="staff-form" onsubmit="saveStaff(event)">
                        <div class="form-group">
                            <label for="staff-name">Name</label>
                            <input type="text" id="staff-name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="Cashier">Cashier</option>
                                <?php if ($_SESSION['user_role'] === 'Admin'): ?>
                                <option value="Manager">Manager</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="store-name">Store Name</label>
                            <input type="text" id="store-name" name="store_name" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="save-btn">Save</button>
                            <button type="button" class="cancel-btn" onclick="closeForm()">Cancel</button>
                            <button type="button" class="delete-btn" onclick="deleteStaff(document.getElementById('employee-id').value)" style="display: none;">Delete</button>
                        </div>
                        
                        <input type="hidden" id="employee-id" name="id">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>