 <?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first!']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$size = isset($_POST['size']) ? mysqli_real_escape_string($conn, $_POST['size']) : 'M';
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($product_id == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product!']);
    exit();
}

// Check if product already exists in cart
$check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id='$user_id' AND product_id='$product_id' AND size='$size'");

if (mysqli_num_rows($check) > 0) {
    // Already exists — update quantity
    $existing = mysqli_fetch_assoc($check);
    $new_qty = $existing['quantity'] + $quantity;
    mysqli_query($conn, "UPDATE cart SET quantity='$new_qty' WHERE id='{$existing['id']}'");
} else {
    // Add new item to cart
    mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity, size) VALUES ('$user_id', '$product_id', '$quantity', '$size')");
}

// Fetch cart count (total quantity, not row count)
$count_result = mysqli_query($conn, "SELECT SUM(quantity) as cnt FROM cart WHERE user_id='$user_id'");
$count = mysqli_fetch_assoc($count_result)['cnt'] ?? 0;

echo json_encode([
    'success' => true,
    'message' => 'Product added to cart successfully!',
    'cart_count' => $count
]);
?>