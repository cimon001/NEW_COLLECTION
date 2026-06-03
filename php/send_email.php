<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';
require_once __DIR__ . '/phpmailer/Exception.php';

function sendOrderEmail($to_email, $to_name, $order_id, $order_total, $order_address, $items) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'iamaeshavsharma02@gmail.com';
        $mail->Password   = 'yarx qdyy nqcw gkvc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Timeout    = 30;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ];

        // Recipients
        $mail->setFrom('iamaeshavsharma02@gmail.com', 'NEW_COLLECTION');
        $mail->addAddress($to_email, $to_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmed! #NC' . str_pad($order_id, 5, '0', STR_PAD_LEFT);

        // Build items HTML
        $items_html = '';
        foreach($items as $item) {
            $items_html .= "
            <tr>
                <td style='padding:10px;border-bottom:1px solid #f0e6d3;font-size:14px;'>{$item['name']}</td>
                <td style='padding:10px;border-bottom:1px solid #f0e6d3;font-size:14px;text-align:center;'>{$item['size']}</td>
                <td style='padding:10px;border-bottom:1px solid #f0e6d3;font-size:14px;text-align:center;'>{$item['quantity']}</td>
                <td style='padding:10px;border-bottom:1px solid #f0e6d3;font-size:14px;text-align:right;color:#c8a96e;font-weight:bold;'>Rs." . number_format($item['price'] * $item['quantity'], 0) . "</td>
            </tr>";
        }

        $order_num = '#NC' . str_pad($order_id, 5, '0', STR_PAD_LEFT);

        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='margin:0;padding:0;background:#f5f5f0;font-family:Arial,sans-serif;'>
            <div style='max-width:600px;margin:0 auto;background:#ffffff;'>

                <!-- Header -->
                <div style='background:#0a0a0a;padding:30px;text-align:center;'>
                    <h1 style='font-size:28px;letter-spacing:6px;color:#c8a96e;margin:0;'>NEW_COLLECTION</h1>
                    <p style='color:#888;font-size:12px;letter-spacing:3px;margin-top:8px;'>PREMIUM STREETWEAR</p>
                </div>

                <!-- Success Banner -->
                <div style='background:#c8a96e;padding:20px;text-align:center;'>
                    <h2 style='color:#0a0a0a;margin:0;font-size:22px;letter-spacing:2px;'>✓ ORDER CONFIRMED!</h2>
                </div>

                <!-- Body -->
                <div style='padding:30px;'>
                    <p style='font-size:16px;color:#333;'>Hi <strong>{$to_name}</strong>! 🎉</p>
                    <p style='color:#666;font-size:14px;line-height:1.7;'>Your order has been placed successfully. We will deliver it within <strong>3-5 business days</strong>.</p>

                    <!-- Order Info -->
                    <div style='background:#f9f5ed;border-left:4px solid #c8a96e;padding:16px;margin:20px 0;'>
                        <p style='margin:0 0 8px;font-size:13px;color:#888;'>ORDER ID</p>
                        <p style='margin:0;font-size:20px;font-weight:bold;color:#c8a96e;'>{$order_num}</p>
                    </div>

                    <!-- Order Details -->
                    <table style='width:100%;border-collapse:collapse;margin-bottom:20px;'>
                        <tr>
                            <td style='padding:8px 0;font-size:13px;color:#888;'>Delivery Address</td>
                            <td style='padding:8px 0;font-size:13px;font-weight:600;text-align:right;'>{$order_address}</td>
                        </tr>
                        <tr>
                            <td style='padding:8px 0;font-size:13px;color:#888;'>Payment Method</td>
                            <td style='padding:8px 0;font-size:13px;font-weight:600;text-align:right;'>Cash on Delivery</td>
                        </tr>
                    </table>

                    <!-- Items -->
                    <h3 style='font-size:14px;letter-spacing:2px;text-transform:uppercase;color:#333;border-bottom:2px solid #c8a96e;padding-bottom:8px;'>Items Ordered</h3>
                    <table style='width:100%;border-collapse:collapse;'>
                        <thead>
                            <tr style='background:#f9f5ed;'>
                                <th style='padding:10px;text-align:left;font-size:12px;color:#888;'>Product</th>
                                <th style='padding:10px;text-align:center;font-size:12px;color:#888;'>Size</th>
                                <th style='padding:10px;text-align:center;font-size:12px;color:#888;'>Qty</th>
                                <th style='padding:10px;text-align:right;font-size:12px;color:#888;'>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$items_html}
                        </tbody>
                        <tfoot>
                            <tr style='background:#0a0a0a;'>
                                <td colspan='3' style='padding:14px;color:#888;font-size:13px;text-align:right;letter-spacing:2px;'>TOTAL</td>
                                <td style='padding:14px;color:#c8a96e;font-size:18px;font-weight:bold;text-align:right;'>Rs." . number_format($order_total, 0) . "</td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- COD Reminder -->
                    <div style='background:#fff8e1;border:1px solid #ffc107;padding:16px;margin-top:20px;border-radius:4px;'>
                        <p style='margin:0;font-size:13px;color:#856404;'>🚚 <strong>Cash on Delivery:</strong> Please keep <strong>Rs." . number_format($order_total, 0) . "</strong> ready at delivery!</p>
                    </div>
                </div>

                <!-- Footer -->
                <div style='background:#0a0a0a;padding:20px;text-align:center;'>
                    <p style='color:#888;font-size:12px;margin:0;'>NEW_COLLECTION | Punjab, India</p>
                    <p style='color:#666;font-size:11px;margin-top:8px;'>cimonsharma95@gmail.com | +91 88378 94309</p>
                    <p style='color:#444;font-size:11px;margin-top:8px;'>© 2026 NEW_COLLECTION. All Rights Reserved.</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
