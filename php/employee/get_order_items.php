<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    echo json_encode(['error' => 'No order_id']);
    exit;
}

$order_id = intval($_GET['order_id']);

// Use prepared statement to prevent SQL injection
$sql = "SELECT 
            oi.product_id,
            oi.quantity,
            oi.unit_price as price,
            p.name,
            oi.total_ammount as total_amount
        FROM order_item oi 
        JOIN products p ON oi.product_id = p.product_id 
        WHERE oi.order_id = ?";

try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $order_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $items = [];
    
    while ($row = $result->fetch_assoc()) {
        // Format numbers to ensure proper JSON encoding
        $row['price'] = floatval($row['price']);
        $row['quantity'] = intval($row['quantity']);
        $row['total_amount'] = floatval($row['total_amount']);
        $items[] = $row;
    }

    echo json_encode($items);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
}
?>