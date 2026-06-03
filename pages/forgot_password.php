<?php
require_once '../php/config.php';

$error = '';
$success = '';
$step = 1;
$verified_email = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Step 1 — Email + Phone verify & Send OTP
    if(isset($_POST['step1'])) {
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
        
        $query = mysqli_query($conn, "SELECT id, name FROM users WHERE email='$email' AND phone='$phone'");
        
        if(mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);
            
            // Generate OTP and update DB
            $otp = rand(100000, 999999);
            mysqli_query($conn, "UPDATE users SET otp='$otp' WHERE email='$email'");
            
            // Send Email
            require_once '../php/send_email.php';
            if(function_exists('sendEmail')) {
                $msg = "Hello {$user['name']},\n\nSomeone requested to reset your NEW_COLLECTION password.\n\nYour 6-digit OTP is: $otp\n\nPlease enter this code to securely change your password.\n\nIf you didn't request this, please ignore this email.";
                sendEmail($email, "Password Reset OTP - NEW_COLLECTION", $msg);
            }
            
            $step = 2; // Move to OTP input
            $verified_email = $email;
        } else {
            $error = "Email or phone number does not match!";
        }
    }
    
    // 🚨 NAYA STEP: Step 2 — Verify OTP
    if(isset($_POST['step2'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $entered_otp = mysqli_real_escape_string($conn, trim($_POST['otp']));
        
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND otp='$entered_otp'");
        
        if(mysqli_num_rows($check) > 0) {
            // OTP Match ho gaya! Clear the OTP from database
            mysqli_query($conn, "UPDATE users SET otp=NULL WHERE email='$email'");
            
            $step = 3; // Move to New Password input
            $verified_email = $email;
        } else {
            $error = "Invalid OTP! Please check your email and try again.";
            $step = 2; // Stay on OTP step
            $verified_email = $email;
        }
    }
    
    // Step 3 — New password set (Purana Step 2)
    if(isset($_POST['step3'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if(empty($new_password) || empty($confirm_password)) {
            $error = "Please fill all fields!";
            $step = 3;
            $verified_email = $email;
        } elseif($new_password !== $confirm_password) {
            $error = "Passwords do not match!";
            $step = 3;
            $verified_email = $email;
        } elseif(strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters!";
            $step = 3;
            $verified_email = $email;
        } else {
            // Change Password
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE email='$email'");
            
            $success = "Password changed successfully! You can now login.";
            $step = 4; // Final Success Step
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .auth-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--black); padding: 100px 20px; }
        .auth-box { background: var(--card); border: 1px solid var(--border); padding: 50px; width: 100%; max-width: 480px; }
        .auth-logo { font-family: 'Bebas Neue', sans-serif; font-size: 28px; letter-spacing: 4px; color: var(--gold); text-align: center; margin-bottom: 8px; }
        .auth-title { font-family: 'Bebas Neue', sans-serif; font-size: 36px; text-align: center; margin-bottom: 6px; letter-spacing: 2px; }
        .auth-sub { text-align: center; color: var(--muted); font-size: 14px; margin-bottom: 36px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .form-group input { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid var(--border); color: var(--white); padding: 14px 18px; font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none; transition: border-color 0.3s; }
        .form-group input:focus { border-color: var(--gold); background: rgba(200,169,110,0.05); }
        .form-group input::placeholder { color: var(--muted); }
        .btn-full { width: 100%; background: var(--gold); color: var(--black); padding: 16px; border: none; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: all 0.3s; margin-top: 10px; }
        .btn-full:hover { background: var(--gold2); transform: translateY(-2px); }
        .auth-link { text-align: center; margin-top: 24px; font-size: 14px; color: var(--muted); }
        .auth-link a { color: var(--gold); text-decoration: none; }
        .alert-error { background: rgba(232,68,68,0.1); border: 1px solid var(--red); color: var(--red); padding: 12px 16px; font-size: 13px; margin-bottom: 20px; text-align: center; }
        .alert-success { background: rgba(76,175,80,0.1); border: 1px solid #4caf50; color: #4caf50; padding: 12px 16px; font-size: 13px; margin-bottom: 20px; text-align: center; }
        .step-indicator { display: flex; justify-content: center; gap: 8px; margin-bottom: 30px; }
        .step-dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.1); }
        .step-dot.active { background: var(--gold); }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="nav-logo">NEW_COLLECTION</a>
        <ul class="nav-links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="products.php">Shop</a></li>
        </ul>
        <div class="nav-actions">
            <a href="login.php" class="nav-btn">Login</a>
        </div>
        <button class="hamburger" id="hamburger" onclick="toggleMobileNav()" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </nav>
    <!-- Mobile Nav -->
    <div class="mobile-nav" id="mobileNav"></div>
    <script>
    (function(){
        var nav = document.querySelector(".nav-links");
        var actions = document.querySelector(".nav-actions");
        var mobileNav = document.getElementById("mobileNav");
        if(nav && actions && mobileNav) {
            var links = nav.innerHTML;
            var btns = actions.innerHTML;
            mobileNav.innerHTML = links.replace(/<li>/g,"").replace(/<\/li>/g,"") + '<div class="mobile-nav-actions">' + btns + '</div>';
        }
    })();
    function toggleMobileNav() {
        var btn = document.getElementById("hamburger");
        var nav = document.getElementById("mobileNav");
        btn.classList.toggle("open");
        nav.classList.toggle("open");
        document.body.style.overflow = nav.classList.contains("open") ? "hidden" : "";
    }
    </script>

    <div class="auth-page">
        <div class="auth-box">
            <div class="auth-logo">NEW_COLLECTION</div>

            <?php if($step == 1): ?>
                <div class="auth-title">FORGOT PASSWORD</div>
                <p class="auth-sub">Enter your email and phone number to verify</p>

                <div class="step-indicator">
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                </div>

                <?php if($error): ?>
                    <div class="alert-error">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="email@example.com" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="Enter registered phone number" required>
                    </div>
                    <button type="submit" name="step1" class="btn-full">Send OTP →</button>
                </form>

            <?php elseif($step == 2): ?>
                <div class="auth-title">VERIFY OTP</div>
                <p class="auth-sub">Enter the 6-digit code sent to your email</p>

                <div class="step-indicator">
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                </div>

                <?php if($error): ?>
                    <div class="alert-error">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($verified_email); ?>">
                    <div class="form-group">
                        <label>6-Digit OTP</label>
                        <input type="text" name="otp" placeholder="------" required maxlength="6" pattern="[0-9]{6}" style="text-align:center; font-size: 20px; letter-spacing: 4px; font-weight: bold;">
                    </div>
                    <button type="submit" name="step2" class="btn-full">Verify OTP →</button>
                </form>

            <?php elseif($step == 3): ?>
                <div class="auth-title">NEW PASSWORD</div>
                <p class="auth-sub">Set your new secure password</p>

                <div class="step-indicator">
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                </div>

                <?php if($error): ?>
                    <div class="alert-error">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($verified_email); ?>">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" placeholder="At least 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>
                    <button type="submit" name="step3" class="btn-full">Change Password →</button>
                </form>

            <?php elseif($step == 4): ?>
                <div class="auth-title">SUCCESS!</div>
                <p class="auth-sub">Your password has been securely changed.</p>

                <div class="step-indicator">
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                </div>

                <div class="alert-success">✅ <?php echo $success; ?></div>

                <a href="login.php" class="btn-full" style="display:block;text-align:center;text-decoration:none;">
                    Go to Login →
                </a>
            <?php endif; ?>

            <?php if($step == 1 || $step == 2): ?>
                <div class="auth-link">
                    Remember password? <a href="login.php">Login Here</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>