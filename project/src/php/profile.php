<?php
session_start();
require_once __DIR__ . '/../database/mysql.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../../src/php/login-page.php');
    exit;
}

$navLabel = 'Profile';
$navLink  = '../../src/php/profile.php';

$userId = (int) $_SESSION['user_id'];

$escape = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

// Load user
$userStmt = mysqli_prepare($conn, 'SELECT id, username, email, profile_picture, bio FROM users WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($userStmt, 'i', $userId);
mysqli_stmt_execute($userStmt);
$userResult = mysqli_stmt_get_result($userStmt);
$user = $userResult instanceof mysqli_result ? mysqli_fetch_assoc($userResult) : null;
mysqli_stmt_close($userStmt);

if (!$user) {
    session_destroy();
    header('Location: ../../src/php/login-page.php');
    exit;
}

// Handle Settings Update
$settingsSuccess = '';
$settingsError   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $newUsername = trim((string) ($_POST['username'] ?? ''));
    $newBio      = trim((string) ($_POST['bio'] ?? ''));

    if ($newUsername !== '') {
        $updateStmt = mysqli_prepare($conn, 'UPDATE users SET username = ?, bio = ? WHERE id = ?');
        mysqli_stmt_bind_param($updateStmt, 'ssi', $newUsername, $newBio, $userId);
        if (mysqli_stmt_execute($updateStmt)) {
            $_SESSION['username'] = $newUsername;
            $user['username']     = $newUsername;
            $user['bio']          = $newBio;
            $settingsSuccess      = 'Profile updated successfully.';
        } else {
            $settingsError = 'Could not update profile. Please try again.';
        }
        mysqli_stmt_close($updateStmt);
    } else {
        $settingsError = 'Username cannot be empty.';
    }
}

// Load favourites
$favourites = [];
$favResult = mysqli_query(
    $conn,
    "SELECT i.id, i.name, i.country, i.image_url
     FROM favourites f
     JOIN islands i ON i.id = f.island_id
     WHERE f.user_id = {$userId}
     ORDER BY f.created_at DESC"
);
if ($favResult instanceof mysqli_result) {
    while ($row = mysqli_fetch_assoc($favResult)) {
        $favourites[] = $row;
    }
    mysqli_free_result($favResult);
}
$favouriteCount = count($favourites);

// Load stories
$stories = [];
$storiesResult = mysqli_query(
    $conn,
    "SELECT id, title, content, image_url, created_at
     FROM posts
     WHERE user_id = {$userId}
     ORDER BY created_at DESC"
);
if ($storiesResult instanceof mysqli_result) {
    while ($row = mysqli_fetch_assoc($storiesResult)) {
        $stories[] = $row;
    }
    mysqli_free_result($storiesResult);
}
$storyCount = count($stories);

$displayName  = $escape($user['username'] ?? 'User');
$displayEmail = $escape($user['email'] ?? '');
$displayBio   = $escape($user['bio'] ?? '');
$profilePic   = trim((string) ($user['profile_picture'] ?? ''));
$uname = $user['username'] ?? 'U';
$parts = explode(' ', $uname);
$initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : substr($parts[0], 1, 1)));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile – Island Odyssey</title>
    <link rel="icon" href="../../assets/img/Logo.png" type="image/png">
    <link rel="stylesheet" href="../../src/styles/style.css">
    <link rel="stylesheet" href="../../src/styles/profile.css">
    <script src="../../src/scripts/main.js" defer></script>
</head>

