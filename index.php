<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="css/growcart.css">
    <title>GRACe Login</title>
</head>
<body>
    <header class="container-fluid">
	<?php require_once 'nav.php'; ?>
    </header>
    <main class="container">
        <article class="" style="width: 60%;margin: auto;">
            <div style="width: 100%;">
                <p>GRACe (Grower's Regulatory Assistance & Compliance Engine)</p>

                <form id="loginForm" class="form" action="login.php" method="post"> <label for="username">Username:</label>
                    <input type="text" id="username" name="username" class="input" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="input" required>

                    <button type="submit" class="button">Login</button>
                </form>

                <div id="loggedInSection" style="display: none;">
                    <h2>Welcome to GRACe</h2>
                    <p>You are now logged in.</p>
                </div>
            </div>
        </article>

    </main>
    <script src="js/growcart.js"></script>
</body>
</html>
