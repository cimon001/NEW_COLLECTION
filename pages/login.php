<?php
require_once '../php/config.php';

$error = '';

// Redirect to homepage if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "enter your email and password";
    } else {
        // Find user in database
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {
                
                // 🚨 NAYA LOCK: VIP PASS CHECK (is_verified) 🚨
                if (isset($user['is_verified']) && $user['is_verified'] == 0) {
                    // User ne OTP verify nahi kiya hai!
                    $_SESSION['temp_email'] = $user['email'];
                    
                    // Ek naya OTP generate karke firse bhej dete hain taaki problem na aaye
                    $new_otp = rand(100000, 999999);
                    mysqli_query($conn, "UPDATE users SET otp='$new_otp' WHERE id='{$user['id']}'");
                    
                    require_once '../php/send_email.php';
                    if(function_exists('sendEmail')) {
                        sendEmail($user['email'], "NEW_COLLECTION Verification", "Hello {$user['name']},\n\nYour new 6-digit OTP code is: $new_otp\n\nPlease enter this code to verify your account.\n\nThanks!");
                    }
                    
                    // JavaScript Redirect to OTP Page (Kyunki header() fail ho sakta hai)
                    echo "<script>window.location.href = 'verify_otp.php';</script>";
                    exit();
                }

                // Set session (Agar account verified hai)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                // Set cart count on login
                $cart_count_query = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='{$user['id']}'");
                $cart_count_row = mysqli_fetch_assoc($cart_count_query);
                $_SESSION['cart_count'] = $cart_count_row['total'] ?? 0;
                
                // Welcome back notification
                require_once '../php/notification_helper.php';
                addNotification($conn, $user['id'], '👋 Welcome back, ' . $user['name'] . '!', 'welcome');

                // Redirect to homepage (JavaScript redirect)
                echo "<script>window.location.href = '../index.php';</script>";
                exit();
            } else {
                $error = "Wrong password!";
            }
        } else {
            $error = "email is not registered!";
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
    <title>Login — NEW_COLLECTION</title>
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
        .input-wrapper { position: relative; width: 100%; }
        .input-wrapper input { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid var(--border); color: var(--white); padding: 14px 45px 14px 18px; font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none; transition: border-color 0.3s; box-sizing: border-box; }
        .input-wrapper input:focus { border-color: var(--gold); background: rgba(200,169,110,0.05); }
        .input-wrapper input::placeholder { color: var(--muted); }
        .hidden-text { -webkit-text-security: disc; }
        .toggle-eye { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 16px; user-select: none; opacity: 0.7; transition: opacity 0.3s; }
        .toggle-eye:hover { opacity: 1; }
        .btn-full { width: 100%; background: var(--gold); color: var(--black); padding: 16px; border: none; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: all 0.3s; margin-top: 10px; }
        .btn-full:hover { background: var(--gold2); transform: translateY(-2px); }
        .auth-link { text-align: center; margin-top: 24px; font-size: 14px; color: var(--muted); }
        .auth-link a { color: var(--gold); text-decoration: none; }
        .auth-link a:hover { text-decoration: underline; }
        .alert-error { background: rgba(232,68,68,0.1); border: 1px solid var(--red); color: var(--red); padding: 12px 16px; font-size: 13px; margin-bottom: 20px; text-align: center; }
        .forgot-link { text-align: right; margin-top: -12px; margin-bottom: 20px; }
        .forgot-link a { color: var(--muted); font-size: 12px; text-decoration: none; transition: color 0.3s; }
        .forgot-link a:hover { color: var(--gold); }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="../index.php" class="nav-logo">NEW_COLLECTION</a>
        <ul class="nav-links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="products.php">Shop</a></li>
            <li><a href="products.php?category=hoodie">Hoodies</a></li>
            <li><a href="products.php?category=jacket">Jackets</a></li>
        </ul>
        <div class="nav-actions">
            <a href="register.php" class="nav-btn">Register</a>
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
            <div class="auth-title">WELCOME BACK</div>
            <p class="auth-sub">Login to your account</p>

            <?php if($error): ?>
                <div class="alert-error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="login-email" name="email" class="hidden-text" placeholder="email@example.com" autocomplete="username" required>
                        <span class="toggle-eye" onclick="toggleVis('login-email', this)">👁️</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="login-password" name="password" placeholder="Enter your password!" autocomplete="current-password" required>
                        <span class="toggle-eye" onclick="toggleVis('login-password', this)">👁️</span>
                    </div>
                </div>
                <div class="forgot-link">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>
                <button type="submit" class="btn-full">Login →</button>
            </form>

            <div class="auth-link">
            No Account? <a href="register.php">Register Here</a>
            </div>
        </div>
    </div>

    <script>
        function toggleVis(id, icon) {
            let el = document.getElementById(id);
            if (id === 'login-email') {
                if (el.classList.contains('hidden-text')) {
                    el.classList.remove('hidden-text');
                    icon.textContent = '🙈';
                } else {
                    el.classList.add('hidden-text');
                    icon.textContent = '👁️';
                }
            } else {
                if (el.type === 'password') {
                    el.type = 'text';
                    icon.textContent = '🙈';
                } else {
                    el.type = 'password';
                    icon.textContent = '👁️';
                }
            }
        }
    </script>
    <script src="../js/main.js"></script>
</body>
</html>