<body>
    <header>
        <div class="logo">
            <img src="../../assets/img/Logo.png" alt="Island Odyssey Logo">
            <span>Island Odyssey</span>
        </div>
        <nav>
            <div class="nav-element"><a href="../../index.php#home">Home</a></div>
            <div class="nav-element"><a href="../../index.php#islands">Islands</a></div>
            <div class="nav-element"><a href="../../index.php#beaches">Beaches</a></div>
            <div class="nav-element"><a href="../../index.php#community">Community</a></div>
            <div class="nav-element"><a href="<?php echo $navLink; ?>"><?php echo $navLabel; ?></a></div>
        </nav>
    </header>

    <main class="profile-page">
        <div class="profile-container">

            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-banner"></div>
                <div class="profile-info">
                    <div class="profile-avatar-wrap">
                        <?php if ($profilePic !== '' && file_exists('../../' . $profilePic)): ?>
                            <img src="../../<?php echo $escape($profilePic); ?>" alt="Profile picture" class="profile-avatar-img">
                        <?php else: ?>
                            <div class="profile-avatar-initials"><?php echo $escape($initials); ?></div>
                        <?php endif; ?>
                        <button class="profile-avatar-btn" title="Change photo" onclick="document.getElementById('avatarUpload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.172a2 2 0 001.664-.89l.812-1.22A2 2 0 019.312 4h5.376a2 2 0 011.664.89l.812 1.22A2 2 0 0018.828 7H21a2 2 0 012 2v9a2 2 0 01-2 2H3a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                        </button>
                        <input type="file" id="avatarUpload" accept="image/*" style="display:none">
                    </div>
                    <div class="profile-details">
                        <h1 class="profile-name"><?php echo $displayName; ?></h1>
                        <p class="profile-email"><?php echo $displayEmail; ?></p>
                        <?php if ($displayBio !== ''): ?>
                            <p class="profile-bio"><?php echo $displayBio; ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="profile-stats">
                        <div class="profile-stat">
                            <span class="profile-stat-number"><?php echo $favouriteCount; ?></span>
                            <span class="profile-stat-label">FAVOURITES</span>
                        </div>
                        <div class="profile-stat-divider"></div>
                        <div class="profile-stat">
                            <span class="profile-stat-number"><?php echo $storyCount; ?></span>
                            <span class="profile-stat-label">STORIES</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="profile-tabs">
                <button class="profile-tab profile-tab--active" data-tab="favourites">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Favourites
                </button>
                <button class="profile-tab" data-tab="stories">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    My Stories
                </button>
                <button class="profile-tab" data-tab="settings">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                    Settings
                </button>
            </div>

            <!-- Favourites -->
            <div class="profile-tab-content" id="tab-favourites">
                <?php if (empty($favourites)): ?>
                    <div class="profile-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <p>No favourite islands yet.</p>
                        <a href="../../index.php#islands" class="btn btn-primary">Explore Islands</a>
                    </div>
                <?php else: ?>
                    <div class="profile-grid">
                        <?php foreach ($favourites as $fav): ?>
                            <?php
                            $favId      = (int) $fav['id'];
                            $favName    = $escape($fav['name'] ?? 'Island');
                            $favCountry = $escape($fav['country'] ?? '');
                            $favImg     = $escape(trim((string) ($fav['image_url'] ?? '')));
                            ?>
                            <div class="profile-island-card">
                                <div class="profile-island-img-wrap">
                                    <img src="../../<?php echo $favImg; ?>" alt="<?php echo $favName; ?>" class="profile-island-img">
                                    <div class="profile-island-overlay">
                                        <h3 class="profile-island-name"><?php echo $favName; ?></h3>
                                        <p class="profile-island-country">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
                                            <?php echo $favCountry; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="profile-island-actions">
                                    <a href="../../src/php/islands-detailed.php?id=<?php echo $favId; ?>" class="profile-island-action-link">View island</a>
                                    <form method="post" action="../../src/php/remove-favourite.php" style="display:inline">
                                        <input type="hidden" name="island_id" value="<?php echo $favId; ?>">
                                        <button type="submit" class="profile-island-action-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Stories -->
            <div class="profile-tab-content profile-tab-content--hidden" id="tab-stories">
                <?php if (empty($stories)): ?>
                    <div class="profile-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <p>You haven't written any stories yet.</p>
                        <a href="../../src/php/travel-story.php" class="btn btn-primary">Write a Story</a>
                    </div>
                <?php else: ?>
                    <div class="profile-grid">
                        <?php foreach ($stories as $story): ?>
                            <?php
                            $storyId      = (int) $story['id'];
                            $storyTitle   = $escape($story['title'] ?? 'Story');
                            $storyImg     = $escape(trim((string) ($story['image_url'] ?? '')));
                            $storyDate    = date('M j, Y', strtotime($story['created_at'] ?? 'now'));
                            $storySnippet = $escape(mb_substr(strip_tags((string) ($story['content'] ?? '')), 0, 80)) . '…';
                            ?>
                            <div class="profile-island-card">
                                <?php if ($storyImg !== ''): ?>
                                    <div class="profile-island-img-wrap">
                                        <img src="../../<?php echo $storyImg; ?>" alt="<?php echo $storyTitle; ?>" class="profile-island-img">
                                        <div class="profile-island-overlay">
                                            <h3 class="profile-island-name"><?php echo $storyTitle; ?></h3>
                                            <p class="profile-island-country"><?php echo $storyDate; ?></p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="profile-story-noimg">
                                        <h3><?php echo $storyTitle; ?></h3>
                                        <p class="profile-story-date"><?php echo $storyDate; ?></p>
                                        <p class="profile-story-snippet"><?php echo $storySnippet; ?></p>
                                    </div>
                                <?php endif; ?>
                                <div class="profile-island-actions">
                                    <a href="../../src/php/travel-story.php?id=<?php echo $storyId; ?>" class="profile-island-action-link">Read story</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Settings -->
            <div class="profile-tab-content profile-tab-content--hidden" id="tab-settings">
                <div class="profile-settings">
                    <?php if ($settingsSuccess !== ''): ?>
                        <div class="profile-alert profile-alert--success"><?php echo $escape($settingsSuccess); ?></div>
                    <?php endif; ?>
                    <?php if ($settingsError !== ''): ?>
                        <div class="profile-alert profile-alert--error"><?php echo $escape($settingsError); ?></div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="update_profile">
                        <div class="profile-settings-group">
                            <label for="settingsUsername">Username</label>
                            <input type="text" id="settingsUsername" name="username" value="<?php echo $displayName; ?>" placeholder="Your name">
                        </div>
                        <div class="profile-settings-group">
                            <label for="settingsEmail">Email</label>
                            <input type="email" id="settingsEmail" value="<?php echo $displayEmail; ?>" disabled>
                        </div>
                        <div class="profile-settings-group">
                            <label for="settingsBio">Bio</label>
                            <textarea id="settingsBio" name="bio" rows="3" placeholder="Tell others about yourself…"><?php echo $displayBio; ?></textarea>
                        </div>
                        <div class="profile-settings-actions">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                            <a href="../../src/php/logout.php" class="btn btn-outline">Log out</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <footer class="site-footer">
        <div class="site-footer-container">
            <div class="site-footer-top">
                <div class="site-footer-brand">
                    <div class="site-footer-logo">
                        <img src="../../assets/img/Logo.png" alt="Island Odyssey Logo">
                        <span>Greek Island Explorer</span>
                    </div>
                    <p>Your ultimate guide to the most beautiful islands, beaches and hidden gems of Greece.</p>
                </div>
                <div class="site-footer-column">
                    <h3>EXPLORE</h3>
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
                    <h3>COMPANY</h3>
                    <a href="#">About</a>
                    <a href="#">Contact</a>
                </div>
            </div>
            <div class="site-footer-divider"></div>
            <div class="site-footer-bottom">
                <p>&copy; 2026 Greek Island Explorer. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        const tabs = document.querySelectorAll('.profile-tab');
        const panels = document.querySelectorAll('.profile-tab-content');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                tabs.forEach(t => t.classList.remove('profile-tab--active'));
                tab.classList.add('profile-tab--active');
                panels.forEach(p => {
                    p.classList.toggle('profile-tab-content--hidden', p.id !== 'tab-' + target);
                });
            });
        });
        document.getElementById('avatarUpload')?.addEventListener('change', function () {
            if (!this.files.length) return;
            const fd = new FormData();
            fd.append('fileToUpload', this.files[0]);
            fetch('../../src/php/createAccount.php', { method: 'POST', body: fd })
                .then(() => location.reload()).catch(console.error);
        });
    </script>
</body>
</html>