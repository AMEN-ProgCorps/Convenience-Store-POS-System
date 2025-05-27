<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

try {
    // Get sales by category for this week based on inventory records
    $sql = "SELECT 
            c.name,
            ABS(SUM(ir.quantity_change)) as total_sold
            FROM categories c
            JOIN products p ON c.category_id = p.category_id
            JOIN inventory_records ir ON p.product_id = ir.product_id
            WHERE YEARWEEK(ir.change_date, 1) = YEARWEEK(CURDATE(), 1)
            AND ir.quantity_change < 0  -- Only count reductions (sales)
            GROUP BY c.category_id, c.name
            ORDER BY total_sold DESC";
    
    $stmt = $conn->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format numbers
    foreach ($categories as &$category) {
        $category['total_sold'] = intval($category['total_sold']);
    }

    echo json_encode($categories);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} 