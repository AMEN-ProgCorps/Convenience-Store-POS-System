<?php
session_start();
include '../import/database.php';

$categoriesWithCounts = [];
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories with item counts
$sql = "SELECT c.name AS category_name, COUNT(p.product_id) AS item_count 
        FROM categories c 
        LEFT JOIN products p ON c.category_id = p.category_id 
        GROUP BY c.category_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categoriesWithCounts[] = $row;
    }
}

// Fetch products
$products = [];
$sql = "SELECT p.product_id, p.name AS product_name, p.price, c.name AS category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
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
        <link rel="stylesheet" href="../../style/itemcategory.css">
        <link rel="stylesheet" href="../../style/discount.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="../../script/ver2.js" defer></script>
        <script src="../../script/sortingCategory.js"></script>
        <script src="../../script/cart.js"></script>
    </head>
    <body>
        <div class="body_container">
            <div class="side-bar"><!--Side bar for tabs-->
                <div class="logo-container">
                    <div class="logo-icon"></div>
                    <div class="logo-label">ToMart</div> 
                </div>
                <div class="username-container">
                        Hello! {Username}
                </div>
                <div class="tab-container">
                    <div class="items-tab tab active">
                        <div class="tab-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"><path fill="currentColor" d="M160 448a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zM160 896a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32z"/></svg>
                        </div>
                        <div class="tab-label">Items</div>
                    </div>
                    <div class="order-tab tab" onclick="toggleMenu('order')">
                        <div class="tab-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="tab-label">My Orders</div>
                    </div>
                </div>
            </div>
            <div class="center-bar"><!--Main bar for for stuffs-->
                <div class="content-container items-container active"><!--Items content-->
                    <div class="center-bar-header">
                        <div class="category-toggle active" onclick="toggleCategory()">
                            <i class="fas fa-bars"></i>
                        </div>
                        <div class="search-bar-container">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search Product here...">
                        </div>
                        <div class="cart_toggle" onclick="toggleCart()">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                    <div class="center-bar-category-container active">
                        <div class="category-items-labels">
                            <!--Filter section-->
                            <div class="category active" onclick="sortCategory('All')">
                                <div class="category-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"><path fill="currentColor" d="M160 448a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zM160 896a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32z"/></svg>
                                </div>
                                <div class="category-label">
                                    <div class="category-label-name">ALL</div>
                                    <div class="category-label-total-outcomes"><?php echo count($products); ?> items</div>
                                </div>
                            </div>
                            <?php foreach ($categoriesWithCounts as $category): ?>
                            <div class="category" onclick="sortCategory('<?php echo $category['category_name']; ?>')">
                                <div class="category-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"><path fill="currentColor" d="M160 448a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zM160 896a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32z"/></svg>
                                </div>
                                <div class="category-label">
                                    <div class="category-label-name"><?php echo htmlspecialchars($category['category_name']); ?></div>
                                    <div class="category-label-total-outcomes"><?php echo $category['item_count']; ?> items</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="center-bar-body-main-container">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                            <div class="item-card" category="<?php echo htmlspecialchars($product['category_name']); ?>" id="<?php echo $product['product_id']; ?>">
                                <div class="item-image"></div>
                                <div class="item-details">
                                    <div class="item-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                    <div class="item-price">₱<?php echo number_format($product['price'], 2); ?></div>
                                    <div class="div_button-container">
                                        <div class="add-to-order active" onclick="addCartToggle(this.closest('.item-card'))">Add&nbsp;<p>to Order</p></div>
                                        <div class="total-input-container">
                                            <button class="add_cart" onclick="removeCartToggle()"><i class="fa-solid fa-cart-shopping"></i>Add</button>
                                            <div class="total_input">
                                                <button class="total_input-subtract"><i class="fa-solid fa-minus"></i></button>
                                                <div class="shownumber">0</div>
                                                <button class="total_input-add"><i class="fa-solid fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No items available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="cart-bar active"><!--Side bar for Cart-->
                <div class="add-cart-container">
                    <div class="cart-top">
                        <div class="cart-top-header">
                            <div class="cart-top-header-content">
                                <div class="cart-top-header-label">My Cart<i class="fas fa-shopping-cart"></i> </div>
                                <div class="cart-top-header-acccount"> {Account_Name} </div>
                            </div>
                        </div>
                        <div class="dashed-line"></div>
                        <div class="cart-top-body">
                            <div class="cart-top-body-product"><!--This is the product layout in cart use this for cloning-->
                                <div class="cart-top-body-product-image"></div>
                                <div class="cart-top-body-product-label">
                                    <div class="cart-top-body-product-label-name">
                                        Item Name
                                    </div>
                                    <div class="cart-top-body-product-label-details">
                                        <div class="cart-top-body-product-label-details-price">
                                            P99.99
                                        </div>
                                        <div class="cart-top-body-product-label-details-quantity">
                                            {Quantity}
                                        </div>
                                    </div>
                                </div>
                                <div class="cart-top-body-product-total_price">₱99.99</div>
                            </div>
                        </div>
                    </div>
                    <div class="cart-down">
                        <div class="total_container">
                            <div class="discount-container">
                                <div class="total-box">
                                    <div class="discount-layer" onclick="toggleDiscount()">
                                        <div class="discount-label">ToMart Discount</div>
                                        <div class="discount-active">Select Voucher</div>
                                        <!--if discount is chosen select Voucher will be change to tha t discount-->
                                    </div>
                                </div>
                            </div>
                            <div class="pricing-container">
                                <div class="total-box">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="payment_container">
                            <div class="box-toggle" onclick="togglePayment()">
                                <div class="cash-box active">
                                    Cash
                                </div>
                                <div class="ewallet-box">
                                    e-Wallet
                                </div>
                            </div>
                        </div>
                        <div class="order_container">
                            <button type="submit" class="order-button">Place Order</button>
                        </div>
                    </div>
                </div>
            </div> 
        </div> 
        <div class="discount_body">
            <div class="discount_sidebar">
                disocunt
            </div>
        </div>
    </body>
</html>