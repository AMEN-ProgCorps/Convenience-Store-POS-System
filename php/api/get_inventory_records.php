<?php
include '../import/database.php';
session_start();

header('Content-Type: application/json');

try {
    $type = isset($_GET['type']) ? $_GET['type'] : 'all';
    
    // Base query for both additions and reductions
    $sql = "SELECT 
            ir.record_id,
            p.name as product_name,
            ir.quantity_change,
            ir.change_date,
            e.name as employee_name,
            CASE 
                WHEN ir.quantity_change > 0 THEN 'Addition'
                ELSE 'Reduction'
            END as change_type
            FROM inventory_records ir
            JOIN products p ON ir.product_id = p.product_id
            LEFT JOIN employees e ON ir.employee_id = e.employee_id
            WHERE 1=1";

    // Filter by type if specified
    if ($type === 'addition') {
        $sql .= " AND ir.quantity_change > 0";
    } elseif ($type === 'reduction') {
        $sql .= " AND ir.quantity_change < 0";
    }

    // Order by most recent first
    $sql .= " ORDER BY ir.change_date DESC LIMIT 10";
    
    $stmt = $conn->query($sql);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format dates and numbers
    foreach ($records as &$record) {
        $record['change_date'] = date('Y-m-d H:i:s', strtotime($record['change_date']));
        $record['quantity_change'] = intval($record['quantity_change']);
    }

    echo json_encode($records);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} 