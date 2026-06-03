<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$change = isset($_GET['change']) ? intval($_GET['change']) : 0;

if ($id > 0) {
    $result = mysqli_query($conn, "SELECT quantity FROM cart WHERE id='$id' AND user_id='$user_id'");
    if (mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
        $new_qty = $item['quantity'] + $change;
        if ($new_qty <= 0) {
            mysqli_query($conn, "DELETE FROM cart WHERE id='$id' AND user_id='$user_id'");
            echo json_encode(['success' => true, 'qty' => 0]);
        } else {
            mysqli_query($conn, "UPDATE cart SET quantity='$new_qty' WHERE id='$id' AND user_id='$user_id'");
            echo json_encode(['success' => true, 'qty' => $new_qty]);
        }
    }
} else {
    echo json_encode(['success' => false]);
}
?>
