<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/classes/Database.php';
require_once __DIR__ . '/../core/classes/Product.php';
require_once __DIR__ . '/../core/classes/User.php';
require_once __DIR__ . '/../core/classes/Cart.php';
require_once __DIR__ . '/../core/classes/Order.php';

$db = new Database();
$productModel = new Product($db);
$userModel = new User($db);
$cartModel = new Cart($db);
$orderModel = new Order($db);

if(isset($_SESSION['user_id']) && (!isset($_SESSION['user_name']) || empty($_SESSION['user_name']))) {
    $user = $userModel->getUserById($_SESSION['user_id']);
    if($user) {
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Store | Premium Books & Novels</title>
    <?php require_once __DIR__ . '/head-assets.php'; ?>
</head>
<body>

<?php 
// Check if we are in admin area
$is_admin_page = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);
?>

<?php if(!$is_admin_page): ?>
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo APP_URL; ?>">
            <i class="fas fa-book-open me-2 text-primary"></i><span class="brand-text">Book Store</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/shop.php">Shop</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Categories</a>
                    <ul class="dropdown-menu shadow border-0">
                        <?php 
                        $cats = $productModel->getCategories();
                        foreach($cats as $c): 
                        ?>
                        <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/shop.php?category=<?php echo $c['id']; ?>"><?php echo $c['name']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/contact.php">Contact</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item position-relative me-3">
                    <a class="nav-link text-danger" href="<?php echo APP_URL; ?>/wishlist.php" title="Wishlist">
                        <i class="fas fa-heart fs-5"></i>
                        <?php if(isset($_SESSION['wishlist_notified']) && $_SESSION['wishlist_notified'] === true): ?>
                        <span class="cart-badge bg-danger">
                            <?php 
                            if(isset($_SESSION['user_id'])) {
                                $w_items = $productModel->getWishlistItems($_SESSION['user_id']);
                                echo count($w_items);
                            } else {
                                echo "0";
                            }
                            ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item position-relative me-3">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/cart.php" title="Cart">
                        <i class="fas fa-book-reader fs-5"></i>
                        <span class="cart-badge">
                            <?php 
                            if(isset($_SESSION['user_id'])) {
                                $items = $cartModel->getCartItems($_SESSION['user_id']);
                                echo count($items);
                            } else {
                                echo "0";
                            }
                            ?>
                        </span>
                    </a>
                </li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <?php if($userModel->isAdmin()): ?>
                                <li><a class="dropdown-item text-primary fw-bold" href="<?php echo APP_URL; ?>/admin/dashboard.php"><i class="fas fa-user-shield me-2"></i>Admin Panel</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/orders.php">My Orders</a></li>
                            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/wishlist.php">My Wishlist</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo APP_URL; ?>/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary px-3 py-1 ms-2" href="<?php echo APP_URL; ?>/login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>
<main class="min-vh-100">
