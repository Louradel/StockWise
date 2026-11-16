<?php
require_once __DIR__ . '/includes/functions.php';
if (!empty($_SESSION['user_id'])) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $stmt = db()->prepare('SELECT id, full_name, username, password_hash FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([trim($_POST['username'] ?? '')]);
        $user = $stmt->fetch();
        if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['full_name'];
            redirect('index.php');
        }
        $error = 'Incorrect username or password.';
    } catch (Throwable $ex) {
        $error = 'Cannot connect to the database. Check config/config.php and import database/stockwise.sql.';
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sign in | StockWise</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-page"><div class="login-card"><div class="login-logo">S</div><p class="eyebrow">INVENTORY MANAGEMENT</p><h1>Welcome back</h1><p class="muted">Sign in to manage your stock.</p>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><label>Username</label><input name="username" required autocomplete="username" placeholder="admin"><label>Password</label><input type="password" name="password" required autocomplete="current-password" placeholder="Your password"><button class="btn primary full">Sign in</button></form>
<p class="login-hint">Initial account: <b>admin</b> / <b>Admin@12345</b><br>Change this password after your first login.</p></div></body></html>
