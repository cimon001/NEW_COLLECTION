 <?php
require_once '../php/config.php';

// Login check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Cart count
$cart_count = 0;
$cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='$user_id'");
$cc_row = mysqli_fetch_assoc($cc);
$cart_count = $cc_row['total'] ?? 0;

// Fetch orders
$orders_result = mysqli_query($conn, 
    "SELECT * FROM orders WHERE user_id='$user_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .orders-page {
            padding: 120px 60px 80px;
            min-height: 100vh;
        }

        .order-card {
            background: var(--card);
            border: 1px solid rgba(255,255,255,0.04);
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }

        .order-card:hover { border-color: var(--border); }

        .order-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            flex-wrap: wrap;
            gap: 12px;
        }

        .order-id {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 20px;
            letter-spacing: 2px;
            color: var(--gold);
        }

        .order-date {
            font-size: 13px;
            color: var(--muted);
        }

        .order-status {
            padding: 6px 16px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* 🎯 STATUS COLORS FIX KIYE HAIN */
        .status-pending { background: rgba(255,165,0,0.1); color: orange; border: 1px solid orange; }
        .status-processing { background: rgba(33,150,243,0.1); color: #2196f3; border: 1px solid #2196f3; }
        .status-shipped { background: rgba(156,39,176,0.1); color: #9c27b0; border: 1px solid #9c27b0; }
        .status-delivered { background: rgba(76,175,80,0.15); color: #4caf50; border: 1px solid #4caf50; }
        .status-cancelled { background: rgba(232,68,68,0.1); color: var(--red); border: 1px solid var(--red); }

        .order-card-body {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .order-items-preview {
            display: flex;
            gap: 8px;
        }

        .order-item-thumb {
            width: 55px;
            height: 70px;
            object-fit: cover;
            background: #1a1a1a;
            border: 1px solid var(--border);
        }

        .order-meta-row {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .order-meta-row span {
            color: var(--white);
            font-weight: 500;
        }

        .order-total {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            color: var(--gold);
        }

        .order-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 8px 20px;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-sm-gold {
            background: var(--gold);
            color: var(--black);
            border: none;
        }

        .btn-sm-gold:hover { background: var(--gold2); }

        .btn-sm-ghost {
            background: none;
            color: var(--muted);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .btn-sm-ghost:hover { border-color: var(--gold); color: var(--gold); }

        .empty-orders {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-orders-icon { font-size: 80px; margin-bottom: 20px; }

        .empty-orders h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 40px;
            margin-bottom: 10px;
        }

        .empty-orders p { color: var(--muted); margin-bottom: 30px; }

        @media(max-width:768px) {
            .orders-page { padding: 100px 20px 40px; }
            .order-card-header { flex-direction: column; align-items: flex-start; }
        }

        /* ===== ORDER TRACKING TIMELINE ===== */
        .order-timeline {
            padding: 20px 24px;
            border-top: 1px solid rgba(255,255,255,0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .order-timeline::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 60px;
            right: 60px;
            height: 2px;
            background: rgba(255,255,255,0.06);
            z-index: 0;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            z-index: 1;
            flex: 1;
        }

        .timeline-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.1);
            background: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.3s;
        }

        .timeline-step.done .timeline-dot {
            background: var(--gold);
            border-color: var(--gold);
        }

        .timeline-step.active .timeline-dot {
            border-color: var(--gold);
            box-shadow: 0 0 12px rgba(200,169,110,0.4);
        }

        .timeline-step.cancelled .timeline-dot {
            background: var(--red);
            border-color: var(--red);
        }

        .timeline-label {
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
            text-align: center;
        }

        .timeline-step.done .timeline-label,
        .timeline-step.active .timeline-label {
            color: var(--gold);
        }

        .timeline-step.cancelled .timeline-label {
            color: var(--red);
        }

        .timeline-line {
            flex: 1;
            height: 2px;
            background: rgba(255,255,255,0.06);
            z-index: 0;
        }

        .timeline-line.done {
            background: var(--gold);
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

    <div class="orders-page">
        <p class="section-label">★ Account</p>
        <h1 class="section-title">MY ORDERS</h1>

        <?php if(mysqli_num_rows($orders_result) > 0): ?>

            <?php while($order = mysqli_fetch_assoc($orders_result)): ?>

            <?php
            // Fetch order items
            $items = mysqli_query($conn, 
                "SELECT order_items.*, products.image, products.name 
                 FROM order_items 
                 JOIN products ON order_items.product_id = products.id 
                 WHERE order_items.order_id='{$order['id']}'");
            $items_count = mysqli_num_rows($items);
            ?>

            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <div class="order-id">#NC<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></div>
                        <div class="order-date"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></div>
                    </div>
                    <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </div>
                </div>

                <div class="order-card-body">
                    <div class="order-items-preview">
                        <?php 
                        $count = 0;
                        while($item = mysqli_fetch_assoc($items)): 
                            if($count >= 3) break;
                        ?>
                        <img class="order-item-thumb"
                             src="../<?php echo $item['image']; ?>"
                             alt="<?php echo $item['name']; ?>"
                             onerror="this.src='https://via.placeholder.com/55x70/161616/c8a96e?text=IMG'">
                        <?php 
                            $count++;
                        endwhile; 
                        if($items_count > 3): ?>
                            <div style="width:55px;height:70px;background:var(--card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:12px;color:var(--muted);">
                                +<?php echo $items_count - 3; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="order-meta">
                        <div class="order-meta-row">
                            Items: <span><?php echo $items_count; ?> products</span>
                        </div>
                        <div class="order-meta-row">
                            Payment: <span>Cash on Delivery</span>
                        </div>
                        <div class="order-meta-row">
                            Address: <span><?php echo strlen($order['shipping_address']) > 40 ? substr($order['shipping_address'], 0, 40) . '...' : $order['shipping_address']; ?></span>
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:var(--muted);margin-bottom:4px;letter-spacing:1px;">TOTAL</div>
                        <div class="order-total">₹<?php echo number_format($order['total_amount'], 0); ?></div>
                    </div>

                    <div class="order-actions">
                        <a href="order_success.php?order_id=<?php echo $order['id']; ?>" 
                           class="btn-sm btn-sm-gold">View Details</a>
                        <?php if(strtolower($order['status']) == 'pending'): ?>
                            <a href="../php/cancel_order.php?id=<?php echo $order['id']; ?>" 
                               class="btn-sm btn-sm-ghost"
                               onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</a>
                        <?php endif; ?>
                    </div>
                </div> <?php if(strtolower($order['status']) != 'cancelled'): ?>
                <div class="order-timeline">
                    <?php
                    $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                    $icons = ['🛍️', '📦', '🚚', '✅'];
                    $labels = ['Ordered', 'Processing', 'Shipped', 'Delivered'];
                    
                    // Normalize case to prevent bugs
                    $currentStatus = strtolower($order['status']);
                    $current = array_search($currentStatus, $statuses);
                    if($current === false) $current = 0;
                    ?>
                    <?php foreach($statuses as $i => $s): ?>
                        <?php if($i > 0): ?>
                            <div class="timeline-line <?php echo $i <= $current ? 'done' : ''; ?>"></div>
                        <?php endif; ?>
                        <div class="timeline-step <?php echo $i < $current ? 'done' : ($i == $current ? 'active' : ''); ?>">
                            <div class="timeline-dot"><?php echo $icons[$i]; ?></div>
                            <div class="timeline-label"><?php echo $labels[$i]; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="order-timeline">
                    <div class="timeline-step cancelled">
                        <div class="timeline-dot">❌</div>
                        <div class="timeline-label">Order Cancelled</div>
                    </div>
                </div>
                <?php endif; ?>

            </div> <?php endwhile; ?>

        <?php else: ?>
        <div class="empty-orders">
            <div class="empty-orders-icon">📦</div>
            <h3>NO ORDERS YET</h3>
            <p>You haven't placed any orders yet — let's go shopping!</p>
            <a href="products.php" class="btn-primary">Shop Now →</a>
        </div>
        <?php endif; ?>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>