
<?php
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        $stmt = db()->prepare(
            'SELECT id, full_name, username, password_hash
             FROM users WHERE username = ? LIMIT 1'
        );

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
        error_log($ex->getMessage());
        $error = 'Cannot connect to the database. Please check your database configuration.';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | StockWise</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="login-page">

<div class="login-card">

    <div class="login-logo">S</div>

    <p class="eyebrow">INVENTORY MANAGEMENT</p>

    <h1>Welcome back</h1>

    <p class="muted">Sign in to manage your stock.</p>

    <?php if ($error): ?>
        <div class="alert error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <input type="hidden" name="csrf"
               value="<?= e(csrf_token()) ?>">

        <label>Username</label>

        <input
            type="text"
            name="username"
            required
            autocomplete="username"
            placeholder="Enter your username"
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            required
            autocomplete="current-password"
            placeholder="Enter your password"
        >

        <button type="submit" class="btn primary full">
            Sign In
        </button>

    </form>

    <div style="text-align:center; margin-top:22px;">

        <p class="muted">Don't have an account yet?</p>

        <a href="register.php"
           style="color:#2563eb; font-weight:600; text-decoration:none;">
            Create an Account
        </a>

    </div>

    

</div>

</body>
</html>
