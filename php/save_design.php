<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);

// Upload folder create karo
$upload_dir = '../uploads/designs/';
if(!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$image_front = '';
$image_back = '';

// Front image upload
$allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB

if(isset($_FILES['image_front']) && $_FILES['image_front']['error'] == 0) {
    if(in_array($_FILES['image_front']['type'], $allowed_types) && $_FILES['image_front']['size'] <= $max_size) {
        $ext = pathinfo($_FILES['image_front']['name'], PATHINFO_EXTENSION);
        $filename = 'front_' . $user_id . '_' . time() . '.' . $ext;
        if(move_uploaded_file($_FILES['image_front']['tmp_name'], $upload_dir . $filename)) {
            $image_front = $filename;
        }
    }
}

// Back image upload
if(isset($_FILES['image_back']) && $_FILES['image_back']['error'] == 0) {
    if(in_array($_FILES['image_back']['type'], $allowed_types) && $_FILES['image_back']['size'] <= $max_size) {
        $ext = pathinfo($_FILES['image_back']['name'], PATHINFO_EXTENSION);
        $filename = 'back_' . $user_id . '_' . time() . '.' . $ext;
        if(move_uploaded_file($_FILES['image_back']['tmp_name'], $upload_dir . $filename)) {
            $image_back = $filename;
        }
    }
}

if(empty($title)) {
    header('Location: ../pages/custom_design.php?error=Please fill all fields!');
    exit();
}

// Save to database
mysqli_query($conn, "INSERT INTO custom_designs (user_id, title, description, image_front, image_back) 
                     VALUES ('$user_id', '$title', '$description', '$image_front', '$image_back')");

// Notification
addNotification($conn, $user_id, '🎨 Your design "' . $title . '" has been submitted! We will review it within 24-48 hours.', 'general');

header('Location: ../pages/custom_design.php?success=1');
exit();
?>