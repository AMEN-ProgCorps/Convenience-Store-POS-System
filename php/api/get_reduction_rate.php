<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

try {
    // Calculate total stock and reductions for today
    $sql = "SELECT 
            (SELECT SUM(stock_level) FROM products) as total_stock,
            COALESCE(
                (SELECT ABS(SUM(quantity_change))
                FROM inventory_records 
                WHERE DATE(change_date) = CURDATE()
                AND quantity_change < 0), 0
            ) as total_reductions";
    
    $stmt = $conn->query($sql);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_stock = floatval($stats['total_stock'] ?? 0);
    $total_reductions = floatval($stats['total_reductions'] ?? 0);

    // Calculate reduction percentage
    $reduction_percentage = $total_stock > 0 ? 
        round(($total_reductions / ($total_stock + $total_reductions)) * 100, 2) : 0;

    echo json_encode([
        'reduction_percentage' => $reduction_percentage
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} 