<?php
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $dbh;
    private $error;

    public function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Auto-redirect to setup.php if not already there
            $current = basename($_SERVER['SCRIPT_NAME'] ?? '');
            if ($current !== 'setup.php' && php_sapi_name() !== 'cli') {
                // Try to detect setup URL dynamically
                $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
                $dir = preg_replace('#/(admin|core|includes)(/.*)?$#', '', $dir);
                $dir = rtrim($dir, '/');
                $setupUrl = $proto . '://' . $host . $dir . '/setup.php';
                // If headers not sent, redirect
                if (!headers_sent()) {
                    header("Location: $setupUrl");
                    exit;
                } else {
                    echo "<div style='font-family:sans-serif;max-width:600px;margin:40px auto;padding:24px;border:1px solid #fecaca;background:#fef2f2;border-radius:12px;text-align:center'>"
                        . "<h3 style='color:#991b1b'>Database not ready!</h3>"
                        . "<p>XAMPP me MySQL start karo aur <a href='$setupUrl' style='color:#0d6efd;font-weight:bold'>setup.php</a> kholo.</p>"
                        . "<p style='font-size:12px;color:#666'>Error: ".htmlspecialchars($this->error)."</p>"
                        . "<a href='$setupUrl' style='display:inline-block;margin-top:10px;padding:10px 20px;background:#0d6efd;color:#fff;border-radius:999px;text-decoration:none'>Go to Setup →</a>"
                        . "</div>";
                    exit;
                }
            }
            // If we are on setup.php, just store error silently
        }
    }

    public function query($sql) {
        if (!$this->dbh) {
            $current = basename($_SERVER['SCRIPT_NAME'] ?? '');
            if ($current !== 'setup.php') {
                die("<div style='font-family:sans-serif;padding:20px;text-align:center'>Database not connected. Please run <a href='setup.php'><b>setup.php</b></a> once. (XAMPP me MySQL ON karo)</div>");
            }
            die("Database not connected. Setup.php se install karo.");
        }
        return $this->dbh->prepare($sql);
    }

    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

    public function getConnection() {
        return $this->dbh;
    }
}
?>
