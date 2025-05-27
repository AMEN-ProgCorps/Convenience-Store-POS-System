<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) || !isset($data['employee_id'])) {
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

$order_id = intval($data['order_id']);
$employee_id = $data['employee_id'];

try {
    // Start transaction
    $conn->beginTransaction();

    // Get order items
    $sql = "SELECT oi.product_id, oi.quantity 
            FROM order_item oi 
            WHERE oi.order_id = :order_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute(['order_id' => $order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add inventory records for each item
    foreach ($order_items as $item) {
        // Insert negative quantity change to represent stock reduction
        $sql = "INSERT INTO inventory_records 
                (product_id, employee_id, quantity_change, change_date) 
                VALUES 
                (:product_id, :employee_id, :quantity_change, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'product_id' => $item['product_id'],
            'employee_id' => $employee_id,
            'quantity_change' => -$item['quantity'] // Negative for reduction
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