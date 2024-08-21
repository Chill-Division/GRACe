<?php require_once 'auth.php'; ?>
<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   

    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">   

    <link rel="stylesheet" href="css/growcart.css"> 
    <title>GRACe - Add New User</title> 
</head>
<body>
    <header class="container-fluid">
	<?php require_once 'nav.php'; ?>
    </header>

    <main class="container">
        <h1>Add New User</h1>

	<form id="addUserForm" class="form" action="handle_add_new_user.php" method="post">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" class="input" required>

            <label for="surname">Surname:</label>   
            <input type="text" id="surname" name="surname" class="input" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="input" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="input" required>

            <button type="submit" class="button">Add User</button>
        </form>
    </main>

    <script src="js/growcart.js"></script> 
</body>
</html>
