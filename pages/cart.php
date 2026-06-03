<?php
require_once '../php/config.php';

// Login check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Cart items fetch karo
$sql = "SELECT cart.id, cart.quantity, cart.size, products.name, products.price, products.image, products.id as product_id 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

// Total calculate karo
$total = 0;
$cart_items = [];
while($item = mysqli_fetch_assoc($result)) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    $cart_items[] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .cart-page {
            padding: 120px 60px 80px;
            min-height: 100vh;
        }

        .cart-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
            margin-top: 40px;
        }

        .cart-item {
            display: flex;
            gap: 20px;
            padding: 24px;
            background: var(--card);
            border: 1px solid rgba(255,255,255,0.04);
            margin-bottom: 16px;
            transition: border-color 0.3s;
        }

        .cart-item:hover { border-color: var(--border); }

        .cart-item-img {
            width: 100px;
            height: 130px;
            object-fit: cover;
            background: #1a1a1a;
            flex-shrink: 0;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-cat {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .cart-item-name {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .cart-item-size {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 16px;
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 0;
            border: 1px solid var(--border);
        }

        .qty-btn {
            background: none;
            border: none;
            color: var(--white);
            width: 34px;
            height: 34px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover { background: rgba(200,169,110,0.1); }

        .qty-num {
            width: 40px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            border-left: 1px solid var(--border);
            border-right: 1px solid var(--border);
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-btn {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 13px;
            cursor: pointer;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: color 0.3s;
            text-decoration: none;
        }

        .remove-btn:hover { color: var(--red); }

        .cart-item-price {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 24px;
            color: var(--gold);
            white-space: nowrap;
        }

        /* Order Summary */
        .order-summary {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 30px;
            height: fit-content;
            position: sticky;
            top: 90px;
        }

        .summary-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 24px;
            letter-spacing: 2px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            font-size: 14px;
        }

        .summary-row span:first-child { color: var(--muted); }

        .summary-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 16px 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .summary-total-label {
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .summary-total-amt {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 32px;
            color: var(--gold);
        }

        .cod-badge {
            background: rgba(200,169,110,0.1);
            border: 1px solid var(--border);
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--muted);
        }

        .empty-cart {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-cart-icon { font-size: 80px; margin-bottom: 20px; }

        .empty-cart h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 40px;
            margin-bottom: 10px;
        }

        .empty-cart p {
            color: var(--muted);
            margin-bottom: 30px;
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
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="nav-actions">
            <a href="profile.php" style="color:var(--gold);font-size:13px;text-decoration:none;font-weight:bold;transition:opacity 0.3s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Hi, <?php echo $_SESSION['user_name']; ?>!</a>
            <a href="../php/logout.php" class="nav-btn">Logout</a>
            <a href="cart.php" class="cart-icon">
                🛒 <span class="cart-count"><?php echo count($cart_items); ?></span>
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

    <div class="cart-page">
        <p class="section-label">★ Your cart</p>
        <h1 class="section-title">SHOPPING CART</h1>

        <?php if(count($cart_items) > 0): ?>
        <div class="cart-container">

            <!-- Cart Items -->
            <div class="cart-items-list">
                <?php foreach($cart_items as $item): ?>
                <div class="cart-item" id="cart-item-<?php echo $item['id']; ?>">
                    <img class="cart-item-img"
                         src="../<?php echo $item['image']; ?>"
                         alt="<?php echo $item['name']; ?>"
                         onerror="this.src='https://via.placeholder.com/100x130/161616/c8a96e?text=IMG'">
                    <div class="cart-item-info">
                        <p class="cart-item-cat">NEW_COLLECTION</p>
                        <h3 class="cart-item-name"><?php echo $item['name']; ?></h3>
                        <p class="cart-item-size">Size: <?php echo $item['size'] ?? 'M'; ?></p>
                        <div class="cart-item-controls">
                            <div class="qty-control">
                                <button class="qty-btn" onclick="updateQty(<?php echo $item['id']; ?>, -1)">−</button>
                                <span class="qty-num" id="qty-<?php echo $item['id']; ?>"><?php echo $item['quantity']; ?></span>
                                <button class="qty-btn" onclick="updateQty(<?php echo $item['id']; ?>, 1)">+</button>
                            </div>
                            <a href="../php/remove_cart.php?id=<?php echo $item['id']; ?>" class="remove-btn">Remove</a>
                        </div>
                    </div>
                    <div class="cart-item-price">
                        ₹<?php echo number_format($item['subtotal'], 0); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-title">ORDER SUMMARY</div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($total, 0); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span style="color:#4caf50;"><?php echo $total >= 999 ? 'FREE' : '₹99'; ?></span>
                </div>
                <div class="summary-row">
                    <span>Discount</span>
                    <span style="color:var(--red);">−₹0</span>
                </div>

                <hr class="summary-divider">

                <div class="summary-total">
                    <span class="summary-total-label">Total</span>
                    <span class="summary-total-amt">
                        ₹<?php echo number_format($total >= 999 ? $total : $total + 99, 0); ?>
                    </span>
                </div>

                <div class="cod-badge">
                    🚚 Cash on Delivery Available
                </div>

                <a href="checkout.php" class="btn-primary" style="display:block;text-align:center;width:100%;">
                    Proceed to Checkout →
                </a>
                <a href="products.php" class="btn-ghost" style="display:block;text-align:center;width:100%;margin-top:12px;">
                    Continue Shopping
                </a>
            </div>
        </div>

        <?php else: ?>
        <div class="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <h3>YOUR CART IS EMPTY</h3>
            <p>Add some products to your Cart!</p>
            <a href="products.php" class="btn-primary">Shop Now →</a>
        </div>
        <?php endif; ?>
    </div>

    <script src="../js/main.js"></script>
    <script>
        function updateQty(cartId, change) {
            fetch('../php/update_cart.php?id=' + cartId + '&change=' + change)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.qty <= 0) {
                        document.getElementById('cart-item-' + cartId).remove();
                    } else {
                        document.getElementById('qty-' + cartId).textContent = data.qty;
                    }
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>