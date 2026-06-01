<?php
if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
    session_start();
}
if (!isset($conn)) {
    require_once __DIR__ . '/../database/mysql.php';
}

$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../src/php/profile.php' : '../../src/php/login-page.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_SESSION['user_id'])) {
        $errorMsg = 'Du musst eingeloggt sein um eine Story zu veröffentlichen.';
    } else {
        $user_id = $_SESSION['user_id'];
        $title = trim($_POST['title'] ?? '');
        $island_name = trim($_POST['island'] ?? '');
        $story = trim($_POST['story'] ?? '');
        $food_highlights = trim($_POST['food1'] ?? '');
        $trip_highlights = trim($_POST['trip1'] ?? '');

        $rating_food = max(1, min(5, intval($_POST['rating_food'] ?? 1)));
        $rating_beaches = max(1, min(5, intval($_POST['rating_beaches'] ?? 1)));
        $rating_nightlife = max(1, min(5, intval($_POST['rating_nightlife'] ?? 1)));
        $rating_atmosphere = max(1, min(5, intval($_POST['rating_atmosphere'] ?? 1)));
        $rating = round(($rating_food + $rating_beaches + $rating_nightlife + $rating_atmosphere) / 4, 1);

        $stmt = $conn->prepare("SELECT id FROM islands WHERE name = ?");
        $stmt->bind_param("s", $island_name);
        $stmt->execute();
        $stmt->bind_result($island_id);
        $stmt->fetch();
        $stmt->close();

        $cover_url = null;
        if (!empty($_FILES['cover']['tmp_name']) && ($_FILES['cover']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed) && $_FILES['cover']['size'] <= 10 * 1024 * 1024) {
                $filename = uniqid('cover_') . '.' . $ext;
                if (move_uploaded_file($_FILES['cover']['tmp_name'], $uploadDir . $filename)) {
                    $cover_url = '../../assets/uploads/' . $filename;
                } else {
                    $errorMsg = 'Das Cover-Bild konnte nicht gespeichert werden.';
                }
            }
        }

        $stmt = $conn->prepare(
            "INSERT INTO posts (user_id, island_id, title, content, image_url, rating, food_highlights, trip_highlights)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iisssdss", $user_id, $island_id, $title, $story, $cover_url, $rating, $food_highlights, $trip_highlights);
        $stmt->execute();
        $post_id = $conn->insert_id;
        $stmt->close();

        if (!empty($_FILES['photos']['tmp_name'][0])) {
            $uploadDir = __DIR__ . '/../../assets/uploads/';
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            foreach ($_FILES['photos']['tmp_name'] as $i => $tmp) {
                if (empty($tmp))
                    continue;
                $ext = strtolower(pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed))
                    continue;
                if ($_FILES['photos']['size'][$i] > 10 * 1024 * 1024)
                    continue;
                $filename = uniqid('photo_') . '.' . $ext;
                if (move_uploaded_file($tmp, $uploadDir . $filename)) {
                    $img_url = '../../assets/uploads/' . $filename;
                    $stmt = $conn->prepare("INSERT INTO post_images (post_id, image_url) VALUES (?, ?)");
                    $stmt->bind_param("is", $post_id, $img_url);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        $successMsg = 'Deine Story wurde erfolgreich veröffentlicht!';
    }
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
    <link rel="stylesheet" href="../../src/styles/travel-story.css">
    <script src="../../src/scripts/main.js" defer></script>
    <script src="../../src/scripts/travel.js" defer></script>
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
            <?php if ($successMsg): ?>
                <div class="form-message success"><?php echo htmlspecialchars($successMsg); ?></div>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
                <div class="form-message error"><?php echo htmlspecialchars($errorMsg); ?></div>
            <?php endif; ?>
            <form class="story-form" method="post" action="" enctype="multipart/form-data">
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
                    <div class="rating-card-input">
                        <div class="rating-overall">
                            <span id="overall-rating-text" class="overall-rating-value">1.0</span>
                            <small>/5</small>
                        </div>

                        <div class="rating-criteria">
                            <div class="rating-criterion">
                                <span class="criterion-label">Food</span>
                                <div class="star-rating category-rating" data-input="rating-food-value" aria-label="Food rating">
                                    <i class="fa-solid fa-star is-filled"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                </div>
                                <span class="criterion-value">1.0</span>
                            </div>

                            <div class="rating-criterion">
                                <span class="criterion-label">Beaches</span>
                                <div class="star-rating category-rating" data-input="rating-beaches-value" aria-label="Beaches rating">
                                    <i class="fa-solid fa-star is-filled"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                </div>
                                <span class="criterion-value">1.0</span>
                            </div>

                            <div class="rating-criterion">
                                <span class="criterion-label">Nightlife</span>
                                <div class="star-rating category-rating" data-input="rating-nightlife-value" aria-label="Nightlife rating">
                                    <i class="fa-solid fa-star is-filled"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                </div>
                                <span class="criterion-value">1.0</span>
                            </div>

                            <div class="rating-criterion">
                                <span class="criterion-label">Atmosphere</span>
                                <div class="star-rating category-rating" data-input="rating-atmosphere-value" aria-label="Atmosphere rating">
                                    <i class="fa-solid fa-star is-filled"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                    <i class="fa-regular fa-star is-empty"></i>
                                </div>
                                <span class="criterion-value">1.0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Cover Image</label>
                    <input type="file" id="cover" name="cover" class="file-input" accept="image/*">
                    <label for="cover" class="upload-zone" id="cover-zone">
                        <div id="cover-preview" class="zone-preview"></div>
                        <i class="fa-regular fa-images zone-icon" style="color: rgb(108, 126, 147);"></i>
                        <p class="upload-title zone-text">Drag & drop or click to upload a cover image</p>
                        <p class="upload-hint zone-text">JPG, PNG up to 10MB</p>
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
                    <label for="photos" class="upload-zone" id="photos-zone">
                        <div id="photos-preview" class="zone-preview preview-gallery"></div>
                        <i class="fa-regular fa-images zone-icon" style="color: rgb(108, 126, 147);"></i>
                        <p class="upload-title zone-text">Drag & drop or click to upload photos</p>
                        <p class="upload-hint zone-text">JPG, PNG up to 10MB</p>
                    </label>
                </div>
                <input type="hidden" name="rating" id="rating-value" value="1.0">
                <input type="hidden" name="rating_food" id="rating-food-value" value="1">
                <input type="hidden" name="rating_beaches" id="rating-beaches-value" value="1">
                <input type="hidden" name="rating_nightlife" id="rating-nightlife-value" value="1">
                <input type="hidden" name="rating_atmosphere" id="rating-atmosphere-value" value="1">
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