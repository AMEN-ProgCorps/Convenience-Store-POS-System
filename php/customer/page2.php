<?php
session_start();
include '../import/database.php';


// Only allow access to logged-in customers (not guest)
if (!isset($_SESSION['user_id']) || strpos($_SESSION['user_id'], 'C') !== 0) {
    header('Location: ../access_portal.php');
    exit;
}

try {
    // Example: Fetch orders for this customer
    $orders = [];
    $customer_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
    $stmt->execute([$customer_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ToMart</title>
        <link rel="icon" href="../../style/ToMart_Logo.png" type="image/x-icon">
        <link rel="stylesheet" href="../../style/index.css">
        <link rel="stylesheet" href="../../style/order.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="../../script/ver2.js" defer></script>
        <script src="../../script/order.js" defer></script>
    </head>
    <body>
        <div class="body_container">
            <div class="side-bar"><!--Side bar for tabs-->
                <div class="logo-container">
                    <div class="logo-icon"></div>
                    <div class="logo-label">ToMart</div> 
                </div>
                <div class="username-container">
                        Hello! <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Customer'; ?>
                </div>
                <div class="tab-container">
                    <div class="items-tab tab" onclick="toggleMenu('items')">
                        <div class="tab-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"><path fill="currentColor" d="M160 448a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zM160 896a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32z"/></svg>
                        </div>
                        <div class="tab-label">Items</div>
                    </div>
                    <div class="order-tab tab active">
                        <div class="tab-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="tab-label">My Orders</div>
                    </div>
                    <div class="logout-tab tab" onclick="window.location.href='../access_portal.php?logout=1';">
                        <div class="tab-icon"><i class="fa-solid fa-circle-user"></i></div>
                        <div class="tab-label">Logout</div>
                    </div>
                </div>
            </div>
            <div class="center-bar"><!--Main bar for for stuffs-->
                <div class="content-container active order-container"><!--My Orders content-->
                    <div class="order-container">
                        <div class="order-header">
                            <div class="order-header-label">My Orders <i class="fa-solid fa-circle-check"></i></div>
                            <div class="order-header-filter">
                                <p>Order by :</p> 
                                <div class="type active" onclick="orderArrangement('Highest')">▲</div>
                                <div class="type" onclick="orderArrangement('Lowest')">▼</div>
                                <p> Type : </p>
                                <div class="options active" onclick="orderFilter('Date')">Date</div>
                                <div class="options" onclick="orderFilter('Quantity')">Quantity</div>
                                <div class="options" onclick="orderFilter('Amount')">Amount</div>
                                <div class="options" onclick="orderFilter('Status')">Status</div>
                            </div>
                        </div>
                        <?php if (empty($orders)): ?>
                            <p>No orders found.</p>
                        <?php else: ?>
                            <?php
                            try {
                                foreach ($orders as $order):
                                    $order_id = $order['order_id'];
                                    $items = [];
                                    
                                    // Get order items
                                    $stmt = $conn->prepare("SELECT oi.*, p.name as product_name 
                                                          FROM order_item oi 
                                                          JOIN products p ON oi.product_id = p.product_id 
                                                          WHERE oi.order_id = ?");
                                    $stmt->execute([$order_id]);
                                    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    $total_amount = 0;
                                    $total_items = 0;
                                    foreach ($items as $item) {
                                        $total_amount += $item['total_ammount'];
                                        $total_items += $item['quantity'];
                                    }
                            ?>
                            <div class="order-box" id="order_<?php echo $order_id; ?>">
                                <div class="main-box">
                                    <div class="date-purchased">Date Purchased: <?php echo htmlspecialchars($order['order_date']); ?></div>
                                    <div class="discount-used">
                                        Discount Applied: 
                                        <?php
                                            // Calculate discount if available
                                            $discount_applied = 0;
                                            if (!empty($order['discount_id'])) {
                                                // Get discount percentage from DB
                                                $stmt = $conn->prepare("SELECT discount_percentage FROM discounts WHERE discount_id = ?");
                                                $stmt->execute([$order['discount_id']]);
                                                if ($discount_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $discount_applied = $order['total_amount'] * ($discount_row['discount_percentage'] / 100);
                                                }
                                            }
                                            echo '-P ' . number_format($discount_applied, 2);
                                        ?>
                                    </div>
                                    <div class="order-status">Status: <?php echo htmlspecialchars($order['order_status']); ?></div>
                                    <?php foreach ($items as $item): ?>
                                    <div class="order-item" product="<?php echo htmlspecialchars($item['product_id']); ?>">
                                        <div class="order-image"></div>
                                        <div class="contents">
                                            <div class="contents-label"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                            <div class="contents-quantity">x<?php echo (int)$item['quantity']; ?></div>
                                        </div>
                                        <div class="order-price">P<?php echo number_format($item['total_ammount'], 2); ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="order-footer">
                                    <?php if (count($items) > 1): ?>
                                        <div class="ampler" onclick='toggleOrderItem()'>View Item/s ▼</div>
                                    <?php else: ?>
                                        <div class="placeholder">.</div>
                                    <?php endif; ?>
                                    <div class="order_totalis">Total <?php echo $total_items; ?> item/s: P<?php echo number_format($total_amount, 2); ?></div>
                                </div>
                            </div>
                            <?php 
                                endforeach;
                            } catch (PDOException $e) {
                                error_log("Database Error: " . $e->getMessage());
                                echo "<p>Error loading order details.</p>";
                            }
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div> 
    </body>
</html>