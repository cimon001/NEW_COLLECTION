<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'please login first!']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);
    $user_id = $_SESSION['user_id'];

    // Validate rating range
    if($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid rating!']);
        exit();
    }

    // Check karo already review di hai ya nahi
    $check = mysqli_query($conn, "SELECT id FROM reviews WHERE product_id='$product_id' AND user_id='$user_id'");
    if(mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already reviewed this product!']);
        exit();
    }

    // Review insert karo
    $sql = "INSERT INTO reviews (product_id, user_id, rating, review) VALUES ('$product_id', '$user_id', '$rating', '$review')";
    if(mysqli_query($conn, $sql)) {
        // Average rating fetch karo
        $avg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE product_id='$product_id'"));
        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully!',
            'avg_rating' => round($avg['avg'], 1),
            'total_reviews' => $avg['total']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Something went wrong!']);
    }
}
?>