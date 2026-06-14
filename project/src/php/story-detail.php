<?php
if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
	session_start();
}

if (!isset($conn)) {
	require_once __DIR__ . '/../database/mysql.php';
}

function sd_escape(mixed $value): string
{
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function sd_fetch_story_by_id(mysqli $conn, int $id): ?array
{
	$result = mysqli_query($conn, "SELECT p.*, u.username, u.profile_picture FROM posts p JOIN users u ON u.id = p.user_id WHERE p.id = {$id} LIMIT 1");
	$post = $result instanceof mysqli_result ? mysqli_fetch_assoc($result) : null;
	if ($result instanceof mysqli_result) {
		mysqli_free_result($result);
	}
	return $post ?: null;
}

function sd_fetch_latest_story_by_user(mysqli $conn, int $userId): ?array
{
	$result = mysqli_query($conn, "SELECT p.*, u.username, u.profile_picture FROM posts p JOIN users u ON u.id = p.user_id WHERE p.user_id = {$userId} ORDER BY p.created_at DESC, p.id DESC LIMIT 1");
	$post = $result instanceof mysqli_result ? mysqli_fetch_assoc($result) : null;
	if ($result instanceof mysqli_result) {
		mysqli_free_result($result);
	}
	return $post ?: null;
}

function sd_fetch_gallery_images(mysqli $conn, int $id): array
{
	$images = [];
	$result = mysqli_query($conn, "SELECT image_url FROM post_images WHERE post_id = {$id}");
	if ($result instanceof mysqli_result) {
		while ($row = mysqli_fetch_assoc($result)) {
			$images[] = (string) ($row['image_url'] ?? '');
		}
		mysqli_free_result($result);
	}
	return $images;
}

function sd_fetch_comments(mysqli $conn, int $id): array
{
	$comments = [];
	$result = mysqli_query($conn, "SELECT c.content, c.created_at, u.username, u.profile_picture FROM comments c JOIN users u ON u.id = c.user_id WHERE c.post_id = {$id} ORDER BY c.created_at ASC");
	if ($result instanceof mysqli_result) {
		while ($row = mysqli_fetch_assoc($result)) {
			$comments[] = $row;
		}
		mysqli_free_result($result);
	}
	return $comments;
}

function sd_split_highlights(string $value): array
{
	$value = trim($value);
	return $value !== '' ? array_values(array_filter(array_map('trim', explode(',', $value)))) : [];
}

function sd_format_date(string $value, string $format): string
{
	if ($value === '') {
		return '';
	}
	return (new DateTime($value))->format($format);
}

function sd_render_stars(int $rating): string
{
	$html = '';
	for ($i = 1; $i <= 5; $i++) {
		$html .= $i <= $rating
			? '<i class="fa-solid fa-star sd-star-filled"></i>'
			: '<i class="fa-regular fa-star sd-star-empty"></i>';
	}
	return $html;
}

function sd_render_avatar(string $picture, string $name, string $fallbackClass, string $fallbackPrefix = 'U'): string
{
	if ($picture !== '') {
		return '<img class="' . $fallbackClass . '" src="' . sd_escape($picture) . '" alt="' . sd_escape($name) . '">';
	}

	return '<span class="' . $fallbackClass . ' sd-author-fallback">' . strtoupper(substr($fallbackPrefix, 0, 1)) . '</span>';
}

function sd_render_gallery(array $images): string
{
	if (empty($images)) {
		return '';
	}

	$html = '<section class="sd-gallery"><div class="sd-section-container"><h2 class="sd-section-title"><i class="fa-regular fa-images" style="color: var(--button-color);"></i>Photo Gallery</h2><div class="sd-gallery-grid">';
	foreach ($images as $index => $image) {
		$spanClass = $index === 0 ? ' sd-gallery-item--wide' : '';
		$html .= '<div class="sd-gallery-item' . $spanClass . '"><img src="' . sd_escape($image) . '" alt="Gallery photo"></div>';
	}
	return $html . '</div></div></section>';
}

function sd_render_highlights(string $title, array $items, string $iconHtml = ''): string
{
	if (empty($items)) {
		return '';
	}

	$html = '<section class="sd-highlights"><div class="sd-section-container"><h2 class="sd-section-title">' . $iconHtml . $title . '</h2>';
	$tagClass = $title === 'Trip Highlights' ? 'sd-trip-list' : 'sd-food-tags';
	$html .= '<div class="' . $tagClass . '">';
	foreach ($items as $item) {
		if ($title === 'Trip Highlights') {
			$html .= '<div class="sd-trip-item"><i class="fa-solid fa-location-dot" style="color: var(--button-color);"></i><span>' . sd_escape($item) . '</span></div>';
		} else {
			$html .= '<span class="sd-food-tag">' . sd_escape($item) . '</span>';
		}
	}
	return $html . '</div></div></section>';
}

function sd_render_comments(array $comments): string
{
	if (empty($comments)) {
		return '<p class="sd-comments-empty">No comments yet. Be the first to share your thoughts!</p>';
	}

	$html = '<div class="sd-comments-list">';
	foreach ($comments as $comment) {
		$username = sd_escape($comment['username'] ?? 'User');
		$picture = trim((string) ($comment['profile_picture'] ?? ''));
		$content = sd_escape($comment['content'] ?? '');
		$date = sd_format_date((string) ($comment['created_at'] ?? ''), 'M j, Y');
		$avatar = sd_render_avatar($picture, (string) ($comment['username'] ?? 'User'), 'sd-comment-avatar', (string) ($comment['username'] ?? 'U'));

		$html .= '<div class="sd-comment-card"><div class="sd-comment-avatar-wrap">' . $avatar . '</div><div class="sd-comment-body"><div class="sd-comment-header"><span class="sd-comment-author">' . $username . '</span><span class="sd-comment-date">' . $date . '</span></div><p class="sd-comment-text">' . $content . '</p></div></div>';
	}

	return $html . '</div>';
}

function sd_render_page(array $data): string
{
	$navLink = $data['navLink'];
	$navLabel = $data['navLabel'];
	$coverImage = $data['coverImage'];
	$storyTitle = $data['storyTitle'];
	$username = $data['username'];
	$formattedDate = $data['formattedDate'];
	$starsHtml = $data['starsHtml'];
	$rating = $data['rating'];
	$storyContent = $data['storyContent'];
	$authorAvatarHtml = $data['authorAvatarHtml'];
	$galleryHtml = $data['galleryHtml'];
	$foodHtml = $data['foodHtml'];
	$tripHtml = $data['tripHtml'];
	$commentsHtml = $data['commentsHtml'];
	$storyId = (int) $data['storyId'];
	$commentFormHtml = $data['commentFormHtml'];

	return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{$storyTitle} – Aegean Breeze</title>
	<link rel="icon" href="../../assets/img/Logo.png" type="image/png">
	<script src="https://kit.fontawesome.com/3a03b4384b.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="../../src/styles/style.css">
	<link rel="stylesheet" href="../../src/styles/story-detail.css">
	<script src="../../src/scripts/main.js" defer></script>
</head>
<body>
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
			<div class="nav-element"><a href="{$navLink}">{$navLabel}</a></div>
		</nav>
	</header>

	<section class="sd-hero" style="--sd-cover: url('{$coverImage}');">
		<div class="sd-hero-overlay"></div>
		<div class="sd-hero-content">
			<span class="sd-badge">Travel Story</span>
			<h1 class="sd-hero-title">{$storyTitle}</h1>
		</div>
	</section>

	<div class="sd-meta-bar">
		<div class="sd-author">
			{$authorAvatarHtml}
			<div class="sd-author-info">
				<span class="sd-author-name">{$username}</span>
				<span class="sd-author-date">{$formattedDate}</span>
			</div>
		</div>
		<div class="sd-rating">
			<div class="sd-stars">{$starsHtml}</div>
			<span class="sd-rating-value">{$rating}.0</span>
		</div>
	</div>

	<section class="sd-story-body">
		<div class="sd-story-container">
			<p class="sd-story-text">{$storyContent}</p>
		</div>
	</section>

	{$galleryHtml}
	{$foodHtml}
	{$tripHtml}

	<section class="sd-comments">
		<div class="sd-section-container">
			<h2 class="sd-section-title">Comments</h2>

			{$commentFormHtml}

			{$commentsHtml}
		</div>
	</section>

	<script>
	function sdPostComment() {
		const textarea = document.getElementById('sd-comment-input');
		const msg = document.getElementById('sd-comment-msg');
		const content = textarea.value.trim();
		if (!content) {
			msg.textContent = 'Bitte zuerst etwas schreiben.';
			msg.style.color = '#c0392b';
			return;
		}
		const btn = document.querySelector('.sd-comment-submit');
		btn.disabled = true;
		btn.innerHTML = '<i class="fa-regular fa-comment"></i> Posting...';

		fetch('../../src/php/post-comment.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'post_id={$storyId}&content=' + encodeURIComponent(content)
		})
		.then(r => r.json())
		.then(data => {
			if (data.success) {
				msg.textContent = 'Kommentar gepostet!';
				msg.style.color = 'var(--button-color)';
				textarea.value = '';
				setTimeout(() => location.reload(), 800);
			} else {
				msg.textContent = data.error || 'Etwas ist schiefgelaufen.';
				msg.style.color = '#c0392b';
			}
		})
		.catch(() => {
			msg.textContent = 'Netzwerkfehler, bitte nochmal versuchen.';
			msg.style.color = '#c0392b';
		})
		.finally(() => {
			btn.disabled = false;
			btn.innerHTML = '<i class="fa-regular fa-comment"></i> Post Comment';
		});
	}
	</script>

	<footer class="site-footer">
		<div class="site-footer-container">
			<div class="site-footer-top">
				<div class="site-footer-brand">
					<a class="site-footer-logo" href="../../index.php#home">
						<img src="../../assets/img/Logo.png" alt="Aegean Breeze Logo">
						<span>Aegean Breeze</span>
					</a>
					<p>Your ultimate guide to the most beautiful islands, beaches and hidden gems of Greece.</p>
				</div>

				<div class="site-footer-column">
					<h3>EXPLORE</h3>
					<a href="../../index.php#home">Home</a>
					<a href="../../index.php#islands">Islands</a>
					<a href="../../index.php#beaches">Beaches</a>
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
HTML;
}

