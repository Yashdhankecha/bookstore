<?php 
require_once 'core/config.php';

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/header.php';

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if($userModel->login($email, $password)) {
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            $userModel->logout();
            $error = "Admin users must login via the Admin Portal.";
        } else {
            header("Location: index.php");
            exit();
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<div class="row min-vh-100 align-items-center justify-content-center py-5">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg rounded-5 p-4 mx-2">
            <div class="text-center mb-4">
                <i class="fas fa-book-open display-4 text-primary mb-3"></i>
                <h3 class="fw-bold">Welcome Back</h3>
                <p class="text-muted small">Login to your Book Store account</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="mb-4" autocomplete="off">
                <!-- Browser Autofill Traps -->
                <input style="display:none" type="text" name="fake_email_remembered"/>
                <input style="display:none" type="password" name="fake_password_remembered"/>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control rounded-3" placeholder="name@example.com" autocomplete="new-password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control rounded-3" placeholder="••••••••" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">Login <i class="fas fa-sign-in-alt ms-1"></i></button>
            </form>

            <div class="text-center">
                <p class="small text-muted mb-0">Don't have an account? <a href="register.php" class="text-primary text-decoration-none fw-bold">Register Now</a></p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="index.php" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
