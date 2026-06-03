<?php
require_once '../php/config.php';

// Admin check
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

$admin_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_admin FROM users WHERE id='{$_SESSION['user_id']}'"));
if (!$admin_check['is_admin']) {
    header('Location: ../index.php');
    exit();
}

// fetch stats
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'"))['total'];

// Send offer notification to all users
$notif_success = '';
if (isset($_POST['send_offer'])) {
    $offer_msg = mysqli_real_escape_string($conn, $_POST['offer_message']);
    $all_users = mysqli_query($conn, "SELECT id FROM users");
    while ($u = mysqli_fetch_assoc($all_users)) {
        addNotification($conn, $u['id'], '🏷️ ' . $offer_msg, 'offer');
    }
    $notif_success = "Offer notification sent to all users!";
}
// Last 30 days revenue chart data
$chart_labels = [];
$chart_revenue = [];
$chart_orders = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = ($i % 5 === 0 || $i === 0) ? date('d M', strtotime("-$i days")) : '';
    $chart_labels[] = date('d M', strtotime("-$i days"));
    $rev = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at)='$date' AND status != 'cancelled'"));
    $chart_revenue[] = $rev['total'] ?? 0;
    $ord = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at)='$date'"));
    $chart_orders[] = $ord['total'] ?? 0;
}
$hoodie_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE p.category='hoodie'"))['total'];
$jacket_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE p.category='jacket'"))['total'];
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status='pending'"))['total'];
$processing_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status='processing'"))['total'];
$delivered_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status='delivered'"))['total'];
$cancelled_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status='cancelled'"))['total'];
// Recent orders
$recent_orders = mysqli_query($conn, "SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — NEW_COLLECTION</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --black: #0a0a0a;
            --dark: #111111;
            --card: #161616;
            --gold: #c8a96e;
            --white: #f5f5f0;
            --muted: #888888;
            --red: #e84444;
            --green: #44e884;
            --border: rgba(200, 169, 110, 0.2);
        }

        body {
            background: var(--black);
            color: var(--white);
            font-family: 'DM Sans', sans-serif;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--dark);
            border-right: 1px solid var(--border);
            padding: 30px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 22px;
            letter-spacing: 3px;
            color: var(--gold);
            padding: 0 24px 30px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .sidebar-logo span {
            display: block;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            letter-spacing: 2px;
            color: var(--muted);
            margin-top: 4px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            color: var(--muted);
            font-size: 13px;
            letter-spacing: 1px;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            color: var(--gold);
            background: rgba(200, 169, 110, 0.05);
            border-left-color: var(--gold);
        }

        .sidebar-menu li a span {
            font-size: 18px;
        }
        
        .sidebar::-webkit-scrollbar {display: none; }
        .sidebar { -ms-overflow-style: none;  scrollbar-width: none; }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 48px;
            letter-spacing: -1px;
        }

        .page-header p {
            color: var(--muted);
            font-size: 14px;
            margin-top: 4px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gold);
        }

        .stat-icon {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .stat-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 42px;
            color: var(--gold);
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-label {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* Table */
        .section-card {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-card h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(200, 169, 110, 0.03);
        }

        .status-badge {
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .status-pending {
            background: rgba(200, 169, 110, 0.15);
            color: var(--gold);
        }

        .status-delivered {
            background: rgba(68, 232, 132, 0.15);
            color: var(--green);
        }

        .status-cancelled {
            background: rgba(232, 68, 68, 0.15);
            color: var(--red);
        }

        .status-processing {
            background: rgba(68, 132, 232, 0.15);
            color: #44a8e8;
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }

        .btn-gold {
            background: var(--gold);
            color: var(--black);
        }

        .btn-red {
            background: var(--red);
            color: white;
        }

        .btn-gold:hover {
            background: #e8c17a;
        }

        .btn-red:hover {
            background: #ff5555;
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            NEW_COLLECTION
            <span>Admin Panel</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><span>📊</span> Dashboard</a></li>
            <li><a href="products.php"><span>👕</span> Products</a></li>
            <li><a href="orders.php"><span>📦</span> Orders</a></li>
            <li><a href="users.php"><span>👥</span> Users</a></li>
            <li><a href="reviews.php"><span>⭐</span> Reviews</a></li>
            <li><a href="custom_designs.php"><span>🎨</span> Custom Designs</a></li>
            <li><a href="messages.php"><span>✉️</span> Messages</a></li>
            <li style="margin-top:auto;border-top:1px solid var(--border);padding-top:10px;">
                <a href="../index.php"><span>🏠</span> View Website</a>
            </li>
            <li><a href="../php/logout.php"><span>🚪</span> Logout</a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- Header -->
        <div class="page-header">
            <h1>DASHBOARD</h1>
            <p>Welcome back, <?php echo $_SESSION['user_name']; ?>! 👋</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-value"><?php echo $total_orders; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value">₹<?php echo number_format($total_revenue ?? 0, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👕</div>
                <div class="stat-value"><?php echo $total_products; ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>

        <!-- Charts -->
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:30px;">
            <div style="background:var(--card);border:1px solid var(--border);padding:24px;">
                <h3 style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--gold);margin-bottom:20px;">📈 LAST 30 DAYS REVENUE</h3>
                <canvas id="revenueChart" height="120"></canvas>
            </div>
            <div style="background:var(--card);border:1px solid var(--border);padding:24px;">
                <h3 style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--gold);margin-bottom:20px;">📊 ORDER STATUS</h3>
                <canvas id="statusChart" height="120"></canvas>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:30px;">
            <div style="background:var(--card);border:1px solid var(--border);padding:24px;">
                <h3 style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--gold);margin-bottom:20px;">👕 CATEGORY SALES</h3>
                <canvas id="categoryChart" height="120"></canvas>
            </div>
            <div style="background:var(--card);border:1px solid var(--border);padding:24px;">
                <h3 style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--gold);margin-bottom:20px;">📦 DAILY ORDERS</h3>
                <canvas id="ordersChart" height="120"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="section-card">
            <h2>RECENT ORDERS</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo $order['name']; ?></td>
                            <td>₹<?php echo number_format($order['total_amount'], 0); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="orders.php?id=<?php echo $order['id']; ?>">
                                    <button class="btn-sm btn-gold">View</button>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <!-- Send Offer Notification -->
        <div class="section-card">
            <h2>SEND OFFER NOTIFICATION</h2>
            <?php if ($notif_success): ?>
                <div style="background:rgba(76,175,80,0.1);border:1px solid #4caf50;color:#4caf50;padding:12px 16px;margin-bottom:20px;font-size:13px;">
                    ✅ <?php echo $notif_success; ?>
                </div>
            <?php endif; ?>
            <form method="POST" style="display:flex;gap:12px;align-items:center;">
                <input type="text" name="offer_message" placeholder="e.g. 50% OFF on all Hoodies this weekend!"
                    style="flex:1;background:var(--dark);border:1px solid var(--border);color:var(--white);padding:12px 16px;font-family:'DM Sans',sans-serif;font-size:14px;outline:none;">
                <button type="submit" name="send_offer" class="btn-sm btn-gold" style="padding:12px 24px;white-space:nowrap;">
                    📢 Send to All Users
                </button>
            </form>
        </div>
    </main>
    <script>
        Chart.defaults.color = '#888888';
        Chart.defaults.borderColor = 'rgba(200,169,110,0.1)';

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: <?php echo json_encode($chart_revenue); ?>,
                    borderColor: '#c8a96e',
                    backgroundColor: 'rgba(200,169,110,0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#c8a96e',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processing', 'Delivered', 'Cancelled'],
                datasets: [{
                    data: [<?php echo $pending_count; ?>, <?php echo $processing_count; ?>, <?php echo $delivered_count; ?>, <?php echo $cancelled_count; ?>],
                    backgroundColor: ['#c8a96e', '#44a8e8', '#44e884', '#e84444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: ['Hoodies', 'Jackets'],
                datasets: [{
                    label: 'Items Sold',
                    data: [<?php echo $hoodie_sales; ?>, <?php echo $jacket_sales; ?>],
                    backgroundColor: ['rgba(200,169,110,0.8)', 'rgba(68,168,232,0.8)'],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('ordersChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Orders',
                    data: <?php echo json_encode($chart_orders); ?>,
                    backgroundColor: 'rgba(68,232,132,0.7)',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>