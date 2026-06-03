<?php
require_once '../php/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_raw = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $country_code = mysqli_real_escape_string($conn, trim($_POST['country_code'] ?? '+91'));
    // Store full number with country code
    $phone = $country_code . $phone_raw;

    // Backend Validation
    if (empty($name) || empty($email) || empty($password) || empty($phone_raw)) {
        $error = "Please fill all fields!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } elseif (!preg_match('/^[0-9]{5,15}$/', $phone_raw)) {
        $error = "Please enter a valid phone number (digits only)!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        // 🔐 CHECK 1: Email already registered?
        $check_email = mysqli_query($conn, "SELECT id, is_verified FROM users WHERE email='$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $existing_user = mysqli_fetch_assoc($check_email);
            if ($existing_user['is_verified'] == 0) {
                $error = "This email is registered but not verified. Please verify your email first.";
            } else {
                $error = "This email address is already registered! Please login instead.";
            }

        // 🔐 CHECK 2: Phone number already registered by a VERIFIED user?
        } elseif (mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE phone='$phone' AND is_verified=1")) > 0) {
            $error = "This phone number is already linked to an existing account!";

        } else {
            // ✅ All checks passed — proceed with registration
            $otp = rand(100000, 999999);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Remove any old UNVERIFIED entry with same phone (cleanup)
            mysqli_query($conn, "DELETE FROM users WHERE phone='$phone' AND is_verified=0");

            // Insert new user
            $sql = "INSERT INTO users (name, email, password, phone, otp, is_verified)
                    VALUES ('$name', '$email', '$hashed_password', '$phone', '$otp', 0)";

            if (mysqli_query($conn, $sql)) {
                // Send OTP Email
                require_once '../php/send_email.php';
                $subject = "Your NEW_COLLECTION Verification Code";
                sendEmail($email, $subject, $otp);

                // Save email in session and redirect
                $_SESSION['temp_email'] = $email;
                header('Location: verify_otp.php');
                exit();

            } else {
                $error = "Something went wrong, please try again.";
            }
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
    <title>Register — NEW_COLLECTION</title>
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
        .form-group input { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid var(--border); color: var(--white); padding: 14px 18px; font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none; transition: border-color 0.3s; box-sizing: border-box; }
        .form-group input:focus { border-color: var(--gold); background: rgba(200,169,110,0.05); }
        .form-group input::placeholder { color: var(--muted); }
        .btn-full { width: 100%; background: var(--gold); color: var(--black); padding: 16px; border: none; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: all 0.3s; margin-top: 10px; }
        .btn-full:hover { background: var(--gold2); transform: translateY(-2px); }
        .auth-link { text-align: center; margin-top: 24px; font-size: 14px; color: var(--muted); }
        .auth-link a { color: var(--gold); text-decoration: none; }
        .auth-link a:hover { text-decoration: underline; }
        .alert-error { background: rgba(232,68,68,0.1); border: 1px solid var(--red); color: var(--red); padding: 12px 16px; font-size: 13px; margin-bottom: 20px; text-align: center; }
        .alert-success { background: rgba(100,200,100,0.1); border: 1px solid #4caf50; color: #4caf50; padding: 12px 16px; font-size: 13px; margin-bottom: 20px; text-align: center; }

        /* ===== PHONE FIELD WITH COUNTRY CODE ===== */
        .phone-wrapper { display: flex; gap: 0; }
        .country-select-wrap { position: relative; flex-shrink: 0; }
        .country-select-wrap select {
            appearance: none;
            -webkit-appearance: none;
            background: rgba(255,255,255,0.07);
            border: 1px solid var(--border);
            border-right: none;
            color: var(--white);
            padding: 14px 34px 14px 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            cursor: pointer;
            transition: border-color 0.3s;
            min-width: 115px;
            height: 51px;
        }
        .country-select-wrap select:focus { border-color: var(--gold); background: rgba(200,169,110,0.08); }
        .country-select-wrap select option { background: #1a1a1a; color: #fff; }
        .country-select-wrap::after {
            content: '▾';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold);
            pointer-events: none;
            font-size: 12px;
        }
        .phone-wrapper input[type='tel'] {
            flex: 1;
            border-left: none !important;
        }
        .phone-wrapper input[type='tel']:focus { border-color: var(--gold); }
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
            <div class="auth-title">CREATE ACCOUNT</div>
            <p class="auth-sub">Join the collection — it's free!</p>

            <?php if($error): ?>
                <div class="alert-error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert-success">✅ <?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="" onsubmit="return validateRegister(event)">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="regEmail" placeholder="Enter your email" required pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$">
                </div>

                <!-- PHONE WITH COUNTRY CODE -->
                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="phone-wrapper">
                        <div class="country-select-wrap">
                            <select name="country_code" id="countryCode">
                                <option value="+91">🇮🇳 +91</option>
                                <option value="+1">🇺🇸 +1</option>
                                <option value="+1">🇨🇦 +1</option>
                                <option value="+44">🇬🇧 +44</option>
                                <option value="+61">🇦🇺 +61</option>
                                <option value="+64">🇳🇿 +64</option>
                                <option value="+92">🇵🇰 +92</option>
                                <option value="+880">🇧🇩 +880</option>
                                <option value="+94">🇱🇰 +94</option>
                                <option value="+977">🇳🇵 +977</option>
                                <option value="+971">🇦🇪 +971</option>
                                <option value="+966">🇸🇦 +966</option>
                                <option value="+974">🇶🇦 +974</option>
                                <option value="+965">🇰🇼 +965</option>
                                <option value="+968">🇴🇲 +968</option>
                                <option value="+973">🇧🇭 +973</option>
                                <option value="+20">🇪🇬 +20</option>
                                <option value="+27">🇿🇦 +27</option>
                                <option value="+234">🇳🇬 +234</option>
                                <option value="+254">🇰🇪 +254</option>
                                <option value="+49">🇩🇪 +49</option>
                                <option value="+33">🇫🇷 +33</option>
                                <option value="+39">🇮🇹 +39</option>
                                <option value="+34">🇪🇸 +34</option>
                                <option value="+31">🇳🇱 +31</option>
                                <option value="+46">🇸🇪 +46</option>
                                <option value="+47">🇳🇴 +47</option>
                                <option value="+45">🇩🇰 +45</option>
                                <option value="+358">🇫🇮 +358</option>
                                <option value="+7">🇷🇺 +7</option>
                                <option value="+86">🇨🇳 +86</option>
                                <option value="+81">🇯🇵 +81</option>
                                <option value="+82">🇰🇷 +82</option>
                                <option value="+65">🇸🇬 +65</option>
                                <option value="+60">🇲🇾 +60</option>
                                <option value="+66">🇹🇭 +66</option>
                                <option value="+62">🇮🇩 +62</option>
                                <option value="+63">🇵🇭 +63</option>
                                <option value="+84">🇻🇳 +84</option>
                                <option value="+55">🇧🇷 +55</option>
                                <option value="+52">🇲🇽 +52</option>
                                <option value="+54">🇦🇷 +54</option>
                                <option value="+56">🇨🇱 +56</option>
                                <option value="+57">🇨🇴 +57</option>
                            </select>
                        </div>
                        <input type="tel" name="phone" id="regPhone" placeholder="Phone number" required maxlength="15">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="regPass" placeholder="At least 6 characters" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" id="regConfirm" placeholder="Confirm your password" required minlength="6">
                </div>
                <button type="submit" class="btn-full">Send OTP →</button>
            </form>

            <div class="auth-link">
                Already have an account? <a href="login.php">Login Here</a>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
    <script>
        function validateRegister(event) {
            const phone = document.getElementById('regPhone').value.trim();
            const pass = document.getElementById('regPass').value;
            const confirm = document.getElementById('regConfirm').value;

            // Allow 5-15 digits (works for all countries)
            const phonePattern = /^[0-9]{5,15}$/;

            if (!phone) {
                alert("⚠️ Please enter your phone number!");
                event.preventDefault();
                return false;
            }

            if (!phonePattern.test(phone)) {
                alert("⚠️ Please enter a valid phone number (digits only, 5-15 digits)!");
                event.preventDefault();
                return false;
            }

            if (pass !== confirm) {
                alert("⚠️ Passwords do not match!");
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</body>
</html>