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
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['category_id'])) {
        throw new Exception('Category ID is required');
    }

    $category_id = intval($data['category_id']);

    // Check if category has products
    $sql = "SELECT COUNT(*) FROM products WHERE category_id = :category_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':category_id' => $category_id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Cannot delete category that has products');
    }

    // Delete category
    $sql = "DELETE FROM categories WHERE category_id = :category_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':category_id' => $category_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Category not found');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 