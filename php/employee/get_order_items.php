<?php
include '../import/database.php';
session_start();

if (!isset($_GET['order_id'])) {
    echo json_encode(['error' => 'No order_id']);
    exit;
}
$order_id = intval($_GET['order_id']);
$sql = "SELECT oi.*, p.name FROM order_item oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = $order_id";
$result = $conn->query($sql);
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
echo json_encode($items);
?>