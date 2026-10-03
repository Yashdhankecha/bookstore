<?php 
ob_start();
require_once 'includes/header.php'; 

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=login_required");
    exit();
}

$user_id = $_SESSION['user_id'];
$cartItems = $cartModel->getCartItems($user_id);
if(empty($cartItems)) {
    header("Location: shop.php");
    exit();
}

$subtotal = 0;
$discount_total = 0;
foreach($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $discount_total += $item['discount'] * $item['quantity'];
}
$total = $subtotal - $discount_total;

$message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Real order placement
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $payment = $_POST['payment'] ?? 'COD';

    // Order items store the actual discounted unit price charged
    $orderItems = array_map(function($item) {
        $item['price'] = $item['price'] - $item['discount'];
        return $item;
    }, $cartItems);

    // Insert into 'orders' and 'order_items' tables
    if($orderModel->createOrder($user_id, $total, $address, $phone, $payment, $orderItems)) {
        $cartModel->clearCart($user_id);
        $_SESSION['last_order_phone'] = $phone;
        header("Location: order-success.php");
        exit();
    } else {
        $message = "Failed to place order. Please try again.";
    }
}
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h4 class="fw-bold mb-4">Shipping Information</h4>
                <form action="checkout.php" method="POST" id="checkoutForm">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" class="form-control rounded-3" value="<?php echo $_SESSION['user_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" class="form-control rounded-3" value="<?php 
                                $u = $userModel->getUserById($user_id);
                                echo $u ? $u['email'] : 'user@example.com'; 
                            ?>" required placeholder="name@example.com">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Phone Number (WhatsApp for Bill)</label>
                            <input type="text" name="phone" id="phone" class="form-control rounded-3" placeholder="+91 98765 43210" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Detailed Address</label>
                            <textarea name="address" class="form-control rounded-3" rows="3" placeholder="Street, Adajan, Surat, Gujarat" required></textarea>
                        </div>
                    </div>

                    <h4 class="fw-bold mb-4">Payment Method</h4>
                    <div class="p-3 border rounded-3 mb-4 bg-light">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment" id="cod" value="COD" checked>
                            <label class="form-check-label fw-bold" for="cod">
                                Cash on Delivery (COD)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment" id="upi" value="UPI">
                            <label class="form-check-label fw-bold" for="upi">
                                UPI / QR Code Payment
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment" id="card" value="CARD">
                            <label class="form-check-label fw-bold" for="card">
                                Credit / Debit Card
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment" id="netbanking" value="NET_BANKING">
                            <label class="form-check-label fw-bold" for="netbanking">
                                Net Banking
                            </label>
                        </div>
                    </div>

                    <!-- Dynamic Payment UI Container -->
                    <div id="payment-extra" class="p-3 border rounded-3 mb-4 bg-white shadow-sm d-none"></div>

                    <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const extra = document.getElementById('payment-extra');
                        const radios = document.querySelectorAll('input[name="payment"]');
                        radios.forEach(radio => {
                            radio.addEventListener('change', (e) => {
                                if(e.target.value === 'UPI') {
                                    extra.classList.remove('d-none');
                                    extra.innerHTML = '<div class="text-center py-3"><i class="fas fa-qrcode display-4 mb-3 text-secondary"></i><p class="mb-0 fw-bold">Scan QR with any UPI App (GPay, PhonePe, Paytm)</p><small class="text-muted">Dummy QR for demo</small></div>';
                                } else if(e.target.value === 'CARD') {
                                    extra.classList.remove('d-none');
                                    extra.innerHTML = '<div class="mb-2"><label class="small fw-bold">Card Number</label><input type="text" class="form-control" placeholder="XXXX XXXX XXXX XXXX"></div><div class="row g-2"><div class="col-6"><label class="small fw-bold">Expiry Date</label><input type="text" class="form-control" placeholder="MM/YY"></div><div class="col-6"><label class="small fw-bold">CVV</label><input type="password" class="form-control" placeholder="123"></div></div>';
                                } else if(e.target.value === 'NET_BANKING') {
                                    extra.classList.remove('d-none');
                                    extra.innerHTML = '<label class="small fw-bold">Select Bank</label><select class="form-select"><option>HDFC Bank</option><option>State Bank of India</option><option>ICICI Bank</option><option>Axis Bank</option></select>';
                                } else {
                                    extra.classList.add('d-none');
                                    extra.innerHTML = '';
                                }
                            });
                        });
                    });
                    </script>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase rounded-3 shadow-sm">Complete Purchase <i class="fas fa-lock ms-1"></i></button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-lg-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">Order Summary</h5>
                <?php foreach($cartItems as $item): ?>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <div class="small">
                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($item['name']); ?></h6>
                        <span class="text-muted">Qty: <?php echo $item['quantity']; ?></span>
                    </div>
                    <span class="fw-bold small"><?php echo CURRENCY; ?><?php echo number_format(($item['price'] - $item['discount']) * $item['quantity'], 2); ?></span>
                </div>
                <?php endforeach; ?>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span><?php echo CURRENCY; ?><?php echo number_format($subtotal, 2); ?></span>
                </div>
                <?php if($discount_total > 0): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Discount</span>
                    <span class="text-danger">-<?php echo CURRENCY; ?><?php echo number_format($discount_total, 2); ?></span>
                </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="text-success">Free</span>
                </div>

                <div class="d-flex justify-content-between my-3 pt-2 border-top">
                    <span class="fw-bold fs-5">TOTAL</span>
                    <span class="fw-bold fs-5 text-primary"><?php echo CURRENCY; ?><?php echo number_format($total, 2); ?></span>
                </div>
                
                <p class="small text-muted text-center mt-3"><i class="fas fa-shield-alt me-1"></i> Secure Checkout Guaranteed</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
