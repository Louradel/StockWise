
<?php
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $username === '' || $password === '') {
        $error = 'Please complete all required fields.';

    } elseif (strlen($full_name) > 120 || strlen($username) > 60) {
        $error = 'Your name or username is too long.';

    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,60}$/', $username)) {
        $error = 'Username must be at least 3 characters and contain only letters, numbers, and underscores.';

    } elseif (strlen($password) < 8) {
        $error = 'Password must contain at least 8 characters.';

    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';

    } else {
        try {
            $check = db()->prepare(
                'SELECT id FROM users WHERE username = ? LIMIT 1'
            );

            $check->execute([$username]);

            if ($check->fetch()) {
                $error = 'This username is already registered. Please choose another.';

            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = db()->prepare(
                    'INSERT INTO users (full_name, username, password_hash)
                     VALUES (?, ?, ?)'
                );

                $stmt->execute([
                    $full_name,
                    $username,
                    $password_hash
                ]);

                $success = 'Account created successfully! You can now sign in.';
            }

        } catch (Throwable $ex) {
            error_log($ex->getMessage());
            $error = 'Registration failed. Please check your database connection.';
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | StockWise</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="login-page">

<div class="login-card">

    <div class="login-logo">S</div>

    <p class="eyebrow">INVENTORY MANAGEMENT</p>

    <h1>Create Account</h1>

    <p class="muted">Register to access StockWise.</p>

    <?php if ($error): ?>
        <div class="alert error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php">

        <input type="hidden" name="csrf"
               value="<?= e(csrf_token()) ?>">

        <label>Full Name</label>

        <input
            type="text"
            name="full_name"
            maxlength="120"
            required
            autocomplete="name"
            placeholder="Enter your full name"
            value="<?= e($_POST['full_name'] ?? '') ?>"
        >

        <label>Username</label>

        <input
            type="text"
            name="username"
            minlength="3"
            maxlength="60"
            pattern="[a-zA-Z0-9_]+"
            required
            autocomplete="username"
            placeholder="Choose a username"
            value="<?= e($_POST['username'] ?? '') ?>"
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            minlength="8"
            required
            autocomplete="new-password"
            placeholder="Create a password"
        >

        <label>Confirm Password</label>

        <input
            type="password"
            name="confirm_password"
            minlength="8"
            required
            autocomplete="new-password"
            placeholder="Confirm your password"
        >

        <button type="submit" class="btn primary full">
            Create Account
        </button>

    </form>

    <div style="text-align:center; margin-top:22px;">

        <p class="muted">Already have an account?</p>

        <a href="login.php"
           style="color:#2563eb; font-weight:600; text-decoration:none;">
            Sign In
        </a>

    </div>

</div>

</body>
</html>
