<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) || !isset($data['quantities']) || !isset($data['original_quantities'])) {
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

$order_id = intval($data['order_id']);
$quantities = $data['quantities'];
$original_quantities = $data['original_quantities'];
$removed_items = $data['removed_items'] ?? [];

// Start transaction
$conn->begin_transaction();

try {
    // First handle removed items
    foreach ($removed_items as $product_id) {
        $product_id = intval($product_id);
        
        // Get the original quantity before deleting
        $stmt = $conn->prepare("SELECT quantity FROM order_item WHERE order_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $order_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $original_quantity = $row['quantity'];
        $stmt->close();
        
        // Delete the order item
        $stmt = $conn->prepare("DELETE FROM order_item WHERE order_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $order_id, $product_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to remove order item");
        }
        $stmt->close();
    }

    // Update remaining quantities
    foreach ($quantities as $product_id => $new_quantity) {
        // Skip if item was removed
        if (in_array($product_id, $removed_items)) {
            continue;
        }

        $product_id = intval($product_id);
        $new_quantity = intval($new_quantity);
        $original_quantity = intval($original_quantities[$product_id]);

        // Get current unit price from order_item
        $stmt = $conn->prepare("SELECT unit_price FROM order_item WHERE order_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $order_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $unit_price = $row['unit_price'];
        $stmt->close();

        // Calculate new total amount
        $new_total = $unit_price * $new_quantity;

        // Update order_item
        $stmt = $conn->prepare("UPDATE order_item SET quantity = ?, total_ammount = ? WHERE order_id = ? AND product_id = ?");
        $stmt->bind_param("idii", $new_quantity, $new_total, $order_id, $product_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update order item");
        }
        $stmt->close();

        // Calculate stock adjustment
        $quantity_difference = $new_quantity - $original_quantity;

        // Update product stock
        if ($quantity_difference != 0) {
            $stmt = $conn->prepare("UPDATE products SET stock_level = stock_level - ? WHERE product_id = ?");
            $stmt->bind_param("ii", $quantity_difference, $product_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update product stock");
            }
            $stmt->close();
        }
    }

    // Update total amount in orders table
    $stmt = $conn->prepare("UPDATE orders SET total_amount = (SELECT SUM(total_ammount) FROM order_item WHERE order_id = ?) WHERE order_id = ?");
    $stmt->bind_param("ii", $order_id, $order_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update order total");
    }
    $stmt->close();

    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?> 