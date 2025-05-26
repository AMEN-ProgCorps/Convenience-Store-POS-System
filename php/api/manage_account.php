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

try {
    $type = $_POST['type'] ?? '';
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';
    $isUpdate = !empty($id);

    // Start transaction
    $conn->beginTransaction();

    if ($type === 'customer') {
        $phone = $_POST['phone_number'] ?? '';
        $email = $_POST['email'] ?? '';

        if ($isUpdate) {
            $stmt = $conn->prepare("UPDATE customer_accounts SET name = ?, phone_number = ?, email = ? WHERE customer_id = ?");
            if (!empty($password)) {
                $stmt = $conn->prepare("UPDATE customer_accounts SET name = ?, phone_number = ?, email = ?, password = ? WHERE customer_id = ?");
                $success = $stmt->execute([$name, $phone, $email, password_hash($password, PASSWORD_DEFAULT), $id]);
            } else {
                $success = $stmt->execute([$name, $phone, $email, $id]);
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO customer_accounts (name, password, phone_number, email) VALUES (?, ?, ?, ?)");
            $success = $stmt->execute([$name, password_hash($password, PASSWORD_DEFAULT), $phone, $email]);
        }
    } else if ($type === 'employee') {
        $role = $_POST['role'] ?? 'Cashier';
        $store_name = $_POST['store_name'] ?? '';

        if ($isUpdate) {
            $stmt = $conn->prepare("UPDATE employee_accounts SET name = ?, role = ?, store_name = ? WHERE employee_id = ?");
            if (!empty($password)) {
                $stmt = $conn->prepare("UPDATE employee_accounts SET name = ?, role = ?, store_name = ?, password = ? WHERE employee_id = ?");
                $success = $stmt->execute([$name, $role, $store_name, password_hash($password, PASSWORD_DEFAULT), $id]);
            } else {
                $success = $stmt->execute([$name, $role, $store_name, $id]);
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO employee_accounts (name, password, role, store_name) VALUES (?, ?, ?, ?)");
            $success = $stmt->execute([$name, password_hash($password, PASSWORD_DEFAULT), $role, $store_name]);
        }
    } else {
        throw new Exception('Invalid account type');
    }

    if (!$success) {
        throw new Exception('Failed to save account');
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
}
?> 