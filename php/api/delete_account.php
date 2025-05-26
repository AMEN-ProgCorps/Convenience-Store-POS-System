<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Verify admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['type'])) {
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

$id = $data['id'];
$type = $data['type'];

try {
    // Start transaction
    $conn->beginTransaction();

    if ($type === 'customer') {
        // First delete related orders
        $stmt = $conn->prepare("DELETE FROM order_item WHERE order_id IN (SELECT order_id FROM orders WHERE customer_id = ?)");
        $stmt->execute([$id]);

        $stmt = $conn->prepare("DELETE FROM orders WHERE customer_id = ?");
        $stmt->execute([$id]);

        // Then delete the customer account
        $stmt = $conn->prepare("DELETE FROM customer_accounts WHERE customer_id = ?");
        $success = $stmt->execute([$id]);
    } else if ($type === 'employee') {
        // First check if this is the last admin
        if ($data['role'] === 'Admin') {
            $stmt = $conn->prepare("SELECT COUNT(*) as admin_count FROM employee_accounts WHERE role = 'Admin'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['admin_count'] <= 1) {
                throw new Exception('Cannot delete the last admin account');
            }
        }

        // Delete the employee account
        $stmt = $conn->prepare("DELETE FROM employee_accounts WHERE employee_id = ?");
        $success = $stmt->execute([$id]);
    } else {
        throw new Exception('Invalid account type');
    }

    if (!$success) {
        throw new Exception('Failed to delete account');
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
}
?> 