 <?php
    require_once '../php/config.php';
    require_once '../php/send_email.php'; // 🎯 Nayi line: Email function file ko link kiya

    // Cart count
    $cart_count = 0;
    if (isset($_SESSION['user_id'])) {
        $cc = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id='{$_SESSION['user_id']}'");
        $cc_row = mysqli_fetch_assoc($cc);
        $cart_count = $cc_row['total'] ?? 0;
    }

    $success = '';
    $error = '';

     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill all required fields!";
    } else {
        // NAYA CODE: Database mein message save karne ke liye
        $sql = "INSERT INTO contact_messages (name, email, subject, message) 
                VALUES ('$name', '$email', '$subject', '$message')";
                
        if(mysqli_query($conn, $sql)) {
            $success = "Message sent successfully! We will reply soon. 😊";
        } else {
            $error = "Failed to send message. Please try again.";
        }
    }
}
    ?>


 <!DOCTYPE html>
 <html lang="en">

 <head>
    <link rel="icon" type="image/png" href="../images/favicon.png">
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Contact — NEW_COLLECTION</title>
     <link rel="stylesheet" href="../css/style.css">
     <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
     <style>
         .contact-page {
             padding: 120px 60px 80px;
             min-height: 100vh;
         }

         .contact-grid {
             display: grid;
             grid-template-columns: 1fr 1fr;
             gap: 60px;
             margin-top: 50px;
         }

         .contact-info h2 {
             font-family: 'Bebas Neue', sans-serif;
             font-size: 42px;
             line-height: 0.9;
             margin-bottom: 20px;
         }

         .contact-info p {
             color: var(--muted);
             font-size: 15px;
             line-height: 1.7;
             margin-bottom: 40px;
         }

         .contact-cards {
             display: flex;
             flex-direction: column;
             gap: 16px;
         }

         .contact-card {
             background: var(--card);
             border: 1px solid var(--border);
             padding: 20px 24px;
             display: flex;
             align-items: center;
             gap: 16px;
             transition: border-color 0.3s;
         }

         .contact-card:hover {
             border-color: var(--gold);
         }

         .contact-card-icon {
             font-size: 28px;
             width: 50px;
             text-align: center;
         }

         .contact-card-title {
             font-size: 11px;
             letter-spacing: 2px;
             text-transform: uppercase;
             color: var(--gold);
             margin-bottom: 4px;
         }

         .contact-card-value {
             font-size: 14px;
             font-weight: 500;
         }

         .contact-hours {
             margin-top: 30px;
             padding: 20px 24px;
             background: rgba(200, 169, 110, 0.05);
             border: 1px solid var(--border);
         }

         .contact-hours-title {
             font-family: 'Bebas Neue', sans-serif;
             font-size: 18px;
             letter-spacing: 2px;
             color: var(--gold);
             margin-bottom: 12px;
         }

         .hours-row {
             display: flex;
             justify-content: space-between;
             font-size: 13px;
             padding: 6px 0;
             border-bottom: 1px solid rgba(255, 255, 255, 0.04);
             color: var(--muted);
         }

         .hours-row span:last-child {
             color: var(--white);
         }

         .contact-form-box {
             background: var(--card);
             border: 1px solid var(--border);
             padding: 40px;
         }

         .contact-form-title {
             font-family: 'Bebas Neue', sans-serif;
             font-size: 28px;
             letter-spacing: 2px;
             margin-bottom: 30px;
         }

         .form-group {
             margin-bottom: 20px;
         }

         .form-group label {
             display: block;
             font-size: 11px;
             letter-spacing: 2px;
             text-transform: uppercase;
             color: var(--muted);
             margin-bottom: 8px;
         }

         .form-group input,
         .form-group textarea,
         .form-group select {
             width: 100%;
             background: rgba(255, 255, 255, 0.04);
             border: 1px solid var(--border);
             color: var(--white);
             padding: 14px 18px;
             font-family: 'DM Sans', sans-serif;
             font-size: 14px;
             outline: none;
             transition: border-color 0.3s;
         }

         .form-group input:focus,
         .form-group textarea:focus,
         .form-group select:focus {
             border-color: var(--gold);
             background: rgba(200, 169, 110, 0.05);
         }

         .form-group input::placeholder,
         .form-group textarea::placeholder {
             color: var(--muted);
         }

         .form-group select option {
             background: var(--dark);
         }

         .form-group textarea {
             resize: vertical;
             min-height: 120px;
         }

         .btn-full {
             width: 100%;
             background: var(--gold);
             color: var(--black);
             padding: 16px;
             border: none;
             font-family: 'DM Sans', sans-serif;
             font-size: 13px;
             font-weight: 700;
             letter-spacing: 2px;
             text-transform: uppercase;
             cursor: pointer;
             transition: all 0.3s;
         }

         .btn-full:hover {
             background: var(--gold2);
             transform: translateY(-2px);
         }

         .alert-error {
             background: rgba(232, 68, 68, 0.1);
             border: 1px solid var(--red);
             color: var(--red);
             padding: 12px 16px;
             font-size: 13px;
             margin-bottom: 20px;
         }

         .alert-success {
             background: rgba(76, 175, 80, 0.1);
             border: 1px solid #4caf50;
             color: #4caf50;
             padding: 12px 16px;
             font-size: 13px;
             margin-bottom: 20px;
         }

         .social-links {
             display: flex;
             gap: 12px;
             margin-top: 30px;
         }

         .social-link {
             width: 44px;
             height: 44px;
             border: 1px solid var(--border);
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 18px;
             transition: all 0.3s;
             text-decoration: none;
         }

         .social-link:hover {
             border-color: var(--gold);
             background: rgba(200, 169, 110, 0.1);
         }

         @media(max-width:768px) {
             .contact-page {
                 padding: 100px 20px 40px;
             }

             .contact-grid {
                 grid-template-columns: 1fr;
                 gap: 30px;
             }
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
             <li><a href="contact.php" style="color:var(--gold)">Contact</a></li>
         </ul>
         <div class="nav-actions">
             <?php if (isset($_SESSION['user_id'])): ?>
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

     <div class="contact-page">
         <p class="section-label">★ Get In Touch</p>
         <h1 class="section-title">CONTACT US</h1>

         <div class="contact-grid">
             <!-- Left — Contact Info -->
             <div class="contact-info">
                 <h2>WE'D LOVE<br>TO HEAR<br>FROM YOU</h2>
                 <p>Any questions — order, size guide, returns — we are here!
                     We reply within 24 hours.</p>
                 <div class="contact-cards">
                     <div class="contact-card">
                         <div class="contact-card-icon">📧</div>
                         <div>
                             <div class="contact-card-title">Email</div>
                             <div class="contact-card-value">cimonsharma95@gmail.com</div>
                         </div>
                     </div>
                     <div class="contact-card">
                         <div class="contact-card-icon">📞</div>
                         <div>
                             <div class="contact-card-title">Phone</div>
                             <div class="contact-card-value">+91 88378 94309</div>
                         </div>
                     </div>
                     <div class="contact-card">
                         <div class="contact-card-icon">📍</div>
                         <div>
                             <div class="contact-card-title">Location</div>
                             <div class="contact-card-value">Jalandhar, Punjab, India</div>
                         </div>
                     </div>
                 </div>

                 <div class="contact-hours">
                     <div class="contact-hours-title">BUSINESS HOURS</div>
                     <div class="hours-row">
                         <span>Monday — Friday</span>
                         <span>9:00 AM — 6:00 PM</span>
                     </div>
                     <div class="hours-row">
                         <span>Saturday</span>
                         <span>10:00 AM — 4:00 PM</span>
                     </div>
                     <div class="hours-row">
                         <span>Sunday</span>
                         <span>Closed</span>
                     </div>
                 </div>

                 <div class="social-links">
                     <a href="#" class="social-link" title="Instagram">📸</a>
                     <a href="#" class="social-link" title="WhatsApp">💬</a>
                     <a href="#" class="social-link" title="Twitter">🐦</a>
                     <a href="#" class="social-link" title="YouTube">▶</a>
                 </div>
             </div>

             <!-- Right — Contact Form -->
             <div class="contact-form-box">
                 <div class="contact-form-title">SEND A MESSAGE</div>

                 <?php if ($error): ?>
                     <div class="alert-error">❌ <?php echo $error; ?></div>
                 <?php endif; ?>

                 <?php if ($success): ?>
                     <div class="alert-success">✅ <?php echo $success; ?></div>
                 <?php endif; ?>

                 <form method="POST" action="">
                     <div class="form-group">
                         <label>Your Name</label>
                         <input type="text" name="name" placeholder="Enter your name"
                             value="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>" required>
                     </div>
                     <div class="form-group">
                         <label>Email Address</label>
                         <input type="email" name="email" placeholder="email@example.com"
                             value="<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>" required>
                     </div>
                     <div class="form-group">
                         <label>Subject</label>
                         <select name="subject">
                             <option value="Order Issue">Order Issue</option>
                             <option value="Return Request">Return Request</option>
                             <option value="Size Guide">Size Guide</option>
                             <option value="Product Query">Product Query</option>
                             <option value="Other">Other</option>
                         </select>
                     </div>
                     <div class="form-group">
                         <label>Message</label>
                         <textarea name="message" placeholder="Write your message..."></textarea>
                     </div>
                     <button type="submit" class="btn-full">Send Message →</button>
                 </form>
             </div>
         </div>
     </div>

     <script src="../js/main.js"></script>

 </body>

 </html>