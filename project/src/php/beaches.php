<?php
if (!isset($conn)) {
	require_once __DIR__ . '/../database/mysql.php';
}

$beaches = [];
$query = "SELECT id, island_id, name, description, image_url, sand_type FROM beaches ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if ($result instanceof mysqli_result) {
	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		$row = mysqli_fetch_assoc($result);
		if ($row) {
			$beaches[] = $row;
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

echo "<section class='stunning-beaches' id='beaches'>";
echo "<div class='container stunning-beaches-container'>";
echo "<div class='stunning-beaches-header'><h2>Stunning Beaches</h2><p>Turquoise waters and golden sands await</p></div>";

if (!empty($beaches)) {
	echo "<div class='beach-slider' aria-label='Stunning beaches slider' tabindex='0'>";
	echo "<div class='beach-slider-track'>";

	for ($i = 0; $i < count($beaches); $i++) {
		$beach = $beaches[$i];
		$name = $escape($beach['name'] ?? 'Beach');
		$subline = $escape($beach['description'] ?? 'Greece');
		$imageUrl = trim((string)($beach['image_url'] ?? ''));
		$safeImageUrl = $escape($imageUrl);

		echo "<article class='beach-card beach-slide'>
				<img src='{$safeImageUrl}' alt='{$name}'>
				<div class='beach-card-content'>
					<h3>{$name}</h3>
					<p>{$subline}</p>
				</div>
			</article>";
	}

	echo "</div>";
	echo "</div>";
} else {
	echo "<p class='beaches-empty'>Aktuell sind noch keine Strände in der Datenbank vorhanden.</p>";
}

echo "</div></section>";