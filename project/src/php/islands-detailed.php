<?php
if (!isset($conn)) {
	require_once __DIR__ . '/../database/mysql.php';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Aegean Breeze</title>
	<link rel="icon" href="../../assets/img/Logo.png" type="image/png">
	<link rel="stylesheet" href="../../src/styles/style.css">
	<link rel="stylesheet" href="../../src/styles/islands.css">
	<link rel="stylesheet" href="../../src/styles/beaches.css">
	<link rel="stylesheet" href="../../src/styles/community.css">
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
			<div class="nav-element"><a href="#home">Home</a></div>
			<div class="nav-element"><a href="#islands">Islands</a></div>
			<div class="nav-element"><a href="#beaches">Beaches</a></div>
			<div class="nav-element"><a href="#community">Community</a></div>
			<div class="nav-element"><a href="#login">Login</a></div>
		</nav>
	</header>

	<!-- Hero Section -->
	<section class="hero" id="home">
		<div class="hero-content">
			<h1>Discover the Most<br>Beautiful Greek<br>Islands</h1>
			<p>Explore islands, beaches and travel stories from the community</p>
			<a href="#islands" class="btn btn-primary">Explore Islands →</a>
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
					<p>l.ebner@students.htl-leonding.ac.at<</p>
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
