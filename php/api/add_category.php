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
    if (!isset($_POST['name'])) {
        throw new Exception('Category name is required');
    }

    $name = trim($_POST['name']);

    // Validate input
    if (empty($name)) {
        throw new Exception('Category name cannot be empty');
    }

    // Check if category already exists
    $sql = "SELECT COUNT(*) FROM categories WHERE name = :name";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':name' => $name]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Category already exists');
    }

    // Get the next available category_id
    $sql = "SELECT COALESCE(MAX(category_id), 0) + 1 FROM categories";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $next_category_id = $stmt->fetchColumn();

    // Insert category with the next available ID
    $sql = "INSERT INTO categories (category_id, name) VALUES (:category_id, :name)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':category_id' => $next_category_id,
        ':name' => $name
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 