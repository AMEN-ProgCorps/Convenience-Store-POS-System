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
        // Call the inventory update API
        $ch = curl_init('http://' . $_SERVER['HTTP_HOST'] . '/Convenience-Store-POS-System/php/api/update_inventory_records.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'order_id' => $order_id,
            'employee_id' => $employee_id
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Failed to update inventory records');
        }

        $result = json_decode($response, true);
        if (!$result['success']) {
            throw new Exception($result['error'] ?? 'Failed to update inventory records');
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