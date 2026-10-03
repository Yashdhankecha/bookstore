<?php 
require_once 'includes/header.php'; 

$success = false;
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $msg = $_POST['message'] ?? '';
    
    if(!empty($name) && !empty($email) && !empty($msg)) {
        $stmt = $db->query("INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $msg);
        if($stmt->execute()) {
            $success = true;
        }
    }
}
?>

<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h1 class="fw-bold display-4 mb-3">Get in Touch</h1>
            <p class="lead text-muted mb-4">Have a question about a book, your order, or just want to say hi? Send us a message and we'll get back to you as soon as possible.</p>
            
            <div class="d-flex mb-4 align-items-center">
                <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                    <i class="fas fa-map-marker-alt fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Our Location</h5>
                    <p class="text-muted mb-0">Adajan, Surat, Gujarat, India</p>
                </div>
            </div>
            
            <div class="d-flex mb-4 align-items-center">
                <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                    <i class="fas fa-phone fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Phone Number</h5>
                    <p class="text-muted mb-0">+91 98765 43210</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3">
                    <i class="fas fa-envelope fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Email Address</h5>
                    <p class="text-muted mb-0">support@bookstore.in</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <h3 class="fw-bold mb-4">Send Message</h3>
                <?php if($success): ?>
                    <div class="alert alert-success">Your message has been sent successfully. We will contact you soon.</div>
                <?php endif; ?>
                <form action="contact.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Your Name</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="your.email@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Message</label>
                        <textarea name="message" class="form-control rounded-3" rows="4" placeholder="How can we help you?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">Send Message <i class="fas fa-paper-plane ms-1"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