$storyId = (int) ($_GET['id'] ?? 0);
$userId = (int) ($_GET['user_id'] ?? 0);
$post = $storyId > 0 ? sd_fetch_story_by_id($conn, $storyId) : null;
if (!$post && $userId > 0) {
	$post = sd_fetch_latest_story_by_user($conn, $userId);
}
if (!$post) {
	die('Story not found');
}

$selectedStoryId = (int) ($post['id'] ?? 0);

$storyTitle = sd_escape($post['title'] ?? 'Travel Story');
$storyContent = nl2br(sd_escape($post['content'] ?? ''));
$username = sd_escape($post['username'] ?? 'Traveler');

$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../src/php/profile.php' : '../../src/php/login-page.php';

$rawCover = trim((string) ($post['image_url'] ?? ''));
$coverImage = sd_escape($rawCover !== '' ? $rawCover : '../../assets/img/islands/island-santorini.jpg');

$profilePicture = trim((string) ($post['profile_picture'] ?? ''));
$authorAvatarHtml = sd_render_avatar($profilePicture, (string) ($post['username'] ?? 'Traveler'), 'sd-author-avatar', (string) ($post['username'] ?? 'T'));

$createdAt = trim((string) ($post['created_at'] ?? ''));
$formattedDate = sd_format_date($createdAt, 'F j, Y');

