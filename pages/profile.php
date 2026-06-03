<?php
require_once '../php/config.php';

// Login check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Update Profile Logic
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    if (empty($name) || empty($phone)) {
        $error = "Name and Phone are required!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must be exactly 10 digits!";
    } else {
        $update_query = "UPDATE users SET name='$name', phone='$phone', address='$address' WHERE id='$user_id'";
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['user_name'] = $name; // Update name in session
            $success = "Profile updated successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

// Fetch latest user details
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));

// Fetch User Orders
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$user_id' ORDER BY created_at DESC");

// Cart count
$cart_count = 0;
$cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='$user_id'");
$cc_row = mysqli_fetch_assoc($cc);
$cart_count = $cc_row['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .profile-page { padding: 120px 60px 80px; min-height: 100vh; }
        .profile-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 40px; margin-top: 40px; }
        
        .profile-card { background: var(--card); border: 1px solid var(--border); padding: 30px; height: fit-content; }
        .profile-title { font-family: 'Bebas Neue', sans-serif; font-size: 28px; letter-spacing: 2px; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 16px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .form-group input, .form-group textarea { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid var(--border); color: var(--white); padding: 14px 18px; font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--gold); background: rgba(200,169,110,0.05); }
        .form-group textarea { resize: vertical; min-height: 100px; }
        
        .btn-primary { width: 100%; background: var(--gold); color: var(--black); padding: 16px; border: none; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: all 0.3s; }
        .btn-primary:hover { background: var(--gold2); transform: translateY(-2px); }
        
        .alert-success { background: rgba(76,175,80,0.1); border: 1px solid #4caf50; color: #4caf50; padding: 12px 16px; font-size: 13px; margin-bottom: 20px; }
        .alert-error { background: rgba(232,68,68,0.1); border: 1px solid var(--red); color: var(--red); padding: 12px 16px; font-size: 13px; margin-bottom: 20px; }

        .order-history { background: var(--card); border: 1px solid var(--border); padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        td { padding: 16px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        
        .status-badge { padding: 4px 10px; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; border-radius: 4px; }
        .status-pending { background: rgba(255,165,0,0.1); color: orange; border: 1px solid orange; }
        .status-processing { background: rgba(33,150,243,0.1); color: #2196f3; border: 1px solid #2196f3; }
        .status-shipped { background: rgba(200,169,110,0.1); color: var(--gold); border: 1px solid var(--gold); }
        .status-delivered { background: rgba(76,175,80,0.1); color: #4caf50; border: 1px solid #4caf50; }
        .status-cancelled { background: rgba(232,68,68,0.1); color: var(--red); border: 1px solid var(--red); }

        @media(max-width:768px) {
            .profile-page { padding: 100px 20px 40px; }
            .profile-grid { grid-template-columns: 1fr; }
            table { display: block; overflow-x: auto; white-space: nowrap; }
        }
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
            <a href="profile.php" style="color:var(--gold);font-size:13px;text-decoration:none;font-weight:bold;">Hi, <?php echo $_SESSION['user_name']; ?>!</a>
            <a href="../php/logout.php" class="nav-btn">Logout</a>
            <a href="cart.php" class="cart-icon">🛒 <span class="cart-count"><?php echo $cart_count; ?></span></a>
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

    <div class="profile-page">
        <p class="section-label">★ Account Settings</p>
        <h1 class="section-title">MY PROFILE</h1>

        <div class="profile-grid">
            <div class="profile-card">
                <div class="profile-title">PERSONAL DETAILS</div>
                
                <?php if($success): ?> <div class="alert-success">✅ <?php echo $success; ?></div> <?php endif; ?>
                <?php if($error): ?> <div class="alert-error">❌ <?php echo $error; ?></div> <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>Email Address (Cannot be changed)</label>
                        <input type="email" value="<?php echo $user['email']; ?>" disabled style="opacity:0.6; cursor:not-allowed;">
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo $user['phone'] ?? ''; ?>" required pattern="[0-9]{10}" maxlength="10">
                    </div>
                    <div class="form-group">
                        <label>Default Delivery Address</label>
                        <textarea name="address" placeholder="Enter your full address here..."><?php echo $user['address'] ?? ''; ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn-primary">Save Changes →</button>
                </form>
            </div>

            <div class="order-history">
                <div class="profile-title">MY ORDERS</div>
                
                <?php if(mysqli_num_rows($orders) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = mysqli_fetch_assoc($orders)): ?>
                                <tr>
                                    <td style="color:var(--gold);font-weight:bold;">#NC<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                    <td>₹<?php echo number_format($order['total_amount'], 0); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color:var(--muted);text-align:center;padding:40px 0;">You haven't placed any orders yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>