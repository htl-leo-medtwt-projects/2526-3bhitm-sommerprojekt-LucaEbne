<?php
if (!isset($conn)) {
	require_once __DIR__ . '/../database/mysql.php';
}

$community_travel_storys = [];
$query = "SELECT p.id, p.user_id, p.title, p.content, p.image_url, u.username, u.profile_picture
FROM posts p
LEFT JOIN users u ON u.id = p.user_id
ORDER BY p.id ASC";
$result = mysqli_query($conn, $query);

if ($result instanceof mysqli_result) {
	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		$row = mysqli_fetch_assoc($result);
		if ($row) {
			$community_travel_storys[] = $row;
		}
	}
	mysqli_free_result($result);
}
// KI hat vorschgeschlagen diesen Escape-Helper zu verwenden,
// damit der Code übersichtlicher wird und um sicherzustellen, 
// dass alle Ausgaben sicher sind.
$escape = static function ($value): string {
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

echo "<section class='community-travel' id='community'>";
echo "<div class='container community-travel-container'>";
echo "<div class='community-travel-header'><h2>Community Travel Stories</h2><p>Real experiences shared by fellow travelers</p></div>";

if (!empty($community_travel_storys)) {
	echo "<div class='community-story-grid' aria-label='Community travel stories'>";

	for ($i = 0; $i < count($community_travel_storys); $i++) {
		$story = $community_travel_storys[$i];
		$title = $escape($story['title'] ?? 'Travel Story');
		$content = $escape($story['content'] ?? 'No content available');
		$username = $escape($story['username'] ?? 'Anonymous');
		$imageUrl = trim((string)($story['image_url'] ?? ''));
		$profilePicture = trim((string)($story['profile_picture'] ?? 'assets/img/default-profile-img.png'));
		$safeImageUrl = $escape($imageUrl !== '' ? $imageUrl : 'assets/img/hero-santorini.jpg');
		$safeProfilePicture = $escape($profilePicture);

		echo "<article class='community-story-card'>
				<img class='community-story-image' src='{$safeImageUrl}' alt='{$title}'>
				<div class='community-story-content'>
					<h3>{$title}</h3>
					<p>{$content}</p>
					<div class='community-story-author'>";

		if ($safeProfilePicture !== '') {
			echo "<img class='community-author-avatar' src='{$safeProfilePicture}' alt='Profile picture of {$username}'>";
		} else {
			echo "<span class='community-author-avatar community-author-fallback' aria-hidden='true'>" . strtoupper(substr($username, 0, 1)) . "</span>";
		}

		echo "<span class='community-author-name'>{$username}</span></div>
				</div>
			</article>";
	}

	echo "</div>";
} else {
	echo "<p class='community-empty'>Aktuell sind noch keine Community-Stories vorhanden.</p>";
}

echo "</div></section>";