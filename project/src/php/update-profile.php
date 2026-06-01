<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../database/mysql.php';

// Only logged-in users
if (empty($_SESSION['user_id'])) {
    header('Location: login-page.php');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$uid      = (int) $_SESSION['user_id'];
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email']    ?? '');
$bio      = trim($_POST['bio']      ?? '');
$newPw    = $_POST['new_password']  ?? '';
$errors   = [];

// --- Validation ---
if ($username === '') {
    $errors[] = 'Name darf nicht leer sein.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Bitte eine gültige E-Mail-Adresse eingeben.';
}

// Check username uniqueness (exclude current user)
if ($username !== '') {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $uid);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = 'Dieser Username ist bereits vergeben.';
    }
    $stmt->close();
}

// Check email uniqueness (exclude current user)
if ($email !== '') {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $uid);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = 'Diese E-Mail-Adresse wird bereits verwendet.';
    }
    $stmt->close();
}

if (!empty($errors)) {
    // Pass errors back via session and redirect
    $_SESSION['settings_errors'] = $errors;
    header('Location: profile.php?tab=settings');
    exit;
}

// --- Update ---
if ($newPw !== '') {
    // Update including password
    $hashedPw = password_hash($newPw, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, bio = ?, password = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $email, $bio, $hashedPw, $uid);
} else {
    // Update without password
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $bio, $uid);
}

if ($stmt->execute()) {
    $_SESSION['settings_success'] = 'Profil erfolgreich gespeichert!';
    // Update session username in case it changed
    $_SESSION['username'] = $username;
} else {
    $_SESSION['settings_errors'] = ['Fehler beim Speichern: ' . $conn->error];
}
$stmt->close();

header('Location: profile.php?tab=settings');
exit;