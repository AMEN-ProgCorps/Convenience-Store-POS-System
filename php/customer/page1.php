<?php
session_start();
include '../import/database.php';

$categoriesWithCounts = [];

try {
    // Fetch categories with item counts
    $sql = "SELECT c.name AS category_name, COUNT(p.product_id) AS item_count 
            FROM categories c 
            LEFT JOIN products p ON c.category_id = p.category_id 
            GROUP BY c.category_id";
    $stmt = $conn->query($sql);
    $categoriesWithCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch products
    $products = [];
    // Fetch products with stock_level
    $sql = "SELECT p.product_id, p.name AS product_name, p.price, p.stock_level, c.name AS category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id";
    $stmt = $conn->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $categoriesWithCounts = [];
    $products = [];
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
        <link rel="stylesheet" href="../../style/itemcategory.css">
        <link rel="stylesheet" href="../../style/discount.css">
        <link rel="stylesheet" href="../../style/receipt.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="../../script/ver2.js" defer></script>
        <script src="../../script/sortingCategory.js"></script>
        <script src="../../script/discount.js"></script>
        <script src="../../script/search.js"></script>
        <script src="../../script/receipt.js"></script>
    </head>
    <body>
        <div class="body_container">
            <div class="side-bar"><!--Side bar for tabs-->
                <div class="logo-container">
                    <div class="logo-icon"></div>
                    <div class="logo-label">ToMart</div> 
                </div>
                <div class="username-container">
                        Hello! <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?>
                </div>
                <div class="tab-container">
                    <div class="items-tab tab active">
                        <div class="tab-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"><path fill="currentColor" d="M160 448a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V160.064a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32V416a32 32 0 0 1-32 32zM160 896a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32zm448 0a32 32 0 0 1-32-32V608a32 32 0 0 1 32-32h255.936a32 32 0 0 1 32 32v256a32 32 0 0 1-32 32z"/></svg>
                        </div>
                        <div class="tab-label">Items</div>
                    </div>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === 'C101'): ?>
                        <!-- Guest: show only items-tab and login-tab -->
                        <div class="login-tab tab" onclick="window.location.href='../access_portal.php';">
                            <div class="tab-icon"><i class="fa-solid fa-circle-user"></i></div>
                            <div class="tab-label">Login</div>
                        </div>
                    <?php else: ?>
                        <!-- Logged in: show items-tab, order-tab, and logout-tab -->
                        <div class="order-tab tab" onclick="toggleMenu('order')">
                            <div class="tab-icon"><i class="fas fa-shopping-cart"></i></div>
                            <div class="tab-label">My Orders</div>
                        </div>
                        <div class="logout-tab tab" onclick="window.location.href='?logout=1';">
                            <div class="tab-icon"><i class="fa-solid fa-circle-user"></i></div>
                            <div class="tab-label">Logout</div>
                        </div>
                    <?php endif; ?>
                </div>
