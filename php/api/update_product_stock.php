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
    if (!isset($_POST['product_id']) || !isset($_POST['stock_change'])) {
        throw new Exception('Missing required fields');
    }

    $product_id = $_POST['product_id'];
    $stock_change = intval($_POST['stock_change']);
    $employee_id = $_SESSION['employee_id'];

    // Validate stock change
    if ($stock_change <= 0) {
        throw new Exception('Stock change must be positive');
    }

    // Start transaction
    $conn->beginTransaction();

    // Update product stock
    $sql = "UPDATE products 
            SET stock_level = stock_level + :stock_change 
            WHERE product_id = :product_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':stock_change' => $stock_change,
        ':product_id' => $product_id
    ]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Product not found');
    }

    // Add inventory record
    $sql = "INSERT INTO inventory_records (product_id, employee_id, quantity_change, change_date) 
            VALUES (:product_id, :employee_id, :quantity_change, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':product_id' => $product_id,
        ':employee_id' => $employee_id,
        ':quantity_change' => $stock_change // This is already positive for additions
    ]);

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