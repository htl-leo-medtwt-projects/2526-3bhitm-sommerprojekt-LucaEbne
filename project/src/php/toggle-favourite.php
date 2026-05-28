<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../database/mysql.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'reason' => 'not_logged_in']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true);
$island_id = (int) ($data['island_id'] ?? 0);
$user_id   = (int) $_SESSION['user_id'];

if ($island_id === 0) {
    echo json_encode(['success' => false, 'reason' => 'invalid_island']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM favorite_islands WHERE user_id = ? AND island_id = ?");
$stmt->bind_param("ii", $user_id, $island_id);
$stmt->execute();
$stmt->store_result();
$exists = $stmt->num_rows > 0;
$stmt->close();

if ($exists) {
    $stmt = $conn->prepare("DELETE FROM favorite_islands WHERE user_id = ? AND island_id = ?");
    $stmt->bind_param("ii", $user_id, $island_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'action' => 'removed']);
} else {
    $stmt = $conn->prepare("INSERT INTO favorite_islands (user_id, island_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $island_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'action' => 'added']);
}