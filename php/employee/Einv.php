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
        <link rel="stylesheet" href="../../style/einv.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../../script/lan.js" defer></script>
        <script src="../../script/einv.js" defer></script>
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
                    <div class="items-tab tab active">
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
                <div class="inventory-header">
                    <div class="stat-box">
                        <div class="stat-title">Total Items in Stock</div>
                        <div class="stat-value" id="total-items">0</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-title">Total Inventory Value</div>
                        <div class="stat-value" id="total-value">₱0.00</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-title">Items Below Stock</div>
                        <div class="stat-value" id="low-stock">0</div>
                    </div>
                </div>

                <div class="charts-container">
                    <div class="chart-box">
                        <div class="chart-title">Most Popular Items Today</div>
                        <canvas id="popular-items-chart"></canvas>
                    </div>
                    <div class="chart-box">
                        <div class="chart-title">Category Sales This Week</div>
                        <canvas id="category-sales-chart"></canvas>
                    </div>
                    <div class="chart-box">
                        <div class="chart-title">Inventory Reduction Rate</div>
                        <canvas id="reduction-rate-chart"></canvas>
                    </div>
                </div>

                <div class="inventory-body">
                    <div class="records-section">
                        <div class="records-header">
                            <i class="fas fa-arrow-down"></i> Stock Reductions
                        </div>
                        <div id="reductions-container">
                            <!-- Reduction records will be loaded here -->
                            <div class="loading">Loading records...</div>
                        </div>
                    </div>
                    <div class="records-section">
                        <div class="records-header">
                            <i class="fas fa-arrow-up"></i> Stock Additions
                        </div>
                        <div id="additions-container">
                            <!-- Addition records will be loaded here -->
                            <div class="loading">Loading records...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cart-bar active"><!--Side bar for Cart-->
                <div class="management-container">
                    <div class="management-header">
                        <div class="management-title">Inventory Management</div>
                    </div>
                    
                    <div class="management-tabs">
                        <div class="management-tab active" onclick="showForm('product')">Add Product</div>
                        <div class="management-tab" onclick="showForm('discount')">Add Discount</div>
                        <div class="management-tab" onclick="showForm('category')">Manage Categories</div>
                    </div>

                    <div id="product-form" class="form-section active">
                        <div class="product-type-selector">
                            <button type="button" class="type-btn active" onclick="switchProductForm('new')">Add New Product</button>
                            <button type="button" class="type-btn" onclick="switchProductForm('existing')">Update Existing Product</button>
                        </div>

                        <!-- New Product Form -->
                        <form id="add-product-form" class="product-form active" onsubmit="handleProductSubmit(event, 'new')">
                            <div class="form-group">
                                <label for="product-name">Product Name</label>
                                <input type="text" id="product-name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    $sql = "SELECT * FROM categories ORDER BY name";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='" . htmlspecialchars($row['category_id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="stock-level">Initial Stock Level</label>
                                <input type="number" id="stock-level" name="stock_level" min="0" required>
                            </div>

                            <div class="form-group">
                                <label for="price">Price (₱)</label>
                                <input type="number" id="price" name="price" min="0" step="0.01" required>
                            </div>

                            <button type="submit" class="submit-btn">Add New Product</button>
                            <div class="error-message" id="product-error"></div>
                            <div class="success-message" id="product-success"></div>
                        </form>

                        <!-- Existing Product Form -->
                        <form id="update-product-form" class="product-form" onsubmit="handleProductSubmit(event, 'existing')">
                            <div class="form-group">
                                <label for="existing-product">Select Product</label>
                                <select id="existing-product" name="product_id" required onchange="loadProductDetails(this.value)">
                                    <option value="">Select Product</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="current-stock">Current Stock Level</label>
                                <input type="text" id="current-stock" disabled>
                            </div>

                            <div class="form-group">
                                <label for="stock-change">Add Stock</label>
                                <input type="number" id="stock-change" name="stock_change" min="1" required>
                            </div>

                            <button type="submit" class="submit-btn">Update Stock</button>
                            <div class="error-message" id="update-error"></div>
                            <div class="success-message" id="update-success"></div>
                        </form>
                    </div>

                    <div id="discount-form" class="form-section">
                        <form id="add-discount-form" onsubmit="handleDiscountSubmit(event)">
                            <div class="form-group">
                                <label for="discount-description">Description</label>
                                <textarea id="discount-description" name="description" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="discount-percentage">Discount Percentage (%)</label>
                                <input type="number" id="discount-percentage" name="discount_percentage" min="0" max="100" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label>Validity Period</label>
                                <div class="date-inputs">
                                    <div>
                                        <label for="valid-from">From</label>
                                        <input type="date" id="valid-from" name="valid_from" required>
                                    </div>
                                    <div>
                                        <label for="valid-till">Till</label>
                                        <input type="date" id="valid-till" name="valid_till" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="submit-btn">Add Discount</button>
                            <div class="error-message" id="discount-error"></div>
                            <div class="success-message" id="discount-success"></div>
                        </form>
                    </div>

                    <div id="category-form" class="form-section">
                        <form id="add-category-form" onsubmit="handleCategorySubmit(event)">
                            <div class="form-group">
                                <label for="category-name">Category Name</label>
                                <input type="text" id="category-name" name="name" required>
                            </div>

                            <button type="submit" class="submit-btn">Add Category</button>
                            <div class="error-message" id="category-error"></div>
                            <div class="success-message" id="category-success"></div>
                        </form>

                        <div class="category-list">
                            <h3>Existing Categories</h3>
                            <div class="categories-container">
                                <?php
                                $sql = "SELECT * FROM categories ORDER BY name";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<div class='category-item'>";
                                    echo "<span>" . htmlspecialchars($row['name']) . "</span>";
                                    echo "<button class='delete-category' onclick='deleteCategory(" . htmlspecialchars($row['category_id']) . ")'>";
                                    echo "<i class='fas fa-trash'></i>";
                                    echo "</button>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </body>
</html>