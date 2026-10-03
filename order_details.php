<?php 
require_once 'includes/header.php'; 

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch order details ensuring it belongs to the logged-in user
$order = $orderModel->getOrderById($order_id);

if (!$order || $order['user_id'] != $user_id) {
    header("Location: orders.php");
    exit();
}

$items = $orderModel->getOrderItems($order_id);
?>

<div class="container py-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">Order Details <span class="text-primary small">#ORD-<?php echo $order['id']; ?></span></h2>
                <div class="d-flex gap-2">
                    <a href="invoice.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn btn-outline-dark rounded-pill px-4">
                        <i class="fas fa-file-invoice me-1"></i> Print Invoice
                    </a>
                    <a href="orders.php" class="btn btn-outline-primary rounded-pill px-4">Back to My Orders</a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Order Items -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <h5 class="fw-bold mb-0">Items in this Order</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-end pe-4">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($items as $i): ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="assets/images/<?php echo $i['image']; ?>" width="50" height="50" class="rounded object-fit-cover me-3" onerror="this.src='https://via.placeholder.com/50x50?text=B'">
                                                <span class="fw-bold"><?php echo htmlspecialchars($i['product_name']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo CURRENCY . number_format($i['price'], 2); ?></td>
                                        <td><?php echo $i['quantity']; ?></td>
                                        <td class="text-end pe-4 fw-bold"><?php echo CURRENCY . number_format($i['price'] * $i['quantity'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Order Info -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <h5 class="fw-bold mb-0">Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status</span>
                                <span class="badge bg-<?php 
                                    echo $order['status'] == 'delivered' ? 'success' : ($order['status'] == 'pending' ? 'warning' : ($order['status'] == 'processing' ? 'primary' : 'info')); 
                                ?>-subtle text-<?php 
                                    echo $order['status'] == 'delivered' ? 'success' : ($order['status'] == 'pending' ? 'warning' : ($order['status'] == 'processing' ? 'primary' : 'info')); 
                                ?> rounded-pill px-2">
                                    <?php echo $order['status'] == 'processing' ? 'Confirmed' : ucfirst($order['status']); ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Date</span>
                                <span><?php echo date("d M, Y", strtotime($order['created_at'])); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Payment</span>
                                <span class="fw-bold"><?php echo $order['payment_method']; ?></span>
                            </div>
                            <hr class="opacity-25">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <span class="fw-bold fs-5">Paid Amount</span>
                                <span class="fw-bold fs-4 text-primary"><?php echo CURRENCY . number_format($order['total_amount'], 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <h5 class="fw-bold mb-0">Shipping To</h5>
                        </div>
                        <div class="card-body">
                            <p class="fw-bold mb-1"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <?php if(!empty($order['phone'])): ?>
                                <p class="small text-muted mb-2"><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($order['phone']); ?></p>
                            <?php endif; ?>
                            <p class="small text-muted mb-0">
                                <i class="fas fa-map-marker-alt me-1"></i> <?php echo nl2br(htmlspecialchars($order['address'])); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
