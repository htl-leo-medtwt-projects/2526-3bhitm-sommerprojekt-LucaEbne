<?php
session_start();
require_once __DIR__ . '/../database/mysql.php';

$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../index.php#home' : '../../src/php/login-page.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username !== '' && $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $profilePicture = null;

        $stmt = mysqli_prepare(
            $conn,
            'INSERT INTO users (username, email, password, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())'
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $hashedPassword, $profilePicture);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: ../../src/php/login-page.php');
                exit;
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
    <script src="https://kit.fontawesome.com/3a03b4384b.js" crossorigin="anonymous"></script>
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
                    <h1>Create your account</h1>
                    <p>Join the community and share your island stories</p>
                </div>

                <div class="profile-upload-container">
                    <div class="profile-image-wrapper">
                        <img src="../../assets/img/default-profile-img.png" alt="Profile Preview"
                            class="profile-preview-img">

                        <div class="camera-badge">
                            <i class="fa-solid fa-camera" style="color: rgb(255, 255, 255);"></i>
                        </div>
                    </div>
                    <p class="upload-text">Click to upload a profile picture</p>
                </div>

                <form class="login-form" method="post" action="">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Elena K." required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required minlength="6">
                        <span class="input-hint">Must be at least 6 characters</span>
                    </div>

                    <button type="submit" class="btn-login">Create account</button>
                </form>

                <div class="login-footer">
                    <p>Already have an account? <a href="../../src/php/login-page.php">Log in</a></p>
                </div>
            </div>
        </div>
    </section>

</body>

</html>