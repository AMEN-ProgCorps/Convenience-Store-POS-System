<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

try {
    // Get total items in stock (sum of quantities)
    $sql = "SELECT 
            SUM(stock_level) as total_items,
            SUM(stock_level * price) as total_value,
            COUNT(CASE WHEN stock_level <= 10 THEN 1 END) as low_stock_items
            FROM products";
    
    $stmt = $conn->query($sql);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Format the response
    $response = [
        'total_items' => intval($stats['total_items'] ?? 0),
        'total_value' => floatval($stats['total_value'] ?? 0),
        'low_stock_items' => intval($stats['low_stock_items'])
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} 