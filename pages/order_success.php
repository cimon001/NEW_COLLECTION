<?php
require_once '../php/config.php';

// Login check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = $_SESSION['user_id'];

// Fetch order details
$order = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id'"
));

if (!$order) {
    header('Location: ../index.php');
    exit();
}

// // Fetch order items
$items_result = mysqli_query(
    $conn,
    "SELECT order_items.*, products.name, products.image 
     FROM order_items 
     JOIN products ON order_items.product_id = products.id 
     WHERE order_items.order_id='$order_id'"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed! — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .success-page {
            padding: 120px 60px 80px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .success-box {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 60px;
            max-width: 700px;
            width: 100%;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(76, 175, 80, 0.1);
            border: 2px solid #4caf50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 24px;
            animation: popIn 0.5s ease;
        }

        @keyframes popIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            70% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 48px;
            letter-spacing: 2px;
            margin-bottom: 10px;
            color: #4caf50;
        }

        .success-sub {
            color: var(--muted);
            font-size: 15px;
            margin-bottom: 40px;
            line-height: 1.7;
        }

        .order-info {
            background: rgba(200, 169, 110, 0.06);
            border: 1px solid var(--border);
            padding: 24px;
            margin-bottom: 30px;
            text-align: left;
        }

        .order-info-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px;
            letter-spacing: 2px;
            color: var(--gold);
            margin-bottom: 16px;
        }

        .order-info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            font-size: 14px;
        }

        .order-info-row:last-child {
            border-bottom: none;
        }

        .order-info-row span:first-child {
            color: var(--muted);
        }

        .order-info-row span:last-child {
            font-weight: 500;
        }

        .order-items-list {
            text-align: left;
            margin-bottom: 30px;
        }

        .order-items-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px;
            letter-spacing: 2px;
            margin-bottom: 16px;
            color: var(--gold);
        }

        .success-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            align-items: center;
        }

        .success-item img {
            width: 50px;
            height: 65px;
            object-fit: cover;
            background: #1a1a1a;
        }

        .success-item-name {
            font-size: 14px;
            font-weight: 500;
        }

        .success-item-detail {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        .success-item-price {
            margin-left: auto;
            font-weight: 700;
            color: var(--gold);
        }

        .success-btns {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cod-reminder {
            background: rgba(200, 169, 110, 0.08);
            border: 1px solid var(--border);
            padding: 16px 24px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }

        .cod-reminder-icon {
            font-size: 28px;
        }

        .cod-reminder-text {
            font-size: 13px;
            line-height: 1.6;
        }

        .cod-reminder-text strong {
            color: var(--gold);
        }

        @media print {

            .navbar,
            .success-btns,
            .cod-reminder,
            .no-print {
                display: none !important;
            }

            body {
                background: white;
                color: black;
            }

            .success-box {
                border: none;
                padding: 20px;
            }

            .success-title {
                color: #2d7a2d;
            }

            .order-info-title,
            .order-items-title {
                color: #8a6d3b;
            }

            .success-item-price {
                color: #8a6d3b;
            }
        }
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
        </ul>
        <div class="nav-actions">
            <span style="color:var(--gold);font-size:13px;">Hi, <?php echo $_SESSION['user_name']; ?>!</span>
            <a href="../php/logout.php" class="nav-btn">Logout</a>
            <a href="cart.php" class="cart-icon">
                🛒 <span class="cart-count">0</span>
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

    <div class="success-page">
        <div class="success-box">

            <!-- Success Icon -->
            <div class="success-icon">✓</div>
            <div class="success-title">ORDER PLACED!</div>
            <p class="success-sub">
                Thank you <?php echo $_SESSION['user_name']; ?>! 🎉<br>
                Your order has been placed successfully.<br>
                We will deliver it soon!
            </p>

            <!-- Order Info -->
            <div class="order-info">
                <div class="order-info-title">ORDER DETAILS</div>
                <div class="order-info-row">
                    <span>Order ID</span>
                    <span style="color:var(--gold);">#NC<?php echo str_pad($order_id, 5, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="order-info-row">
                    <span>Order Date</span>
                    <span><?php echo date('d M Y', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="order-info-row">
                    <span>Delivery Address</span>
                    <span><?php echo $order['shipping_address']; ?></span>
                </div>
                <div class="order-info-row">
                    <span>Phone</span>
                    <span><?php echo $order['phone']; ?></span>
                </div>
                <div class="order-info-row">
                    <span>Total Amount</span>
                    <span style="color:var(--gold);font-size:18px;">₹<?php echo number_format($order['total_amount'], 0); ?></span>
                </div>
                <div class="order-info-row">
                    <span>Status</span>
                    <span style="color:#4caf50;">✓ Confirmed</span>
                </div>
            </div>

            <!-- COD Reminder -->
            <div class="cod-reminder">
                <div class="cod-reminder-icon">🚚</div>
                <div class="cod-reminder-text">
                    <strong>Cash on Delivery</strong><br>
                    Please keep <strong>₹<?php echo number_format($order['total_amount'], 0); ?></strong> cash ready at delivery.
                    Expected delivery: <strong>3-5 business days</strong>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-items-list">
                <div class="order-items-title">ITEMS ORDERED</div>
                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                    <div class="success-item">
                        <img src="../<?php echo $item['image']; ?>"
                            alt="<?php echo $item['name']; ?>"
                            onerror="this.src='https://via.placeholder.com/50x65/161616/c8a96e?text=IMG'">
                        <div>
                            <div class="success-item-name"><?php echo $item['name']; ?></div>
                            <div class="success-item-detail">
                                Qty: <?php echo $item['quantity']; ?> · Size: <?php echo $item['size'] ?? 'M'; ?>
                            </div>
                        </div>
                        <div class="success-item-price">
                            ₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Buttons -->
            <div class="success-btns">
                <a href="products.php" class="btn-primary">Continue Shopping →</a>
                <a href="orders.php" class="btn-ghost">View My Orders</a>
                <button onclick="downloadInvoice()" class="btn-ghost" style="border-color:var(--gold);color:var(--gold);cursor:pointer;">
                    🖨️ Download Invoice
                </button>
            </div>

        </div>
    </div>

    <script src="../js/main.js"></script>
    <script>
        function downloadInvoice() {
            const invoiceContent = `
        <html>
        <head>
            <title>Invoice #NC<?php echo str_pad($order_id, 5, '0', STR_PAD_LEFT); ?></title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; color: #333; }
                .header { text-align: center; border-bottom: 3px solid #c8a96e; padding-bottom: 20px; margin-bottom: 30px; }
                .brand { font-size: 32px; font-weight: bold; color: #c8a96e; letter-spacing: 4px; }
                .invoice-title { font-size: 18px; color: #666; margin-top: 8px; }
                .section { margin-bottom: 24px; }
                .section-title { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #c8a96e; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 12px; }
                .row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; border-bottom: 1px solid #f5f5f5; }
                .row span:first-child { color: #888; }
                .row span:last-child { font-weight: 600; }
                table { width: 100%; border-collapse: collapse; }
                th { background: #f9f5ed; padding: 10px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
                td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
                .total-row { font-weight: bold; font-size: 16px; color: #c8a96e; }
                .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; color: #888; font-size: 12px; }
                .status { background: #e8f5e9; color: #2e7d32; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="brand">NEW_COLLECTION</div>
                <div class="invoice-title">INVOICE / RECEIPT</div>
            </div>

            <div class="section">
                <div class="section-title">Order Details</div>
                <div class="row"><span>Order ID</span><span>#NC<?php echo str_pad($order_id, 5, '0', STR_PAD_LEFT); ?></span></div>
                <div class="row"><span>Order Date</span><span><?php echo date('d M Y', strtotime($order['created_at'])); ?></span></div>
                <div class="row"><span>Customer Name</span><span><?php echo $_SESSION['user_name']; ?></span></div>
                <div class="row"><span>Phone</span><span><?php echo $order['phone']; ?></span></div>
                <div class="row"><span>Delivery Address</span><span><?php echo $order['shipping_address']; ?></span></div>
                <div class="row"><span>Payment Method</span><span>Cash on Delivery</span></div>
                <div class="row"><span>Status</span><span><span class="status">✓ Confirmed</span></span></div>
            </div>

            <div class="section">
                <div class="section-title">Items Ordered</div>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Re-fetch items for invoice
                        $inv_items = mysqli_query($conn, "SELECT order_items.*, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id='$order_id'");
                        while ($inv = mysqli_fetch_assoc($inv_items)):
                        ?>
                        <tr>
                            <td><?php echo $inv['name']; ?></td>
                            <td><?php echo $inv['size'] ?? 'M'; ?></td>
                            <td><?php echo $inv['quantity']; ?></td>
                            <td>Rs.<?php echo number_format($inv['price'], 0); ?></td>
                            <td>Rs.<?php echo number_format($inv['price'] * $inv['quantity'], 0); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <tr class="total-row">
                            <td colspan="4" style="text-align:right;padding-right:10px;">TOTAL</td>
                            <td>Rs.<?php echo number_format($order['total_amount'], 0); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="footer">
                <p>Thank you for shopping with NEW_COLLECTION!</p>
                <p>Contact: cimonsharma95@gmail.com | +91 88378 94309 | Punjab, India</p>
                <p style="margin-top:8px;color:#c8a96e;">© 2026 NEW_COLLECTION. All Rights Reserved.</p>
            </div>
        </body>
        </html>
    `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(invoiceContent);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
    </script>
</body>

</html>