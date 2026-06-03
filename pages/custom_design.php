<?php
require_once '../php/config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$cart_count = 0;
$cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='{$_SESSION['user_id']}'");
$cc_row = mysqli_fetch_assoc($cc);
$cart_count = $cc_row['total'] ?? 0;

$success = '';
$error = '';

// Fetch user's designs
$my_designs = mysqli_query($conn, "SELECT * FROM custom_designs WHERE user_id='{$_SESSION['user_id']}' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Design — NEW_COLLECTION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .custom-page { padding: 120px 60px 80px; min-height: 100vh; }
        .upload-box {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 40px;
            margin-top: 40px;
            margin-bottom: 60px;
        }
        .upload-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group.full { grid-column: 1 / -1; }
        .form-group label {
            font-size: 11px; letter-spacing: 2px;
            text-transform: uppercase; color: var(--muted);
        }
        .form-group input,
        .form-group textarea {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            color: var(--white);
            padding: 14px 18px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus { border-color: var(--gold); }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .upload-area {
            border: 2px dashed rgba(200,169,110,0.3);
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        .upload-area:hover { border-color: var(--gold); background: rgba(200,169,110,0.05); }
        .upload-area input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .upload-icon { font-size: 40px; margin-bottom: 10px; }
        .upload-text { font-size: 13px; color: var(--muted); }
        .preview-img {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            margin-top: 10px;
            display: none;
        }
        .btn-submit {
            background: var(--gold);
            color: var(--black);
            border: none;
            padding: 16px 40px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover { background: var(--gold2); transform: translateY(-2px); }
        .alert-success {
            background: rgba(76,175,80,0.1);
            border: 1px solid #4caf50;
            color: #4caf50;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .alert-error {
            background: rgba(232,68,68,0.1);
            border: 1px solid var(--red);
            color: var(--red);
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        /* My Designs */
        .designs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 30px;
        }
        .design-card {
            background: var(--card);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.3s;
        }
        .design-card:hover { border-color: var(--gold); transform: translateY(-4px); }
        .design-img {
            height: 200px;
            overflow: hidden;
            background: #1a1a1a;
        }
        .design-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .design-info { padding: 16px; }
        .design-title { font-size: 15px; font-weight: 600; margin-bottom: 6px; }
        .design-desc { font-size: 12px; color: var(--muted); margin-bottom: 10px; line-height: 1.5; }
        .design-status {
            padding: 4px 12px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-pending { background: rgba(255,165,0,0.1); color: orange; border: 1px solid orange; }
        .status-approved { background: rgba(76,175,80,0.1); color: #4caf50; border: 1px solid #4caf50; }
        .status-rejected { background: rgba(232,68,68,0.1); color: var(--red); border: 1px solid var(--red); }
        .design-earnings {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 20px;
            color: var(--gold);
            margin-top: 8px;
        }
        @media(max-width:768px) {
            .custom-page { padding: 100px 20px 40px; }
            .upload-grid { grid-template-columns: 1fr; }
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

    <div class="custom-page">
        <p class="section-label">★ Custom Order</p>
        <h1 class="section-title">DESIGN YOUR OWN</h1>
        <p style="color:var(--muted);font-size:15px;margin-top:-30px;margin-bottom:40px;">
            Upload your design — we'll make it for you! If others buy your design, you earn <strong style="color:var(--gold);">5% profit</strong> on every sale!
        </p>

        <?php if($success): ?>
            <div class="alert-success">✅ <?php echo $success; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Upload Form -->
        <div class="upload-box">
            <h2 style="font-family:'Bebas Neue',sans-serif;font-size:28px;margin-bottom:24px;letter-spacing:1px;">SUBMIT YOUR DESIGN</h2>
            <form method="POST" action="../php/save_design.php" enctype="multipart/form-data">
                <div class="upload-grid">
                    <div class="form-group full">
                        <label>Design Title</label>
                        <input type="text" name="title" placeholder="e.g. Spider Graphic Hoodie" required>
                    </div>
                    <div class="form-group full">
                        <label>Description — What do you want?</label>
                        <textarea name="description" placeholder="Describe your design in detail — colors, placement, style, size..."></textarea>
                    </div>

                    <!-- Front Image -->
                    <div class="form-group">
                        <label>Front Design Image</label>
                        <div class="upload-area" id="frontArea">
                            <input type="file" name="image_front" accept="image/*" onchange="previewImage(this, 'frontPreview', 'frontArea')">
                            <div class="upload-icon">🖼️</div>
                            <div class="upload-text">Click to upload front design<br><span style="font-size:11px;">PNG, JPG — Max 5MB</span></div>
                            <img id="frontPreview" class="preview-img">
                        </div>
                    </div>

                    <!-- Back Image -->
                    <div class="form-group">
                        <label>Back Design Image (Optional)</label>
                        <div class="upload-area" id="backArea">
                            <input type="file" name="image_back" accept="image/*" onchange="previewImage(this, 'backPreview', 'backArea')">
                            <div class="upload-icon">🖼️</div>
                            <div class="upload-text">Click to upload back design<br><span style="font-size:11px;">PNG, JPG — Max 5MB</span></div>
                            <img id="backPreview" class="preview-img">
                        </div>
                    </div>

                    <div class="form-group full" style="background:rgba(200,169,110,0.06);border:1px solid var(--border);padding:16px;">
                        <p style="font-size:13px;color:var(--muted);line-height:1.7;">
                            ℹ️ After submission, our team will review your design and set the price within <strong style="color:var(--white);">24-48 hours</strong>. You'll be notified once approved!<br>
                            💰 When someone buys your design, you'll earn <strong style="color:var(--gold);">5% of the sale price</strong> automatically!
                        </p>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Submit Design →</button>
            </form>
        </div>

        <!-- My Designs -->
        <p class="section-label">★ My Designs</p>
        <h2 class="section-title" style="font-size:40px;">MY SUBMISSIONS</h2>

        <?php if(mysqli_num_rows($my_designs) > 0): ?>
        <div class="designs-grid">
            <?php while($design = mysqli_fetch_assoc($my_designs)): ?>
            <div class="design-card">
                <div class="design-img">
                    <img src="../uploads/designs/<?php echo $design['image_front']; ?>"
                         onerror="this.src='https://via.placeholder.com/300x200/161616/c8a96e?text=Design'"
                         alt="<?php echo $design['title']; ?>">
                </div>
                <div class="design-info">
                    <div class="design-title"><?php echo $design['title']; ?></div>
                    <div class="design-desc"><?php echo substr($design['description'], 0, 80); ?>...</div>
                    <span class="design-status status-<?php echo $design['status']; ?>">
                        <?php echo ucfirst($design['status']); ?>
                    </span>
                    <?php if($design['price'] > 0): ?>
                        <div style="font-size:13px;color:var(--muted);margin-top:8px;">
                            Price set: <strong style="color:var(--white);">₹<?php echo number_format($design['price'], 0); ?></strong>
                        </div>
                    <?php endif; ?>
                    <div class="design-earnings">
                        💰 Earned: ₹<?php echo number_format($design['total_earnings'], 0); ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:60px 20px;color:var(--muted);">
            <div style="font-size:60px;margin-bottom:16px;">🎨</div>
            <h3 style="font-family:'Bebas Neue',sans-serif;font-size:32px;color:var(--white);margin-bottom:8px;">NO DESIGNS YET</h3>
            <p>Submit your first design above!</p>
        </div>
        <?php endif; ?>
    </div>

    <script src="../js/main.js"></script>
    <script>
    function previewImage(input, previewId, areaId) {
        const file = input.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
    </script>
</body>
</html>