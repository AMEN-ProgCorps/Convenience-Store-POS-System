<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

try {
    // Get the most popular items based on order quantities today
    $sql = "SELECT 
            p.name,
            SUM(oi.quantity) as quantity
            FROM products p
            JOIN order_item oi ON p.product_id = oi.product_id
            JOIN orders o ON oi.order_id = o.order_id
            WHERE DATE(o.order_date) = CURDATE()
            AND o.order_status = 'completed'
            GROUP BY p.product_id, p.name
            ORDER BY quantity DESC
            LIMIT 5";
    
    $stmt = $conn->query($sql);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($items);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} 