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

 // Status update
if(isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Get order user_id
    $order_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id FROM orders WHERE id='$order_id'"));
    $order_user_id = $order_data['user_id'];
    
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id='$order_id'");
    
    // Send notification to user
    $order_num = '#NC' . str_pad($order_id, 5, '0', STR_PAD_LEFT);
    if($status == 'delivered') {
        require_once '../php/notification_helper.php';
        if(function_exists('addNotification')) { addNotification($conn, $order_user_id, '✅ Your order ' . $order_num . ' has been delivered! Enjoy your purchase!', 'delivered'); }
    } elseif($status == 'cancelled') {
        require_once '../php/notification_helper.php';
        if(function_exists('addNotification')) { addNotification($conn, $order_user_id, '❌ Your order ' . $order_num . ' has been cancelled. Contact us for help.', 'cancelled'); }
    } elseif($status == 'processing') {
        require_once '../php/notification_helper.php';
        if(function_exists('addNotification')) { addNotification($conn, $order_user_id, '📦 Your order ' . $order_num . ' is now being processed!', 'order'); }
    } elseif($status == 'shipped') {
        require_once '../php/notification_helper.php';
        if(function_exists('addNotification')) { addNotification($conn, $order_user_id, '🚚 Your order ' . $order_num . ' has been shipped! On the way!', 'order'); }
    }
}

