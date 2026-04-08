<?php
require './src/database/mysql.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Aegean Breeze</title>
	<link rel="icon" href="assets/img/Logo.png" type="image/png">
	<link rel="stylesheet" href="src/styles/style.css">
	<script src="src/scripts/main.js" defer></script>
</head>

<body>
	<!-- Header / Navigation -->
	<header>
		<div class="logo">
			<img src="assets/img/Logo.png" alt="Aegean Breeze Logo">
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

	<?php include './src/php/islands.php'; ?>
</body>

</html>
