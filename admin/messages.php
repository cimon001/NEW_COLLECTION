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

// Delete Message Logic
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM contact_messages WHERE id='$id'");
    header('Location: messages.php');
    exit();
}

// Fetch all messages
$messages = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY created_at DESC");
$total_messages = mysqli_num_rows($messages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --black: #0a0a0a; --dark: #111111; --card: #161616;
            --gold: #c8a96e; --white: #f5f5f0; --muted: #888888;
            --red: #e84444; --border: rgba(200,169,110,0.2);
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
        td { padding: 16px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: top; }
        tr:hover td { background: rgba(200,169,110,0.03); }
        .btn-red { background: var(--red); color: white; border: none; padding: 6px 14px; font-family: 'DM Sans', sans-serif; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; }
        a { text-decoration: none; color: inherit; }
        .msg-box { background: rgba(255,255,255,0.02); padding: 10px; border-radius: 4px; font-size: 13px; line-height: 1.5; color: var(--muted); margin-top: 6px; }
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
            <li><a href="custom_designs.php"><span>🎨</span> Custom Designs</a></li>
            <li><a href="messages.php" class="active"><span>✉️</span> Messages</a></li>
            <li style="margin-top:20px;border-top:1px solid var(--border);padding-top:10px;">
                <a href="../index.php"><span>🏠</span> View Website</a>
            </li>
            <li><a href="../php/logout.php"><span>🚪</span> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1>CUSTOMER MESSAGES</h1>
            <p style="color:var(--muted);font-size:14px;margin-top:4px;"><?php echo $total_messages; ?> total messages received</p>
        </div>

        <div class="section-card">
            <?php if($total_messages == 0): ?>
                <p style="color:var(--muted);text-align:center;padding:40px 0;">No messages yet!</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th width="20%">Customer Details</th>
                        <th width="15%">Subject</th>
                        <th width="45%">Message</th>
                        <th width="10%">Date</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($msg = mysqli_fetch_assoc($messages)): ?>
                    <tr>
                        <td>
                            <strong style="color:var(--gold);"><?php echo $msg['name']; ?></strong><br>
                            <span style="font-size:12px;color:var(--muted);"><?php echo $msg['email']; ?></span>
                        </td>
                        <td style="font-weight:500;"><?php echo $msg['subject']; ?></td>
                        <td>
                            <div class="msg-box"><?php echo nl2br($msg['message']); ?></div>
                        </td>
                        <td style="color:var(--muted);font-size:12px;"><?php echo date('d M Y, h:i A', strtotime($msg['created_at'])); ?></td>
                        <td>
                            <a href="messages.php?delete=<?php echo $msg['id']; ?>"
                               onclick="return confirm('Are you sure you want to delete this message?')">
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