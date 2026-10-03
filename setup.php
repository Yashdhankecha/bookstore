<?php
// setup.php - One Click XAMPP Setup for Bookstore
// Just start XAMPP and open this file in browser: http://localhost/book/setup.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

define('DB_HOST_SETUP', '127.0.0.1');
define('DB_USER_SETUP', 'root');
define('DB_PASS_SETUP', '');
define('DB_NAME_SETUP', 'bookstore');

$projectRoot = __DIR__;
$sqlFile     = $projectRoot . '/database.sql';
$sqlFileAlt  = $projectRoot . '/bookstore (1).sql';
$lockFile    = $projectRoot . '/install.lock';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$folder   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($folder === '.' || $folder === '/' || $folder === '\\') $folder = '';
$appUrl   = $protocol . '://' . $host . $folder;

$status = null; // null | success | error | already
$logs = [];
$force = isset($_GET['force']) && $_GET['force'] == '1';
$action = $_POST['action'] ?? null;

// Helper: reads actual core/config.php so custom credentials persist
function getConfigCreds() {
    $cfgFile = __DIR__ . '/core/config.php';
    $host = DB_HOST_SETUP; $user = DB_USER_SETUP; $pass = DB_PASS_SETUP; $name = DB_NAME_SETUP;
    if (file_exists($cfgFile)) {
        $c = file_get_contents($cfgFile);
        if (preg_match("/define\('DB_HOST',\s*'(.*?)'\)/", $c, $m)) $host = $m[1];
        if (preg_match("/define\('DB_USER',\s*'(.*?)'\)/", $c, $m)) $user = $m[1];
        if (preg_match("/define\('DB_PASS',\s*'(.*?)'\)/", $c, $m)) $pass = $m[1];
        if (preg_match("/define\('DB_NAME',\s*'(.*?)'\)/", $c, $m)) $name = $m[1];
    }
    return [$host, $user, $pass, $name];
}
function isDatabaseReady($host = null, $user = null, $pass = null, $name = null) {
    if ($host === null) {
        [$host, $user, $pass, $name] = getConfigCreds();
    }
    try {
        $pdo = new PDO('mysql:host='.$host.';dbname='.$name, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        // Check core tables exist
        $required = ['users','products','categories','authors','carts','orders'];
        foreach ($required as $t) {
            if (!in_array($t, $tables)) return false;
        }
        return count($tables) >= 6;
    } catch (Exception $e) {
        return false;
    }
}

$alreadyInstalled = file_exists($lockFile) && isDatabaseReady();

// Load current config credentials for default form values & install
[$cfgHost, $cfgUser, $cfgPass, $cfgName] = getConfigCreds();
$dbHost = $_POST['db_host'] ?? $cfgHost;
$dbUser = $_POST['db_user'] ?? $cfgUser;
$dbPass = $_POST['db_pass'] ?? $cfgPass;
$dbName = $_POST['db_name'] ?? $cfgName;
$dbHost = trim($dbHost) ?: $cfgHost;
$dbUser = trim($dbUser) ?: $cfgUser;
$dbName = trim($dbName) ?: $cfgName;

if ($action === 'install') {
    // Force reinstall if requested
    if ($alreadyInstalled && !$force) {
        $status = 'already';
    } else {
        try {
            $logs[] = "Checking system requirements...";
            // 1. Check PHP version
            if (version_compare(PHP_VERSION, '7.4.0', '<')) {
                throw new Exception("PHP 7.4 or higher is required. Current: " . PHP_VERSION);
            }
            $logs[] = "PHP Version: " . PHP_VERSION . " - OK";

            // 2. Check PDO MySQL
            if (!extension_loaded('pdo_mysql')) {
                throw new Exception("PDO MySQL extension is not enabled. Please enable it in php.ini.");
            }
            $logs[] = "PDO MySQL enabled - OK";

            // 3. Check SQL file exists
            $chosenSqlFile = null;
            if (file_exists($sqlFile)) $chosenSqlFile = $sqlFile;
            elseif (file_exists($sqlFileAlt)) $chosenSqlFile = $sqlFileAlt;
            else throw new Exception("database.sql file not found! It must be in the project root.");

            $logs[] = "SQL file found: " . basename($chosenSqlFile);

            // 4. Connect to MySQL server (without DB)
            $logs[] = "Connecting to MySQL (".$dbHost." | ".$dbUser.")...";
            try {
                $pdoServer = new PDO('mysql:host='.$dbHost, $dbUser, $dbPass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
            } catch (PDOException $e) {
                throw new Exception("MySQL connection failed! Is MySQL running in XAMPP? Please check credentials. Error: " . $e->getMessage());
            }
            $logs[] = "MySQL connected successfully!";

            // 5. Create database if not exists
            $logs[] = "Creating database '".$dbName."'...";
            $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `".$dbName."` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            $logs[] = "Database ready";

            // 6. Connect to bookstore DB
            $pdo = new PDO('mysql:host='.$dbHost.';dbname='.$dbName.';charset=utf8mb4', $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            // 7. Import SQL
            $logs[] = "Importing data... please wait";
            $sql = file_get_contents($chosenSqlFile);
            if ($sql === false) throw new Exception("Failed to read SQL file!");

            // Clean SQL: remove CREATE DATABASE / USE lines (we already handled)
            $sql = preg_replace('/CREATE DATABASE.*;/i', '', $sql);
            $sql = preg_replace('/USE\s+`?bookstore`?\s*;/i', '', $sql);
            $sql = preg_replace('/USE\s+`?library_db`?\s*;/i', '', $sql);

            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            $queries = preg_split('/;\s*[\r\n]+/', $sql);
            $executed = 0;
            $skipped = 0;
            foreach ($queries as $q) {
                $q = trim($q);
                if (empty($q) || $q === '' ) { $skipped++; continue; }
                // Skip comment-only chunks
                if (preg_match('/^(\s*--|\s*\/\*|\s*#)/', $q)) {
                    $lines = explode("\n", $q);
                    $filtered = [];
                    foreach ($lines as $line) {
                        $t = trim($line);
                        if ($t === '' || str_starts_with($t, '--') || str_starts_with($t, '#') || str_starts_with($t, '/*')) continue;
                        $filtered[] = $line;
                    }
                    $q = implode("\n", $filtered);
                    $q = trim($q);
                    if ($q === '') { $skipped++; continue; }
                }
                try {
                    $pdo->exec($q);
                    $executed++;
                } catch (PDOException $e) {
                    // Ignore duplicate entry errors on re-install
                    if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
                        $skipped++;
                        continue;
                    }
                    $logs[] = "Query skipped (".substr($q,0,40)."..): " . $e->getMessage();
                }
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            $logs[] = "Import complete! ($executed queries executed)";

            // 8. Verify tables
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $logs[] = "Tables created: " . implode(', ', $tables);

            // Verify admin user exists
            $stmt = $pdo->query("SELECT email FROM users WHERE role='admin' LIMIT 1");
            $admin = $stmt->fetch();
            if ($admin) {
                $logs[] = "Admin user found: " . $admin['email'];
            } else {
                $logs[] = "Admin user not found, creating default admin...";
                $hash = password_hash('password', PASSWORD_DEFAULT);
                $pdo->exec("INSERT IGNORE INTO users (name,email,password,role) VALUES ('Admin','admin@bookstore.com','".$hash."','admin')");
            }

            // 9. Update core/config.php if custom credentials used
            if ($dbHost !== DB_HOST_SETUP || $dbUser !== DB_USER_SETUP || $dbPass !== DB_PASS_SETUP || $dbName !== DB_NAME_SETUP) {
                $configPath = $projectRoot . '/core/config.php';
                if (file_exists($configPath)) {
                    $cfg = file_get_contents($configPath);
                    $cfg = preg_replace("/define\('DB_HOST',\s*'.*?'\);/", "define('DB_HOST', '$dbHost');", $cfg);
                    $cfg = preg_replace("/define\('DB_USER',\s*'.*?'\);/", "define('DB_USER', '$dbUser');", $cfg);
                    $escapedPass = str_replace("'", "\\'", $dbPass);
                    $cfg = preg_replace("/define\('DB_PASS',\s*'.*?'\);/", "define('DB_PASS', '$escapedPass');", $cfg);
                    $cfg = preg_replace("/define\('DB_NAME',\s*'.*?'\);/", "define('DB_NAME', '$dbName');", $cfg);
                    file_put_contents($configPath, $cfg);
                    $logs[] = "core/config.php updated with custom DB credentials";
                }
            }

            // 10. Create lock file
            file_put_contents($lockFile, "Installed on " . date('Y-m-d H:i:s') . "\nAPP_URL: $appUrl\nDB: $dbHost/$dbName\n");

            // 11. Check writable for uploads if folder exists
            $imgDir = $projectRoot . '/assets/images';
            if (!is_dir($imgDir)) {
                @mkdir($imgDir, 0777, true);
                $logs[] = "Created assets/images folder";
            }

            $status = 'success';
            $logs[] = "Setup 100% complete! You can now use the site.";

        } catch (Exception $e) {
            $status = 'error';
            $logs[] = "ERROR: " . $e->getMessage();
        }
    }
} else {
    // GET request - check status for display
    if ($alreadyInstalled && !isset($_GET['force'])) {
        $status = 'already';
    }
}

// Handle reset/lock removal
if (isset($_GET['reset']) && $_GET['reset']=='1') {
    if (file_exists($lockFile)) unlink($lockFile);
    header("Location: setup.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bookstore - One Click Setup</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body{ background:#f8f9fa; font-family:'Segoe UI',system-ui,sans-serif; }
.setup-card{ max-width:780px; margin:40px auto; border-radius:18px; overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,.08); border:0; }
.setup-header{ background:linear-gradient(135deg,#0d6efd 0%,#0b3d91 100%); color:#fff; padding:32px; }
.setup-header h1{ font-weight:800; }
.step{ display:flex; gap:14px; align-items:flex-start; padding:14px 20px; background:#fff; border:1px solid #eef2f7; border-radius:12px; margin-bottom:10px; }
.step i{ width:36px; height:36px; display:grid; place-items:center; border-radius:10px; font-size:16px; flex-shrink:0; }
.log-box{ background:#0f172a; color:#e2e8f0; border-radius:12px; padding:16px; font-family:ui-monospace,monospace; font-size:13px; max-height:320px; overflow:auto; white-space:pre-wrap; }
.success-box{ background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:12px; padding:18px; }
.error-box{ background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:12px; padding:18px; }
.btn-pill{ border-radius:999px; padding:10px 28px; font-weight:700; letter-spacing:.3px; }
.cred-table td{ padding:6px 10px; }
</style>
</head>
<body>

<div class="container px-3">
<div class="card setup-card">
    <div class="setup-header">
        <div class="d-flex align-items-center gap-3 mb-2">
            <span style="width:48px;height:48px;background:rgba(255,255,255,.2);display:grid;place-items:center;border-radius:12px;"><i class="fas fa-book-open fs-4"></i></span>
            <div>
                <h1 class="h3 mb-0">Book Store Setup</h1>
                <small class="opacity-75">XAMPP One-Click Installer • Ready in one click!</small>
            </div>
        </div>
        <div class="mt-3 d-flex flex-wrap gap-2">
            <span class="badge bg-white text-primary"><i class="fas fa-server me-1"></i> XAMPP Ready</span>
            <span class="badge bg-white text-primary"><i class="fas fa-database me-1"></i> Auto DB Import</span>
            <span class="badge bg-white text-primary"><i class="fas fa-magic me-1"></i> Auto Config</span>
            <span class="badge bg-light text-dark">Folder: <?= htmlspecialchars($folder ?: '/') ?> &nbsp;|&nbsp; URL: <?= htmlspecialchars($appUrl) ?></span>
        </div>
    </div>

    <div class="card-body p-4 p-md-5">

        <?php if ($status === 'success'): ?>
            <div class="success-box mb-4">
                <h5 class="fw-bold mb-2"><i class="fas fa-check-circle me-2"></i> Setup Successful! 🎉</h5>
                <p class="mb-0">Your Bookstore project is now <b>ready to run</b>. Use the buttons below to open the site.</p>
            </div>

            <div class="log-box mb-4"><?php foreach($logs as $l) echo htmlspecialchars($l) . "\n"; ?></div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-white h-100">
                        <h6 class="fw-bold"><i class="fas fa-user-shield text-primary me-2"></i>Admin Login</h6>
                        <table class="cred-table small mb-2">
                            <tr><td class="text-muted">Email:</td><td><code>admin@bookstore.com</code></td></tr>
                            <tr><td class="text-muted">Password:</td><td><code>password</code></td></tr>
                            <tr><td class="text-muted">Role:</td><td><span class="badge bg-danger">admin</span></td></tr>
                        </table>
                        <small class="text-muted">Default credentials. You can change the password after login.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-white h-100">
                        <h6 class="fw-bold"><i class="fas fa-link text-success me-2"></i>Quick Links</h6>
                        <div class="d-grid gap-2">
                            <a href="<?= htmlspecialchars($appUrl) ?>/" class="btn btn-primary btn-sm"><i class="fas fa-home me-2"></i>Open Home Page</a>
                            <a href="<?= htmlspecialchars($appUrl) ?>/admin/dashboard.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-gauge me-2"></i>Open Admin Panel</a>
                            <a href="<?= htmlspecialchars($appUrl) ?>/login.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-right-to-bracket me-2"></i>Open User Login</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning small">
                <i class="fas fa-triangle-exclamation me-2"></i><b>Security Tip:</b> Setup is complete. You can delete <code>setup.php</code> or keep it (it will block re-installation automatically). To delete, remove <code>setup.php</code> from the project folder.
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= htmlspecialchars($appUrl) ?>/" class="btn btn-success btn-pill"><i class="fas fa-rocket me-2"></i>Go to Site</a>
                <a href="setup.php?reset=1" class="btn btn-outline-secondary btn-pill" onclick="return confirm('Reset installation lock?')"><i class="fas fa-rotate me-2"></i>Reset Setup</a>
            </div>

        <?php elseif ($status === 'already'): ?>
            <div class="success-box mb-4">
                <h5 class="fw-bold"><i class="fas fa-circle-check me-2"></i> Already Installed!</h5>
                <p class="mb-2">Bookstore is already set up. All tables exist in the database.</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= htmlspecialchars($appUrl) ?>/" class="btn btn-primary btn-sm btn-pill">Go to Home →</a>
                    <a href="<?= htmlspecialchars($appUrl) ?>/admin/dashboard.php" class="btn btn-outline-primary btn-sm btn-pill">Go to Admin Panel →</a>
                </div>
            </div>
            <div class="alert alert-light border small">
                <i class="fas fa-rotate me-2"></i>Need to reinstall? (e.g., to reset the database) Click the button below:<br>
                <a href="setup.php?force=1" class="btn btn-warning btn-sm mt-2" onclick="event.preventDefault(); document.getElementById('forceForm').submit();"><i class="fas fa-hammer me-2"></i>Force Re-Install</a>
                <form id="forceForm" method="POST" class="d-none"><input type="hidden" name="action" value="install"></form>
                <a href="setup.php?reset=1" class="btn btn-outline-danger btn-sm mt-2 ms-2">Reset Lock</a>
            </div>
            <details class="mt-3">
                <summary class="text-muted small" style="cursor:pointer;">Show debug info</summary>
                <div class="log-box mt-2">Lock file: <?= file_exists($lockFile) ? 'YES - '.htmlspecialchars(file_get_contents($lockFile)) : 'NO' ?><br>DB Ready: <?= isDatabaseReady() ? 'YES' : 'NO' ?><br>App URL: <?= htmlspecialchars($appUrl) ?><br>SQL file: <?= file_exists($sqlFile) ? $sqlFile.' (exists)' : 'NOT FOUND' ?></div>
            </details>

        <?php elseif ($status === 'error'): ?>
            <div class="error-box mb-4">
                <h5 class="fw-bold"><i class="fas fa-circle-xmark me-2"></i> Setup Failed!</h5>
                <p class="mb-0">An error occurred. Check the log below and verify XAMPP settings.</p>
            </div>
            <div class="log-box mb-4"><?php foreach($logs as $l) echo htmlspecialchars($l) . "\n"; ?></div>
            <div class="alert alert-info small">
                <b>Common fixes:</b>
                <ul class="mb-0 mt-2">
                    <li>In XAMPP Control Panel, both <b>Apache</b> and <b>MySQL</b> must be <span class="badge bg-success">Started</span> (green)</li>
                    <li>MySQL default port should be <code>3306</code></li>
                    <li>In <code>core/config.php</code>, DB_USER should be <code>root</code> and DB_PASS should be empty (XAMPP default)</li>
                    <li>Refresh this page and click <b>Install</b> again</li>
                </ul>
            </div>
            <a href="setup.php" class="btn btn-primary btn-pill"><i class="fas fa-rotate me-2"></i>Retry</a>

        <?php else: ?>
            <!-- Fresh install screen -->
            <h4 class="fw-bold mb-1">XAMPP One-Click Setup 🚀</h4>
            <p class="text-muted mb-4">Just click <b>Install Now</b> below — the database will be created automatically, tables will be imported, and the site will be ready. No manual phpMyAdmin import needed!</p>

            <div class="step">
                <i class="bg-primary bg-opacity-10 text-primary"><i class="fas fa-check"></i></i>
                <div>
                    <b>1. System Check</b>
                    <div class="small text-muted">PHP <?= PHP_VERSION ?> • PDO MySQL: <?= extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled' ?> • Folder: <?= htmlspecialchars($folder ?: 'htdocs root') ?></div>
                </div>
            </div>
            <div class="step">
                <i class="bg-success bg-opacity-10 text-success"><i class="fas fa-database"></i></i>
                <div>
                    <b>2. Auto Database Import</b>
                    <div class="small text-muted"><?= file_exists($sqlFile) ? 'database.sql found ('.round(filesize($sqlFile)/1024,1).' KB)' : 'database.sql not found' ?> — will be imported automatically</div>
                </div>
            </div>
            <div class="step">
                <i class="bg-warning bg-opacity-10 text-warning"><i class="fas fa-gear"></i></i>
                <div>
                    <b>3. Auto Configuration</b>
                    <div class="small text-muted">APP_URL auto-detected: <code><?= htmlspecialchars($appUrl) ?></code> • DB: root / (no password) / bookstore</div>
                </div>
            </div>

            <form method="POST" class="mt-4">
                <input type="hidden" name="action" value="install">

                <!-- Advanced DB settings (collapsed by default) -->
                <div class="mb-3">
                    <a class="small text-decoration-none" data-bs-toggle="collapse" href="#advSettings" role="button" aria-expanded="false">
                        <i class="fas fa-cog me-1"></i> Advanced Settings (change DB credentials if needed)
                    </a>
                    <div class="collapse mt-2" id="advSettings">
                        <div class="card card-body bg-light border-0 small">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label mb-1">DB Host</label>
                                    <input type="text" name="db_host" value="<?= htmlspecialchars($dbHost) ?>" class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="form-label mb-1">DB Name</label>
                                    <input type="text" name="db_name" value="<?= htmlspecialchars($dbName) ?>" class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="form-label mb-1">DB User</label>
                                    <input type="text" name="db_user" value="<?= htmlspecialchars($dbUser) ?>" class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="form-label mb-1">DB Password</label>
                                    <input type="text" name="db_pass" value="<?= htmlspecialchars($dbPass) ?>" class="form-control form-control-sm" placeholder="(empty for XAMPP)">
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info py-2 px-2 mb-0 small">XAMPP default: Host=127.0.0.1, User=root, Pass=(empty), DB=bookstore<br> Fill this only if you have set a MySQL password.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-pill w-100 shadow">
                    <i class="fas fa-bolt me-2"></i>Install Now — Get Ready in One Click
                </button>
                <div class="text-center mt-2">
                    <small class="text-muted"><i class="fas fa-clock me-1"></i> Takes 2-5 seconds • Keep Apache & MySQL running in XAMPP</small>
                </div>
            </form>

            <div class="mt-4 p-3 bg-light rounded-3 small">
                <b><i class="fas fa-circle-info text-primary me-1"></i> How does it work?</b>
                <ol class="mb-0 mt-2 ps-3">
                    <li>Open XAMPP Control Panel → Start <b>Apache</b> → Start <b>MySQL</b></li>
                    <li>Open in browser: <code><?= htmlspecialchars($appUrl) ?>/setup.php</code></li>
                    <li>Click <b>Install Now</b> → Done! Then open <a href="<?= htmlspecialchars($appUrl) ?>/"><?= htmlspecialchars($appUrl) ?>/</a></li>
                </ol>
            </div>

            <?php if (!extension_loaded('pdo_mysql') || !file_exists($sqlFile)): ?>
            <div class="alert alert-danger mt-3 small mb-0">
                <i class="fas fa-triangle-exclamation me-2"></i><b>Fix before setup:</b>
                <ul class="mb-0 mt-1">
                    <?php if (!extension_loaded('pdo_mysql')) echo "<li>Enable PDO MySQL extension (php.ini)</li>"; ?>
                    <?php if (!file_exists($sqlFile)) echo "<li>Place database.sql file in the project folder</li>"; ?>
                </ul>
            </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>

    <div class="card-footer bg-white text-center small text-muted py-3">
        Bookstore Project • Made for XAMPP • You can delete <span class="text-primary fw-bold">setup.php</span> after installation
    </div>
</div>

<p class="text-center small text-muted mt-3">Need help? Keep MySQL port 3306 • DB credentials: root / (blank) • Admin: admin@bookstore.com / password</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
