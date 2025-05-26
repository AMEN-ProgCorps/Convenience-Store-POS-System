<?php
header('Content-Type: application/json');
include '../import/database.php';

try {
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    
    $sql = "SELECT p.product_id as id, p.name, p.stock_level as quantity, p.price as amount, c.name as category 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE 1=1";
    $params = array();
    
    if (!empty($query)) {
        $sql .= " AND (p.name LIKE ? OR p.product_id LIKE ?)";
        $searchTerm = "%{$query}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the price to 2 decimal places
    foreach ($items as &$item) {
        $item['amount'] = number_format((float)$item['amount'], 2, '.', '');
    }
    
    echo json_encode($items);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} 