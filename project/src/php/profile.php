<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../database/mysql.php';

$navLabel = !empty($_SESSION['user_id']) ? 'Profile' : 'Login';
$navLink = !empty($_SESSION['user_id']) ? '../../src/php/profile.php' : '../../src/php/login-page.php';

$favouritesHtml = '';
if (!empty($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("
        SELECT i.id, i.name, i.image_url, i.country
        FROM favorite_islands fi
        JOIN islands i ON i.id = fi.island_id
        WHERE fi.user_id = ?
        ORDER BY fi.created_at DESC
    ");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($rows)) {
        $favouritesHtml = '<p style="color:#888; padding: 24px 0;">Du hast noch keine Favourites gespeichert.</p>';
    } else {
        foreach ($rows as $island) {
            $id = (int) $island['id'];
            $name = htmlspecialchars($island['name']);
            $img = htmlspecialchars($island['image_url']);
            $country = htmlspecialchars($island['country']);

            $favouritesHtml .= "
            <div class='island-card'>
                <div class='card-image-wrapper' style='background-image: url(\"{$img}\");'>
                    <div class='card-overlay'>
                        <h3 class='island-name'>{$name}</h3>
                        <p class='island-location'><i class='fa-solid fa-location-dot'></i> {$country}</p>
                    </div>
                </div>
                <div class='card-actions'>
                    <a href='islands-detailed.php?id={$id}' class='btn-view'>View island</a>
                    <button class='btn-remove' data-id='{$id}'>
                        <i class='fa-solid fa-heart'></i> Remove
                    </button>
                </div>
            </div>";
        }
    }
}


$storiesHtml = '';
if (!empty($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("
        SELECT p.id, p.title, p.content, p.rating, p.created_at, i.name AS island_name
        FROM posts p
        LEFT JOIN islands i ON i.id = p.island_id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $stories = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($stories)) {
        $storiesHtml = '<p style="color:#888; padding: 24px 0;">Du hast noch keine Stories geschrieben.</p>';
    } else {
        foreach ($stories as $story) {
            $sid = (int) $story['id'];
            $title = htmlspecialchars($story['title']);
            $excerpt = htmlspecialchars(mb_strimwidth($story['content'], 0, 100, '…'));
            $island = htmlspecialchars($story['island_name'] ?? 'Unknown');
            $date = date('Y-m-d', strtotime($story['created_at']));
            $rating = (int) $story['rating'];

            $stars = '';
            for ($i = 1; $i <= 5; $i++) {
                $stars .= $i <= $rating
                    ? '<i class="fa-solid fa-star"></i>'
                    : '<i class="fa-regular fa-star"></i>';
            }

            $storiesHtml .= "
            <div class='story-card'>
                <div class='story-meta'>
                    <i class='fa-solid fa-location-dot'></i> {$island}
                    <span class='story-date'>• {$date}</span>
                </div>
                <div class='story-body'>
                    <div>
                        <h3 class='story-title'>{$title}</h3>
                        <p class='story-excerpt'>{$excerpt}</p>
                        <div class='story-stars'>{$stars}</div>
                    </div>
                    <a href='story-detail.php?id={$sid}' class='btn-read'>Read</a>
                </div>
            </div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aegean Breeze</title>
    <link rel="icon" href="../../assets/img/Logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../src/styles/style.css">
    <link rel="stylesheet" href="../../src/styles/profile.css">
    <script src="../../src/scripts/profile.js" defer></script>
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

        <div class="profile-header">
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
        </div>

        <nav class="profile-tabs">
            <button class="tab-btn active" onclick="showTab('favourites', this)">
                <i class="fa-regular fa-heart"></i> Favourites
            </button>
            <button class="tab-btn" onclick="showTab('stories', this)">
                <i class="fa-regular fa-bookmark"></i> My Stories
            </button>
            <button class="tab-btn" onclick="showTab('settings', this)">
                <i class="fa-solid fa-gear"></i>Settings
            </button>
        </nav>

        <!-- TAB: Favourites -->
        <main class="tab-content active" id="tab-favourites">
            <div class="cards-grid">
                <?php echo $favouritesHtml; ?>
            </div>
        </main>

        <!-- TAB: My Stories -->
        <main class="tab-content" id="tab-stories">
            <div class="stories-list">
                <?= $storiesHtml ?>
            </div>  
            <div style="text-align:center; margin-top: 32px;">
                <a href="travel-story.php" class="btn-new-story">Write a new story</a>
            </div>
        </main>

        <!-- TAB: Settings -->
        <main class="tab-content" id="tab-settings">
            <div class="settings-box">
                <h2 class="settings-title">Account settings</h2>
                <p class="settings-subtitle">Update your personal information</p>

                <div class="settings-field">
                    <label>Name</label>
                    <input type="text" value="Elena K.">
                </div>
                <div class="settings-field">
                    <label>Email</label>
                    <input type="text" value="elena@example.com">
                </div>
                <div class="settings-field">
                    <label>Bio</label>
                    <textarea>Greek island lover. Always chasing the next sunset. 🌅</textarea>
                </div>
                <div class="settings-field">
                    <label>New password</label>
                    <input type="password" placeholder="Leave empty to keep current">
                </div>
                <button class="btn-save">
                    <i class="fa-regular fa-floppy-disk"></i> Save changes
                </button>
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