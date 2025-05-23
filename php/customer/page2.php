<?php
session_start();
include '../import/database.php';


// Only allow access to logged-in customers (not guest)
if (!isset($_SESSION['user_id']) || strpos($_SESSION['user_id'], 'C') !== 0) {
    header('Location: ../access_portal.php');
    exit;
}

// Example: Fetch orders for this customer
$orders = [];
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Replace with your actual orders table and fields
$customer_id = $conn->real_escape_string($_SESSION['user_id']);
$sql = "SELECT * FROM orders WHERE customer_id = '$customer_id' ORDER BY order_date DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$conn->close();
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
                    <div class="logout-tab tab" onclick="window.location.href='?logout=1';">
                        <div class="tab-icon"><i class="fa-solid fa-circle-user"></i></div>
                        <div class="tab-label">Logout</div>
                    </div>
                </div>
            </div>
            <div class="center-bar"><!--Main bar for for stuffs-->
                <div class="content-container active order-container"><!--My Orders content-->
                    <h2>My Orders</h2>
                    <?php if (empty($orders)): ?>
                        <p>No orders found.</p>
                    <?php else: ?>
                     <div class="order-box" id="{order_id}">
                        <div class="main-box">
                            <div class="order-item" product="product_id"><!--items will shows here base on the order_item table-->
                                <div class="order-image"></div>
                                <div class="contents">
                                    <div class="contents-label">{Product name}</div>
                                    <div class="contents-quantity">x{quantity}</div>
                                </div>
                                <div class="order-price">P{total_price}</div>
                            </div>
                        </div>
                        .footer
                     </div>
                    <?php endif; ?>
                </div>
            </div>
        </div> 
    </body>
</html>