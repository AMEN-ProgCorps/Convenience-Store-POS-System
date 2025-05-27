<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

try {
    $sql = "SELECT 
            p.product_id,
            p.name,
            p.stock_level,
            p.price,
            c.name as category_name,
            c.category_id
            FROM products p
            JOIN categories c ON p.category_id = c.category_id
            ORDER BY p.name ASC";
    
    $stmt = $conn->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} 