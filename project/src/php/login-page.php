
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aegean Breeze</title>
    <link rel="icon" href="../../assets/img/Logo.png" type="image/png">
    <link rel="stylesheet" href="../../src/styles/style.css">
    <link rel="stylesheet" href="../../src/styles/login.css">
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
            <div class="nav-element"><a href="../../src/php/login-page.php">Login</a></div>
        </nav>
    </header>

    <!-- Login Section -->
    <section class="login-page">
        <div class="login-content">
            <div class="login-card">
                <div class="login-header">
                    <img src="../../assets/img/Logo.png" alt="Aegean Breeze Logo" class="login-logo">
                    <h1>Welcome Back</h1>
                    <p>Login to share your travel stories</p>
                </div>
                <form class="login-form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password">
                    </div>
                    <button type="button" class="btn btn-login">Login</button>
                </form>

                <div class="login-footer">
                    <p>Don't have an account? <a href="../../index.php#signup">Create an account</a></p>
                </div>
            </div>
        </div>
    </section>

</body>

</html>