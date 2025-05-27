<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get the request data
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = $data['order_id'] ?? null;
    $action = $data['action'] ?? null; // 'approve' or 'decline'
    $employee_id = $_SESSION['employee_id'] ?? null;

    if (!$order_id || !$action || !$employee_id) {
        throw new Exception('Missing required parameters');
    }

    // Start transaction
    $conn->beginTransaction();

    if ($action === 'approve') {
        // Update order status to completed and create inventory records
        $sql = "UPDATE orders 
                SET order_status = 'completed' 
                WHERE order_id = :order_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute(['order_id' => $order_id]);

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

    } else if ($action === 'decline') {
        // Update order status to cancelled
        $sql = "UPDATE orders 
                SET order_status = 'cancelled' 
                WHERE order_id = :order_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute(['order_id' => $order_id]);
    } else {
        throw new Exception('Invalid action');
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order ' . ($action === 'approve' ? 'approved' : 'declined') . ' successfully'
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