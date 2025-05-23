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

// Debug: Check DB variables
if (!isset($servername, $username, $password, $dbname)) {
    echo json_encode(['error' => 'Database config missing']);
    exit;
}

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

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// Insert order

// Debug: check for SQL errors
if (!$conn) {
    echo json_encode(['error' => 'No DB connection']);
    exit;
}

// Insert order with discount_id moved to orders table
$stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, discount_id, payment_type, order_status) VALUES (?, ?, ?, ?, 'pending')");
if (!$stmt) {
    echo json_encode(['error' => 'Order insert prepare failed', 'sqlerror' => $conn->error]);
    $conn->close();
    exit;
}
$stmt->bind_param('sdss', $user_id, $total, $discount_id, $payment_type);
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Order insert failed', 'sqlerror' => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}
$order_id = $conn->insert_id;
$stmt->close();

// Insert order items (remove discount_id from order_item)
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

    $stmt = $conn->prepare("INSERT INTO order_item (order_id, product_id, total_ammount, quantity, unit_price) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('iidid', $order_id, $product_id, $item_total, $quantity, $unit_price);
        if (!$stmt->execute()) {
            error_log('Order item insert failed: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log('Order item prepare failed: ' . $conn->error);
    }
    // Update product stock
    $conn->query("UPDATE products SET stock_level = stock_level - " . intval($quantity) . " WHERE product_id = " . intval($product_id));
}

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

$conn->close();
echo json_encode(['success' => true, 'receipt' => $receipt]);
