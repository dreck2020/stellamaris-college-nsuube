<?php
// contact.php - Contact Us Page
session_start();
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

$message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message_text = $_POST['message'];
    
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    if($stmt->execute([$name, $email, $phone, $subject, $message_text])) {
        $message = '<div class="alert alert-success">Message sent successfully! We will respond shortly.</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to send message. Please try again.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Stella Maris College Nsuube</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-wrapper">
        <?php include 'includes/header.php'; ?>
        
        <section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/contact-bg.jpg'); background-size: cover; padding: 60px 0; color: white; text-align: center;">
            <div class="container">
                <h1>Contact Us</h1>
                <p>Get in touch with Stella Maris College</p>
            </div>
        </section>
        
        <section class="contact-section" style="padding: 60px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="contact-info" style="background: #f5f5f5; padding: 30px; border-radius: 15px;">
                            <h3>Get in Touch</h3>
                            <div class="info-item" style="margin: 20px 0;">
                                <i class="fas fa-map-marker-alt" style="font-size: 24px; color: #1a4d8c;"></i>
                                <h4>Address</h4>
                                <p>P.O. Box 123, Nsuube, Uganda</p>
                            </div>
                            <div class="info-item" style="margin: 20px 0;">
                                <i class="fas fa-phone-alt" style="font-size: 24px; color: #1a4d8c;"></i>
                                <h4>Phone</h4>
                                <p>+256 123 456 789</p>
                                <p>+256 987 654 321</p>
                            </div>
                            <div class="info-item" style="margin: 20px 0;">
                                <i class="fas fa-envelope" style="font-size: 24px; color: #1a4d8c;"></i>
                                <h4>Email</h4>
                                <p>info@stellamaris.edu.ug</p>
                                <p>admissions@stellamaris.edu.ug</p>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock" style="font-size: 24px; color: #1a4d8c;"></i>
                                <h4>Office Hours</h4>
                                <p>Monday - Friday: 8:00 AM - 5:00 PM</p>
                                <p>Saturday: 9:00 AM - 1:00 PM</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="contact-form" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            <h3>Send us a Message</h3>
                            <?php echo $message; ?>
                            <form method="POST">
                                <div class="form-group mb-3">
                                    <input type="text" name="name" placeholder="Your Name" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                                </div>
                                <div class="form-group mb-3">
                                    <input type="email" name="email" placeholder="Your Email" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                                </div>
                                <div class="form-group mb-3">
                                    <input type="tel" name="phone" placeholder="Phone Number" class="form-control" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" name="subject" placeholder="Subject" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                                </div>
                                <div class="form-group mb-3">
                                    <textarea name="message" rows="5" placeholder="Your Message" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                                </div>
                                <button type="submit" class="btn-submit" style="background: #1a4d8c; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; width: 100%;">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <?php include 'includes/footer.php'; ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>