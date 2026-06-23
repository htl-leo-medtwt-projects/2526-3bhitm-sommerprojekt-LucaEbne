<?php
session_start();
require_once __DIR__ . '/../database/mysql.php';

$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../src/php/profile.php' : '../../src/php/login-page.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    $file = $_FILES['fileToUpload'];

    if ($file['error'] === UPLOAD_ERR_OK && !empty($file['tmp_name'])) {
        $targetDir = "../../assets/uploads/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $check = getimagesize($file["tmp_name"]);

        if ($check !== false) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($extension, $allowedExtensions, true)) {
                $extension = 'jpg';
            }
            $fileName = 'profile_' . bin2hex(random_bytes(8)) . '.' . $extension;
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                $_SESSION['temp_profile_picture'] = $targetFile;
                echo json_encode(['success' => true, 'path' => $targetFile]);
                exit;
            }
        }
    }

    echo json_encode(['success' => false]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $profilePicture = $_SESSION['temp_profile_picture'] ?? '../../assets/img/default-profile-img.png';

    if ($username !== '' && $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6) {

        $checkStmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ?');
        mysqli_stmt_bind_param($checkStmt, 's', $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        $emailExists = mysqli_stmt_num_rows($checkStmt) > 0;
        mysqli_stmt_close($checkStmt);

        if ($emailExists) {
            $errorMessage = 'Diese E-Mail-Adresse ist bereits registriert. Bitte melde dich an oder verwende eine andere E-Mail.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO users (username, email, password, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())'
            );

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $hashedPassword, $profilePicture);

                if (mysqli_stmt_execute($stmt)) {
                    unset($_SESSION['temp_profile_picture']);
                    header('Location: ../../src/php/login-page.php');
                    exit;
                }

                mysqli_stmt_close($stmt);
            }
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
        <a class="logo" href="../../index.php#home">
            <img src="../../assets/img/Logo.png" alt="Aegean Breeze Logo">
            <span>Aegean Breeze</span>
        </a>
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
                                class="profile-preview-img" id="preview-img" style="cursor: pointer;">

                            <input type="file" id="fileInput" name="fileToUpload" style="display: none;"
                                accept="image/*">
                    </div>
                    <div class="camera-badge" id="camera-btn" style="cursor: pointer;">
                        <i class="fa-solid fa-camera" style="color: rgb(255, 255, 255);"></i>
                    </div>
                    <p class="upload-text">Click to upload a profile picture</p>
                </div>

                <form class="login-form" id="create-account-form" method="post" action="">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Elena K." required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required
                            value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        <?php if (!empty($errorMessage)): ?>
                            <span class="input-error"><?php echo htmlspecialchars($errorMessage); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required
                            minlength="6">
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