function sendContactEmail($user_name, $user_email, $msg_subject, $msg_body) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'iamaeshavsharma02@gmail.com'; // Aapka main sender email
        $mail->Password   = 'yarx qdyy nqcw gkvc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender Info
        $mail->setFrom('iamaeshavsharma02@gmail.com', 'Hero\'s Collection Support');

        // 🎯 Yahan humne dono Admins ko add kar diya hai
        $mail->addAddress('bhaiyababu687q@gmail.com', 'Admin Babu Bhaiya');
        $mail->addAddress('cimonsharma95@gmail.com', 'Admin Cimon');

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Request: ' . $msg_subject;

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #c8a96e; padding: 20px;'>
            <h2 style='color: #c8a96e; border-bottom: 1px solid #eee; padding-bottom: 10px;'>New Message from Hero's Collection</h2>
            <p><strong>Name:</strong> {$user_name}</p>
            <p><strong>Email:</strong> {$user_email}</p>
            <p><strong>Subject:</strong> {$msg_subject}</p>
            <div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #c8a96e; margin-top: 15px;'>
                <strong>Message:</strong><br/>
                " . nl2br($msg_body) . "
            </div>
            <p style='font-size: 12px; color: #888; margin-top: 20px;'>*This is an automated alert from your website contact form.</p>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
// 🎯 OTP EMAIL FUNCTION
function sendEmail($to_email, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'iamaeshavsharma02@gmail.com';
        $mail->Password   = 'yarx qdyy nqcw gkvc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Timeout    = 30;

        // Fix SSL certificate issue on localhost/XAMPP
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ];

        $mail->setFrom('iamaeshavsharma02@gmail.com', 'NEW_COLLECTION');
        $mail->addAddress($to_email);
        $mail->CharSet = 'UTF-8';

        // HTML Email for OTP (looks better, less spam)
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='margin:0;padding:0;background:#f5f5f0;font-family:Arial,sans-serif;'>
            <div style='max-width:500px;margin:30px auto;background:#0a0a0a;border:1px solid #c8a96e;'>
                <div style='padding:25px;text-align:center;border-bottom:1px solid #222;'>
                    <h1 style='font-size:24px;letter-spacing:5px;color:#c8a96e;margin:0;'>NEW_COLLECTION</h1>
                    <p style='color:#666;font-size:11px;letter-spacing:3px;margin-top:6px;'>PREMIUM STREETWEAR</p>
                </div>
                <div style='padding:35px;text-align:center;'>
                    <h2 style='color:#ffffff;font-size:20px;letter-spacing:2px;margin-bottom:10px;'>EMAIL VERIFICATION</h2>
                    <p style='color:#999;font-size:14px;line-height:1.7;margin-bottom:30px;'>Use the code below to verify your email address.</p>
                    <div style='background:#1a1a1a;border:2px solid #c8a96e;padding:20px 40px;display:inline-block;margin-bottom:25px;'>
                        <span style='font-size:38px;font-weight:bold;color:#c8a96e;letter-spacing:10px;'>{$message}</span>
                    </div>
                    <p style='color:#666;font-size:12px;'>This code expires in 10 minutes.<br>Do not share this code with anyone.</p>
                </div>
                <div style='background:#050505;padding:15px;text-align:center;'>
                    <p style='color:#444;font-size:11px;margin:0;'>© 2026 NEW_COLLECTION. Punjab, India</p>
                </div>
            </div>
        </body>
        </html>";
        $mail->AltBody = "Your NEW_COLLECTION verification OTP is: {$message}. Valid for 10 minutes.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log('OTP Email Error: ' . $mail->ErrorInfo . ' | To: ' . $to_email);
        return false;
    }
}
?>