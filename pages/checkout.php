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

// Agar cart empty hai
if (mysqli_num_rows($result) == 0) {
    header('Location: cart.php');
    exit();
}

$total = 0;
$cart_items = [];
while ($item = mysqli_fetch_assoc($result)) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    $cart_items[] = $item;
}

$shipping = $total >= 999 ? 0 : 99;
$grand_total = $total + $shipping;

// Cart count
$cart_count = count($cart_items);

// User details fetch karo
$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_result);

// Order place karo
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);

    $full_address = "$address, $city, $state - $pincode";

    if (empty($name) || empty($phone) || empty($address) || empty($city) || empty($pincode)) {
        $error = "Please fill all required fields!";
    } else {
        // Order insert karo
        $order_sql = "INSERT INTO orders (user_id, total_amount, payment_method, shipping_address, phone) 
                      VALUES ('$user_id', '$grand_total', 'Cash on Delivery', '$full_address', '$phone')";

        if (mysqli_query($conn, $order_sql)) {
            $order_id = mysqli_insert_id($conn);

            // Order items insert karo
            foreach ($cart_items as $item) {
                mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, size, price) 
                                     VALUES ('$order_id', '{$item['product_id']}', '{$item['quantity']}', '{$item['size']}', '{$item['price']}')");
            }

            // Cart clear karo
            mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id'");
            $_SESSION['cart_count'] = 0;

            // Order notification
            require_once '../php/notification_helper.php';
            if(function_exists('addNotification')) {
                addNotification($conn, $user_id, '🛍️ Your order #NC' . str_pad($order_id, 5, '0', STR_PAD_LEFT) . ' has been placed successfully!', 'order');
            }

            // Send email
            require_once '../php/send_email.php';
            if(function_exists('sendOrderEmail')) {
                $email_items = [];
                foreach ($cart_items as $ci) {
                    $email_items[] = [
                        'name' => $ci['name'],
                        'size' => $ci['size'] ?? 'M',
                        'quantity' => $ci['quantity'],
                        'price' => $ci['price']
                    ];
                }
                sendOrderEmail($user['email'], $user['name'], $order_id, $grand_total, $full_address, $email_items);
            }

            // Success page pe bhejo
            header("Location: order_success.php?order_id=$order_id");
            exit();
        } else {
            $error = "Order could not be placed, please try again!";
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
    <title>Checkout — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .checkout-page { padding: 120px 60px 80px; min-height: 100vh; }
        .checkout-grid { display: grid; grid-template-columns: 1fr 400px; gap: 40px; margin-top: 40px; }
        .checkout-form-section h2 { font-family: 'Bebas Neue', sans-serif; font-size: 28px; letter-spacing: 2px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border); color: var(--white); padding: 14px 18px; font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--gold); background: rgba(200, 169, 110, 0.05); }
        .form-group input::placeholder, .form-group textarea::placeholder { color: var(--muted); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        
        /* Locked field styling */
        .locked-field { background: rgba(0,0,0,0.3) !important; cursor: not-allowed; color: var(--gold) !important; font-weight: bold; }

        .order-summary { background: var(--card); border: 1px solid var(--border); padding: 30px; height: fit-content; position: sticky; top: 90px; }
        .summary-title { font-family: 'Bebas Neue', sans-serif; font-size: 24px; letter-spacing: 2px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border); }
        .order-item { display: flex; gap: 12px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.04); }
        .order-item img { width: 60px; height: 75px; object-fit: cover; background: #1a1a1a; }
        .order-item-info { flex: 1; }
        .order-item-name { font-size: 13px; font-weight: 500; margin-bottom: 4px; }
        .order-item-qty { font-size: 12px; color: var(--muted); }
        .order-item-price { font-size: 14px; font-weight: 700; color: var(--gold); white-space: nowrap; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
        .summary-row span:first-child { color: var(--muted); }
        .summary-divider { border: none; border-top: 1px solid var(--border); margin: 16px 0; }
        .summary-total { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .summary-total-label { font-size: 13px; letter-spacing: 2px; text-transform: uppercase; }
        .summary-total-amt { font-family: 'Bebas Neue', sans-serif; font-size: 32px; color: var(--gold); }
        .cod-box { background: rgba(200, 169, 110, 0.08); border: 1px solid var(--border); padding: 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
        .cod-box span:first-child { font-size: 24px; }
        .cod-box-text { font-size: 13px; }
        .cod-box-title { font-weight: 600; margin-bottom: 2px; }
        .cod-box-sub { color: var(--muted); font-size: 12px; }
        .btn-full { width: 100%; background: var(--gold); color: var(--black); padding: 18px; border: none; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: all 0.3s; }
        .btn-full:hover { background: var(--gold2); transform: translateY(-2px); }
        .alert-error { background: rgba(232, 68, 68, 0.1); border: 1px solid var(--red); color: var(--red); padding: 12px 16px; font-size: 13px; margin-bottom: 20px; }
        @media(max-width:768px) { .checkout-page { padding: 100px 20px 40px; } .checkout-grid { grid-template-columns: 1fr; } .form-row { grid-template-columns: 1fr; } }
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

    <div class="checkout-page">
        <p class="section-label">★ Almost Done</p>
        <h1 class="section-title">CHECKOUT</h1>

        <?php if ($error): ?>
            <div class="alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="checkout-grid">

                <div class="checkout-form-section">
                    <h2>SHIPPING DETAILS</h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" value="<?php echo $user['phone'] ?? ''; ?>" required pattern="[0-9]{10}" maxlength="10">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Street Address</label>
                        <textarea name="address" placeholder="House/flat number, street, area..." required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Pincode</label>
                            <input type="text" id="pincode" name="pincode" maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit PIN" required>
                            <small id="pin-error" style="color: #ff4444; display: none; margin-top: 5px;">Invalid PIN Code!</small>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" id="city" name="city" class="locked-field" placeholder="Auto-fills from PIN" readonly required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>State</label>
                        <input type="text" id="state" name="state" class="locked-field" placeholder="Auto-fills from PIN" readonly required>
                    </div>

                    <h2 style="margin-top:30px;">PAYMENT METHOD</h2>
                    <div class="cod-box">
                        <span>🚚</span>
                        <div class="cod-box-text">
                            <div class="cod-box-title">Cash on Delivery</div>
                            <div class="cod-box-sub">Pay when your order arrives at your door</div>
                        </div>
                    </div>
                </div>

                <div class="order-summary">
                    <div class="summary-title">ORDER SUMMARY</div>

                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <img src="../<?php echo $item['image']; ?>"
                                alt="<?php echo $item['name']; ?>"
                                onerror="this.src='https://via.placeholder.com/60x75/161616/c8a96e?text=IMG'">
                            <div class="order-item-info">
                                <div class="order-item-name"><?php echo $item['name']; ?></div>
                                <div class="order-item-qty">Qty: <?php echo $item['quantity']; ?> · Size: <?php echo $item['size'] ?? 'M'; ?></div>
                            </div>
                            <div class="order-item-price">₹<?php echo number_format($item['subtotal'], 0); ?></div>
                        </div>
                    <?php endforeach; ?>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₹<?php echo number_format($total, 0); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span style="color:#4caf50;"><?php echo $shipping == 0 ? 'FREE' : '₹99'; ?></span>
                    </div>

                    <hr class="summary-divider">

                    <div class="summary-total">
                        <span class="summary-total-label">Total</span>
                        <span class="summary-total-amt">₹<?php echo number_format($grand_total, 0); ?></span>
                    </div>

                    <button type="submit" class="btn-full">
                        Place Order — COD →
                    </button>

                    <a href="cart.php" style="display:block;text-align:center;margin-top:14px;font-size:12px;color:var(--muted);letter-spacing:1px;text-decoration:none;">
                        ← Back to Cart
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script src="../js/main.js"></script>

    <script>
        document.getElementById('pincode').addEventListener('input', function() {
            let pin = this.value.trim();
            
            if (pin.length === 6) {
                fetch('https://api.zippopotam.us/in/' + pin)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Invalid PIN');
                    }
                    return response.json();
                })
                .then(data => {
                    let place = data.places[0];
                    document.getElementById('city').value = place['place name'];
                    document.getElementById('state').value = place['state'];
                    document.getElementById('pin-error').style.display = 'none';
                })
                .catch(error => {
                    document.getElementById('city').value = '';
                    document.getElementById('state').value = '';
                    document.getElementById('pin-error').style.display = 'block';
                });
            } else {
                document.getElementById('city').value = '';
                document.getElementById('state').value = '';
                document.getElementById('pin-error').style.display = 'none';
            }
        });
    </script>
</body>
</html>