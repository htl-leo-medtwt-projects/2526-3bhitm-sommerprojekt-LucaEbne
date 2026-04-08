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
?>
