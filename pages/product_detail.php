<?php
require_once '../php/config.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id == 0) {
    header('Location: products.php');
    exit();
}

// fetch product
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id='$product_id'"));

if (!$product) {
    header('Location: products.php');
    exit();
}

// Cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='{$_SESSION['user_id']}'");
    $cc_row = mysqli_fetch_assoc($cc);
    $cart_count = $cc_row['total'] ?? 0;
}
// Wishlist check
$is_wishlisted = false;
$wishlist_count = 0;
if (isset($_SESSION['user_id'])) {
    $wl_check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id='{$_SESSION['user_id']}' AND product_id='$product_id'");
    $is_wishlisted = mysqli_num_rows($wl_check) > 0;
    $wl_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist WHERE user_id='{$_SESSION['user_id']}'"));
    $wishlist_count = $wl_count['total'];
}

// Highlights array
$highlights = $product['highlights'] ? explode(',', $product['highlights']) : [];

// // Build images array
$images = [];
if ($product['image']) $images[] = ['src' => '../' . $product['image'], 'type' => 'image', 'label' => 'Front'];
if ($product['image_back']) $images[] = ['src' => '../' . $product['image_back'], 'type' => 'image', 'label' => 'Back'];
if ($product['image_detail1']) $images[] = ['src' => '../' . $product['image_detail1'], 'type' => 'image', 'label' => 'Detail 1'];
if ($product['image_detail2']) $images[] = ['src' => '../' . $product['image_detail2'], 'type' => 'image', 'label' => 'Detail 2'];
if ($product['video']) $images[] = ['src' => '../' . $product['video'], 'type' => 'video', 'label' => 'Video'];

// Related products
$related = mysqli_query($conn, "SELECT * FROM products WHERE category='{$product['category']}' AND id != '$product_id' ORDER BY RAND() LIMIT 4");
// Fetch reviews
$reviews = mysqli_query($conn, "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id=u.id WHERE r.product_id='$product_id' ORDER BY r.created_at DESC");
$review_stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE product_id='$product_id'"));
$avg_rating = round($review_stats['avg'], 1);
$total_reviews = $review_stats['total'];

