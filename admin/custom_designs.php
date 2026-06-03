<?php
require_once '../php/config.php';

// Admin check
if(!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}
$admin_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_admin FROM users WHERE id='{$_SESSION['user_id']}'"));
if(!$admin_check['is_admin']) {
    header('Location: ../index.php');
    exit();
}

// Approve/Reject/Set Price
if(isset($_POST['update_design'])) {
    $design_id = intval($_POST['design_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $price = floatval($_POST['price']);

    mysqli_query($conn, "UPDATE custom_designs SET status='$status', price='$price' WHERE id='$design_id'");

    // Get design owner
    $design = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id, title FROM custom_designs WHERE id='$design_id'"));

    if($status == 'approved') {
        addNotification($conn, $design['user_id'], '✅ Your design "' . $design['title'] . '" has been approved! Price set: ₹' . number_format($price, 0), 'general');
    } elseif($status == 'rejected') {
        addNotification($conn, $design['user_id'], '❌ Your design "' . $design['title'] . '" was not approved. Contact us for more info.', 'general');
    }
}

// Fetch all designs
$designs = mysqli_query($conn, "SELECT cd.*, u.name as user_name, u.email FROM custom_designs cd JOIN users u ON cd.user_id=u.id ORDER BY cd.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Designs — Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --black: #0a0a0a; --dark: #111111; --card: #161616;
            --gold: #c8a96e; --white: #f5f5f0; --muted: #888888;
            --red: #e84444; --green: #44e884; --border: rgba(200,169,110,0.2);
        }
        body { background: var(--black); color: var(--white); font-family: 'DM Sans', sans-serif; display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--dark); border-right: 1px solid var(--border); padding: 30px 0; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar-logo { font-family: 'Bebas Neue', sans-serif; font-size: 22px; letter-spacing: 3px; color: var(--gold); padding: 0 24px 30px; border-bottom: 1px solid var(--border); margin-bottom: 20px; }
        .sidebar-logo span { display: block; font-family: 'DM Sans', sans-serif; font-size: 11px; letter-spacing: 2px; color: var(--muted); margin-top: 4px; }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 12px; padding: 14px 24px; color: var(--muted); font-size: 13px; letter-spacing: 1px; text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { color: var(--gold); background: rgba(200,169,110,0.05); border-left-color: var(--gold); }
        .sidebar-menu li a span { font-size: 18px; }
        
        .sidebar::-webkit-scrollbar {display: none; }
        .sidebar { -ms-overflow-style: none;  scrollbar-width: none; }
        .main-content { margin-left: 250px; flex: 1; padding: 40px; }
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-family: 'Bebas Neue', sans-serif; font-size: 48px; letter-spacing: -1px; }
        .designs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .design-card { background: var(--card); border: 1px solid var(--border); overflow: hidden; }
        .design-imgs { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; background: #1a1a1a; }
        .design-imgs img { width: 100%; height: 150px; object-fit: cover; }
        .design-info { padding: 20px; }
        .design-title { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .design-user { font-size: 12px; color: var(--gold); margin-bottom: 8px; }
        .design-desc { font-size: 13px; color: var(--muted); line-height: 1.6; margin-bottom: 16px; }
        .design-status { padding: 4px 12px; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; display: inline-block; margin-bottom: 16px; }
        .status-pending { background: rgba(255,165,0,0.1); color: orange; border: 1px solid orange; }
        .status-approved { background: rgba(76,175,80,0.1); color: #4caf50; border: 1px solid #4caf50; }
        .status-rejected { background: rgba(232,68,68,0.1); color: var(--red); border: 1px solid var(--red); }
        .form-inline { display: flex; flex-direction: column; gap: 10px; }
        .form-inline input, .form-inline select {
            background: var(--dark); border: 1px solid var(--border);
            color: var(--white); padding: 10px 12px;
            font-family: 'DM Sans', sans-serif; font-size: 13px;
        }
        .btn-gold { background: var(--gold); color: var(--black); border: none; padding: 10px 20px; font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; transition: all 0.3s; }
        .btn-gold:hover { background: #e8c17a; }
        .earnings-badge { font-family: 'Bebas Neue', sans-serif; font-size: 18px; color: var(--gold); margin-top: 8px; }
        a { text-decoration: none; color: inherit; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">NEW_COLLECTION<span>Admin Panel</span></div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span>📊</span> Dashboard</a></li>
            <li><a href="products.php"><span>👕</span> Products</a></li>
            <li><a href="orders.php"><span>📦</span> Orders</a></li>
            <li><a href="users.php"><span>👥</span> Users</a></li>
            <li><a href="reviews.php"><span>⭐</span> Reviews</a></li>
            <li><a href="custom_designs.php" class="active"><span>🎨</span> Custom Designs</a></li>
            <li><a href="messages.php"><span>✉️</span> Messages</a></li>
            <li style="margin-top:20px;border-top:1px solid var(--border);padding-top:10px;">
                <a href="../index.php"><span>🏠</span> View Website</a>
            </li>
            <li><a href="../php/logout.php"><span>🚪</span> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1>CUSTOM DESIGNS</h1>
            <p style="color:var(--muted);font-size:14px;"><?php echo mysqli_num_rows($designs); ?> designs submitted</p>
        </div>

        <?php if(mysqli_num_rows($designs) > 0): ?>
        <div class="designs-grid">
            <?php while($design = mysqli_fetch_assoc($designs)): ?>
            <div class="design-card">
                <!-- Images -->
                <div class="design-imgs">
                    <img src="../uploads/designs/<?php echo $design['image_front']; ?>"
                         onerror="this.src='https://via.placeholder.com/200x150/161616/c8a96e?text=Front'"
                         alt="Front">
                    <?php if($design['image_back']): ?>
                    <img src="../uploads/designs/<?php echo $design['image_back']; ?>"
                         onerror="this.src='https://via.placeholder.com/200x150/161616/c8a96e?text=Back'"
                         alt="Back">
                    <?php else: ?>
                    <div style="background:#1a1a1a;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:12px;">No Back</div>
                    <?php endif; ?>
                </div>

                <div class="design-info">
                    <div class="design-title"><?php echo $design['title']; ?></div>
                    <div class="design-user">👤 <?php echo $design['user_name']; ?> — <?php echo $design['email']; ?></div>
                    <div class="design-desc"><?php echo $design['description']; ?></div>
                    <span class="design-status status-<?php echo $design['status']; ?>">
                        <?php echo ucfirst($design['status']); ?>
                    </span>
                    <div class="earnings-badge">💰 Earnings: ₹<?php echo number_format($design['total_earnings'], 0); ?></div>
                    <div style="font-size:12px;color:var(--muted);margin-top:4px;margin-bottom:16px;">
                        Submitted: <?php echo date('d M Y', strtotime($design['created_at'])); ?>
                    </div>

                    <!-- Update Form -->
                    <form method="POST" class="form-inline">
                        <input type="hidden" name="design_id" value="<?php echo $design['id']; ?>">
                        <input type="number" name="price" placeholder="Set Price (₹)" value="<?php echo $design['price']; ?>">
                        <select name="status">
                            <option value="pending" <?php echo $design['status']=='pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $design['status']=='approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $design['status']=='rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                        <button type="submit" name="update_design" class="btn-gold">Update →</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:80px 20px;color:var(--muted);">
            <div style="font-size:64px;margin-bottom:16px;">🎨</div>
            <h3 style="font-family:'Bebas Neue',sans-serif;font-size:36px;color:var(--white);margin-bottom:8px;">NO DESIGNS YET</h3>
            <p>No custom designs submitted yet!</p>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>