<?php
// Handle logout action
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    header('Location: ../access_portal.php');
    exit;
}
?>
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
                            <div class="item-card" category="<?php echo htmlspecialchars($product['category_name']); ?>" id="<?php echo $product['product_id']; ?>" data-stock="<?php echo (int)$product['stock_level']; ?>">
                                <div class="item-image"></div>
                                <div class="item-details">
                                    <div class="item-onstocks">
                                        <div class="item-onstocks-label">Stock: </div>
                                        <div class="item-onstocks-quantity"><?php echo (int)$product['stock_level']; ?></div>
                                    </div>
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
                                <div class="cart-top-header-acccount"> <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?> </div>
                            </div>
                        </div>
                        <div class="dashed-line"></div>
                        <div class="cart-top-body">
                            <?php if (empty($products)): ?>
                                <p>No item present</p>
                            <?php endif; ?>
                            <!-- Cart product template for JS cloning (hidden) -->
                            <div id="cart-product-template" style="display:none">
                                <div class="cart-top-body-product">
                                    <div class="cart-top-body-product-image"></div>
                                    <div class="cart-top-body-product-label">
                                        <div class="cart-top-body-product-label-name">Item Name</div>
                                        <div class="cart-top-body-product-label-details">
                                            <div class="cart-top-body-product-label-details-price">P99.99</div>
                                            <div class="cart-top-body-product-label-details-quantity">{Quantity}</div>
                                        </div>
                                    </div>
                                    <div class="cart-top-body-product-total_price">₱99.99</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cart-down">
                        <div class="total_container">
                            <?php if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'C101'): ?>
                            <div class="discount-container">
                                <div class="total-box total-box-discount">
                                    <div class="discount-layer" onclick="toggleDiscount()">
                                        <div class="discount-label">ToMart Discount</div>
                                        <div class="discount-active">Select Voucher</div><!--if discount active Select Voucher will be change to the VoucherName-->
                                        <!--if discount is chosen select Voucher will be change to tha t discount-->
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="pricing-container">
                                <div class="total-box total-box-real">
                                    <div class="t1 total-box-label">Sub Total</div><!--Stage one -->
                                    <div class="t2 total-box-label-price">0</div><!--Sub Total of product-->
                                    <div class="t3 total-box-label">Discount</div><!--Stage two-->
                                    <div class="t4 total-box-label-price">0</div><!--Discount that is active-->
                                    <div class="t5 total-box-label">Tax 12%</div>
                                    <div class="t6 total-box-label-price">0</div><!--Tax of overall total product-->
                                    <div class="out7 total-box-label-output">
                                        <div class="total-box-label">Total</div><!--Stage three-->
                                        <div class="total-box-label-price">0</div><!--Total of product-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'C101'): ?>
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
                        <?php endif; ?>
                        <div class="order_container">
                            <button type="submit" class="order-button">Place Order</button>
                        </div>
                    </div>
                </div>
            </div> 
        </div> 
        <div class="discount_body" id="discount-body">
            <div class="discount_container">
                <div class="discount_sidebar">
                    <div class="discount_header">
                            <div class="cart-top-header-label">Select Discount Vouchers</div>
                    </div>
                    <div class="d_line"></div>  
                    <div class="discount_box-container">
                        <?php
                        // Fetch all discounts (valid and expired)
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        $today = date('Y-m-d');
                        $discounts = [];
                        $sql = "SELECT * FROM discounts";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $isExpired = ($today > $row['valid_till']);
                                $notYetValid = ($today < $row['valid_from']);
                                $warning = '';
                                if ($isExpired) $warning = 'Discount Expired';
                                elseif ($notYetValid) $warning = 'Discount Not Yet Valid';
                                $discounts[] = [
                                    'id' => $row['discount_id'],
                                    'desc' => $row['description'],
                                    'percent' => $row['discount_percentage'],
                                    'warning' => $warning
                                ];
                            }
                        }
                        $conn->close();
                        foreach ($discounts as $discount): ?>
                        <div class="dbox" data-id="<?php echo $discount['id']; ?>" data-percent="<?php echo $discount['percent']; ?>">
                            <div class="dbox-container">
                                <div class="dbox-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1"><path d="M10.51 3.665a2 2 0 0 1 2.98 0l.7.782a2 2 0 0 0 1.601.663l1.05-.058a2 2 0 0 1 2.107 2.108l-.058 1.049a2 2 0 0 0 .663 1.6l.782.7a2 2 0 0 1 0 2.981l-.782.7a2 2 0 0 0-.663 1.601l.058 1.05a2 2 0 0 1-2.108 2.107l-1.049-.058a2 2 0 0 0-1.6.663l-.7.782a2 2 0 0 1-2.981 0l-.7-.782a2 2 0 0 0-1.601-.663l-1.05.058a2 2 0 0 1-2.107-2.108l.058-1.049a2 2 0 0 0-.663-1.6l-.782-.7a2 2 0 0 1 0-2.981l.782-.7a2 2 0 0 0 .663-1.601l-.058-1.05A2 2 0 0 1 7.16 5.053l1.049.058a2 2 0 0 0 1.6-.663z"/><path stroke-linejoin="round" stroke-width="1.5" d="M9.5 9.5h.01v.01H9.5zm5 5h.01v.01h-.01z"/><path stroke-linecap="round" stroke-linejoin="round" d="m15 9l-6 6"/></g></svg>
                                </div>
                                <div class="dbox-label">
                                    <?php echo htmlspecialchars($discount['desc']); ?> (<?php echo $discount['percent']; ?>%)
                                </div>
                            </div>
                            <div class="dbox-warning"<?php if ($discount['warning']) echo ' style="display:flex"'; ?>>
                                <div class="dbox-warning-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2m0 2a8 8 0 1 0 0 16a8 8 0 0 0 0-16m0 11a1 1 0 1 1 0 2a1 1 0 0 1 0-2m0-9a1 1 0 0 1 1 1v6a1 1 0 1 1-2 0V7a1 1 0 0 1 1-1"/></g></svg>
                                </div>
                                <div class="dbox-warning-details">
                                    <?php echo htmlspecialchars($discount['warning']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="discount_footer">
                        <div class="dfinisher">
                            <button type="button" class="cancel" onclick="toggleDiscount()">Cancel</button>
                            <button type="button" class="accept">Ok</button><!--accept will be the one to set the discount to the cart-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="discount_body" id="receipt" style="display:none;">
            <div class="discount_container">
                <div class="discount_sidebar">
                    <div class="discount_header">
                            <div class="cart-top-header-label">Online Receipt</div>
                    </div>
                    <div class="d_line"></div>  
                    <div class="receipt-contents">
                        <p>
                            
                        </p>
                        <button type="button" class="cancel" onclick="closeReceipt()">Close Receipt</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>