$rating = (int) ($post['rating'] ?? 0);
$starsHtml = sd_render_stars($rating);

$galleryHtml = sd_render_gallery(sd_fetch_gallery_images($conn, $selectedStoryId));
$foodHtml = sd_render_highlights('Food I Tried', sd_split_highlights((string) ($post['food_highlights'] ?? '')), '<i class="fa-solid fa-utensils" style="color: var(--button-color);"></i>');
	$tripHtml = sd_render_highlights('Trip Highlights', sd_split_highlights((string) ($post['trip_highlights'] ?? '')), '<i class="fa-solid fa-mountain-sun" style="color: var(--button-color);"></i>');
$commentsHtml = sd_render_comments(sd_fetch_comments($conn, $selectedStoryId));

if (!empty($_SESSION['user_id'])) {
	$commentFormHtml = '<div class="sd-comment-form-wrap">
		<textarea id="sd-comment-input" class="sd-comment-textarea" placeholder="Write a comment..." rows="4"></textarea>
		<div class="sd-comment-form-footer">
			<button class="sd-comment-submit" onclick="sdPostComment()">
				<i class="fa-regular fa-comment"></i> Post Comment
			</button>
		</div>
		<p id="sd-comment-msg" class="sd-comment-msg"></p>
	</div>';
} else {
	$commentFormHtml = '<div class="sd-comment-login-hint"><a href="../../src/php/login-page.php">Log in</a> to leave a comment.</div>';
}

echo sd_render_page([
	'navLink' => $navLink,
	'navLabel' => $navLabel,
	'coverImage' => $coverImage,
	'storyTitle' => $storyTitle,
	'username' => $username,
	'formattedDate' => $formattedDate,
	'starsHtml' => $starsHtml,
	'rating' => $rating,
	'storyContent' => $storyContent,
	'authorAvatarHtml' => $authorAvatarHtml,
	'galleryHtml' => $galleryHtml,
	'foodHtml' => $foodHtml,
	'tripHtml' => $tripHtml,
	'commentsHtml' => $commentsHtml,
	'storyId' => $selectedStoryId,
	'commentFormHtml' => $commentFormHtml,
]);