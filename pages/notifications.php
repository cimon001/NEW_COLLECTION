<?php
require_once '../php/config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Mark all as read
mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE user_id='$user_id'");

// Fetch all notifications
$notifs = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id='$user_id' ORDER BY created_at DESC");

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
    <title>Notifications — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .notif-page { padding: 120px 60px 80px; min-height: 100vh; }
        .notif-list { margin-top: 40px; display: flex; flex-direction: column; gap: 12px; }
        .notif-card {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: border-color 0.3s;
        }
        .notif-card:hover { border-color: var(--gold); }
        .notif-icon { font-size: 28px; width: 50px; text-align: center; flex-shrink: 0; }
        .notif-msg { font-size: 14px; color: var(--white); flex: 1; }
        .notif-time { font-size: 12px; color: var(--muted); white-space: nowrap; }
        .notif-type-order { border-left: 3px solid var(--gold); }
        .notif-type-welcome { border-left: 3px solid #4caf50; }
        .notif-type-offer { border-left: 3px solid #2196f3; }
        .notif-type-general { border-left: 3px solid var(--muted); }
        .empty-notif {
            text-align: center;
            padding: 80px 20px;
            color: var(--muted);
        }
        .empty-notif h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 36px;
            margin-bottom: 10px;
            color: var(--white);
        }
        @media(max-width:768px) { .notif-page { padding: 100px 20px 40px; } }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="../index.php" class="nav-logo">NEW_COLLECTION</a>
        <ul class="nav-links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="products.php">Shop</a></li>
            <li><a href="products.php?category=hoodie">Hoodies</a></li>
            <li><a href="products.php?category=jacket">Jackets</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="nav-actions">
            <span style="color:var(--gold);font-size:13px;">Hi, <?php echo $_SESSION['user_name']; ?>!</span>
            <a href="../php/logout.php" class="nav-btn">Logout</a>
            <a href="cart.php" class="cart-icon">
                🛒 <span class="cart-count"><?php echo $cart_count; ?></span>
            </a>
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

    <div class="notif-page">
        <p class="section-label">★ Account</p>
        <h1 class="section-title">NOTIFICATIONS</h1>

        <?php if(mysqli_num_rows($notifs) > 0): ?>
        <div class="notif-list">
            <?php while($notif = mysqli_fetch_assoc($notifs)): ?>
            <?php
            $icon = '🔔';
            if($notif['type'] == 'order') $icon = '📦';
            elseif($notif['type'] == 'welcome') $icon = '👋';
            elseif($notif['type'] == 'offer') $icon = '🏷️';
            elseif($notif['type'] == 'delivered') $icon = '✅';
            elseif($notif['type'] == 'cancelled') $icon = '❌';
            elseif($notif['type'] == 'new_product') $icon = '🆕';
            ?>
            <div class="notif-card notif-type-<?php echo $notif['type']; ?>">
                <div class="notif-icon"><?php echo $icon; ?></div>
                <div class="notif-msg"><?php echo $notif['message']; ?></div>
                <div class="notif-time"><?php echo date('d M, h:i A', strtotime($notif['created_at'])); ?></div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-notif">
            <div style="font-size:64px;margin-bottom:20px;">🔔</div>
            <h3>NO NOTIFICATIONS</h3>
            <p>You're all caught up!</p>
        </div>
        <?php endif; ?>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>