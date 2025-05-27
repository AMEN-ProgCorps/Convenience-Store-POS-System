<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['employee_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Validate required fields
    if (!isset($_POST['name']) || !isset($_POST['category_id']) || 
        !isset($_POST['stock_level']) || !isset($_POST['price'])) {
        throw new Exception('Missing required fields');
    }

    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $stock_level = intval($_POST['stock_level']);
    $price = floatval($_POST['price']);

    // Validate inputs
    if (empty($name)) {
        throw new Exception('Product name is required');
    }
    if ($category_id <= 0) {
        throw new Exception('Valid category is required');
    }
    if ($stock_level < 0) {
        throw new Exception('Stock level cannot be negative');
    }
    if ($price <= 0) {
        throw new Exception('Price must be greater than 0');
    }

    // Check if product name already exists
    $sql = "SELECT COUNT(*) FROM products WHERE name = :name";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':name' => $name]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Product with this name already exists');
    }

    // Check if category exists
    $sql = "SELECT COUNT(*) FROM categories WHERE category_id = :category_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':category_id' => $category_id]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception('Selected category does not exist');
    }

    // Get the next available product_id
    $sql = "SELECT COALESCE(MAX(product_id), 0) + 1 FROM products";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $next_product_id = $stmt->fetchColumn();

    // Start transaction
    $conn->beginTransaction();

    // Insert product with the next available ID
    $sql = "INSERT INTO products (product_id, name, category_id, stock_level, price) 
            VALUES (:product_id, :name, :category_id, :stock_level, :price)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':product_id' => $next_product_id,
        ':name' => $name,
        ':category_id' => $category_id,
        ':stock_level' => $stock_level,
        ':price' => $price
    ]);

    // Add inventory record for initial stock if stock_level > 0
    if ($stock_level > 0) {
        $sql = "INSERT INTO inventory_records (product_id, employee_id, quantity_change, change_date) 
                VALUES (:product_id, :employee_id, :quantity_change, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':product_id' => $next_product_id,
            ':employee_id' => $_SESSION['employee_id'],
            ':quantity_change' => $stock_level
        ]);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 