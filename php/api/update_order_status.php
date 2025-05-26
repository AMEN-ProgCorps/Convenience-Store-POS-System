<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) || !isset($data['status'])) {
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

$order_id = intval($data['order_id']);
$status = $data['status'];

// Validate status
if (!in_array($status, ['completed', 'cancelled'])) {
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

try {
    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    if (!$stmt->execute([$status, $order_id])) {
        throw new Exception("Failed to update order status");
    }
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 