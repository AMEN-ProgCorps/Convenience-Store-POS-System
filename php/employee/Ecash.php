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
        <link rel="stylesheet" href="../../style/ecash.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="../../script/lan.js" defer></script>
        <script src="../../script/cashier.js"defer></script>
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
                <?php if ($role === 'Cashier'): ?>
                    <div class="order-tab tab active">
                        <div class="tab-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="tab-label">Cashier</div>
                    </div>
                    <div class="items-tab tab" onclick="window.location.href='Esear.php'">
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
                    <div class="items-tab tab" onclick="window.location.href='Esear.php'">
                        <div class="tab-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="tab-label">Search Items</div>
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
                    <div class="items-tab tab" onclick="window.location.href='Esear.php'">
                        <div class="tab-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="tab-label">Search Items</div>
                    </div>
                    <div class="order-tab tab active">
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
                <div class="cashier-container">
                    <div class="cashier-header">
                        <div class="ctitle">
                            Cashier
                        </div>
                    </div>
                    <div class="cashier-body">
                        <?php
                        // Fetch orders for display
                        $pending_orders = [];
                        $completed_orders = [];
                        $cancelled_orders = [];
                        $sql = "SELECT * FROM orders ORDER BY order_date DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            switch ($row['order_status']) {
                                case 'pending':
                                    $pending_orders[] = $row;
                                    break;
                                case 'completed':
                                    $completed_orders[] = $row;
                                    break;
                                case 'cancelled':
                                    $cancelled_orders[] = $row;
                                    break;
                            }
                        }
                        ?>
                        <div class="order-label active" onclick="showOrders('pending',this)">Pending</div>
                        <div class="order-list show" id="pending_orders">
                        <?php foreach ($pending_orders as $order): ?>
                            <div class="order-item" order="<?= htmlspecialchars($order['order_id']) ?>">
                                <div class="order-id-box"><?= htmlspecialchars($order['order_id']) ?></div>
                                <div class="order-item-details">
                                    <div class="oid">Customer: <span><?= htmlspecialchars($order['customer_id']) ?></span></div>
                                    <div class="oid">Total Amount: <span><?= htmlspecialchars($order['total_amount']) ?></span></div>
                                    <div class="oid">Payment: <span><?= htmlspecialchars($order['payment_type']) ?></span></div>
                                </div>
                                <div class="view_items" onclick="itemGoes('<?= htmlspecialchars($order['order_id']) ?>')">VIEW DETAILS →</div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                        <div class="order-label" onclick="showOrders('complete',this)">Completed</div>
                        <div class="order-list" id="complete_orders">
                        <?php foreach ($completed_orders as $order): ?>
                            <div class="order-item" order="<?= htmlspecialchars($order['order_id']) ?>">
                                <div class="order-id-box"><?= htmlspecialchars($order['order_id']) ?></div>
                                <div class="order-item-details">
                                    <div class="oid">Customer: <span><?= htmlspecialchars($order['customer_id']) ?></span></div>
                                    <div class="oid">Total Amount: <span><?= htmlspecialchars($order['total_amount']) ?></span></div>
                                    <div class="oid">Payment: <span><?= htmlspecialchars($order['payment_type']) ?></span></div>
                                </div>
                                <div class="view_items" onclick="checkItem('<?= htmlspecialchars($order['order_id']) ?>')">VIEW DETAILS →</div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                        <div class="order-label" onclick="showOrders('cancelled',this)">Cancelled</div>
                        <div class="order-list" id="cancelled_orders">
                        <?php foreach ($cancelled_orders as $order): ?>
                            <div class="order-item" order="<?= htmlspecialchars($order['order_id']) ?>">
                                <div class="order-id-box"><?= htmlspecialchars($order['order_id']) ?></div>
                                <div class="order-item-details">
                                    <div class="oid">Customer: <span><?= htmlspecialchars($order['customer_id']) ?></span></div>
                                    <div class="oid">Total Amount: <span><?= htmlspecialchars($order['total_amount']) ?></span></div>
                                    <div class="oid">Payment: <span><?= htmlspecialchars($order['payment_type']) ?></span></div>
                                </div>
                                <div class="view_items" onclick="checkItem('<?= htmlspecialchars($order['order_id']) ?>')">VIEW DETAILS →</div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cart-bar"><!--Side bar for Cart-->
                <div class="order-details-container">
                    <div class="cashier-header">
                        <div class="ctitle">
                            Order Details
                        </div>
                    </div>
                    <div class="d_line"></div>  
                    <div class="details-body">
                        <div class="oi-body">
                            <!-- Order items will be dynamically inserted here -->
                        </div>
                        <div class="oi-id">
                            <div class="oi-id-label"></div>
                        </div>
                        <div class="oi-total">
                            <div class="oi-total-label">Total Amount: <span>0.00</span></div>
                        </div>
                        <div class="oi-payment">
                            <!-- Payment details will be dynamically inserted here -->
                        </div>
                        <div class="oi-button">
                            <!-- Buttons will be dynamically inserted here -->
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </body>
</html>