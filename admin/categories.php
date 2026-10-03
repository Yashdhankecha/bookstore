<?php
require_once '../includes/header.php';

if(!$userModel->isAdmin()) {
    header("Location: index.php");
    exit();
}

$categories = $productModel->getCategories();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Admin Sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-5 px-md-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold mb-0">Category Management</h2>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus me-1"></i> Add New Category
                </button>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i> Category <?php echo htmlspecialchars($_GET['success']); ?> successfully.
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Categories Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($categories)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">No categories yet.</td></tr>
                        <?php else: foreach($categories as $cat): ?>
                        <tr>
                            <td class="ps-4 py-3">#<?php echo $cat['id']; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td class="text-muted small"><?php echo htmlspecialchars($cat['description']); ?></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border p-1 px-2 rounded-3 me-1" title="Edit" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?php echo $cat['id']; ?>"><i class="fas fa-edit text-primary"></i></button>
                                <form action="admin_actions.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this category? Its products will become uncategorized.');">
                                    <input type="hidden" name="action" value="delete_category">
                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-light border p-1 px-2 rounded-3"><i class="fas fa-trash text-danger"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editCategoryModal<?php echo $cat['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="fw-bold">Edit Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <form action="admin_actions.php" method="POST">
                                            <input type="hidden" name="action" value="edit_category">
                                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Category Name</label>
                                                <input type="text" name="name" class="form-control rounded-3" value="<?php echo htmlspecialchars($cat['name']); ?>" required>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label small fw-bold">Description</label>
                                                <textarea name="description" class="form-control rounded-3" rows="3"><?php echo htmlspecialchars($cat['description']); ?></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="admin_actions.php" method="POST">
                    <input type="hidden" name="action" value="add_category">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Category Name</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Enter name" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Short description"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3">Save Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
