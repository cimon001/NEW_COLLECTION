<?php
require_once 'config.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'login']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);

// Check if already in wishlist
$check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id='$user_id' AND product_id='$product_id'"));

if($check) {
    mysqli_query($conn, "DELETE FROM wishlist WHERE user_id='$user_id' AND product_id='$product_id'");
    echo json_encode(['status' => 'removed']);
} else {
    mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ('$user_id', '$product_id')");
    echo json_encode(['status' => 'added']);
}
?>