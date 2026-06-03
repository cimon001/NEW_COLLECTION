 <?php
require_once '../php/config.php';

// // Fetch cart count from database
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='{$_SESSION['user_id']}'");
    $cc_row = mysqli_fetch_assoc($cc);
    $cart_count = $cc_row['total'] ?? 0;
}
// // Fetch wishlist items
$wishlist_ids = [];
$wishlist_count = 0;
if (isset($_SESSION['user_id'])) {
    $wl = mysqli_query($conn, "SELECT product_id FROM wishlist WHERE user_id='{$_SESSION['user_id']}'");
    while ($w = mysqli_fetch_assoc($wl)) {
        $wishlist_ids[] = $w['product_id'];
    }
    $wishlist_count = count($wishlist_ids);
}

// Filter logic
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// // Build query
$where = [];

if (isset($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    $where[] = "category = '$category'";
}

if (isset($_GET['badge'])) {
    $badge = urldecode($_GET['badge']);
    $badge = mysqli_real_escape_string($conn, $badge);
    $where[] = "badge = '$badge'";
}

// 🎯 YEH HAI WO JAADUI LINE JO SEARCH KO ZINDA KAREGI 🎯
if (!empty($search)) {
    $where[] = "(name LIKE '%$search%' OR description LIKE '%$search%' OR highlights LIKE '%$search%')";
}

$sql = "SELECT * FROM products";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .shop-page {
            padding: 120px 60px 80px;
            min-height: 100vh;
        }

        .shop-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 50px;
        }

        .shop-filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .filter-btn {
            background: none;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--muted);
            padding: 8px 20px;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .filter-btn:hover,
        .filter-btn.active {
            border-color: var(--gold);
            color: var(--gold);
        }

        .search-bar {
            display: flex;
            gap: 0;
            border: 1px solid var(--border);
            max-width: 300px;
        }

        .search-bar input {
            flex: 1;
            background: none;
            border: none;
            outline: none;
            padding: 10px 16px;
            color: var(--white);
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
        }

        .search-bar input::placeholder {
            color: var(--muted);
        }

        .search-bar button {
            background: var(--gold);
            border: none;
            color: var(--black);
            padding: 10px 16px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .search-bar button:hover {
            background: var(--gold2);
        }

        .products-count {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 30px;
        }

        .no-products {
            text-align: center;
            padding: 80px 20px;
            color: var(--muted);
        }

        .no-products h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 36px;
            margin-bottom: 10px;
            color: var(--white);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="../index.php" class="nav-logo">NEW_COLLECTION</a>
        <ul class="nav-links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="products.php" style="color:var(--gold)">Shop</a></li>
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
                <!-- NOTIFICATION ICON -->
                <a href="notifications.php" class="notif-icon" style="position:relative;font-size:22px;text-decoration:none;">
                    🔔 <span style="position:absolute;top:-8px;right:-8px;background:#e84444;color:white;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><?php echo $notif_count; ?></span>
                </a>

                <!-- USER MENU DROPDOWN -->
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
                        <a href="profile.php" class="user-dropdown-item">
                            <span>👤</span> My Profile
                        </a>
                        <a href="orders.php" class="user-dropdown-item">
                            <span>📦</span> My Orders
                        </a>
                        <a href="wishlist.php" class="user-dropdown-item">
                            <span>❤️</span> Wishlist
                            <?php if ($wishlist_count > 0): ?>
                                <span style="margin-left:auto;background:var(--red);color:white;font-size:10px;padding:2px 6px;border-radius:10px;"><?php echo $wishlist_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="user-dropdown-divider"></div>
                        <a href="../php/logout.php" class="user-dropdown-item" style="color:var(--red);">
                            <span>🚪</span> Logout
                        </a>
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

    <div class="shop-page">
        <!-- Header -->
        <div class="shop-header">
            <div>
                <p class="section-label">★ Our Collection</p>
               <h1 class="section-title" style="margin-bottom:0;">
    <?php
    if (isset($_GET['badge'])) {
        // Agar SALE ya NEW IN par click kiya hai
        echo strtoupper($_GET['badge']); 
    } elseif ($category == 'hoodie') {
        echo 'HOODIES';
    } elseif ($category == 'jacket') {
        echo 'JACKETS';
    } elseif (!empty($search)) {
        // Agar kisi ne search bar use kiya hai
        echo 'SEARCH RESULTS'; 
    } else {
        echo 'ALL PRODUCTS';
    }
    ?>
</h1>
            </div>
            <!-- Search -->
            <form method="GET" action="">
                <?php if ($category): ?>
                    <input type="hidden" name="category" value="<?php echo $category; ?>">
                <?php endif; ?>
                <div class="search-bar">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo $search; ?>">
                    <button type="submit">🔍</button>
                </div>
            </form>
        </div>

        <!-- Filters -->
        <div class="shop-filters">
            <a href="products.php" class="filter-btn <?php echo empty($category) ? 'active' : ''; ?>">All</a>
            <a href="products.php?category=hoodie" class="filter-btn <?php echo $category == 'hoodie' ? 'active' : ''; ?>">Hoodies</a>
            <a href="products.php?category=jacket" class="filter-btn <?php echo $category == 'jacket' ? 'active' : ''; ?>">Jackets</a>
            <a href="products.php?badge=SALE" class="filter-btn <?php echo isset($_GET['badge']) && $_GET['badge'] == 'SALE' ? 'active' : ''; ?>">On Sale</a>
            <a href="products.php?badge=NEW%20IN" class="filter-btn <?php echo isset($_GET['badge']) && $_GET['badge'] == 'NEW IN' ? 'active' : ''; ?>">New In</a>
        </div>

        <!-- Products Count -->
        <p class="products-count">
            <?php echo mysqli_num_rows($result); ?> products found
        </p>

        <!-- Products Grid -->
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="products-grid">
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                    <div class="product-card" onclick="window.location.href='product_detail.php?id=<?php echo $product['id']; ?>'">
                        <div class="product-img">
                            <img src="../<?php echo $product['image']; ?>"
                                alt="<?php echo $product['name']; ?>"
                                onerror="this.src='https://via.placeholder.com/300x400/161616/c8a96e?text=<?php echo urlencode($product['name']); ?>'">
                            <?php if ($product['badge']): ?>
                                <div class="product-badge <?php echo strtolower(str_replace(' ', '-', $product['badge'])); ?>">
                                    <?php echo $product['badge']; ?>
                                </div>
                            <?php endif; ?>
                            <button class="wishlist-btn" onclick="event.stopPropagation(); toggleWishlist(<?php echo $product['id']; ?>, this)">
                                <?php echo in_array($product['id'], $wishlist_ids) ? '❤️' : '🤍'; ?>
                            </button>
                            <div class="product-actions"
                                onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">
                                + Add to Cart
                            </div>
                        </div>
                        <div class="product-info">
                            <p class="product-cat"><?php echo ucfirst($product['category']); ?></p>
                            <h3 class="product-name"><?php echo $product['name']; ?></h3>
                            <div class="product-price">
                                <span class="price-new">₹<?php echo number_format($product['price'], 0); ?></span>
                                <?php if ($product['old_price']): ?>
                                    <span class="price-old">₹<?php echo number_format($product['old_price'], 0); ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="margin-top:10px;">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>"
                                    style="font-size:12px;color:var(--gold);letter-spacing:1px;">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php else: ?>
            <div class="no-products">
                <h3>NO PRODUCTS FOUND</h3>
                <p>Try different filter and search again</p>
                <a href="products.php" class="btn-primary" style="margin-top:20px;display:inline-block;">View All</a>
            </div>
        <?php endif; ?>

    </div>

    <script src="../js/main.js"></script>
    <script>
        function addToCart(productId, productName, price) {
            fetch('/new_collection/php/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId + '&size=M&quantity=1'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('✓ ' + productName + ' added to cart!');
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    } else {
                        showToast('⚠️ Please login first!');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 1500);
                    }
                })
                .catch(() => {
                    showToast('✓ Product added to cart!');
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = parseInt(cartCount.textContent) + 1;
                    }
                });
        }
    </script>
    <!-- WHATSAPP BUTTON -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="https://wa.me/918837894309" target="_blank" class="whatsapp-btn">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.533 5.857L.057 23.428l5.701-1.498A11.959 11.959 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.802a9.823 9.823 0 01-5.013-1.371l-.36-.214-3.733.979 1.004-3.648-.234-.374A9.786 9.786 0 012.198 12C2.198 6.579 6.579 2.198 12 2.198S21.802 6.579 21.802 12 17.421 21.802 12 21.802z" />
            </svg>
            <span class="whatsapp-tooltip">Chat on WhatsApp</span>
        </a>
        <!-- CHATBOT -->
        <div class="chatbot-btn" onclick="toggleChat()">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
                <path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18a7.955 7.955 0 01-4.055-1.104l-.291-.173-3.018.898.898-3.018-.173-.291A7.955 7.955 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z" />
                <path d="M8 11h8M8 14h5" stroke="white" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <span class="chat-tooltip">Need Help?</span>
        </div>

        <div class="chatbot-box" id="chatbot-box">
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="chat-avatar">NC</div>
                    <div>
                        <div class="chat-name">NEW_COLLECTION Support</div>
                        <div class="chat-status">🟢 Online</div>
                    </div>
                </div>
                <button class="chat-close" onclick="toggleChat()">✕</button>
            </div>

            <div class="chat-messages" id="chat-messages"></div>

            <div class="chat-input-area">
                <input type="text" id="chat-input" placeholder="Type a message..." onkeypress="handleKey(event)">
                <button onclick="sendUserMessage()">➤</button>
            </div>
        </div>

        <style>
            .chatbot-btn {
                position: fixed;
                bottom: 90px;
                right: 28px;
                width: 40px;
                height: 40px;
                background: #c8a96e;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 9999;
                box-shadow: 0 4px 20px rgba(200, 169, 110, 0.4);
                transition: all 0.3s;
            }

            .chatbot-btn:hover {
                transform: scale(1.1);
            }

            .chat-tooltip {
                position: absolute;
                right: 65px;
                background: #161616;
                color: #f5f5f0;
                padding: 6px 12px;
                font-size: 12px;
                white-space: nowrap;
                border: 1px solid rgba(200, 169, 110, 0.3);
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s;
            }

            .chatbot-btn:hover .chat-tooltip {
                opacity: 1;
            }

            .chatbot-box {
                position: fixed;
                bottom: 170px;
                right: 28px;
                width: 360px;
                height: 500px;
                background: #111111;
                border: 1px solid rgba(200, 169, 110, 0.3);
                border-radius: 12px;
                display: none;
                flex-direction: column;
                z-index: 9998;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
                overflow: hidden;
            }

            .chatbot-box.open {
                display: flex;
            }

            .chat-header {
                background: #161616;
                padding: 16px 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-bottom: 1px solid rgba(200, 169, 110, 0.2);
            }

            .chat-header-info {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .chat-avatar {
                width: 40px;
                height: 40px;
                background: #c8a96e;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Bebas Neue', sans-serif;
                font-size: 14px;
                color: #0a0a0a;
            }

            .chat-name {
                font-size: 14px;
                font-weight: 600;
                color: #f5f5f0;
            }

            .chat-status {
                font-size: 11px;
                color: #888;
                margin-top: 2px;
            }

            .chat-close {
                background: none;
                border: none;
                color: #888;
                font-size: 16px;
                cursor: pointer;
            }

            .chat-close:hover {
                color: #f5f5f0;
            }

            .chat-messages {
                flex: 1;
                overflow-y: auto;
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .chat-messages::-webkit-scrollbar {
                width: 4px;
            }

            .chat-messages::-webkit-scrollbar-thumb {
                background: rgba(200, 169, 110, 0.3);
            }

            .msg {
                max-width: 85%;
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .msg.bot {
                align-self: flex-start;
            }

            .msg.user {
                align-self: flex-end;
            }

            .msg-bubble {
                padding: 10px 14px;
                font-size: 13px;
                line-height: 1.5;
                border-radius: 12px;
            }

            .msg.bot .msg-bubble {
                background: #1e1e1e;
                color: #f5f5f0;
                border-bottom-left-radius: 4px;
                border: 1px solid rgba(255, 255, 255, 0.06);
            }

            .msg.user .msg-bubble {
                background: #c8a96e;
                color: #0a0a0a;
                font-weight: 500;
                border-bottom-right-radius: 4px;
            }

            .chat-options {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 6px;
            }

            .chat-opt-btn {
                background: transparent;
                border: 1px solid rgba(200, 169, 110, 0.4);
                color: #c8a96e;
                padding: 7px 12px;
                font-size: 12px;
                cursor: pointer;
                border-radius: 20px;
                transition: all 0.2s;
                font-family: 'DM Sans', sans-serif;
            }

            .chat-opt-btn:hover {
                background: rgba(200, 169, 110, 0.1);
            }

            .chat-input-area {
                padding: 12px 16px;
                border-top: 1px solid rgba(200, 169, 110, 0.2);
                display: flex;
                gap: 8px;
            }

            .chat-input-area input {
                flex: 1;
                background: #1e1e1e;
                border: 1px solid rgba(200, 169, 110, 0.2);
                color: #f5f5f0;
                padding: 10px 14px;
                font-family: 'DM Sans', sans-serif;
                font-size: 13px;
                border-radius: 20px;
                outline: none;
            }

            .chat-input-area input:focus {
                border-color: #c8a96e;
            }

            .chat-input-area button {
                background: #c8a96e;
                border: none;
                color: #0a0a0a;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 16px;
                transition: all 0.2s;
            }

            .chat-input-area button:hover {
                background: #e8c17a;
            }

            .typing {
                display: flex;
                align-items: center;
                gap: 4px;
                padding: 10px 14px;
            }

            .typing span {
                width: 7px;
                height: 7px;
                background: #888;
                border-radius: 50%;
                animation: typing 1.2s infinite;
            }

            .typing span:nth-child(2) {
                animation-delay: 0.2s;
            }

            .typing span:nth-child(3) {
                animation-delay: 0.4s;
            }

            @keyframes typing {

                0%,
                60%,
                100% {
                    transform: translateY(0);
                }

                30% {
                    transform: translateY(-6px);
                }
            }

            /* ===== WHATSAPP BUTTON ===== */
            .whatsapp-btn {
                position: fixed;
                bottom: 32px;
                right: 28px;
                width: 40px;
                height: 40px;
                background: #25D366;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
                transition: all 0.3s;
                cursor: pointer;
                text-decoration: none;
            }

            .whatsapp-btn:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 30px rgba(37, 211, 102, 0.6);
            }

            .whatsapp-btn svg {
                width: 30px;
                height: 30px;
                flex-shrink: 0;
            }

            .whatsapp-tooltip {
                position: absolute;
                right: 65px;
                background: #fff;
                color: #000;
                padding: 8px 14px;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
                opacity: 0;
                transition: opacity 0.3s;
                pointer-events: none;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            }

            .whatsapp-btn:hover .whatsapp-tooltip {
                opacity: 1;
            }
        </style>
        <script>
            const responses = {
                start: {
                    msg: "👋 Hello! Welcome to NEW_COLLECTION Support.\nHow can I help you today?",
                    options: [{
                            text: "📦 Order Problem",
                            next: "order"
                        },
                        {
                            text: "🚚 Delivery Problem",
                            next: "delivery"
                        },
                        {
                            text: "💰 Payment Problem",
                            next: "payment"
                        },
                        {
                            text: "❌ Cancel Order",
                            next: "cancel"
                        },
                        {
                            text: "📞 Talk to Support",
                            next: "whatsapp"
                        }
                    ]
                },
                order: {
                    msg: "I understand you have an order issue. What's the problem?",
                    options: [{
                            text: "Order not received",
                            next: "not_received"
                        },
                        {
                            text: "Wrong item received",
                            next: "wrong_item"
                        },
                        {
                            text: "Check order status",
                            next: "order_status"
                        },
                        {
                            text: "⬅ Go Back",
                            next: "start"
                        }
                    ]
                },
                delivery: {
                    msg: "Sorry to hear about your delivery issue! What happened?",
                    options: [{
                            text: "Delivery delayed",
                            next: "delayed"
                        },
                        {
                            text: "Package damaged",
                            next: "damaged"
                        },
                        {
                            text: "Wrong address",
                            next: "wrong_address"
                        },
                        {
                            text: "⬅ Go Back",
                            next: "start"
                        }
                    ]
                },
                payment: {
                    msg: "Let me help with your payment issue. What's the problem?",
                    options: [{
                            text: "Refund not received",
                            next: "refund"
                        },
                        {
                            text: "Charged twice",
                            next: "charged"
                        },
                        {
                            text: "COD issue",
                            next: "cod"
                        },
                        {
                            text: "⬅ Go Back",
                            next: "start"
                        }
                    ]
                },
                cancel: {
                    msg: "You want to cancel your order? Here's what you can do:",
                    options: [{
                            text: "How to cancel?",
                            next: "how_cancel"
                        },
                        {
                            text: "Cancel via WhatsApp",
                            next: "whatsapp"
                        },
                        {
                            text: "⬅ Go Back",
                            next: "start"
                        }
                    ]
                },
                not_received: {
                    msg: "⏳ Orders usually arrive in 5-7 business days.\n\nIf it's been longer, please contact us on WhatsApp with your Order ID and we'll track it immediately!",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                wrong_item: {
                    msg: "😔 We're sorry you received the wrong item!\n\nPlease WhatsApp us with:\n• Your Order ID\n• Photo of wrong item\n\nWe'll send the correct item ASAP!",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                order_status: {
                    msg: "📦 To check your order status:\n\n1. Login to your account\n2. Go to 'My Orders'\n3. You'll see live status\n\nOr contact us on WhatsApp with your Order ID!",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                delayed: {
                    msg: "🚚 We apologize for the delay!\n\nDelivery usually takes 5-7 days. If it's been more than 10 days, please contact us immediately on WhatsApp with your Order ID.",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                damaged: {
                    msg: "😔 We're sorry your package was damaged!\n\nPlease WhatsApp us with:\n• Your Order ID\n• Photos of damaged package\n\nWe'll arrange a replacement immediately!",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                wrong_address: {
                    msg: "📍 Wrong address issue?\n\nIf order is not shipped yet, we can change it. Contact us IMMEDIATELY on WhatsApp with your Order ID!",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                refund: {
                    msg: "💰 Refunds are processed within 5-7 business days after order cancellation.\n\nIf it's been longer, please contact us on WhatsApp with your Order ID!",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                charged: {
                    msg: "⚠️ We only accept Cash on Delivery (COD).\n\nIf you were charged, please contact us immediately on WhatsApp with your bank statement!",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                cod: {
                    msg: "🚚 For COD orders, payment is collected at delivery.\n\nIf you have any COD related issue, please contact us on WhatsApp!",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                how_cancel: {
                    msg: "❌ To cancel your order:\n\n1. Go to 'My Orders'\n2. Select your order\n3. Click 'Cancel Order'\n\nOr contact us on WhatsApp if order is already shipped!",
                    options: [{
                        text: "📞 Contact on WhatsApp",
                        next: "whatsapp"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                },
                whatsapp: {
                    msg: "📞 Our support team is ready to help!\n\nClick below to chat with us on WhatsApp. We reply within minutes! ⚡",
                    options: [{
                        text: "💬 Open WhatsApp",
                        next: "open_wa"
                    }, {
                        text: "🏠 Main Menu",
                        next: "start"
                    }]
                }
            };

            let chatOpen = false;
            let isTyping = false;

            function toggleChat() {
                chatOpen = !chatOpen;
                const box = document.getElementById('chatbot-box');
                box.classList.toggle('open', chatOpen);
                if (chatOpen && document.getElementById('chat-messages').children.length === 0) {
                    setTimeout(() => showBotMessage('start'), 500);
                }
            }

            function showBotMessage(key) {
                if (isTyping) return;
                isTyping = true;
                const messages = document.getElementById('chat-messages');

                // Typing indicator
                const typing = document.createElement('div');
                typing.className = 'msg bot';
                typing.id = 'typing-indicator';
                typing.innerHTML = '<div class="msg-bubble typing"><span></span><span></span><span></span></div>';
                messages.appendChild(typing);
                messages.scrollTop = messages.scrollHeight;

                setTimeout(() => {
                    typing.remove();
                    isTyping = false;

                    const data = responses[key];
                    if (!data) return;

                    const msgDiv = document.createElement('div');
                    msgDiv.className = 'msg bot';

                    let html = `<div class="msg-bubble">${data.msg.replace(/\n/g, '<br>')}</div>`;

                    if (data.options) {
                        html += '<div class="chat-options">';
                        data.options.forEach(opt => {
                            if (opt.next === 'open_wa') {
                                html += `<button class="chat-opt-btn" onclick="window.open('https://wa.me/918837894309','_blank')">${opt.text}</button>`;
                            } else {
                                html += `<button class="chat-opt-btn" onclick="handleOption('${opt.text}','${opt.next}')">${opt.text}</button>`;
                            }
                        });
                        html += '</div>';
                    }

                    msgDiv.innerHTML = html;
                    messages.appendChild(msgDiv);
                    messages.scrollTop = messages.scrollHeight;
                }, 1000);
            }

            function handleOption(text, next) {
                const messages = document.getElementById('chat-messages');
                const userDiv = document.createElement('div');
                userDiv.className = 'msg user';
                userDiv.innerHTML = `<div class="msg-bubble">${text}</div>`;
                messages.appendChild(userDiv);
                messages.scrollTop = messages.scrollHeight;
                setTimeout(() => showBotMessage(next), 300);
            }

            function sendUserMessage() {
                const input = document.getElementById('chat-input');
                const text = input.value.trim();
                if (!text) return;
                input.value = '';

                const messages = document.getElementById('chat-messages');
                const userDiv = document.createElement('div');
                userDiv.className = 'msg user';
                userDiv.innerHTML = `<div class="msg-bubble">${text}</div>`;
                messages.appendChild(userDiv);
                messages.scrollTop = messages.scrollHeight;

                // Simple keyword detection
                const t = text.toLowerCase();
                if (t.includes('order')) showBotMessage('order');
                else if (t.includes('deliver') || t.includes('shipping')) showBotMessage('delivery');
                else if (t.includes('pay') || t.includes('refund') || t.includes('money')) showBotMessage('payment');
                else if (t.includes('cancel')) showBotMessage('cancel');
                else if (t.includes('whatsapp') || t.includes('support') || t.includes('help') || t.includes('hello') || t.includes('hi')) showBotMessage('whatsapp');
                else showBotMessage('start');
            }

            function handleKey(e) {
                if (e.key === 'Enter') sendUserMessage();
            }
        </script>
    <?php endif; ?>
</body>

</html>