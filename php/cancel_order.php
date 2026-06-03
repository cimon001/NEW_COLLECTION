<?php
require_once 'config.php';

// Login check
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if ($order_id > 0) {
    // Check if order belongs to user and is pending
    $check = mysqli_query($conn, 
        "SELECT id FROM orders WHERE id='$order_id' AND user_id='$user_id' AND status='pending'");
    
    if (mysqli_num_rows($check) > 0) {
        // Cancel order
        mysqli_query($conn, 
            "UPDATE orders SET status='cancelled' WHERE id='$order_id'");
        
        // Send cancellation notification to user
        $order_num = '#NC' . str_pad($order_id, 5, '0', STR_PAD_LEFT);
        addNotification($conn, $user_id, '❌ Your order ' . $order_num . ' has been cancelled successfully.', 'cancelled');
    }
}

header('Location: ../pages/orders.php');
exit();
?>
