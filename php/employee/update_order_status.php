<?php
include '../import/database.php';
session_start();

if (!isset($_POST['order_id']) || !isset($_POST['action'])) {
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$order_id = intval($_POST['order_id']);
$action = $_POST['action'];
$employee_id = $_SESSION['user_id'] ?? null;

if (!$employee_id) {
    echo json_encode(['error' => 'Employee not logged in']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Get order items before updating status
    $sql = "SELECT oi.product_id, oi.quantity, p.stock_level 
            FROM order_item oi 
            JOIN products p ON oi.product_id = p.product_id 
            WHERE oi.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orderItems = $result->fetch_all(MYSQLI_ASSOC);

    if ($action === 'approve') {
        // Update order status to completed
        $status = 'completed';
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();

        // Record inventory changes
        foreach ($orderItems as $item) {
            // Add record to inventory_records
            $sql = "INSERT INTO inventory_records (product_id, employee_id, quantity_change) 
                   VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $negative_quantity = -$item['quantity']; // Negative because items are being sold
            $stmt->bind_param("isi", $item['product_id'], $employee_id, $negative_quantity);
            $stmt->execute();
        }

    } elseif ($action === 'decline') {
        // Update order status to cancelled
        $status = 'cancelled';
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();

        // Revert product quantities
        foreach ($orderItems as $item) {
            // Update product stock level
            $new_stock = $item['stock_level'] + $item['quantity'];
            $sql = "UPDATE products SET stock_level = ? WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $new_stock, $item['product_id']);
            $stmt->execute();
        }
    } else {
        throw new Exception('Invalid action');
    }

    // If everything is successful, commit the transaction
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // If there's an error, rollback the transaction
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>