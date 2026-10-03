<?php
require_once 'core/config.php';
require_once 'core/classes/Database.php';
require_once 'core/classes/Product.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$productModel = new Product($db);
$user_id = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? 0;

    if($action === 'add') {
        $productModel->addToWishlist($user_id, $product_id);
        $_SESSION['wishlist_notified'] = true;
        header("Location: shop.php?success=wishlist_added");
        exit();
    } elseif($action === 'remove') {
        $productModel->removeFromWishlist($user_id, $product_id);
        header("Location: wishlist.php?success=wishlist_removed");
        exit();
    }
}

header("Location: shop.php");
exit();
?>
