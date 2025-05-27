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
        <link rel="stylesheet" href="../../style/search.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="../../script/lan.js" defer></script>
        <script src="../../script/Esearch.js" defer></script>
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
                    if ($role !== 'Admin' && $role !== 'Manager' && $role !== 'Cashier') {
                        header('Location: ../access_portal.php');
                        exit;
                    }
                ?>
                <?php if ($role === 'Cashier'): ?>
                    <div class="order-tab tab" onclick="window.location.href='Ecash.php'">
                        <div class="tab-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div class="tab-label">Cashier</div>
                    </div>
                    <div class="items-tab tab active">
                        <div class="tab-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="tab-label">Search Items</div>
                    </div>
                <?php elseif ($role === 'Manager'): ?>
                    <div class="items-tab tab" onclick="window.location.href='Einv.php'">
                        <div class="tab-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="tab-label">Inventory</div>
                    </div>
                    <div class="items-tab tab active">
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
                    <div class="items-tab tab" onclick="window.location.href='Estaf.php'">
                        <div class="tab-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="tab-label">Staff</div>
                    </div>
                <?php else: ?>
                    <!-- Admin or unknown role: show all tabs -->
                    <div class="items-tab tab" onclick="window.location.href='Einv.php'">
                        <div class="tab-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="tab-label">Inventory</div>
                    </div>
                    <div class="items-tab tab active">
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
                    <?php if ($role === 'Manager'): ?>
                        <div class="items-tab tab" onclick="window.location.href='Estaf.php'">
                            <div class="tab-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="tab-label">Staff</div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                    <div class="logout-tab tab" onclick="window.location.href='../access_portal.php?logout=1';">
                        <div class="tab-icon"><i class="fa-solid fa-circle-user"></i></div>
                        <div class="tab-label">Logout</div>
                    </div>
                </div>
            </div>
            <div class="center-bar"><!--Main bar for for stuffs-->
                <div class="search-header">
                    <div class="search-container">
                        <input type="text" id="searchInput" placeholder="Search items...">
                        <button type="button" id="searchButton">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="items-box-container">
                    <!-- Items will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </body>
</html>