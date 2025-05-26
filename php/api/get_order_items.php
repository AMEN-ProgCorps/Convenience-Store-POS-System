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
        throw new Exception("Failed to prepare statement");
    }

    if (!$stmt->execute([$order_id])) {
        throw new Exception("Failed to execute statement");
    }

    $items = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Format numbers to ensure proper JSON encoding
        $row['price'] = floatval($row['price']);
        $row['quantity'] = intval($row['quantity']);
        $row['total_amount'] = floatval($row['total_amount']);
        $items[] = $row;
    }

    echo json_encode($items);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 