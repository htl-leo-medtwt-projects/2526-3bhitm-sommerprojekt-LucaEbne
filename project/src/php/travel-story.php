<?php
if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
    session_start();
}
if (!isset($conn)) {
    require_once __DIR__ . '/../database/mysql.php';
}

$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../src/php/profile.php' : '../../src/php/login-page.php';

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
                        <i class="fa-solid fa-location-dot"></i>
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
                    <div class="rating-display">
                        <div class="star-rating" aria-label="1 out of 5 stars">
                            <i class="fa-solid fa-star is-filled"></i>
                            <i class="fa-regular fa-star is-empty"></i>
                            <i class="fa-regular fa-star is-empty"></i>
                            <i class="fa-regular fa-star is-empty"></i>
                            <i class="fa-regular fa-star is-empty"></i>
                        </div>
                        <span class="rating-text">1.0</span>
                    </div>
                </div>

                <div class="form-row">
                    <label>Cover Image</label>
                    <input type="file" id="cover" name="cover" class="file-input" accept="image/*">
                    <label for="cover" class="upload-zone">
                        <i class="fa-regular fa-images" style="color: rgb(108, 126, 147);"></i>
                        <p class="upload-title">Drag & drop or click to upload a cover image</p>
                        <p class="upload-hint">JPG, PNG up to 10MB</p>
                    </label>
                </div>


                <div class="form-row">
                    <label for="story">Your Story</label>
                    <textarea type="text" class="story-input" id="story" name="story"
                        placeholder="Share your experience, tips and highlights..." required></textarea>
                </div>

                <div class="form-row">
                    <div class="food-highlights">
                        <i class="fa-solid fa-bowl-food" style="color: rgb(106, 192, 192);"></i>
                        <label>Food Highlights (optional)</label>
                    </div>
                    <input type="text" id="food1" name="food1" placeholder="e.g Gyros, Souvlaki, ...">
                </div>

                <div class="form-row">
                    <div class="trip-highlights">
                        <i class="fa-solid fa-mountain-sun" style="color: rgb(106, 192, 192);"></i>
                        <label>Trip Highlights (optional)</label>
                    </div>
                    <input type="text" id="trip1" name="trip1" placeholder="e.g Sunset in Oia, Red Beach, ...">
                </div>


                <div class="form-row">
                    <label>Photo Gallery (optional)</label>
                    <input type="file" id="photos" name="photos[]" class="file-input" accept="image/*" multiple>
                    <label for="photos" class="upload-zone">
                        <i class="fa-regular fa-images" style="color: rgb(108, 126, 147);"></i>
                        <p class="upload-title">Drag & drop or click to upload photos</p>
                        <p class="upload-hint">JPG, PNG up to 10MB</p>
                    </label>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fa-regular fa-paper-plane" style="color: rgb(255, 255, 255);"></i> 
                    Publish Story
                </button>

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