<?php
// KI hat diese if-Abfrage hinzugefügt, da ich sonst ein Session Fehler bekommen habe.
if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
	session_start();
}
// Ender der KI-Änderung
if (!isset($conn)) {
	require_once __DIR__ . '/../database/mysql.php';
}

$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../src/php/profile.php' : '../../src/php/login-page.php';

$escape = static function ($value): string {
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

function renderBeachCards(array $beaches, callable $escape): string
{
	$html = '';

	for ($i = 0; $i < count($beaches); $i++) {
		$beach = $beaches[$i];
		$beachName = $escape($beach['name'] ?? 'Beach');
		$beachDesc = $escape($beach['description'] ?? '');
		$beachImg = $escape($beach['image'] ?? '');

		$html .= "<article class='island-beach-card'>
			<img src='{$beachImg}' alt='{$beachName}'>
			<div class='island-beach-card-content'>
			<h3>{$beachName}</h3>
			<p>{$beachDesc}</p>
			</div>
			</article>";
	}

	return $html;
}

function renderFoodCards(array $foods, callable $escape): string
{
	$html = '';

	for ($i = 0; $i < count($foods); $i++) {
		$food = $foods[$i];
		$foodName = $escape($food['name'] ?? 'Food');
		$foodDesc = $escape($food['description'] ?? '');
		$foodImg = $escape($food['image'] ?? '');

		$html .= "<article class='island-food-card'>
			<img src='{$foodImg}' alt='{$foodName}'>
			<div class='island-food-card-content'>
				<h3>{$foodName}</h3>
				<p>{$foodDesc}</p>
			</div>
			</article>";
	}

	return $html;
}

$id = (int) ($_GET['id'] ?? 0);
$result = mysqli_query($conn, "SELECT id, name, description, `full-description`, image_url, country FROM islands WHERE id = {$id} LIMIT 1");
$selectedIsland = $result instanceof mysqli_result ? mysqli_fetch_assoc($result) : null;

if ($result instanceof mysqli_result) {
	mysqli_free_result($result);
}

if (!$selectedIsland) {
	die('Island not found');
}

$rawImage = trim((string) $selectedIsland['image_url']);
$heroImagePath = '../../' . $rawImage;

$islandName = $escape($selectedIsland['name']);
$islandDescription = $escape($selectedIsland['description']);
$islandFullDescription = $escape($selectedIsland['full-description']);
$islandCountry = $escape($selectedIsland['country']);
$heroImage = $escape($heroImagePath);

$beaches = [
	['name' => 'Red Beach', 'description' => 'Dramatic red volcanic cliffs meet crystal-clear waters.', 'image' => '../../assets/img/beaches/beach-red_beach.jpg'],
	['name' => 'Balos', 'description' => 'Shallow turquoise lagoon with soft white and pink sand.', 'image' => '../../assets/img/beaches/beach-balos.jpg'],
	['name' => 'Elafonisi', 'description' => 'Famous for bright blue water and unique pink shoreline.', 'image' => '../../assets/img/beaches/beach-elafonisi.jpg']
];

$foods = [
	['name' => 'Gyros', 'description' => 'Tender meat wrapped in crispy pita with fresh vegetables.', 'image' => '../../assets/img/food/food-gyros.jpg'],
	['name' => 'Souvlaki', 'description' => 'Grilled skewers of marinated meat with herbs and lemon.', 'image' => '../../assets/img/food/food-souvlaki.jpg'],
	['name' => 'Moussaka', 'description' => 'Layered eggplant, meat and creamy béchamel sauce.', 'image' => '../../assets/img/food/food-moussaka.jpg']
];

$beachesHtml = renderBeachCards($beaches, $escape);
$foodsHtml = renderFoodCards($foods, $escape);

$community_travel_storys = [];
$storyQuery = "SELECT p.*, u.username, u.profile_picture 
               FROM posts p
               JOIN users u ON p.user_id = u.id 
               WHERE p.island_id = {$id}
               ORDER BY p.created_at DESC LIMIT 3";

$result = mysqli_query($conn, $storyQuery);

if ($result instanceof mysqli_result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $community_travel_storys[] = $row;
    }
    mysqli_free_result($result);
}

function getStarRatingHtml(int $rating): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<i class="fa-solid fa-star" style="color: rgb(106, 192, 192);"></i>';
        } else {
            $html .= '<i class="fa-light fa-star" style="color: rgb(191, 241, 241);"></i>';
        }
    }
    return $html;
}

$escape = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

