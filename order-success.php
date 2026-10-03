<?php 
require_once 'includes/header.php'; 
?>

<div class="row min-vh-100 align-items-center justify-content-center py-5 text-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-lg rounded-5 p-5 mx-2">
            <div class="mb-4">
                <div class="rounded-circle bg-success-light p-4 d-inline-block shadow-sm">
                    <i class="fas fa-check-circle display-1 text-success"></i>
                </div>
            </div>
            <h1 class="fw-bold mb-3">Order Placed Perfectly!</h1>
            <p class="lead text-muted mb-3">Thank you for your purchase. We've received your order and it's being processed.</p>
            
            <!-- Email Billing Notification -->
            <div class="alert alert-info border-0 shadow-sm rounded-4 py-3 mb-4 d-inline-block px-4">
                <i class="fas fa-envelope-open-text me-2"></i> A copy of your invoice has been sent to <strong class="text-dark">user@example.com</strong>
            </div>
            
            <?php if(isset($_SESSION['last_order_phone']) && !empty($_SESSION['last_order_phone'])): 
                // Fetch latest order of the user to generate detailed bill
                $latest_order_list = $orderModel->getUserOrders($_SESSION['user_id']);
                $bill_text = "Hello! Please send me the bill for Book Store.\n\n";
                if (!empty($latest_order_list)) {
                    $latest_order = $latest_order_list[0];
                    $bill_text .= "*Order ID*: #ORD-".$latest_order['id']."\n";
                    $bill_text .= "*Total Amount*: ₹".number_format($latest_order['total_amount'], 2)."\n";
                    $bill_text .= "*Payment Mode*: ".$latest_order['payment_method']."\n\n";
                    $bill_text .= "*Order Items*:\n";
                    $items = $orderModel->getOrderItems($latest_order['id']);
                    foreach($items as $i) {
                        $bill_text .= "- " . $i['product_name'] . " x " . $i['quantity'] . " (₹" . $i['price'] . ")\n";
                    }
                    $bill_text .= "\n*View Invoice*: " . APP_URL . "/invoice.php?id=" . $latest_order['id'];
                    $bill_text .= "\n\nPlease let me know the dispatch details. Thank you!";
                }
                
                $phone_num = preg_replace('/[^0-9]/', '', $_SESSION['last_order_phone']);
                $wa_link = "https://wa.me/{$phone_num}?text=" . rawurlencode($bill_text);
            ?>
            <div class="mb-5 py-4 bg-success-subtle rounded-4">
                <i class="fab fa-whatsapp display-4 text-success mb-3"></i>
                <h5 class="fw-bold mb-2">Sending your bill to WhatsApp...</h5>
                <p class="text-muted small mb-3">Please wait 3 seconds while we connect you.</p>
                <a href="<?php echo $wa_link; ?>" id="wa-btn" target="_blank" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm">
                    <i class="fab fa-whatsapp me-2 fs-5 align-middle"></i> Send Bill on WhatsApp
                </a>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const waBtn = document.getElementById('wa-btn');
                    setTimeout(() => {
                        window.open(waBtn.href, '_blank');
                    }, 3000);
                });
            </script>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-6">
                    <a href="orders.php" class="btn btn-outline-primary w-100 py-3 fw-bold rounded-3">View Complete Order</a>
                </div>
                <div class="col-6">
                    <a href="index.php" class="btn btn-primary w-100 py-3 fw-bold rounded-3 text-white">Back to Home <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
