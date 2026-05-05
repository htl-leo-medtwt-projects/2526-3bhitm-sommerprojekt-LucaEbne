<?php
if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
    session_start();
}
if (!isset($conn)) {
    require_once __DIR__ . '/../database/mysql.php';
}

$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../index.php#home' : '../../src/php/login-page.php';

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
    <link rel="stylesheet" href="../../src/styles/travel-story.css">
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



    <section class="travel-story-header" id="home">
        <div class="travel-story-header-content">
            <h1>Write Your Travel Story</h1>
            <p>Share your Greek island adventure with the Aegean Breeze community</p>
        </div>

        <div class="story-form-wrap">
            <form class="story-form" method="post" action="">
                <div class="form-row">
                    <label for="title">Story Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g My Sunset Trip to Santorini" required>
                </div>

                <div class="form-row">
                    <label for="island">Which island did you visit</label>
                    <div class="select-with-icon">
                        <i class="fa-solid fa-location-dot" style="color: rgb(106, 192, 192);"></i>
                        <select id="island" name="island" required>
                            <option value="">Select an island</option>
                            <option>Mykonos</option>
                            <option>Santorini</option>
                            <option>Crete</option>
                            <option>Rhodes</option>
                        </select>
                    </div>
                </div>

                <div class="form-row rating-row">
                    <label>Your Rating</label>

                </div>
            </form>
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