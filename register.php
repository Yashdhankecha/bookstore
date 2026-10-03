<?php 
require_once 'core/config.php';

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/header.php';

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if($userModel->register($name, $email, $password)) {
        $success = "Registration successful! You can now login.";
    } else {
        $error = "Registration failed. Email might already exist.";
    }
}
?>

<div class="row min-vh-100 align-items-center justify-content-center py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-lg rounded-5 p-4 mx-2">
            <div class="text-center mb-4">
                <i class="fas fa-user-plus display-4 text-primary mb-3"></i>
                <h3 class="fw-bold">Create Account</h3>
                <p class="text-muted small">Join the Book Store community</p>
            </div>
            
            <?php if($success): ?>
                <div class="alert alert-success py-2 small text-center"><?php echo $success; ?> <a href="login.php" class="fw-bold">Login</a></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="mb-4" autocomplete="off">
                <!-- Browser Autofill Traps -->
                <input style="display:none" type="text" name="fake_email_remembered"/>
                <input style="display:none" type="password" name="fake_password_remembered"/>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Full Name</label>
                    <input type="text" name="name" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="Enter your full name" autocomplete="new-password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="name@example.com" autocomplete="new-password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="••••••••" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">Sign Up <i class="fas fa-check-circle ms-1"></i></button>
            </form>

            <div class="text-center">
                <p class="small text-muted mb-0">Already have an account? <a href="login.php" class="text-primary text-decoration-none fw-bold">Login Here</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
