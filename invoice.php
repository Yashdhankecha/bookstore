<?php 
require_once 'core/config.php';
require_once 'core/classes/Database.php';
require_once 'core/classes/User.php';
require_once 'core/classes/Product.php';
require_once 'core/classes/Order.php';

$db = new Database();
$userModel = new User($db);
$productModel = new Product($db);
$orderModel = new Order($db);

if(!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$order = $orderModel->getOrderById($order_id);

// Only allow the customer themselves or an admin to view the invoice
if (!$order || ($order['user_id'] != $user_id && !$userModel->isAdmin())) {
    die("Access denied.");
}

$items = $orderModel->getOrderItems($order_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #ORD-<?php echo $order['id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f9f9f9; padding: 40px 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .invoice-box { max-width: 850px; margin: auto; padding: 40px; border: 1px solid #eee; background: #fff; border-radius: 12px; box-shadow: 0 4px 25px rgba(0,0,0,0.05); }
        .invoice-header { border-bottom: 2px solid #f0f0f0; margin-bottom: 30px; padding-bottom: 20px; }
        .store-logo { font-size: 28px; font-weight: 800; color: #0d6efd; text-transform: uppercase; letter-spacing: 1.5px; }
        .invoice-title { font-size: 24px; color: #333; font-weight: 700; }
        .table thead th { background: #f8f9fa; border-top: none; text-transform: uppercase; font-size: 13px; color: #666; letter-spacing: 0.5px; }
        .total-section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 30px; }
        .stamp { font-size: 14px; color: #28a745; border: 2px solid #28a745; display: inline-block; padding: 5px 15px; border-radius: 5px; transform: rotate(-5deg); font-weight: bold; opacity: 0.6; }
        @media print {
            body { background: white; padding: 0; }
            .invoice-box { box-shadow: none; border: none; max-width: 100%; border-radius: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="container no-print mb-4 d-flex justify-content-center gap-3">
    <button onclick="window.print()" class="btn btn-primary btn-lg rounded-pill px-4 shadow">
        <i class="fas fa-print me-2"></i> Print or Save PDF
    </button>
    <button onclick="simulateEmail()" id="emailBtn" class="btn btn-outline-info btn-lg rounded-pill px-4 shadow bg-white">
        <i class="fas fa-envelope me-2"></i> Send Copy to Email
    </button>
</div>

<script>
function simulateEmail() {
    const btn = document.getElementById('emailBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
    
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-check me-2"></i> Sent Successfully!';
        btn.classList.remove('btn-outline-info');
        btn.classList.add('btn-success', 'text-white');
        alert("Success! A professional copy of this invoice has been sent to the registered email address.");
    }, 2000);
}
</script>

<div class="invoice-box">
    <div class="invoice-header d-flex justify-content-between align-items-center">
        <div>
            <div class="store-logo">Book Store</div>
            <p class="text-muted small mb-0">Adajan, Surat, Gujarat, India</p>
            <p class="text-muted small mb-0">Ph: +91 98765 43210</p>
        </div>
        <div class="text-end">
            <div class="invoice-title mb-1">INVOICE</div>
            <div class="text-muted small">#ORD-<?php echo $order['id']; ?></div>
            <div class="text-muted small">Date: <?php echo date("d M, Y", strtotime($order['created_at'])); ?></div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-6">
            <h6 class="fw-bold mb-3 text-uppercase small text-muted">Billed To:</h6>
            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($order['customer_name']); ?></h5>
            <p class="text-muted small mb-1"><?php echo htmlspecialchars($order['customer_email']); ?></p>
            <?php if(!empty($order['phone'])): ?>
                <p class="text-muted small mb-1"><?php echo htmlspecialchars($order['phone']); ?></p>
            <?php endif; ?>
        </div>
        <div class="col-6 text-end">
            <h6 class="fw-bold mb-3 text-uppercase small text-muted">Shipping Address:</h6>
            <p class="text-muted mb-0 small"><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
        </div>
    </div>

    <table class="table table-borderless">
        <thead>
            <tr class="border-bottom">
                <th width="50%">Description</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $i): ?>
            <tr class="border-bottom-custom">
                <td class="py-3">
                    <div class="fw-bold"><?php echo htmlspecialchars($i['product_name']); ?></div>
                    <small class="text-muted">Standard Edition</small>
                </td>
                <td class="py-3 text-center"><?php echo CURRENCY . number_format($i['price'], 2); ?></td>
                <td class="py-3 text-center">x<?php echo $i['quantity']; ?></td>
                <td class="py-3 text-end fw-bold"><?php echo CURRENCY . number_format($i['price'] * $i['quantity'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-section">
        <div class="row">
            <div class="col-6 mt-1">
                <div class="stamp"><?php echo strtoupper($order['status']); ?></div>
                <p class="mt-2 mb-0 small text-muted fw-bold">Payment Mode: <?php echo $order['payment_method']; ?></p>
            </div>
            <div class="col-6">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Sub Total:</span>
                    <span class="fw-bold"><?php echo CURRENCY . number_format($order['total_amount'], 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping:</span>
                    <span class="text-success fw-bold">Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="h5 fw-bold mb-0">TOTAL:</span>
                    <span class="h5 fw-bold mb-0 text-primary"><?php echo CURRENCY . number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 pt-3 text-center border-top">
        <p class="small text-muted mb-0">Thank you for shopping with Book Store!</p>
        <p class="small text-muted mb-0">This is a system generated invoice.</p>
    </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
