<?php
if (!isset($conn)) {
	require_once __DIR__ . '/../database/mysql.php';
}

$islands = [];
$query = "SELECT id, name, description, image_url, country FROM islands ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if ($result instanceof mysqli_result) {
	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		$row = mysqli_fetch_assoc($result);
		if ($row) {
			$islands[] = $row;
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

echo "<section class='featured-islands' id='islands'>";
echo "<div class='container featured-islands-container'>";
echo "<div class='featured-islands-header'><h2>Featured Islands</h2><p>Hand-picked destinations for your next Mediterranean escape</p></div>";

if (!empty($islands)) {
	echo "<div class='island-grid'>";

	for ($i = 0; $i < count($islands); $i++) {
		$island = $islands[$i];
		$name = $escape($island['name'] ?? 'Island');
		$description = $escape($island['description'] ?? 'Discover this beautiful island.');
		$imageUrl = trim((string)($island['image_url'] ?? ''));
		$safeImageUrl = $escape($imageUrl);

		echo "<article class='island-card'>
				<img src='{$safeImageUrl}' alt='{$name}'>
				<div class='island-card-content'>
					<h3>{$name}</h3>
					<p>{$description}</p>
					<div class='island-card-footer'>
						<a class='island-btn' href='src/php/islands-detailed.php?id=" . urlencode($island['id'] ?? '') . "'>View Island</a>
					</div>
				</div>
			</article>";
	}

	echo "</div>";
} else {
	echo "<p class='islands-empty'>Aktuell sind noch keine Inseln in der Datenbank vorhanden.</p>";
}

echo "</div></section>";