function renderCommunityStories(array $community_travel_storys, callable $escape){
echo "<section class='community-travel' id='community'>";
echo "<div class='container community-travel-container'>";
echo "<div class='community-travel-header'>
        <h2>Travel Stories</h2>
        <p>Recent experiences from our community</p>
      </div>";

if (!empty($community_travel_storys)) {
    echo "<div class='community-story-grid'>";

    for ($i = 0; $i < count($community_travel_storys); $i++) {
        $story = $community_travel_storys[$i];
        
        $title = $escape($story['title'] ?? 'Travel Story');
        $username = $escape($story['username'] ?? 'Traveler');
        $rating = (int)($story['rating'] ?? 0);
        
        $content = (string)($story['content'] ?? '');
        if (strlen($content) > 120) {
            $content = substr($content, 0, 117) . '...';
        }
        $content = $escape($content);

        $profilePicture = trim((string)($story['profile_picture'] ?? ''));
        $safeProfilePicture = ($profilePicture !== '') ? $escape($profilePicture) : '../../assets/img/default-profile-img.png';

        echo "<article class='community-story-card'>
                <div class='community-story-content'>
                    <div class='community-story-author'>
                        <img src='{$safeProfilePicture}' class='community-author-avatar' alt='Avatar'>
                        <span class='community-author-name'>{$username}</span>
                    </div>
                    <h3>{$title}</h3>
                    <p>{$content}</p>
                    <div class='story-footer' style='display: flex; align-items: center; gap: 10px; margin-top: 15px;'>
                        <div class='stars'>" . getStarRatingHtml($rating) . "</div>
                        <span style='font-size: 0.9em; color: #666;'>" . number_format($rating, 1) . "</span>
                    </div>
                </div>
            </article>";
    }

    echo "</div>";
} else {
    echo "<p class='community-empty'>No stories yet for this island.</p>";
}

echo "</div></section>";
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
	<link rel="stylesheet" href="../../src/styles/community.css">
	<link rel="stylesheet" href="../../src/styles/islands-detailed.css">
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

	<!-- Hero Section -->
	<section class="hero island-detail-hero" id="home" style="--detail-hero-image: url('<?php echo $heroImage; ?>');">
		<div class="island-detail-content">
			<div class="island-detail-country">
				<i class="fa-solid fa-location-dot" style="color: rgb(106, 192, 192);"></i>
				<?php echo $islandCountry; ?>
			</div>
			<h1><?php echo $islandName; ?></h1>
			<p><?php echo $islandDescription; ?></p>
			<div class="island-detail-actions">
				<a href="#about" class="btn btn-primary">
					<i class="fa-regular fa-heart" style="color: rgb(255, 255, 255);"></i>
					Add to Favorites</a>
				<a href="../../src/php/travel-story.php" class="btn btn-primary">
					<i class="fa-solid fa-pen" style="color: rgb(255, 255, 255);"></i>
					Write Travel Story</a>
			</div>
		</div>
	</section>

	<section class="island-detail-about" id="about">
		<h2>About the Island</h2>
		<p><?php echo $islandFullDescription; ?></p>
	</section>

	<!-- Top Beaches Section -->
	<section class="island-detail-beaches">
		<div class="island-detail-beaches-container">
			<h2>Top Beaches</h2>
			<div class="island-beaches-grid">
				<?php echo $beachesHtml; ?>
			</div>
		</div>
	</section>

	<!-- Local Food Section -->
	<section class="island-detail-food">
		<div class="island-detail-food-container">
			<h2>Local Food</h2>
			<div class="island-food-grid">
				<?php echo $foodsHtml; ?>
			</div>
		</div>
	</section>


	<?php renderCommunityStories($community_travel_storys, $escape); ?>

	<!-- Island Rating Section -->
	<section class="island-rating">
		<div class="island-rating-container">
			<h2>Island Rating</h2>
			<div class="rating-card">
				<div class="rating-value"><span class="score">4.8</span><small>/5</small></div>
				<div class="rating-bars">
					<div class="rating-row"><span class="label">Food</span><div class="bar"><div class="bar-fill" style="width:88%"></div></div><span class="value">4.6</span></div>
					<div class="rating-row"><span class="label">Beaches</span><div class="bar"><div class="bar-fill" style="width:90%"></div></div><span class="value">4.8</span></div>
					<div class="rating-row"><span class="label">Nightlife</span><div class="bar"><div class="bar-fill" style="width:74%"></div></div><span class="value">4.2</span></div>
					<div class="rating-row"><span class="label">Atmosphere</span><div class="bar"><div class="bar-fill" style="width:94%"></div></div><span class="value">4.9</span></div>
				</div>
			</div>
		</div>
	</section>



	<footer class="site-footer">
		<div class="site-footer-container">
			<div class="site-footer-top">
				<div class="site-footer-brand">
					<div class="site-footer-logo">
						<img src="../../assets/img/Logo.png" alt="Aegean Breeze Logo">
						<span>Aegean Breeze</span>
					</div>
					<p>Your ultimate guide to the most beautiful islands, beaches and hidden gems of Greece.</p>
				</div>

				<div class="site-footer-column">
					<h3>EXPLORE</h3>
					<a href="#home">Home</a>
					<a href="#islands">Islands</a>
					<a href="#beaches">Beaches</a>
				</div>

				<div class="site-footer-column">
					<h3>COMMUNITY</h3>
					<a href="#">Stories</a>
					<a href="#">Forum</a>
					<a href="#">Events</a>
				</div>

				<div class="site-footer-column">
					<h3>ABOUT ME</h3>
					<p>Luca Ebner</p>
					<p>l.ebner@students.htl-leonding.ac.at</p>
					<p>HTL-Leonding</p>
				</div>
			</div>

			<div class="site-footer-divider"></div>

			<div class="site-footer-bottom">
				<p>&copy; 2026 Greek Island Explorer. All rights reserved.</p>
			</div>
		</div>
	</footer>


</body>

</html>