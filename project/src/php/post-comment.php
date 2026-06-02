<?php
if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
	session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
	echo json_encode(['success' => false, 'error' => 'Du musst eingeloggt sein um zu kommentieren.']);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
	exit;
}

require_once __DIR__ . '/../database/mysql.php';

$postId  = (int) ($_POST['post_id'] ?? 0);
$content = trim((string) ($_POST['content'] ?? ''));
$userId  = (int) $_SESSION['user_id'];

if ($postId <= 0) {
	echo json_encode(['success' => false, 'error' => 'Ungültige Post-ID.']);
	exit;
}

if ($content === '') {
	echo json_encode(['success' => false, 'error' => 'Kommentar darf nicht leer sein.']);
	exit;
}

if (strlen($content) > 2000) {
	echo json_encode(['success' => false, 'error' => 'Kommentar ist zu lang (max. 2000 Zeichen).']);
	exit;
}

$safeContent = mysqli_real_escape_string($conn, $content);

$result = mysqli_query($conn, "INSERT INTO comments (post_id, user_id, content, created_at) VALUES ({$postId}, {$userId}, '{$safeContent}', NOW())");

if ($result) {
	echo json_encode(['success' => true]);
} else {
	echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . mysqli_error($conn)]);
}