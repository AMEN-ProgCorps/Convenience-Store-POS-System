<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['employee_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Validate required fields
    if (!isset($_POST['description']) || !isset($_POST['discount_percentage']) || 
        !isset($_POST['valid_from']) || !isset($_POST['valid_till'])) {
        throw new Exception('Missing required fields');
    }

    $description = trim($_POST['description']);
    $percentage = floatval($_POST['discount_percentage']);
    $valid_from = $_POST['valid_from'];
    $valid_till = $_POST['valid_till'];

    // Validate inputs
    if (empty($description)) {
        throw new Exception('Description is required');
    }
    if ($percentage <= 0 || $percentage > 100) {
        throw new Exception('Discount percentage must be between 0 and 100');
    }
    if (strtotime($valid_till) <= strtotime($valid_from)) {
        throw new Exception('End date must be after start date');
    }

    // Get the next available discount_id
    $sql = "SELECT COALESCE(MAX(discount_id), 0) + 1 FROM discounts";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $next_discount_id = $stmt->fetchColumn();

    // Insert discount with the next available ID
    $sql = "INSERT INTO discounts (discount_id, description, discount_percentage, valid_from, valid_till) 
            VALUES (:discount_id, :description, :percentage, :valid_from, :valid_till)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':discount_id' => $next_discount_id,
        ':description' => $description,
        ':percentage' => $percentage,
        ':valid_from' => $valid_from,
        ':valid_till' => $valid_till
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 