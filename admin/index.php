<?php
require_once '../core/config.php';
require_once '../core/classes/Database.php';
require_once '../core/classes/User.php';

$db = new Database();
$userModel = new User($db);

if($userModel->isAdmin()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if($userModel->login($email, $password)) {
        if ($userModel->isAdmin()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $userModel->logout();
            $error = "Access denied. You do not have admin permissions.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Book Store</title>
    <?php require_once __DIR__ . '/../includes/head-assets.php'; ?>
</head>
<body class="bg-light">

<div class="row min-vh-100 align-items-center justify-content-center py-5">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg rounded-5 p-4 mx-2">
            <div class="text-center mb-4">
                <i class="fas fa-user-shield display-4 text-primary mb-3"></i>
                <h3 class="fw-bold">Admin Portal</h3>
                <p class="text-muted small">Login to manage the Book Store</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST" class="mb-4" autocomplete="off">
                <!-- Browser Autofill Traps -->
                <input style="display:none" type="text" name="fake_email_remembered"/>
                <input style="display:none" type="password" name="fake_password_remembered"/>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control rounded-3" placeholder="admin@example.com" autocomplete="new-password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control rounded-3" placeholder="••••••••" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">Admin Login <i class="fas fa-sign-in-alt ms-1"></i></button>
            </form>

            <div class="text-center mt-4">
                <a href="../index.php" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Back to Storefront</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
