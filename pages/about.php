<?php
require_once '../php/config.php';

$cart_count = 0;
$wishlist_count = 0;
if (isset($_SESSION['user_id'])) {
    $cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='{$_SESSION['user_id']}'");
    $cc_row = mysqli_fetch_assoc($cc);
    $cart_count = $cc_row['total'] ?? 0;

    $wl = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist WHERE user_id='{$_SESSION['user_id']}'"));
    $wishlist_count = $wl['total'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap"
        rel="stylesheet">
    <style>
        .about-page {
            padding: 120px 60px 80px;
            min-height: 100vh;
        }

        /* VIP Profile Dropdown CSS (Updated to match index.php exactly) */
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

        /* Hero Section */
        .about-hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            margin-bottom: 100px;
            padding-bottom: 80px;
            border-bottom: 1px solid var(--border);
        }

        .about-hero-content h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 80px;
            line-height: 0.9;
            letter-spacing: -2px;
            margin-bottom: 24px;
        }

        .about-hero-content h1 span {
            color: var(--gold);
        }

        .about-hero-content p {
            font-size: 16px;
            color: var(--muted);
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .about-hero-img {
            position: relative;
            aspect-ratio: 3 / 2;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .about-hero-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .about-hero-img::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(200, 169, 110, 0.2), transparent);
            z-index: 1;
        }

        /* Stats */
        .about-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            margin-bottom: 100px;
            border: 1px solid var(--border);
        }

        .stat-item {
            padding: 40px 30px;
            text-align: center;
            border-right: 1px solid var(--border);
            transition: background 0.3s;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-item:hover {
            background: rgba(200, 169, 110, 0.05);
        }

        .stat-number {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 60px;
            color: var(--gold);
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* Story Section */
        .about-story {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            margin-bottom: 100px;
            padding-bottom: 80px;
            border-bottom: 1px solid var(--border);
        }

        .story-content h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 52px;
            line-height: 0.9;
            margin-bottom: 24px;
            letter-spacing: -1px;
        }

        .story-content h2 span {
            color: var(--gold);
        }

        .story-content p {
            font-size: 15px;
            color: var(--muted);
            line-height: 1.8;
            margin-bottom: 16px;
        }

        .story-img {
            aspect-ratio: 3 / 2;
            overflow: hidden;
            position: relative;
            border: 1px solid var(--border);
        }

        .story-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .story-img:hover img {
            transform: scale(1.05);
        }

        /* Values */
        .about-values {
            margin-bottom: 100px;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 50px;
        }

        .value-card {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 36px 30px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .value-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gold);
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .value-card:hover::before {
            transform: scaleX(1);
        }

        .value-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .value-icon {
            font-size: 40px;
            margin-bottom: 16px;
        }

        .value-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 24px;
            letter-spacing: 1px;
            margin-bottom: 12px;
            color: var(--gold);
        }

        .value-desc {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.7;
        }

        /* Team Section */
        .about-team {
            margin-bottom: 100px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 50px;
        }

        .team-card {
            background: var(--card);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.3s;
            text-align: center;
        }

        .team-card:hover {
            border-color: var(--gold);
            transform: translateY(-6px);
        }

        .team-img {
            height: 280px;
            background: linear-gradient(135deg, #161616, #1a1a1a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
        }

        .team-info {
            padding: 24px;
        }

        .team-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 22px;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }

        .team-role {
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 12px;
        }

        .team-desc {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        /* CTA Section */
        .about-cta {
            background: linear-gradient(135deg, #161616 0%, #1a1205 50%, #161616 100%);
            border: 1px solid var(--border);
            padding: 80px 60px;
            text-align: center;
        }

        .about-cta h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 60px;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }

        .about-cta h2 span {
            color: var(--gold);
        }

        .about-cta p {
            font-size: 16px;
            color: var(--muted);
            margin-bottom: 30px;
        }

        .cta-btns {
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        @media(max-width:768px) {
            .about-page {
                padding: 100px 20px 40px;
            }

            .about-hero {
                grid-template-columns: 1fr;
            }

            .about-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .about-story {
                grid-template-columns: 1fr;
            }

            .values-grid {
                grid-template-columns: 1fr;
            }

            .team-grid {
                grid-template-columns: 1fr;
            }

            .about-cta {
                padding: 40px 20px;
            }

            .about-hero-content h1 {
                font-size: 50px;
            }
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
            <li><a href="about.php" style="color:var(--gold)">About</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                $notif_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications WHERE user_id='{$_SESSION['user_id']}' AND is_read=0");
                $notif_count = mysqli_fetch_assoc($notif_result)['total'];
                ?>
                <a href="notifications.php" class="notif-icon"
                    style="position:relative;font-size:22px;text-decoration:none;">
                    🔔 <span
                        style="position:absolute;top:-8px;right:-8px;background:#e84444;color:white;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><?php echo $notif_count; ?></span>
                </a>

                <div class="user-menu" id="userMenu">
                    <div class="user-menu-btn" onclick="toggleUserMenu()">
                        <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                        <span class="user-name-text">Hi, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>!</span>
                        <span style="font-size:10px;margin-left:4px;">▼</span>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-dropdown-header">
                            <div class="user-dropdown-avatar">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
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
                                <span
                                    style="margin-left:auto;background:var(--red);color:white;font-size:10px;padding:2px 6px;border-radius:10px;"><?php echo $wishlist_count; ?></span>
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
        <button class="hamburger" id="hamburger" onclick="toggleMobileNav()" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </nav>
    <!-- Mobile Nav -->
    <div class="mobile-nav" id="mobileNav"></div>
    <script>
        (function () {
            var nav = document.querySelector(".nav-links");
            var actions = document.querySelector(".nav-actions");
            var mobileNav = document.getElementById("mobileNav");
            if (nav && actions && mobileNav) {
                var links = nav.innerHTML;
                var btns = actions.innerHTML;
                mobileNav.innerHTML = links.replace(/<li>/g, "").replace(/<\/li>/g, "") + '<div class="mobile-nav-actions">' + btns + '</div>';
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

    <div class="about-page">

        <div class="about-hero">
            <div class="about-hero-content">
                <p class="section-label">★ Our Story</p>
                <h1>WE ARE<br><span>NEW_</span><br>COLLECTION</h1>
                <p>Born in Punjab, built for the bold. NEW_COLLECTION is more than a clothing brand — it's a movement
                    for those who dare to stand out. We craft premium hoodies and jackets that speak louder than words.
                </p>
                <p>Every stitch tells a story. Every piece is made with passion, precision, and purpose. Welcome to the
                    new generation of streetwear.</p>
                <a href="products.php" class="btn-primary">Shop The Collection →</a>
            </div>
            <div class="about-hero-img">
                <img src="../images/banners/banner1.png" alt="NEW_COLLECTION"
                    onerror="this.style.background='linear-gradient(135deg, #161616, #c8a96e22)';this.style.display='block';">
            </div>
        </div>

        <div class="about-stats">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Premium Products</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Quality Assured</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">2026</div>
                <div class="stat-label">Est. Year</div>
            </div>
        </div>

        <div class="about-story">
            <div class="story-img">
                <img src="../images/banners/banner2.png" alt="Our Story" onerror="this.style.background='#161616';">
            </div>
            <div class="story-content">
                <p class="section-label">★ How It Started</p>
                <h2>FROM PUNJAB<br>TO THE <span>WORLD</span></h2>
                <p>It started with a simple idea — quality streetwear that doesn't break the bank. Founded in Jalandhar,
                    Punjab, NEW_COLLECTION was born out of a passion for fashion and technology.</p>
                <p>We noticed a gap in the market — premium quality hoodies and jackets at affordable prices, delivered
                    right to your doorstep with Cash on Delivery option.</p>
                <p>Today, we serve hundreds of happy customers across India, with new drops every Friday and a community
                    that keeps growing every day.</p>
                <div style="display:flex;gap:20px;margin-top:24px;">
                    <div style="text-align:center;padding:16px 24px;border:1px solid var(--border);">
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--gold);">400GSM</div>
                        <div style="font-size:11px;color:var(--muted);letter-spacing:1px;">COTTON QUALITY</div>
                    </div>
                    <div style="text-align:center;padding:16px 24px;border:1px solid var(--border);">
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--gold);">COD</div>
                        <div style="font-size:11px;color:var(--muted);letter-spacing:1px;">CASH ON DELIVERY</div>
                    </div>
                    <div style="text-align:center;padding:16px 24px;border:1px solid var(--border);">
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--gold);">7 DAY</div>
                        <div style="font-size:11px;color:var(--muted);letter-spacing:1px;">EASY RETURNS</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-values">
            <p class="section-label">★ What We Stand For</p>
            <h2 class="section-title">OUR VALUES</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">🧵</div>
                    <div class="value-title">PREMIUM QUALITY</div>
                    <div class="value-desc">Every product is made with 400GSM heavyweight cotton — built to last,
                        designed to impress. No shortcuts, no compromises.</div>
                </div>
                <div class="value-card">
                    <div class="value-icon">💰</div>
                    <div class="value-title">AFFORDABLE PRICE</div>
                    <div class="value-desc">Premium doesn't have to mean expensive. We keep our prices fair so everyone
                        can wear quality streetwear without breaking the bank.</div>
                </div>
                <div class="value-card">
                    <div class="value-icon">🚚</div>
                    <div class="value-title">FAST DELIVERY</div>
                    <div class="value-desc">Order today, receive in 3-5 business days. With Cash on Delivery available,
                        shopping has never been easier or safer.</div>
                </div>
                <div class="value-card">
                    <div class="value-icon">🎨</div>
                    <div class="value-title">CUSTOM DESIGNS</div>
                    <div class="value-desc">Have a unique design in mind? Upload it and we'll make it for you! Your
                        creativity, our craftsmanship — perfect combination.</div>
                </div>
                <div class="value-card">
                    <div class="value-icon">🔄</div>
                    <div class="value-title">EASY RETURNS</div>
                    <div class="value-desc">Not satisfied? Return within 7 days, no questions asked. Your satisfaction
                        is our top priority, always.</div>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <div class="value-title">COMMUNITY FIRST</div>
                    <div class="value-desc">We believe in giving back. Designers earn 5% on every sale of their uploaded
                        designs. Together we grow together.</div>
                </div>
            </div>
        </div>

        <div class="about-team">
            <p class="section-label">★ The People Behind It</p>
            <h2 class="section-title">OUR TEAM</h2>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-img">👨‍💻</div>
                    <div class="team-info">
                        <div class="team-name">FOUNDER & DEVELOPER</div>
                        <div class="team-role">Full Stack Developer</div>
                        <div class="team-desc">Built the entire NEW_COLLECTION platform from scratch using PHP, MySQL,
                            and modern web technologies. BCA Student, passionate about creating digital experiences.
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-img">🎨</div>
                    <div class="team-info">
                        <div class="team-name">DESIGN TEAM</div>
                        <div class="team-role">Creative Design</div>
                        <div class="team-desc">Our creative team works tirelessly to bring you the freshest designs in
                            hoodies and jackets. New drops every Friday!</div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-img">📦</div>
                    <div class="team-info">
                        <div class="team-name">OPERATIONS TEAM</div>
                        <div class="team-role">Delivery & Support</div>
                        <div class="team-desc">Fast, reliable delivery across India. Our support team is available 24/7
                            on WhatsApp to resolve any issues.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-cta">
            <p class="section-label">★ Join The Movement</p>
            <h2>READY TO WEAR IT <span>LOUD?</span></h2>
            <p>Explore our premium collection of hoodies and jackets. New drops every Friday!</p>
            <div class="cta-btns">
                <a href="products.php" class="btn-primary">Shop Now →</a>
                <a href="contact.php" class="btn-ghost">Contact Us →</a>
            </div>
        </div>

    </div>

    <footer>
        <div class="footer-top">
            <div class="footer-brand">
                <h2>NEW_COLLECTION</h2>
                <p>Premium streetwear for the bold generation. Hoodies & Jackets crafted with passion.</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="products.php">Shop</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Size Guide</a></li>
                    <li><a href="#">Returns</a></li>
                    <li><a href="orders.php">Track Order</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Contact</h4>
                <p>📧 cimonsharma95@gmail.com</p>
                <p>📞 +91 88378 94309</p>
                <p>📍 Jalandhar, Punjab, India</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 NEW_COLLECTION. All Rights Reserved.</p>
            <div class="payment-methods">
                <span>COD</span>
                <span>UPI</span>
                <span>VISA</span>
            </div>
        </div>
    </footer>

    <script src="../js/main.js"></script>
    <script>
        function toggleUserMenu() {
            document.getElementById('userDropdown').classList.toggle('open');
        }
        document.addEventListener('click', function (e) {
            const menu = document.getElementById('userMenu');
            if (menu && !menu.contains(e.target)) {
                document.getElementById('userDropdown').classList.remove('open');
            }
        });
    </script>
</body>

</html>