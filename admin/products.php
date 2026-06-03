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

    // Delete product
    
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);

        // Step 1: Database se saari photos aur video ka path nikalo
        $get_media = mysqli_query($conn, "SELECT image, image_back, image_detail1, image_detail2, video FROM products WHERE id='$id'");
        $media_row = mysqli_fetch_assoc($get_media);

        // Step 2: Ek-ek karke check karo aur folder se delete (unlink) kardo
        if ($media_row) {
            $files_to_delete = [
                $media_row['image'], 
                $media_row['image_back'], 
                $media_row['image_detail1'], 
                $media_row['image_detail2'], 
                $media_row['video']
            ];

            foreach ($files_to_delete as $file) {
                // Check karo ki database mein path khali toh nahi, aur file asliyat mein folder mein hai ya nahi
                if (!empty($file) && file_exists("../" . $file)) {
                    unlink("../" . $file); // Yeh file ko server se hamesha ke liye uda dega
                }
            }
        }

        
        mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
        header('Location: products.php');
        exit();
    }
    // Helper Function for File Uploads
    function uploadFile($fileInputName, $oldPath) {
        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
            // Folder check/create
            $target_dir = "../images/uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            // Unique filename banaya taaki overwrite na ho
            $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($_FILES[$fileInputName]["name"]));
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $target_file)) {
                return "images/uploads/" . $filename; // DB ke liye path
            }
        }
        return $oldPath; // Agar nayi file nahi aayi, toh purana path return kardo
    }

    // Add/Edit product
    if (isset($_POST['save_product'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $price = intval($_POST['price']);
        $old_price = intval($_POST['old_price']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $sizes = mysqli_real_escape_string($conn, $_POST['sizes']);
        $stock = intval($_POST['stock']);
        $badge = mysqli_real_escape_string($conn, $_POST['badge']);
        $highlights = mysqli_real_escape_string($conn, $_POST['highlights']);

        // File Upload Logic
        $image = mysqli_real_escape_string($conn, uploadFile('image', $_POST['old_image'] ?? ''));
        $image_back = mysqli_real_escape_string($conn, uploadFile('image_back', $_POST['old_image_back'] ?? ''));
        $image_detail1 = mysqli_real_escape_string($conn, uploadFile('image_detail1', $_POST['old_image_detail1'] ?? ''));
        $image_detail2 = mysqli_real_escape_string($conn, uploadFile('image_detail2', $_POST['old_image_detail2'] ?? ''));
        $video = mysqli_real_escape_string($conn, uploadFile('video', $_POST['old_video'] ?? ''));

        if (isset($_POST['product_id']) && $_POST['product_id'] > 0) {
            // Edit
            $id = intval($_POST['product_id']);
            mysqli_query($conn, "UPDATE products SET name='$name', description='$description', price='$price', old_price='$old_price', category='$category', image='$image', image_back='$image_back', image_detail1='$image_detail1', image_detail2='$image_detail2', video='$video', highlights='$highlights', sizes='$sizes', stock='$stock', badge='$badge' WHERE id='$id'");
        } else {
            // Add
            mysqli_query($conn, "INSERT INTO products (name, description, price, old_price, category, image, image_back, image_detail1, image_detail2, video, highlights, sizes, stock, badge) VALUES ('$name', '$description', '$price', '$old_price', '$category', '$image', '$image_back', '$image_detail1', '$image_detail2', '$video', '$highlights', '$sizes', '$stock', '$badge')");
        }
        header('Location: products.php');
        exit();
    }

    // Edit fetch
    $edit_product = null;
    if (isset($_GET['edit'])) {
        $edit_id = intval($_GET['edit']);
        $edit_product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id='$edit_id'"));
    }

    // Products fetch
    $products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products — Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
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

        .sidebar::-webkit-scrollbar {
            display: none;
        }

        .sidebar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-header h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 48px;
            letter-spacing: -1px;
        }

        .btn-gold {
            background: var(--gold);
            color: var(--black);
            border: none;
            padding: 12px 24px;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-gold:hover {
            background: #e8c17a;
        }

        .btn-red {
            background: var(--red);
            color: white;
            border: none;
            padding: 6px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            border: none;
            font-family: 'DM Sans', sans-serif;
        }

        .form-card {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-card h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            margin-bottom: 24px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group.half {
            grid-column: span 2;
        }

        .form-group label {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            background: var(--dark);
            border: 1px solid var(--border);
            color: var(--white);
            padding: 12px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--gold);
        }

        /* File Input Styling */
        .form-group input[type="file"] {
            padding: 9px 14px;
            cursor: pointer;
        }
        .form-group input[type="file"]::file-selector-button {
            background: var(--gold);
            color: var(--black);
            border: none;
            padding: 5px 10px;
            margin-right: 10px;
            font-weight: bold;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            border-radius: 2px;
        }

        .form-section-title {
            grid-column: 1 / -1;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px;
            letter-spacing: 2px;
            color: var(--gold);
            padding: 16px 0 8px;
            border-top: 1px solid var(--border);
            margin-top: 8px;
        }

        .section-card {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 30px;
        }

        .section-card h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            margin-bottom: 20px;
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
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(200, 169, 110, 0.03);
        }

        .product-thumb {
            width: 50px;
            height: 65px;
            object-fit: cover;
            background: var(--dark);
        }

        .badge-tag {
            padding: 3px 10px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .badge-sale {
            background: rgba(232, 68, 68, 0.15);
            color: var(--red);
        }

        .badge-new {
            background: rgba(200, 169, 110, 0.15);
            color: var(--gold);
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-logo">NEW_COLLECTION<span>Admin Panel</span></div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span>📊</span> Dashboard</a></li>
            <li><a href="products.php" class="active"><span>👕</span> Products</a></li>
            <li><a href="orders.php"><span>📦</span> Orders</a></li>
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
                <h1>PRODUCTS</h1>
                <p style="color:var(--muted);font-size:14px;">Manage your products</p>
            </div>
        </div>

        <div class="form-card">
            <h2><?php echo $edit_product ? 'EDIT PRODUCT' : 'ADD NEW PRODUCT'; ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                <?php else: ?>
                    <input type="hidden" name="product_id" value="0">
                <?php endif; ?>
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Product Name</label>
                        <input type="text" name="name" required value="<?php echo $edit_product['name'] ?? ''; ?>">
                    </div>
                    <div class="form-group full">
                        <label>Description</label>
                        <textarea name="description"><?php echo $edit_product['description'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-group full">
                        <label>Key Highlights (comma separated)</label>
                        <input type="text" name="highlights" placeholder="400GSM Cotton, Oversized Fit, Kangaroo Pocket" value="<?php echo $edit_product['highlights'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Price (₹)</label>
                        <input type="number" name="price" required value="<?php echo $edit_product['price'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Old Price (₹) — Optional</label>
                        <input type="number" name="old_price" value="<?php echo $edit_product['old_price'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" required value="<?php echo $edit_product['stock'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="hoodie" <?php echo ($edit_product['category'] ?? '') == 'hoodie' ? 'selected' : ''; ?>>Hoodie</option>
                            <option value="jacket" <?php echo ($edit_product['category'] ?? '') == 'jacket' ? 'selected' : ''; ?>>Jacket</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Badge</label>
                        <select name="badge">
                            <option value="" <?php echo ($edit_product['badge'] ?? '') == '' ? 'selected' : ''; ?>>None</option>
                            <option value="SALE" <?php echo ($edit_product['badge'] ?? '') == 'SALE' ? 'selected' : ''; ?>>SALE</option>
                            <option value="NEW IN" <?php echo ($edit_product['badge'] ?? '') == 'NEW IN' ? 'selected' : ''; ?>>NEW IN</option>
                            <option value="HOT" <?php echo ($edit_product['badge'] ?? '') == 'HOT' ? 'selected' : ''; ?>>HOT</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sizes (comma separated)</label>
                        <input type="text" name="sizes" placeholder="S,M,L,XL,XXL" value="<?php echo $edit_product['sizes'] ?? 'S,M,L,XL,XXL'; ?>">
                    </div>

                    <div class="form-section-title">📸 IMAGES & MEDIA UPLOAD</div>
                    
                    <div class="form-group full">
                        <label>Main Image Path (Front) <?php if($edit_product && $edit_product['image']) echo "<span style='color:var(--gold)'>(Current: ".basename($edit_product['image']).")</span>"; ?></label>
                        <input type="hidden" name="old_image" value="<?php echo $edit_product['image'] ?? ''; ?>">
                        <input type="file" name="image" accept="image/*">
                    </div>
                    
                    <div class="form-group full">
                        <label>Back Image <?php if($edit_product && $edit_product['image_back']) echo "<span style='color:var(--gold)'>(Current: ".basename($edit_product['image_back']).")</span>"; ?></label>
                        <input type="hidden" name="old_image_back" value="<?php echo $edit_product['image_back'] ?? ''; ?>">
                        <input type="file" name="image_back" accept="image/*">
                    </div>
                    
                    <div class="form-group half">
                        <label>Detail Image 1 <?php if($edit_product && $edit_product['image_detail1']) echo "<span style='color:var(--gold)'>(Current: ".basename($edit_product['image_detail1']).")</span>"; ?></label>
                        <input type="hidden" name="old_image_detail1" value="<?php echo $edit_product['image_detail1'] ?? ''; ?>">
                        <input type="file" name="image_detail1" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label>Detail Image 2 <?php if($edit_product && $edit_product['image_detail2']) echo "<span style='color:var(--gold)'>(Current: ".basename($edit_product['image_detail2']).")</span>"; ?></label>
                        <input type="hidden" name="old_image_detail2" value="<?php echo $edit_product['image_detail2'] ?? ''; ?>">
                        <input type="file" name="image_detail2" accept="image/*">
                    </div>
                    
                    <div class="form-group full">
                        <label>Video File (Optional) <?php if($edit_product && $edit_product['video']) echo "<span style='color:var(--gold)'>(Current: ".basename($edit_product['video']).")</span>"; ?></label>
                        <input type="hidden" name="old_video" value="<?php echo $edit_product['video'] ?? ''; ?>">
                        <input type="file" name="video" accept="video/mp4,video/webm">
                    </div>
                </div>
                <div style="margin-top:20px;">
                    <button type="submit" name="save_product" class="btn-gold">
                        <?php echo $edit_product ? '✓ Update Product' : '+ Add Product'; ?>
                    </button>
                    <?php if ($edit_product): ?>
                        <a href="products.php" style="margin-left:10px;color:var(--muted);font-size:13px;">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="section-card">
            <h2>ALL PRODUCTS</h2>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Badge</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td>
                                <img src="../<?php echo $p['image']; ?>" class="product-thumb"
                                     onerror="this.src='https://via.placeholder.com/50x65/161616/c8a96e?text=IMG'">
                            </td>
                            <td><strong><?php echo $p['name']; ?></strong></td>
                            <td style="color:var(--muted);"><?php echo ucfirst($p['category']); ?></td>
                            <td>
                                <strong>₹<?php echo number_format($p['price'], 0); ?></strong>
                                <?php if ($p['old_price']): ?>
                                    <span style="color:var(--muted);font-size:12px;text-decoration:line-through;margin-left:6px;">₹<?php echo number_format($p['old_price'], 0); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['stock'] <= 5 && $p['stock'] > 0): ?>
                                    <span style="color:#ff9800;font-weight:700;"><?php echo $p['stock']; ?> ⚠️</span>
                                <?php elseif ($p['stock'] == 0): ?>
                                    <span style="color:var(--red);font-weight:700;">Out of Stock ❌</span>
                                <?php else: ?>
                                    <?php echo $p['stock']; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['badge']): ?>
                                    <span class="badge-tag <?php echo $p['badge'] == 'SALE' ? 'badge-sale' : 'badge-new'; ?>">
                                        <?php echo $p['badge']; ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:var(--muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:8px;">
                                    <a href="products.php?edit=<?php echo $p['id']; ?>">
                                        <button class="btn-sm btn-gold">Edit</button>
                                    </a>
                                    <a href="products.php?delete=<?php echo $p['id']; ?>"
                                         onclick="return confirm('Are you sure you want to delete this product?')">
                                        <button class="btn-sm btn-red">Delete</button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>