// // Check if user already reviewed
$user_reviewed = false;
if (isset($_SESSION['user_id'])) {
    $check = mysqli_query($conn, "SELECT id FROM reviews WHERE product_id='$product_id' AND user_id='{$_SESSION['user_id']}'");
    $user_reviewed = mysqli_num_rows($check) > 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .detail-page {
            padding: 100px 60px 80px;
            min-height: 100vh;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 80px;
        }

        /* ===== IMAGE GALLERY ===== */
        .gallery-wrapper {
            position: sticky;
            top: 90px;
        }

        .gallery-main {
            width: 100%;
            aspect-ratio: 3/4;
            background: var(--card);
            overflow: hidden;
            position: relative;
            margin-bottom: 12px;
        }

        .gallery-main img,
        .gallery-main video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-main video {
            cursor: pointer;
        }

        .gallery-thumbs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .gallery-thumb {
            width: 70px;
            height: 90px;
            background: var(--card);
            border: 2px solid transparent;
            cursor: pointer;
            overflow: hidden;
            transition: border-color 0.3s;
            position: relative;
        }

        .gallery-thumb.active {
            border-color: var(--gold);
        }

        .gallery-thumb img,
        .gallery-thumb video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
        }

        .gallery-thumb-video::after {
            content: '▶';
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.5);
            color: var(--gold);
            font-size: 20px;
        }

        .gallery-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: var(--gold);
            color: var(--black);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            padding: 4px 10px;
            z-index: 2;
        }

        .gallery-badge.sale {
            background: var(--red);
            color: white;
        }

        .gallery-badge.new-in {
            background: var(--black);
            color: var(--white);
            border: 1px solid var(--border);
        }

        /* Gallery Arrows */
        .gallery-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(200, 169, 110, 0.3);
            color: var(--gold);
            width: 44px;
            height: 44px;
            font-size: 22px;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            border-radius: 50%;
        }

        .gallery-arrow:hover {
            background: var(--gold);
            color: var(--black);
        }

        .gallery-arrow-left {
            left: 12px;
        }

        .gallery-arrow-right {
            right: 12px;
        }

        /* ===== PRODUCT INFO ===== */
        .detail-info {
            padding: 10px 0;
        }

        .detail-cat {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 12px;
        }

        .detail-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 48px;
            line-height: 0.95;
            letter-spacing: -1px;
            margin-bottom: 20px;
        }

        .detail-price {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .detail-price-new {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 40px;
            color: var(--white);
        }

        .detail-price-old {
            font-size: 20px;
            color: var(--muted);
            text-decoration: line-through;
        }

        .detail-price-save {
            background: var(--red);
            color: white;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
        }

        .detail-desc {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border);
        }

        /* Highlights */
        .highlights-title {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 14px;
        }

        .highlights-list {
            list-style: none;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border);
        }

        .highlights-list li {
            font-size: 13px;
            color: var(--muted);
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .highlights-list li::before {
            content: '✦';
            color: var(--gold);
            font-size: 10px;
            flex-shrink: 0;
        }

        /* Size */
        .size-label {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .sizes {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .size-btn {
            background: none;
            border: 1px solid var(--border);
            color: var(--white);
            width: 50px;
            height: 50px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .size-btn:hover,
        .size-btn.active {
            border-color: var(--gold);
            background: rgba(200, 169, 110, 0.1);
            color: var(--gold);
        }

        /* Quantity */
        .qty-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            border: 1px solid var(--border);
        }

        .qty-btn {
            background: none;
            border: none;
            color: var(--white);
            width: 44px;
            height: 44px;
            font-size: 20px;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover {
            background: rgba(200, 169, 110, 0.1);
        }

        .qty-num {
            width: 50px;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            border-left: 1px solid var(--border);
            border-right: 1px solid var(--border);
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Buttons */
        .detail-btns {
            display: flex;
            gap: 14px;
            margin-bottom: 24px;
        }

        .btn-add {
            flex: 1;
            background: var(--gold);
            color: var(--black);
            border: none;
            padding: 18px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-add:hover {
            background: var(--gold2);
            transform: translateY(-2px);
        }

        .btn-buy {
            flex: 1;
            background: transparent;
            border: 2px solid var(--gold);
            color: var(--gold);
            padding: 18px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-buy:hover {
            background: var(--gold);
            color: var(--black);
            transform: translateY(-2px);
        }

        /* Features */
        .detail-features {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .detail-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: var(--muted);
        }

        .detail-feature span:first-child {
            font-size: 18px;
        }

        /* Related */
        .related-section {
            margin-top: 60px;
        }

        @media(max-width:768px) {
            .detail-page {
                padding: 100px 20px 40px;
            }

            .detail-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .detail-name {
                font-size: 36px;
            }

            .gallery-wrapper {
                position: relative;
                top: 0;
            }
        }

        /* Reviews */
        .reviews-section {
            margin-top: 80px;
            padding-top: 60px;
            border-top: 1px solid var(--border);
        }

        .reviews-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .rating-summary {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .rating-big {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 70px;
            color: var(--gold);
            line-height: 1;
        }

        .stars-display span {
            font-size: 24px;
        }

        .write-review {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 30px;
            margin-bottom: 40px;
        }

        .star-selector {
            display: flex;
            gap: 8px;
            cursor: pointer;
        }

        .star-pick {
            font-size: 32px;
            color: #444;
            transition: color 0.2s;
            cursor: pointer;
        }

        .star-pick.active {
            color: var(--gold);
        }

        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .review-card {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 24px;
        }

        .review-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .review-avatar {
            width: 44px;
            height: 44px;
            background: var(--gold);
            color: var(--black);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }

        .review-stars {
            margin-left: auto;
        }

        .review-stars span {
            font-size: 18px;
        }

        .review-text {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.7;
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                $notif_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications WHERE user_id='{$_SESSION['user_id']}' AND is_read=0");
                $notif_count = mysqli_fetch_assoc($notif_result)['total'];
                ?>
                <a href="notifications.php" class="notif-icon" style="position:relative;font-size:22px;text-decoration:none;">
                    🔔 <?php if($notif_count > 0): ?><span style="position:absolute;top:-8px;right:-8px;background:#e84444;color:white;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><?php echo $notif_count; ?></span><?php endif; ?>
                </a>
                <div class="user-menu" id="userMenu">
                    <div class="user-menu-btn" onclick="toggleUserMenu()">
                        <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                        <span class="user-name-text">Hi, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>!</span>
                        <span style="font-size:10px;margin-left:4px;">▼</span>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-dropdown-header">
                            <div class="user-dropdown-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                            <div>
                                <div style="font-weight:600;font-size:14px;"><?php echo $_SESSION['user_name']; ?></div>
                                <div style="font-size:11px;color:#888;"><?php echo $_SESSION['user_email'] ?? ''; ?></div>
                            </div>
                        </div>
                        <div class="user-dropdown-divider"></div>
                        <a href="profile.php" class="user-dropdown-item"><span>👤</span> My Profile</a>
                        <a href="orders.php" class="user-dropdown-item"><span>📦</span> My Orders</a>
                        <a href="wishlist.php" class="user-dropdown-item">
                            <span>❤️</span> Wishlist
                            <?php if ($wishlist_count > 0): ?>
                                <span style="margin-left:auto;background:var(--red);color:white;font-size:10px;padding:2px 6px;border-radius:10px;"><?php echo $wishlist_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="user-dropdown-divider"></div>
                        <a href="../php/logout.php" class="user-dropdown-item" style="color:var(--red);"><span>🚪</span> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="nav-btn">Login</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-icon">
                🛒 <span class="cart-count"><?php echo $cart_count; ?></span>
            </a>
        </div>

        <style>
            .user-menu {
                position: relative;
            }

            .user-menu-btn {
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                padding: 6px 10px;
                border: 1px solid var(--border);
                transition: all 0.3s;
            }

            .user-menu-btn:hover {
                border-color: var(--gold);
                background: rgba(200, 169, 110, 0.05);
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                background: var(--gold);
                color: var(--black);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Bebas Neue', sans-serif;
                font-size: 16px;
                font-weight: 700;
            }

            .user-name-text {
                font-size: 13px;
                color: var(--gold);
                font-weight: 500;
            }

            .user-dropdown {
                position: absolute;
                top: calc(100% + 12px);
                right: 0;
                width: 220px;
                background: var(--card);
                border: 1px solid var(--border);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
                display: none;
                z-index: 9999;
                animation: dropDown 0.2s ease;
            }

            .user-dropdown.open {
                display: block;
            }

            @keyframes dropDown {
                from {
                    opacity: 0;
                    transform: translateY(-8px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .user-dropdown-header {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 16px;
                background: rgba(200, 169, 110, 0.06);
            }

            .user-dropdown-avatar {
                width: 40px;
                height: 40px;
                background: var(--gold);
                color: var(--black);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Bebas Neue', sans-serif;
                font-size: 18px;
                flex-shrink: 0;
            }

            .user-dropdown-divider {
                height: 1px;
                background: var(--border);
                margin: 4px 0;
            }

            .user-dropdown-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 16px;
                font-size: 13px;
                color: var(--white);
                text-decoration: none;
                transition: all 0.2s;
            }

            .user-dropdown-item:hover {
                background: rgba(200, 169, 110, 0.08);
                color: var(--gold);
            }

            .user-dropdown-item span:first-child {
                font-size: 16px;
                width: 20px;
            }
        </style>

        <script>
            function toggleUserMenu() {
                document.getElementById('userDropdown').classList.toggle('open');
            }
            document.addEventListener('click', function(e) {
                const menu = document.getElementById('userMenu');
                if (menu && !menu.contains(e.target)) {
                    document.getElementById('userDropdown').classList.remove('open');
                }
            });
        </script>
        <button class="hamburger" id="hamburger" onclick="toggleMobileNav()" aria-label="Menu"><span></span><span></span><span></span></button>
</nav>
<div class="mobile-nav" id="mobileNav">
    <a href="../index.php">Home</a>
    <a href="products.php">Shop</a>
    <a href="products.php?category=hoodie">Hoodies</a>
    <a href="products.php?category=jacket">Jackets</a>
    <a href="contact.php">Contact</a>
    <a href="about.php">About</a>
    <a href="custom_designs_gallery.php">Custom</a>
    <div class="mobile-nav-actions">
        <a href="cart.php" style="font-size:16px;font-family:'DM Sans',sans-serif;padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.06);">🛒 Cart</a>
        <a href="profile.php" style="font-size:16px;font-family:'DM Sans',sans-serif;padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.06);">👤 Profile</a>
        <a href="orders.php" style="font-size:16px;font-family:'DM Sans',sans-serif;padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.06);">📦 Orders</a>
        <a href="../php/logout.php" style="font-size:16px;font-family:'DM Sans',sans-serif;padding:14px 0;color:#e84444;">🚪 Logout</a>
    </div>
</div>
<script>function toggleMobileNav(){var b=document.getElementById("hamburger"),n=document.getElementById("mobileNav");b.classList.toggle("open");n.classList.toggle("open");document.body.style.overflow=n.classList.contains("open")?"hidden":"";}document.addEventListener("click",function(e){var n=document.getElementById("mobileNav"),b=document.getElementById("hamburger");if(n&&n.classList.contains("open")&&!n.contains(e.target)&&!b.contains(e.target)){n.classList.remove("open");b.classList.remove("open");document.body.style.overflow="";}});</script>

    <div class="detail-page">

        <!-- Breadcrumb -->
        <div style="font-size:13px;color:var(--muted);margin-bottom:30px;">
            <a href="../index.php" style="color:var(--muted);">Home</a> →
            <a href="products.php" style="color:var(--muted);">Shop</a> →
            <span style="color:var(--gold);"><?php echo $product['name']; ?></span>
        </div>

        <div class="detail-grid">

            <!-- LEFT — Image Gallery -->
            <div class="gallery-wrapper">

                <!-- Main Image/Video -->
                <div class="gallery-main" id="galleryMain" style="position:relative;">
                    <?php if (count($images) > 1): ?>
                        <button class="gallery-arrow gallery-arrow-left" onclick="prevMedia()">‹</button>
                        <button class="gallery-arrow gallery-arrow-right" onclick="nextMedia()">›</button>
                    <?php endif; ?>
                    <?php if (!empty($images) && $images[0]['type'] == 'video'): ?>
                        <video id="mainVideo" controls>
                            <source src="<?php echo $images[0]['src']; ?>" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <img id="mainImg"
                            src="<?php echo !empty($images) ? $images[0]['src'] : 'https://via.placeholder.com/400x533/161616/c8a96e?text=No+Image'; ?>"
                            alt="<?php echo $product['name']; ?>">
                    <?php endif; ?>

                    <?php if ($product['badge']): ?>
                        <div class="gallery-badge <?php echo strtolower(str_replace(' ', '-', $product['badge'])); ?>">
                            <?php echo $product['badge']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Thumbnails -->
                <?php if (count($images) > 1): ?>
                    <div class="gallery-thumbs">
                        <?php foreach ($images as $i => $img): ?>
                            <div class="gallery-thumb <?php echo $i == 0 ? 'active' : ''; ?> <?php echo $img['type'] == 'video' ? 'gallery-thumb-video' : ''; ?>"
                                onclick="switchMedia(<?php echo $i; ?>)">
                                <?php if ($img['type'] == 'video'): ?>
                                    <video src="<?php echo $img['src']; ?>" muted></video>
                                <?php else: ?>
                                    <img src="<?php echo $img['src']; ?>"
                                        alt="<?php echo $img['label']; ?>"
                                        onerror="this.src='https://via.placeholder.com/70x90/161616/c8a96e?text=<?php echo $img['label']; ?>'">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT — Product Info -->
            <div class="detail-info">
                <div class="detail-cat">★ <?php echo ucfirst($product['category']); ?></div>
                <h1 class="detail-name"><?php echo $product['name']; ?></h1>

                <div class="detail-price">
                    <span class="detail-price-new">₹<?php echo number_format($product['price'], 0); ?></span>
                    <?php if ($product['old_price']): ?>
                        <span class="detail-price-old">₹<?php echo number_format($product['old_price'], 0); ?></span>
                        <?php $save = round((1 - $product['price'] / $product['old_price']) * 100); ?>
                        <span class="detail-price-save">-<?php echo $save; ?>% OFF</span>
                    <?php endif; ?>
                </div>

                <p class="detail-desc"><?php echo $product['description']; ?></p>

                <!-- Key Highlights -->
                <?php if (!empty($highlights)): ?>
                    <div class="highlights-title">KEY HIGHLIGHTS</div>
                    <ul class="highlights-list">
                        <?php foreach ($highlights as $highlight): ?>
                            <li><?php echo trim($highlight); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Size Selection -->
                <div class="size-label">Select Size</div>
                <div class="sizes" id="sizeSelector">
                    <?php
                    $sizes = explode(',', $product['sizes']);
                    foreach ($sizes as $i => $size):
                    ?>
                        <button class="size-btn <?php echo $i == 0 ? 'active' : ''; ?>"
                            onclick="selectSize(this, '<?php echo trim($size); ?>')">
                            <?php echo trim($size); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Quantity -->
                <div class="qty-row">
                    <div class="size-label" style="margin-bottom:0;">Quantity</div>
                    <div class="qty-control">
                        <button class="qty-btn" onclick="changeQty(-1)">−</button>
                        <span class="qty-num" id="qtyNum">1</span>
                        <button class="qty-btn" onclick="changeQty(1)">+</button>
                    </div>
                    <span style="font-size:13px;color:var(--muted);">
                        <?php echo $product['stock']; ?> in stock
                    </span>
                </div>

                <!-- Buttons -->
                <div class="detail-btns">
                    <button class="btn-add" onclick="addToCartDetail()">+ Add to Cart</button>
                    <button class="btn-buy" onclick="buyNow()">Buy Now →</button>
                </div>
                <div style="margin-bottom:24px;">
                    <button onclick="toggleWishlistDetail(<?php echo $product['id']; ?>, this)"
                        style="background:none;border:1px solid var(--border);color:var(--white);padding:12px 20px;font-family:'DM Sans',sans-serif;font-size:13px;cursor:pointer;transition:all 0.3s;display:flex;align-items:center;gap:8px;">
                        <span id="wishlistIcon"><?php echo $is_wishlisted ? '❤️' : '🤍'; ?></span>
                        <span id="wishlistText"><?php echo $is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?></span>
                    </button>
                </div>

                <!-- Features -->
                <div class="detail-features">
                    <div class="detail-feature"><span>🚚</span> Free shipping on orders above ₹999</div>
                    <div class="detail-feature"><span>🔄</span> 7 day easy returns</div>
                    <div class="detail-feature"><span>🚀</span> Cash on Delivery available</div>
                    <div class="detail-feature"><span>🧵</span> Premium 400GSM heavyweight cotton</div>
                    <div class="detail-feature">
                        <span>📱</span>
                        <a href="https://wa.me/?text=Check out this product: <?php echo urlencode($product['name']); ?> - Rs.<?php echo $product['price']; ?> <?php echo urlencode('http://localhost/NEW_COLLECTION/pages/product_detail.php?id=' . $product['id']); ?>"
                            target="_blank"
                            style="color:var(--gold);text-decoration:none;">
                            Share on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- REVIEWS SECTION -->
        <div class="reviews-section">
            <div class="reviews-header">
                <div>
                    <p class="section-label">★ Customer Feedback</p>
                    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:40px;margin-bottom:0;">REVIEWS & RATINGS</h2>
                </div>
                <div class="rating-summary">
                    <div class="rating-big"><?php echo $avg_rating ?: '0'; ?></div>
                    <div>
                        <div class="stars-display">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span style="color:<?php echo $i <= round($avg_rating) ? '#c8a96e' : '#444'; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <p style="font-size:13px;color:var(--muted);"><?php echo $total_reviews; ?> reviews</p>
                    </div>
                </div>
            </div>

            <!-- Write Review -->
            <?php if (isset($_SESSION['user_id']) && !$user_reviewed): ?>
                <div class="write-review">
                    <h3 style="font-size:16px;font-weight:600;margin-bottom:20px;letter-spacing:1px;">WRITE A REVIEW</h3>
                    <div class="star-selector" id="starSelector">
                        <span onclick="setRating(1)" class="star-pick">★</span>
                        <span onclick="setRating(2)" class="star-pick">★</span>
                        <span onclick="setRating(3)" class="star-pick">★</span>
                        <span onclick="setRating(4)" class="star-pick">★</span>
                        <span onclick="setRating(5)" class="star-pick">★</span>
                    </div>
                    <textarea id="reviewText" placeholder="Share your experience..."
                        style="width:100%;background:var(--card);border:1px solid var(--border);
                         color:var(--white);padding:16px;font-family:'DM Sans',sans-serif;
                         font-size:14px;resize:vertical;min-height:120px;margin:16px 0;"></textarea>
                    <button onclick="submitReview()" class="btn-add" style="max-width:200px;">
                        Submit Review
                    </button>
                </div>
            <?php elseif (!isset($_SESSION['user_id'])): ?>
                <div class="write-review">
                    <p style="color:var(--muted);">Please <a href="login.php" style="color:var(--gold);">login</a> to write a review!</p>
                </div>
            <?php elseif ($user_reviewed): ?>
                <div class="write-review">
                    <p style="color:var(--gold);">✓ You have already reviewed this product!</p>
                </div>
            <?php endif; ?>

            <!-- Reviews List -->
            <div class="reviews-list" id="reviewsList">
                <?php if ($total_reviews == 0): ?>
                    <p style="color:var(--muted);text-align:center;padding:40px 0;">No reviews yet — be the first to review!</p>
                <?php else: ?>
                    <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                        <div class="review-card">
                            <div class="review-top">
                                <div class="review-avatar"><?php echo strtoupper(substr($review['name'], 0, 1)); ?></div>
                                <div>
                                    <p style="font-weight:600;font-size:14px;"><?php echo $review['name']; ?></p>
                                    <p style="font-size:12px;color:var(--muted);"><?php echo date('d M Y', strtotime($review['created_at'])); ?></p>
                                </div>
                                <div class="review-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span style="color:<?php echo $i <= $review['rating'] ? '#c8a96e' : '#444'; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="review-text"><?php echo $review['review']; ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (mysqli_num_rows($related) > 0): ?>
            <div class="related-section">
                <p class="section-label">★ You May Also Like</p>
                <h2 style="font-family:'Bebas Neue',sans-serif;font-size:40px;margin-bottom:30px;">RELATED PRODUCTS</h2>
                <div class="products-grid">
                    <?php while ($rel = mysqli_fetch_assoc($related)): ?>
                        <div class="product-card" onclick="window.location.href='product_detail.php?id=<?php echo $rel['id']; ?>'">
                            <div class="product-img">
                                <img src="../<?php echo $rel['image']; ?>"
                                    alt="<?php echo $rel['name']; ?>"
                                    onerror="this.src='https://via.placeholder.com/300x400/161616/c8a96e?text=<?php echo urlencode($rel['name']); ?>'">
                                <?php if ($rel['badge']): ?>
                                    <div class="product-badge <?php echo strtolower(str_replace(' ', '-', $rel['badge'])); ?>">
                                        <?php echo $rel['badge']; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="product-actions"
                                    onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">
                                    + Add to Cart
                                </div>
                            </div>
                            <div class="product-info">
                                <p class="product-cat"><?php echo ucfirst($rel['category']); ?></p>
                                <h3 class="product-name"><?php echo $rel['name']; ?></h3>
                                <div class="product-price">
                                    <span class="price-new">₹<?php echo number_format($rel['price'], 0); ?></span>
                                    <?php if ($rel['old_price']): ?>
                                        <span class="price-old">₹<?php echo number_format($rel['old_price'], 0); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div style="margin-top:10px;">
                                    <a href="product_detail.php?id=<?php echo $rel['id']; ?>"
                                        style="font-size:12px;color:var(--gold);letter-spacing:1px;">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <script src="../js/main.js"></script>
    <script>
        // Media data from PHP
        const mediaItems = <?php echo json_encode($images); ?>;

        let selectedSize = '<?php echo trim(explode(',', $product['sizes'])[0]); ?>';
        let qty = 1;

        // Switch media in gallery

        let currentMediaIndex = 0;

        function prevMedia() {
            currentMediaIndex = (currentMediaIndex - 1 + mediaItems.length) % mediaItems.length;
            switchMedia(currentMediaIndex);
        }

        function nextMedia() {
            currentMediaIndex = (currentMediaIndex + 1) % mediaItems.length;
            switchMedia(currentMediaIndex);
        }

        function switchMedia(index) {
            currentMediaIndex = index;
            const item = mediaItems[index];
            const mainDiv = document.getElementById('galleryMain');
            // Badge preserve karo
            const badge = mainDiv.querySelector('.gallery-badge');

            if (item.type === 'video') {
                mainDiv.innerHTML = `
    <button class="gallery-arrow gallery-arrow-left" onclick="prevMedia()">‹</button>
    <button class="gallery-arrow gallery-arrow-right" onclick="nextMedia()">›</button>
    <video id="mainVideo" controls style="width:100%;height:100%;object-fit:cover;">
        <source src="${item.src}" type="video/mp4">
    </video>
`;
            } else {
                mainDiv.innerHTML = `
    <button class="gallery-arrow gallery-arrow-left" onclick="prevMedia()">‹</button>
    <button class="gallery-arrow gallery-arrow-right" onclick="nextMedia()">›</button>
    <img id="mainImg" src="${item.src}" 
         alt="${item.label}"
         style="width:100%;height:100%;object-fit:cover;"
         onerror="this.src='https://via.placeholder.com/400x533/161616/c8a96e?text=${item.label}'">
`;
            }

            if (badge) mainDiv.appendChild(badge);

            // Active thumb update karo
            document.querySelectorAll('.gallery-thumb').forEach((t, i) => {
                t.classList.toggle('active', i === index);
            });
        }

        // Size select
        function selectSize(btn, size) {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedSize = size;
        }

        // Quantity change
        function changeQty(change) {
            qty = Math.max(1, Math.min(<?php echo $product['stock']; ?>, qty + change));
            document.getElementById('qtyNum').textContent = qty;
        }

        // Add to cart
        function addToCartDetail() {
            fetch('/NEW_COLLECTION/php/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=<?php echo $product['id']; ?>&size=' + selectedSize + '&quantity=' + qty
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('✓ <?php echo addslashes($product['name']); ?> added to cart!');
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) cartCount.textContent = data.cart_count;
                    } else {
                        showToast('⚠️ Please login first!');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 1500);
                    }
                })
                .catch(() => {
                    showToast('⚠️ Please login first!');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
                });
        }

        // Buy Now
        function buyNow() {
            fetch('/NEW_COLLECTION/php/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=<?php echo $product['id']; ?>&size=' + selectedSize + '&quantity=' + qty
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/NEW_COLLECTION/pages/checkout.php';
                    } else {
                        showToast('⚠️ Please login first!');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 1500);
                    }
                })
                .catch(() => {
                    showToast('⚠️ Please login first!');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
                });
        }

        // Related products add to cart
        function addToCart(productId, productName, price) {
            fetch('/NEW_COLLECTION/php/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=' + productId + '&size=M&quantity=1'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('✓ ' + productName + ' added to cart!');
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) cartCount.textContent = data.cart_count;
                    } else {
                        showToast('⚠️ Please login first!');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 1500);
                    }
                });
        }
        // Reviews
        let selectedRating = 0;

        function setRating(rating) {
            selectedRating = rating;
            document.querySelectorAll('.star-pick').forEach((star, i) => {
                star.classList.toggle('active', i < rating);
            });
        }

        function submitReview() {
            if (selectedRating === 0) {
                showToast('⚠️ Please select a rating!');
                return;
            }

            const reviewText = document.getElementById('reviewText').value.trim();
            if (reviewText === '') {
                showToast('⚠️ Please write a review!');
                return;
            }

            fetch('/NEW_COLLECTION/php/add_review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=<?php echo $product_id; ?>&rating=' + selectedRating + '&review=' + encodeURIComponent(reviewText)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('✓ Review submitted successfully!');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('⚠️ ' + data.message);
                    }
                });
        }

        // 🎯 YAHAN FIX KIYA HAI: Is function ko bahar nikal diya!
        function toggleWishlistDetail(productId, btn) {
            fetch('../php/wishlist_toggle.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=' + productId
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'added') {
                        document.getElementById('wishlistIcon').textContent = '❤️';
                        document.getElementById('wishlistText').textContent = 'Remove from Wishlist';
                        showToast('❤️ Added to Wishlist!');
                    } else if (data.status === 'removed') {
                        document.getElementById('wishlistIcon').textContent = '🤍';
                        document.getElementById('wishlistText').textContent = 'Add to Wishlist';
                        showToast('💔 Removed from Wishlist!');
                    } else if (data.status === 'login') {
                        showToast('⚠️ Please login first!');
                        setTimeout(() => window.location.href = 'login.php', 1500);
                    }
                });
        }
        // Video autoplay on scroll
        const videoObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const video = entry.target;
                if (entry.isIntersecting) {
                    video.muted = true;
                    video.play().catch(e => console.log(e));
                } else {
                    video.pause();
                }
            });
        }, {
            threshold: 0.3
        });

        function observeVideos() {
            document.querySelectorAll('video').forEach(video => {
                video.muted = true;
                videoObserver.observe(video);
            });
        }

        observeVideos();

        const originalSwitchMedia = switchMedia;
        switchMedia = function(index) {
            originalSwitchMedia(index);
            setTimeout(observeVideos, 100);
        }
    </script>
</body>

</html>