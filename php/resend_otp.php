<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/send_email.php';

header('Content-Type: application/json');

// Must have session with temp_email
if (!isset($_SESSION['temp_email'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please register again.']);
    exit();
}

$email = $_SESSION['temp_email'];

// Verify email matches session (security check)
$posted_email = trim($_POST['email'] ?? '');
if ($posted_email !== $email) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

// Check user exists and is not already verified
$check = mysqli_query($conn, "SELECT id, name FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "' AND is_verified = 0");

if (mysqli_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Account not found or already verified.']);
    exit();
}

$user = mysqli_fetch_assoc($check);

// Generate a new OTP
$new_otp = rand(100000, 999999);

// Update OTP in DB
$update = mysqli_query($conn, "UPDATE users SET otp = '$new_otp' WHERE id = '{$user['id']}'");

if (!$update) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    exit();
}

// Send the new OTP via email
$subject = "Your NEW_COLLECTION Verification Code";
$sent = sendEmail($email, $subject, $new_otp);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'OTP sent successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again.']);
}
?>
