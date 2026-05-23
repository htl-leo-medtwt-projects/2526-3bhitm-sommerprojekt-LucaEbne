<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../src/php/profile.php' : '../../src/php/login-page.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Elena K.</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../src/styles/style.css">
    <link rel="stylesheet" href="../../src/styles/profile.css">
</head>
<body>

<!-- Header / Navigation (inline) -->
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

<div class="profile-container">
    
    <header class="profile-header">
        <div class="banner-gradient"></div>
        
        <div class="profile-info-bar">
            <div class="avatar-container">
                <div class="avatar-letters">EK</div>
                <button class="edit-avatar-btn"><i class="fa-solid fa-camera"></i></button>
            </div>
            
            <div class="user-details">
                <h1 class="user-name">Elena K.</h1>
                <p class="user-email">elena@example.com</p>
                <p class="user-bio">Greek island lover. Always chasing the next sunset. 🌅</p>
            </div>
            
            <div class="stats-container">
                <div class="stat-box">
                    <span class="stat-number">3</span>
                    <span class="stat-label">FAVOURITES</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number">2</span>
                    <span class="stat-label">STORIES</span>
                </div>
            </div>
        </div>
    </header>

    <nav class="profile-tabs">
        <button class="tab-btn active"><i class="fa-regular fa-heart"></i> Favourites</button>
        <button class="tab-btn"><i class="fa-regular fa-bookmark"></i> My Stories</button>
        <button class="tab-btn">Settings</button>
    </nav>

    <main class="cards-grid">
        
        <div class="island-card">
            <div class="card-image-wrapper" style="background-image: url('../../assets/img/islands/island-santorini.jpg');">
                <div class="card-overlay">
                    <h3 class="island-name">Santorini</h3>
                    <p class="island-location"><i class="fa-solid fa-location-dot"></i> Cyclades</p>
                </div>
            </div>
            <div class="card-actions">
                <a href="#" class="btn-view">View island</a>
                <button class="btn-remove"><i class="fa-solid fa-heart"></i> Remove</button>
            </div>
        </div>

        <div class="island-card">
            <div class="card-image-wrapper" style="background-image: url('../../assets/img/islands/island-mykonos.jpg');">
                <div class="card-overlay">
                    <h3 class="island-name">Mykonos</h3>
                    <p class="island-location"><i class="fa-solid fa-location-dot"></i> Cyclades</p>
                </div>
            </div>
            <div class="card-actions">
                <a href="#" class="btn-view">View island</a>
                <button class="btn-remove"><i class="fa-solid fa-heart"></i> Remove</button>
            </div>
        </div>

        <div class="island-card">
            <div class="card-image-wrapper" style="background-image: url('../../assets/img/islands/island-crete.jpg');">
                <div class="card-overlay">
                    <h3 class="island-name">Crete</h3>
                    <p class="island-location"><i class="fa-solid fa-location-dot"></i> Greek Islands</p>
                </div>
            </div>
            <div class="card-actions">
                <a href="#" class="btn-view">View island</a>
                <button class="btn-remove"><i class="fa-solid fa-heart"></i> Remove</button>
            </div>
        </div>

    </main>

</div>

    <!-- Footer (inline) -->
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