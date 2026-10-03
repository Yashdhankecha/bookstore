<?php
require_once 'core/config.php';
require_once 'core/classes/Database.php';
require_once 'core/classes/Cart.php';

$db = new Database();
$cartModel = new Cart($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? null;
    $qty = $_POST['qty'] ?? 1;

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?error=login_required");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    if ($action === 'add' && $product_id) {
        $cartModel->addToCart($user_id, $product_id, $qty);
        header("Location: cart.php?success=added");
        exit();
    } elseif ($action === 'remove' && isset($_POST['cart_id'])) {
        $cartModel->removeFromCart($_POST['cart_id']);
        header("Location: cart.php?success=removed");
        exit();
    } elseif ($action === 'update_qty' && isset($_POST['cart_id']) && isset($_POST['qty_change'])) {
        $cartModel->updateQuantity($_POST['cart_id'], (int)$_POST['qty_change']);
        header("Location: cart.php");
        exit();
    }
}

header("Location: index.php");
exit();
?>
