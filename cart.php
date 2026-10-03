<?php 
require_once 'includes/header.php'; 

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=login_required");
    exit();
}

$user_id = $_SESSION['user_id'];
$cartItems = $cartModel->getCartItems($user_id);
$subtotal = 0;
$discount_total = 0;
foreach($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $discount_total += $item['discount'] * $item['quantity'];
}
$total = $subtotal - $discount_total;
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Your Shopping Cart</h2>
        </div>
        
        <?php if(empty($cartItems)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-shopping-basket display-1 opacity-25 mb-3"></i>
                <h4 class="fw-light">Your cart is empty.</h4>
                <a href="shop.php" class="btn btn-primary mt-3">Go to Shop</a>
            </div>
        <?php else: ?>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="py-3">Price</th>
                                <th class="py-3">Quantity</th>
                                <th class="py-3 text-end px-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cartItems as $item):
                                $item_price = $item['price'] - $item['discount'];
                                $line_total = $item_price * $item['quantity'];
                            ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/<?php echo $item['image']; ?>" class="rounded-3 me-3" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/60x60?text=S'">
                                        <div>
                                            <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <form action="cart_action.php" method="POST">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                <input type="hidden" name="action" value="remove">
                                                <button type="submit" class="btn btn-link text-danger p-0 small text-decoration-none border-0">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php echo CURRENCY; ?><?php echo number_format($item_price, 2); ?>
                                    <?php if($item['discount'] > 0): ?>
                                        <br><small class="text-muted text-decoration-line-through"><?php echo CURRENCY; ?><?php echo number_format($item['price'], 2); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <form action="cart_action.php" method="POST" class="m-0">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="update_qty">
                                            <input type="hidden" name="qty_change" value="-1">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary px-2 py-0 rounded-circle" style="width:28px; height:28px;" <?php echo $item['quantity']<=1?'disabled':''; ?>>-</button>
                                        </form>
                                        <span class="mx-3 fw-bold fs-5"><?php echo $item['quantity']; ?></span>
                                        <form action="cart_action.php" method="POST" class="m-0">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="update_qty">
                                            <input type="hidden" name="qty_change" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary px-2 py-0 rounded-circle" style="width:28px; height:28px;">+</button>
                                        </form>
                                    </div>
                                </td>
                                <td class="text-end px-4"><?php echo CURRENCY; ?><?php echo number_format($line_total, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span class="fw-bold"><?php echo CURRENCY; ?><?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <?php if($discount_total > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount</span>
                        <span class="text-danger">-<?php echo CURRENCY; ?><?php echo number_format($discount_total, 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span class="text-success">Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-primary"><?php echo CURRENCY; ?><?php echo number_format($total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-primary w-100 py-3 fw-bold text-uppercase rounded-3">Proceed to Checkout</a>
                    <a href="shop.php" class="btn btn-link w-100 text-decoration-none mt-2 text-muted">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
