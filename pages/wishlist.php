<?php
require_once '../php/config.php';

$wishlist_items = [];
$wishlist_count = 0;

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT p.* FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id='$user_id' ORDER BY w.created_at DESC");
    while($item = mysqli_fetch_assoc($result)) {
        $wishlist_items[] = $item;
    }
    $wishlist_count = count($wishlist_items);
}

$cart_count = 0;
if(isset($_SESSION['user_id'])) {
    $cart_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM cart WHERE user_id='{$_SESSION['user_id']}'"));
    $cart_count = $cart_result['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .wishlist-page { padding: 120px 60px 80px; min-height: 100vh; }
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-top: 40px;
        }
        .empty-wishlist {
            text-align: center;
            padding: 80px 20px;
            color: var(--muted);
        }
        .empty-wishlist .empty-icon { font-size: 64px; margin-bottom: 20px; }
        .empty-wishlist h2 { font-family: 'Bebas Neue', sans-serif; font-size: 36px; margin-bottom: 10px; color: var(--white); }
        .empty-wishlist p { font-size: 14px; margin-bottom: 24px; }
        .btn-shop { background: var(--gold); color: var(--black); padding: 14px 32px; font-weight: 700; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; text-decoration: none; display: inline-block; }
        @media(max-width:768px) {
            .wishlist-page { padding: 100px 20px 40px; }
            .wishlist-grid { grid-template-columns: repeat(2, 1fr); }
        }
        .wishlist-grid .product-img-wrap {
    height: 280px;
    overflow: hidden;
}

.wishlist-grid .product-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #fff;
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
            <?php if(isset($_SESSION['user_id'])): ?>
                <span style="color:var(--gold);font-size:13px;">Hi, <?php echo $_SESSION['user_name']; ?>!</span>
                <a href="../php/logout.php" class="nav-btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-btn">Login</a>
            <?php endif; ?>
            <a href="wishlist.php" class="wishlist-icon">
                🤍 <span class="wishlist-count" id="wishlist-count"><?php echo $wishlist_count; ?></span>
            </a>
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

    <div class="wishlist-page">
        <p class="section-label">★ Saved Items</p>
        <h1 class="section-title">MY WISHLIST</h1>

        <?php if(empty($wishlist_items)): ?>
            <div class="empty-wishlist">
                <div class="empty-icon">🤍</div>
                <h2>Your Wishlist is Empty!</h2>
                <p>Save your favorite products here</p>
                <a href="products.php" class="btn-shop">Shop Now →</a>
            </div>
        <?php else: ?>
            <p style="color:var(--muted);font-size:13px;margin-top:8px;"><?php echo $wishlist_count; ?> saved items</p>
            <div class="wishlist-grid">
                <?php foreach($wishlist_items as $item): ?>
                <div class="product-card">
                    <?php if($item['badge']): ?>
                        <div class="product-badge <?php echo strtolower($item['badge']) == 'sale' ? 'badge-sale' : 'badge-new'; ?>">
                            <?php echo $item['badge']; ?>
                        </div>
                    <?php endif; ?>
                    <div class="product-img-wrap">
                        <img src="../<?php echo $item['image']; ?>"
                             alt="<?php echo $item['name']; ?>"
                             onerror="this.src='https://via.placeholder.com/300x380/161616/c8a96e?text=IMG'">
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo ucfirst($item['category']); ?></div>
                        <div class="product-name"><?php echo $item['name']; ?></div>
                        <div class="product-price">
                            ₹<?php echo number_format($item['price'], 0); ?>
                            <?php if($item['old_price']): ?>
                                <span class="product-old-price">₹<?php echo number_format($item['old_price'], 0); ?></span>
                            <?php endif; ?>
                        </div>
                        <div style="display:flex;gap:8px;margin-top:12px;">
                            <a href="product_detail.php?id=<?php echo $item['id']; ?>" class="btn-view" style="flex:1;text-align:center;">View →</a>
                            <button onclick="removeWishlist(<?php echo $item['id']; ?>, this)"
                                style="background:rgba(232,68,68,0.1);border:1px solid var(--red);color:var(--red);padding:10px 14px;cursor:pointer;font-size:16px;">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="../js/main.js"></script>
    <script>
    function removeWishlist(productId, btn) {
        fetch('../php/wishlist_toggle.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'product_id=' + productId
        })
        .then(r => r.json())
        .then(data => {
            if(data.status === 'removed') {
                btn.closest('.product-card').remove();
            }
        });
    }
    </script>
</body>
</html>
