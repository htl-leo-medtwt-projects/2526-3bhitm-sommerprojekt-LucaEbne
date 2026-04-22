<?php
session_start();
require_once __DIR__ . '/../database/mysql.php';

$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../index.php#home' : '../../src/php/login-page.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: ../../index.php#home');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email !== '' && $password !== '') {
        $stmt = mysqli_prepare($conn, 'SELECT id, username, email, password FROM users WHERE email = ? LIMIT 1');

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result instanceof mysqli_result) {
                $user = mysqli_fetch_assoc($result);

                if ($user && password_verify($password, (string)$user['password'])) {
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['username'] = (string)$user['username'];
                    $_SESSION['email'] = (string)$user['email'];

                    mysqli_free_result($result);
                    mysqli_stmt_close($stmt);
                    header('Location: ../../index.php#home');
                    exit;
                }

                mysqli_free_result($result);
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aegean Breeze</title>
    <link rel="icon" href="../../assets/img/Logo.png" type="image/png">
    <link rel="stylesheet" href="../../src/styles/style.css">
    <link rel="stylesheet" href="../../src/styles/login.css">
    <script src="../../src/scripts/main.js" defer></script>
</head>

<body>
    <!-- Header / Navigation -->
    <header>
        <div class="logo">
            <img src="../../assets/img/Logo.png" alt="Aegean Breeze Logo">
            <span>Aegean Breeze</span>
        </div>
        <nav>
            <div class="nav-element"><a href="../../index.php#home">Home</a></div>
            <div class="nav-element"><a href="../../index.php#islands">Islands</a></div>
            <div class="nav-element"><a href="../../index.php#beaches">Beaches</a></div>
            <div class="nav-element"><a href="../../index.php#community">Community</a></div>
            <div class="nav-element"><a href="<?php echo $navLink; ?>"><?php echo $navLabel; ?></a></div>
        </nav>
    </header>

    <!-- Login Section -->
    <section class="login-page">
        <div class="login-content">
            <div class="login-card">
                <div class="login-header">
                    <img src="../../assets/img/Logo.png" alt="Aegean Breeze Logo" class="login-logo">
                    <h1>Welcome Back</h1>
                    <p>Login to share your travel stories</p>
                </div>
                <form class="login-form" method="post" action="">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-login">Login</button>
                </form>

                <div class="login-footer">
                    <p>Don't have an account? <a href="../../src/php/createAccount.php">Create an account</a></p>
                </div>
            </div>
        </div>
    </section>

</body>

</html>