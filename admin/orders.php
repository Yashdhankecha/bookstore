<?php 
require_once '../includes/header.php'; 

if(!$userModel->isAdmin()) {
    header("Location: index.php");
    exit();
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Admin Sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-5 px-md-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold mb-0">Order Management</h2>
            </div>

            <!-- Orders Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Order ID</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $orders = $orderModel->getAllOrders();
                        if (empty($orders)): 
                        ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No orders found yet.</td></tr>
                        <?php else: foreach($orders as $order): ?>
                        <tr>
                            <td class="ps-4 py-3">#ORD-<?php echo $order['id']; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td class="text-primary fw-bold"><?php echo CURRENCY . number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <?php
                                    $status_color = ['pending' => 'warning', 'processing' => 'primary', 'shipped' => 'info', 'delivered' => 'success', 'cancelled' => 'danger'][$order['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $status_color; ?>-subtle text-<?php echo $status_color; ?> rounded-pill px-2">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?php echo date("Y-m-d", strtotime($order['created_at'])); ?></td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <form action="admin_actions.php" method="POST" class="d-flex gap-1 m-0">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="redirect" value="orders.php">
                                        <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                        <select name="status" class="form-select form-select-sm rounded-3" style="width: auto;" onchange="this.form.submit()">
                                            <?php foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s): ?>
                                                <option value="<?php echo $s; ?>" <?php echo $order['status'] == $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-light border p-1 px-2 rounded-3" title="View Details">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                <div class="p-4 text-center">
                    <p class="text-muted mb-0 small">Real-time order synchronization enabled.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
