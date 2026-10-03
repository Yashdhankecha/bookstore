<?php 
require_once '../includes/header.php'; 

if(!$userModel->isAdmin()) {
    header("Location: index.php");
    exit();
}

// Handle message deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_message') {
    $id = $_POST['id'];
    $stmt = $db->query("DELETE FROM messages WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: messages.php?success=deleted");
    exit();
}

$success_msg = '';
if(isset($_GET['success']) && $_GET['success'] === 'deleted') {
    $success_msg = "Message deleted successfully.";
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Admin Sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-5 px-md-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold mb-0 text-primary"><i class="fas fa-envelope-open-text me-2"></i>Customer Inquiries</h2>
            </div>

            <?php if($success_msg): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_msg; ?>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Messages Grid -->
            <div class="row g-4">
                <?php 
                $stmt = $db->query("SELECT * FROM messages ORDER BY created_at DESC");
                $stmt->execute();
                $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if(empty($msgs)):
                ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-envelope-open text-muted display-1 mb-3 opacity-25"></i>
                        <p class="text-muted fs-5">No messages found from customers.</p>
                    </div>
                <?php else: foreach($msgs as $m): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 position-relative animate-fade-in">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($m['name']); ?></h6>
                                        <small class="text-primary"><?php echo htmlspecialchars($m['email']); ?></small>
                                    </div>
                                    <span class="badge bg-light text-muted fw-normal rounded-pill px-2 border">
                                        <?php echo date("d M, Y", strtotime($m['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 border border-light-subtle" style="min-height: 120px;">
                                    <p class="small mb-0 text-dark opacity-75"><?php echo nl2br(htmlspecialchars($m['message'])); ?></p>
                                </div>
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <a href="mailto:<?php echo $m['email']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                        <i class="fas fa-reply me-1 text-primary"></i> Reply
                                    </a>
                                    <form action="messages.php" method="POST" class="m-0" onsubmit="return confirm('Delete this message permanently?');">
                                        <input type="hidden" name="action" value="delete_message">
                                        <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-light border p-1 px-2 rounded-3" title="Delete">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
