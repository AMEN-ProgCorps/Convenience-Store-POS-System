<?php
session_start();
include '../import/database.php';
header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['error' => 'Invalid input']);
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

$stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, payment_type, order_status) VALUES (?, ?, ?, 'pending')");
if (!$stmt) {
    echo json_encode(['error' => 'Order insert prepare failed', 'sqlerror' => $conn->error]);
    $conn->close();
    exit;
}
$stmt->bind_param('sds', $user_id, $total, $payment_type);
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Order insert failed', 'sqlerror' => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}
$order_id = $conn->insert_id;
$stmt->close();

// Insert order items


foreach ($cart as $item) {
    $product_id = $item['product_id'];
    $quantity = $item['quantity'];
    $unit_price = $item['unit_price'];
    $item_total = $item['total'];
    if ($discount_id) {
        $stmt = $conn->prepare("INSERT INTO order_item (order_id, product_id, discount_id, total_ammount, quantity, unit_price) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param('iiidid', $order_id, $product_id, $discount_id, $item_total, $quantity, $unit_price);
            if (!$stmt->execute()) {
                error_log('Order item insert failed: ' . $stmt->error);
            }
            $stmt->close();
        } else {
            error_log('Order item prepare failed: ' . $conn->error);
        }
    } else {
        // Use NULL for discount_id by omitting it from the bind_param and using the correct types
        $stmt = $conn->prepare("INSERT INTO order_item (order_id, product_id, total_ammount, quantity, unit_price) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param('iidid', $order_id, $product_id, $item_total, $quantity, $unit_price);
            if (!$stmt->execute()) {
                error_log('Order item insert failed (no discount): ' . $stmt->error);
            }
            $stmt->close();
        } else {
            error_log('Order item prepare failed (no discount): ' . $conn->error);
        }
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
