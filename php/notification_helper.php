<?php
function addNotification($conn, $user_id, $message, $type = 'general') {
    $message = mysqli_real_escape_string($conn, $message);
    $type = mysqli_real_escape_string($conn, $type);
    mysqli_query($conn, "INSERT INTO notifications (user_id, message, type) VALUES ('$user_id', '$message', '$type')");
}
?>