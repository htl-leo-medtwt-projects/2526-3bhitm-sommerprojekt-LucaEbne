<?php
if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Du musst eingeloggt sein.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

require_once __DIR__ . '/../database/mysql.php';

$postId = (int) ($_POST['post_id'] ?? 0);
$userId = (int) $_SESSION['user_id'];

if ($postId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Post-ID.']);
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT user_id FROM posts WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $postId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $ownerId);
$found = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$found) {
    echo json_encode(['success' => false, 'error' => 'Beitrag wurde nicht gefunden.']);
    exit;
}

if ((int) $ownerId !== $userId) {
    echo json_encode(['success' => false, 'error' => 'Du kannst nur deine eigenen Beiträge löschen.']);
    exit;
}

// Dank ON DELETE CASCADE werden comments, post_images und likes automatisch mitgelöscht
$stmt = mysqli_prepare($conn, 'DELETE FROM posts WHERE id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $postId, $userId);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);