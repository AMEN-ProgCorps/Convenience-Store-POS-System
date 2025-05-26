<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get the order ID from the request
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = $data['order_id'] ?? null;
    $employee_id = $data['employee_id'] ?? null;

    if (!$order_id || !$employee_id) {
        throw new Exception('Missing required parameters');
    }

    // Start transaction
    $conn->beginTransaction();

    // Get all order items for this order
    $sql = "SELECT oi.product_id, oi.quantity 
            FROM order_item oi 
            WHERE oi.order_id = :order_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute(['order_id' => $order_id]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each order item, create an inventory record and update product stock
    foreach ($orderItems as $item) {
        // Create inventory record (reduction)
        $sql = "INSERT INTO inventory_records (product_id, employee_id, quantity_change, change_date) 
                VALUES (:product_id, :employee_id, :quantity_change, CURRENT_TIMESTAMP)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'product_id' => $item['product_id'],
            'employee_id' => $employee_id,
            'quantity_change' => -$item['quantity'] // Negative for reduction
        ]);

        // Update product stock level
        $sql = "UPDATE products 
                SET stock_level = stock_level - :quantity 
                WHERE product_id = :product_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'quantity' => $item['quantity'],
            'product_id' => $item['product_id']
        ]);
    }

    // Update order status to completed
    $sql = "UPDATE orders 
            SET order_status = 'completed' 
            WHERE order_id = :order_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute(['order_id' => $order_id]);

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Inventory records updated successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 