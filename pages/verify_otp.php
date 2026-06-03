<?php
require_once '../php/config.php';

// Redirect if no session
if (!isset($_SESSION['temp_email'])) {
    header('Location: register.php');
    exit();
}

$email = $_SESSION['temp_email'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = mysqli_real_escape_string($conn, trim($_POST['otp']));

    $check_query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND otp='$entered_otp'");

    if (mysqli_num_rows($check_query) > 0) {
        $user = mysqli_fetch_assoc($check_query);

        mysqli_query($conn, "UPDATE users SET is_verified = 1, otp = NULL WHERE id='{$user['id']}'");

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        unset($_SESSION['temp_email']);

        $success = "Email verified successfully! Taking you to homepage...";
        echo "<script>setTimeout(() => { window.location.href = '../index.php'; }, 2000);</script>";

    } else {
        $error = "Invalid OTP! Please check your email and try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .auth-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--black); padding: 100px 20px; }
        .auth-box { background: var(--card); border: 1px solid var(--border); padding: 50px; width: 100%; max-width: 480px; text-align: center; }
        .auth-logo { font-family: 'Bebas Neue', sans-serif; font-size: 28px; letter-spacing: 4px; color: var(--gold); margin-bottom: 8px; }
        .auth-title { font-family: 'Bebas Neue', sans-serif; font-size: 36px; margin-bottom: 6px; letter-spacing: 2px; }
        .auth-sub { color: var(--muted); font-size: 14px; margin-bottom: 36px; line-height: 1.6; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .form-group input { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid var(--border); color: var(--white); padding: 14px 18px; font-family: 'DM Sans', sans-serif; font-size: 24px; text-align: center; letter-spacing: 8px; outline: none; transition: border-color 0.3s; font-weight: bold; box-sizing: border-box; }
        .form-group input:focus { border-color: var(--gold); background: rgba(200,169,110,0.05); }
        .btn-full { width: 100%; background: var(--gold); color: var(--black); padding: 16px; border: none; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: all 0.3s; margin-top: 10px; }
        .btn-full:hover { background: var(--gold2); transform: translateY(-2px); }
        .alert-error { background: rgba(232,68,68,0.1); border: 1px solid var(--red); color: var(--red); padding: 12px 16px; font-size: 13px; margin-bottom: 20px; }
        .alert-success { background: rgba(100,200,100,0.1); border: 1px solid #4caf50; color: #4caf50; padding: 12px 16px; font-size: 13px; margin-bottom: 20px; }
        .back-link { margin-top: 24px; font-size: 13px; }
        .back-link a { color: var(--muted); text-decoration: none; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px; }
        .back-link a:hover { color: var(--gold); }

        /* ===== RESEND OTP SECTION ===== */
        .resend-section {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 14px;
        }

        /* Countdown Ring */
        .countdown-wrap {
            position: relative;
            width: 64px;
            height: 64px;
        }
        .countdown-ring {
            width: 64px;
            height: 64px;
            transform: rotate(-90deg);
        }
        .countdown-ring circle {
            fill: none;
            stroke-width: 4;
        }
        .countdown-ring .track {
            stroke: rgba(255,255,255,0.08);
        }
        .countdown-ring .progress {
            stroke: var(--gold);
            stroke-linecap: round;
            stroke-dasharray: 175.93; /* 2 * PI * r (r=28) */
            stroke-dashoffset: 0;
            transition: stroke-dashoffset 1s linear;
        }
        .countdown-number {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'DM Sans', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--gold);
        }

        /* Resend button */
        .resend-text {
            font-size: 13px;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
        }
        .resend-text span { color: var(--white); font-weight: 500; }

        #resendBtn {
            display: none;
            background: transparent;
            border: 1px solid var(--gold);
            color: var(--gold);
            padding: 11px 28px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        #resendBtn:hover { background: var(--gold); color: var(--black); }
        #resendBtn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Sending spinner state */
        #resendBtn.sending::after {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 8px;
            vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .resend-msg {
            font-size: 12px;
            padding: 8px 14px;
            border-radius: 2px;
            display: none;
        }
        .resend-msg.success { background: rgba(100,200,100,0.1); border: 1px solid #4caf50; color: #4caf50; }
        .resend-msg.error   { background: rgba(232,68,68,0.1); border: 1px solid var(--red); color: var(--red); }
    </style>
</head>
<body>
    <div class="auth-page">
        <div class="auth-box">
            <div class="auth-logo">NEW_COLLECTION</div>
            <div class="auth-title">VERIFY EMAIL</div>
            <p class="auth-sub">We have sent a 6-digit verification code to<br><strong style="color:var(--white);"><?php echo htmlspecialchars($email); ?></strong></p>

            <?php if($error): ?>
                <div class="alert-error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Enter 6-Digit OTP</label>
                    <input type="text" name="otp" id="otpInput" placeholder="------" required maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code" inputmode="numeric">
                </div>
                <button type="submit" class="btn-full">Verify →</button>
            </form>

            <!-- ===== RESEND OTP SECTION ===== -->
            <div class="resend-section">

                <!-- Countdown Ring (visible during cooldown) -->
                <div class="countdown-wrap" id="countdownWrap">
                    <svg class="countdown-ring" viewBox="0 0 64 64">
                        <circle class="track" cx="32" cy="32" r="28"/>
                        <circle class="progress" id="ringProgress" cx="32" cy="32" r="28"/>
                    </svg>
                    <div class="countdown-number" id="countdownNum">30</div>
                </div>

                <p class="resend-text" id="resendHint">Resend OTP in <span id="countdownText">30</span>s</p>

                <!-- Resend button (hidden during cooldown) -->
                <button id="resendBtn" onclick="resendOTP()">↻ Resend OTP</button>

                <!-- Status message -->
                <div class="resend-msg" id="resendMsg"></div>
            </div>

            <div class="back-link">
                <a href="register.php">← Entered wrong email? Go back</a>
            </div>
        </div>
    </div>

    <script>
        // ===== COUNTDOWN TIMER =====
        const COOLDOWN    = 30;
        const circumference = 175.93; // 2 * PI * 28
        let timeLeft      = COOLDOWN;
        let timerInterval = null;

        const ringProgress  = document.getElementById('ringProgress');
        const countdownNum  = document.getElementById('countdownNum');
        const countdownText = document.getElementById('countdownText');
        const countdownWrap = document.getElementById('countdownWrap');
        const resendHint    = document.getElementById('resendHint');
        const resendBtn     = document.getElementById('resendBtn');
        const resendMsg     = document.getElementById('resendMsg');

        function startCountdown() {
            timeLeft = COOLDOWN;
            countdownWrap.style.display = 'block';
            resendHint.style.display    = 'block';
            resendBtn.style.display     = 'none';
            resendMsg.style.display     = 'none';

            // Reset ring
            ringProgress.style.strokeDashoffset = 0;
            countdownNum.textContent  = timeLeft;
            countdownText.textContent = timeLeft;

            timerInterval = setInterval(() => {
                timeLeft--;

                // Update ring (drain clockwise)
                const offset = circumference * (1 - timeLeft / COOLDOWN);
                ringProgress.style.strokeDashoffset = offset;

                countdownNum.textContent  = timeLeft;
                countdownText.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    // Hide countdown, show resend button
                    countdownWrap.style.display = 'none';
                    resendHint.style.display    = 'none';
                    resendBtn.style.display     = 'inline-block';
                }
            }, 1000);
        }

        // ===== RESEND OTP via FETCH =====
        function resendOTP() {
            resendBtn.disabled = true;
            resendBtn.classList.add('sending');
            resendBtn.textContent = 'Sending';
            resendMsg.style.display = 'none';

            fetch('../php/resend_otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=<?php echo urlencode($email); ?>'
            })
            .then(r => r.json())
            .then(data => {
                resendBtn.classList.remove('sending');
                resendBtn.textContent = '↻ Resend OTP';

                resendMsg.style.display = 'block';
                if (data.success) {
                    resendMsg.className = 'resend-msg success';
                    resendMsg.textContent = '✅ New OTP sent! Check your email.';
                    startCountdown(); // Restart cooldown
                } else {
                    resendMsg.className = 'resend-msg error';
                    resendMsg.textContent = '❌ ' + (data.message || 'Failed to send OTP. Try again.');
                    resendBtn.disabled = false;
                }
            })
            .catch(() => {
                resendBtn.classList.remove('sending');
                resendBtn.textContent = '↻ Resend OTP';
                resendMsg.style.display = 'block';
                resendMsg.className = 'resend-msg error';
                resendMsg.textContent = '❌ Network error. Please try again.';
                resendBtn.disabled = false;
            });
        }

        // Auto-start countdown when page loads
        startCountdown();

        // Auto-focus OTP input
        document.getElementById('otpInput').focus();
    </script>
</body>
</html>