<?php
// Debug: Show all errors as JSON (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Always return JSON, even on fatal error
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo json_encode(['error' => 'Fatal server error', 'details' => $error['message']]);
    }
});

header('Content-Type: application/json');
session_start();
include '../import/database.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);
if (!$data) {
    echo json_encode([
        'error' => 'Invalid input',
        'raw_input' => $raw_input
    ]);
    exit;
}

$cart = $data['cart'] ?? [];
$discount_id = $data['discount_id'] ?? null;
$discount_percent = $data['discount_percent'] ?? 0;
$payment_type = $data['payment_type'] ?? 'cash';
$sub_total = $data['sub_total'] ?? 0;
$discount_amount = $data['discount_amount'] ?? 0;
$tax = $data['tax'] ?? 0;
$total = $data['total'] ?? 0;

$user_id = $_SESSION['user_id'] ?? 'C101';

if (empty($cart)) {
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

try {
    // Start transaction
    $conn->beginTransaction();

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, discount_id, payment_type, order_status) VALUES (?, ?, ?, ?, 'pending')");
    if (!$stmt->execute([$user_id, $total, $discount_id, $payment_type])) {
        throw new Exception('Order insert failed');
    }
    $order_id = $conn->lastInsertId();

    // Insert order items
    foreach ($cart as $item) {
        $product_id = $item['product_id'] ?? null;
        $quantity = $item['quantity'] ?? null;
        $unit_price = $item['unit_price'] ?? null;
        $item_total = $item['total'] ?? null;

        // Validate required fields
        if (!is_numeric($product_id) || !is_numeric($quantity) || !is_numeric($unit_price) || !is_numeric($item_total)) {
            error_log('Order item skipped due to invalid data: ' . json_encode($item));
            continue; // Skip this item
        }

        // Insert order item
        $stmt = $conn->prepare("INSERT INTO order_item (order_id, product_id, total_ammount, quantity, unit_price) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt->execute([$order_id, $product_id, $item_total, $quantity, $unit_price])) {
            throw new Exception('Order item insert failed');
        }

        // Update product stock
        $stmt = $conn->prepare("UPDATE products SET stock_level = stock_level - ? WHERE product_id = ?");
        if (!$stmt->execute([intval($quantity), intval($product_id)])) {
            throw new Exception('Failed to update product stock');
        }
    }

    // Commit transaction
    $conn->commit();

    // Prepare receipt data
    $receipt = [
        'order_id' => $order_id,
        'cart' => $cart,
        'sub_total' => $sub_total,
        'discount' => $discount_amount,
        'tax' => $tax,
        'total' => $total,
        'payment_type' => $payment_type,
        'discount_percent' => $discount_percent,
        'discount_id' => $discount_id
    ];

    echo json_encode(['success' => true, 'receipt' => $receipt]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
}
?> 