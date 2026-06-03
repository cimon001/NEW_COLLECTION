<?php
require_once '../php/config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}
$admin_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_admin FROM users WHERE id='{$_SESSION['user_id']}'"));
if(!$admin_check['is_admin']) {
    header('Location: ../index.php');
    exit();
}

// Delete review
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM reviews WHERE id='$id'");
    header('Location: reviews.php');
    exit();
}

// Reviews fetch
$reviews = mysqli_query($conn, "SELECT r.*, u.name as user_name, p.name as product_name FROM reviews r JOIN users u ON r.user_id=u.id JOIN products p ON r.product_id=p.id ORDER BY r.created_at DESC");
$total_reviews = mysqli_num_rows($reviews);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews — Admin Panel</title>
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
        .section-card { background: var(--card); border: 1px solid var(--border); padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(200,169,110,0.03); }
        .stars { color: var(--gold); font-size: 16px; letter-spacing: 2px; }
        .btn-red { background: var(--red); color: white; border: none; padding: 6px 14px; font-family: 'DM Sans', sans-serif; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; }
        .review-text { color: var(--muted); font-size: 13px; max-width: 300px; }
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
            <li><a href="reviews.php" class="active"><span>⭐</span> Reviews</a></li>
            <li><a href="custom_designs.php"><span>🎨</span> Custom Designs</a></li>
            <li><a href="messages.php"><span>✉️</span> Messages</a></li>
            <li style="margin-top:20px;border-top:1px solid var(--border);padding-top:10px;">
                <a href="../index.php"><span>🏠</span> View Website</a>
            </li>
            <li><a href="../php/logout.php"><span>🚪</span> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1>REVIEWS</h1>
            <p style="color:var(--muted);font-size:14px;margin-top:4px;"><?php echo $total_reviews; ?> total reviews</p>
        </div>

        <div class="section-card">
            <?php if($total_reviews == 0): ?>
                <p style="color:var(--muted);text-align:center;padding:40px 0;">No review yet!</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($review = mysqli_fetch_assoc($reviews)): ?>
                    <tr>
                        <td><strong><?php echo $review['user_name']; ?></strong></td>
                        <td style="color:var(--gold);"><?php echo $review['product_name']; ?></td>
                        <td>
                            <div class="stars">
                                <?php for($i=1; $i<=5; $i++) echo $i <= $review['rating'] ? '★' : '☆'; ?>
                            </div>
                        </td>
                        <td class="review-text"><?php echo $review['review']; ?></td>
                        <td style="color:var(--muted);font-size:13px;"><?php echo date('d M Y', strtotime($review['created_at'])); ?></td>
                        <td>
                            <a href="reviews.php?delete=<?php echo $review['id']; ?>"
                               onclick="return confirm('Are you sure you want to delete this review?')">
                                <button class="btn-red">Delete</button>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
