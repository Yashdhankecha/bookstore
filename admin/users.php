<?php
require_once '../includes/header.php';

if(!$userModel->isAdmin()) {
    header("Location: index.php");
    exit();
}

$users = $userModel->getAllUsers();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Admin Sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-5 px-md-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold mb-0">User Management</h2>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 border-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i> User <?php echo htmlspecialchars($_GET['success']); ?> successfully.
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Users Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">UID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td class="ps-4 py-3">#USR-<?php echo $u['id']; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($u['name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $u['role'] == 'admin' ? 'primary' : 'secondary'; ?>-subtle text-<?php echo $u['role'] == 'admin' ? 'primary' : 'secondary'; ?> rounded-pill px-2">
                                    <?php echo ucfirst($u['role']); ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?php echo date("Y-m-d", strtotime($u['created_at'])); ?></td>
                            <td class="text-end pe-4">
                                <?php if($u['id'] == $_SESSION['user_id']): ?>
                                    <span class="text-muted small fst-italic">This is you</span>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light border p-1 px-2 rounded-3 me-1" title="Edit Role" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $u['id']; ?>"><i class="fas fa-user-edit text-primary"></i></button>
                                    <form action="admin_actions.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this user permanently?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-light border p-1 px-2 rounded-3" title="Delete User"><i class="fas fa-trash text-danger"></i></button>
                                    </form>

                                    <!-- Edit Role Modal -->
                                    <div class="modal fade" id="editUserModal<?php echo $u['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="fw-bold">Edit Role: <?php echo htmlspecialchars($u['name']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <form action="admin_actions.php" method="POST">
                                                        <input type="hidden" name="action" value="edit_user_role">
                                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                        <div class="mb-4">
                                                            <label class="form-label small fw-bold">Role</label>
                                                            <select name="role" class="form-select rounded-3">
                                                                <option value="user" <?php echo $u['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                                                <option value="admin" <?php echo $u['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3">Save Role</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
