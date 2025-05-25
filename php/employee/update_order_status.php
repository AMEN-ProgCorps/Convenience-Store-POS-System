<?php
include '../import/database.php';
session_start();

if (!isset($_POST['order_id']) || !isset($_POST['action'])) {
    echo json_encode(['error' => 'Missing data']);
    exit;
}
$order_id = intval($_POST['order_id']);
$action = $_POST['action'];
if ($action === 'approve') {
    $status = 'completed';
} elseif ($action === 'decline') {
    $status = 'cancelled';
} else {
    echo json_encode(['error' => 'Invalid action']);
    exit;
}
$sql = "UPDATE orders SET order_status = '$status' WHERE order_id = $order_id";
if ($conn->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => $conn->error]);
}
?>