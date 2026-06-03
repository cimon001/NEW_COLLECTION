<?php
require_once '../php/config.php';

$cart_count = 0;
if(isset($_SESSION['user_id'])) {
    $cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='{$_SESSION['user_id']}'");
    $cc_row = mysqli_fetch_assoc($cc);
    $cart_count = $cc_row['total'] ?? 0;
}

// Fetch approved designs only
$designs = mysqli_query($conn, "SELECT cd.*, u.name as designer_name FROM custom_designs cd JOIN users u ON cd.user_id=u.id WHERE cd.status='approved' ORDER BY cd.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head><link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Designs Gallery — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .gallery-page { padding: 120px 60px 80px; min-height: 100vh; }
        .designs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 40px;
        }
        .design-card {
            background: var(--card);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }
        .design-card:hover { border-color: var(--gold); transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        .design-imgs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px;
            height: 220px;
            background: #1a1a1a;
        }
        .design-imgs img { width: 100%; height: 100%; object-fit: cover; }
        .design-imgs.single img { grid-column: 1 / -1; }
        .design-info { padding: 20px; }
        .design-title { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .design-designer { font-size: 12px; color: var(--gold); margin-bottom: 8px; }
        .design-desc { font-size: 13px; color: var(--muted); line-height: 1.6; margin-bottom: 16px; }
        .design-price {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            color: var(--white);
            margin-bottom: 12px;
        }
        .design-profit {
            font-size: 11px;
            color: #4caf50;
            margin-bottom: 16px;
        }
        .btn-order {
            width: 100%;
            background: var(--gold);
            color: var(--black);
            border: none;
            padding: 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-order:hover { background: var(--gold2); }
        .empty-gallery {
            text-align: center;
            padding: 80px 20px;
            color: var(--muted);
        }
        @media(max-width:768px) {
            .gallery-page { padding: 100px 20px 40px; }
            .designs-grid { grid-template-columns: 1fr; }
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
            <?php if(isset($_SESSION['user_id'])): ?>
                <span style="color:var(--gold);font-size:13px;">Hi, <?php echo $_SESSION['user_name']; ?>!</span>
                <a href="../php/logout.php" class="nav-btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-btn">Login</a>
            <?php endif; ?>
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

    <div class="gallery-page">
        <p class="section-label">★ Community Designs</p>
        <h1 class="section-title">CUSTOM GALLERY</h1>
        <p style="color:var(--muted);font-size:15px;margin-top:-30px;margin-bottom:10px;">
            Designs created by our community — Order any design and we'll make it for you!
        </p>
        <a href="custom_design.php" class="btn-primary" style="display:inline-block;margin-bottom:40px;">
            + Submit Your Design
        </a>

        <?php if(mysqli_num_rows($designs) > 0): ?>
        <div class="designs-grid">
            <?php while($design = mysqli_fetch_assoc($designs)): ?>
            <div class="design-card">
                <div class="design-imgs <?php echo empty($design['image_back']) ? 'single' : ''; ?>">
                    <img src="../uploads/designs/<?php echo $design['image_front']; ?>"
                         onerror="this.src='https://via.placeholder.com/300x220/161616/c8a96e?text=Front'"
                         alt="Front">
                    <?php if($design['image_back']): ?>
                    <img src="../uploads/designs/<?php echo $design['image_back']; ?>"
                         onerror="this.src='https://via.placeholder.com/300x220/161616/c8a96e?text=Back'"
                         alt="Back">
                    <?php endif; ?>
                </div>
                <div class="design-info">
                    <div class="design-title"><?php echo $design['title']; ?></div>
                    <div class="design-designer">🎨 By <?php echo $design['designer_name']; ?></div>
                    <div class="design-desc"><?php echo substr($design['description'], 0, 100); ?>...</div>
                    <div class="design-price">₹<?php echo number_format($design['price'], 0); ?></div>
                    <div class="design-profit">💰 Designer earns 5% on every sale!</div>
                    <button class="btn-order" onclick="orderDesign(<?php echo $design['id']; ?>, '<?php echo addslashes($design['title']); ?>', <?php echo $design['price']; ?>)">
                        Order This Design →
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-gallery">
            <div style="font-size:64px;margin-bottom:16px;">🎨</div>
            <h3 style="font-family:'Bebas Neue',sans-serif;font-size:36px;color:var(--white);margin-bottom:8px;">NO DESIGNS YET</h3>
            <p>Be the first to submit a design!</p>
            <a href="custom_design.php" class="btn-primary" style="display:inline-block;margin-top:20px;">Submit Design →</a>
        </div>
        <?php endif; ?>
    </div>

    <script src="../js/main.js"></script>
    <script>
    function orderDesign(designId, title, price) {
        <?php if(isset($_SESSION['user_id'])): ?>
        if(confirm('Order "' + title + '" for ₹' + price + '?\n\nThis is a custom order — we will contact you for details!')) {
            fetch('../php/order_custom_design.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'design_id=' + designId
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    showToast('✅ Custom order placed! We will contact you soon!');
                } else {
                    showToast('⚠️ ' + data.message);
                }
            });
        }
        <?php else: ?>
        showToast('⚠️ Please login first!');
        setTimeout(() => { window.location.href = 'login.php'; }, 1500);
        <?php endif; ?>
    }
    </script>
</body>
</html>