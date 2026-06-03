<?php
require_once 'config.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first!']);
    exit();
}

$user_id = $_SESSION['user_id'];
$design_id = intval($_POST['design_id']);

// Fetch design
$design = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM custom_designs WHERE id='$design_id' AND status='approved'"));

if(!$design) {
    echo json_encode(['success' => false, 'message' => 'Design not found!']);
    exit();
}

// Save order in design_orders table
mysqli_query($conn, "INSERT INTO design_orders (design_id, buyer_id, profit_amount) VALUES ('$design_id', '$user_id', '" . ($design['price'] * 0.05) . "')");

// 5% profit designer ko do
$profit = $design['price'] * 0.05;
mysqli_query($conn, "UPDATE custom_designs SET total_earnings = total_earnings + '$profit' WHERE id='$design_id'");

// Buyer ko notification
addNotification($conn, $user_id, '🎨 Your custom order for "' . $design['title'] . '" has been placed! We will contact you soon.', 'order');

// Designer ko notification
addNotification($conn, $design['user_id'], '💰 Someone ordered your design "' . $design['title'] . '"! You earned ₹' . number_format($profit, 0) . '!', 'offer');

echo json_encode(['success' => true]);
?>