// Orders fetch karo (Added phone and address to SQL query)
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$sql = "SELECT o.*, u.name, u.email, u.phone as u_phone, u.address as u_address FROM orders o JOIN users u ON o.user_id=u.id";
if($status_filter) $sql .= " WHERE o.status='$status_filter'";
$sql .= " ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $sql);
$total_orders = mysqli_num_rows($orders);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders — Admin Panel</title>
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
        .page-header { margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .page-header h1 { font-family: 'Bebas Neue', sans-serif; font-size: 48px; letter-spacing: -1px; }
        .filter-tabs { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px; }
        .filter-tab { padding: 8px 20px; font-size: 12px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; border: 1px solid var(--border); color: var(--muted); text-decoration: none; transition: all 0.3s; }
        .filter-tab:hover, .filter-tab.active { border-color: var(--gold); color: var(--gold); background: rgba(200,169,110,0.05); }
        .section-card { background: var(--card); border: 1px solid var(--border); padding: 30px; overflow-x: auto;}
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(200,169,110,0.03); }
        .status-badge { padding: 4px 12px; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .status-pending { background: rgba(200,169,110,0.15); color: var(--gold); }
        .status-delivered { background: rgba(68,232,132,0.15); color: var(--green); }
        .status-cancelled { background: rgba(232,68,68,0.15); color: var(--red); }
        .status-processing { background: rgba(68,132,232,0.15); color: #44a8e8; }
        .status-shipped { background: rgba(156,39,176,0.15); color: #9c27b0; }
        .status-select { background: var(--dark); border: 1px solid var(--border); color: var(--white); padding: 6px 10px; font-family: 'DM Sans', sans-serif; font-size: 12px; cursor: pointer; }
        .btn-sm { padding: 6px 14px; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; border: none; transition: all 0.3s; font-family: 'DM Sans', sans-serif; }
        .btn-gold { background: var(--gold); color: var(--black); }
        .btn-gold:hover { background: #e8c17a; }
        a { text-decoration: none; color: inherit; }
        /* Custom VIP Scrollbar */
::-webkit-scrollbar {
    width: 8px; /* Upar-neeche (vertical) wale scrollbar ki motai */
    height: 8px; /* Daaye-baaye (horizontal) wale scrollbar ki motai */
}

/* Scrollbar ka background (Track) */
::-webkit-scrollbar-track {
    background: var(--black); 
}

/* Scrollbar ka pakadne wala hissa (Thumb) */
::-webkit-scrollbar-thumb {
    background: rgba(200, 169, 110, 0.5); /* Halka Golden color */
    border-radius: 4px;
}

/* Jab mouse scrollbar par jaye toh solid Golden ho jaye */
::-webkit-scrollbar-thumb:hover {
    background: var(--gold); 
}
        
        /* 🔥 YEH HAIN NAYE IMAGE WALE STYLES 🔥 */
        .item-preview { display: flex; align-items: center; gap: 12px; }
        .item-thumb { width: 40px; height: 50px; object-fit: cover; border-radius: 4px; background: var(--dark); border: 1px solid var(--border); }
        .item-details { font-size: 12px; color: var(--muted); max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">NEW_COLLECTION<span>Admin Panel</span></div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span>📊</span> Dashboard</a></li>
            <li><a href="products.php"><span>👕</span> Products</a></li>
            <li><a href="orders.php" class="active"><span>📦</span> Orders</a></li>
            <li><a href="users.php"><span>👥</span> Users</a></li>
            <li><a href="reviews.php"><span>⭐</span> Reviews</a></li>
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
            <div>
                <h1>ORDERS</h1>
                <p style="color:var(--muted);font-size:14px;"><?php echo $total_orders; ?> orders found</p>
            </div>
        </div>

        <div class="filter-tabs">
            <a href="orders.php" class="filter-tab <?php echo !$status_filter ? 'active' : ''; ?>">All</a>
            <a href="orders.php?status=pending" class="filter-tab <?php echo $status_filter=='pending' ? 'active' : ''; ?>">Pending</a>
            <a href="orders.php?status=processing" class="filter-tab <?php echo $status_filter=='processing' ? 'active' : ''; ?>">Processing</a>
            <a href="orders.php?status=shipped" class="filter-tab <?php echo $status_filter=='shipped' ? 'active' : ''; ?>">Shipped</a>
            <a href="orders.php?status=delivered" class="filter-tab <?php echo $status_filter=='delivered' ? 'active' : ''; ?>">Delivered</a>
            <a href="orders.php?status=cancelled" class="filter-tab <?php echo $status_filter=='cancelled' ? 'active' : ''; ?>">Cancelled</a>
        </div>

        <div class="section-card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Items Ordered</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = mysqli_fetch_assoc($orders)): 
                        
                        // 🔥 NAYA LOGIC: FETCH ORDER ITEMS AND IMAGES 🔥
                        $order_id = $order['id'];
                        $items_query = mysqli_query($conn, "SELECT p.name, p.image, oi.quantity FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id='$order_id'");
                        
                        $item_names = [];
                        $first_image = '';
                        
                        while($item = mysqli_fetch_assoc($items_query)) {
                            if(empty($first_image)) {
                                $first_image = $item['image']; // Pehle item ki photo pakad lo
                            }
                            $item_names[] = $item['name'] . " (x" . $item['quantity'] . ")";
                        }
                        $items_string = implode(", ", $item_names);

                        // Checking both 'orders' and 'users' table for phone/address safely
                        $safe_phone = !empty($order['phone']) ? $order['phone'] : (!empty($order['u_phone']) ? $order['u_phone'] : 'N/A');
                        $safe_address = !empty($order['address']) ? $order['address'] : (!empty($order['u_address']) ? $order['u_address'] : 'N/A');
                    ?>
                    <tr>
                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                        
                        <td>
                            <div class="item-preview" title="<?php echo htmlspecialchars($items_string); ?>">
                                <?php if($first_image): ?>
                                    <img src="../<?php echo $first_image; ?>" class="item-thumb" onerror="this.src='https://via.placeholder.com/40x50/161616/c8a96e?text=IMG'">
                                <?php else: ?>
                                    <div class="item-thumb" style="display:flex;align-items:center;justify-content:center;font-size:10px;color:var(--muted);">N/A</div>
                                <?php endif; ?>
                                <div class="item-details">
                                    <span style="color:var(--white);"><?php echo $items_string ? $items_string : 'Item details missing'; ?></span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div><?php echo htmlspecialchars($order['name']); ?></div>
                            <div style="font-size:12px;color:var(--muted);"><?php echo htmlspecialchars($order['email']); ?></div>
                        </td>
                        
                        <td style="font-size:13px; color:var(--muted);">
                            <?php echo htmlspecialchars($safe_phone); ?>
                        </td>
                        <td style="font-size:12px; color:var(--muted); max-width: 200px; line-height: 1.4;">
                            <?php echo htmlspecialchars($safe_address); ?>
                        </td>

                        <td><strong>₹<?php echo number_format($order['total_amount'], 0); ?></strong></td>
                        <td style="color:var(--gold);font-size:12px;">COD</td>
                        <td style="color:var(--muted);font-size:13px;"><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display:flex;gap:8px;align-items:center;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="status-select">
                                    <option value="pending" <?php echo strtolower($order['status'])=='pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo strtolower($order['status'])=='processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo strtolower($order['status'])=='shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo strtolower($order['status'])=='delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo strtolower($order['status'])=='cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn-sm btn-gold">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>