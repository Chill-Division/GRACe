<?php require_once 'auth.php'; ?>
<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   

    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">   

    <link rel="stylesheet" href="css/growcart.css"> 
    <title>GrowCART - Administration</title> 
</head>
<body>
    <header class="container-fluid">
	<?php require_once 'nav.php'; ?>
    </header>

    <main class="container">
        <h1>Administration</h1>

        <section>
            <h2>Company Management</h2>
            <ul>
                <li><a href="add_verified_company.php">Add Verified Company</a></li>
            </ul>
        </section>

        <section>
            <h2>Genetics Management</h2>
            <ul>
                <li><a href="add_new_genetics.php">Add New Genetics</a></li>
            </ul>
        </section>

        <section>
            <h2>Record management</h2>
            <ul>
                <li><a href="police_vet_check_records.php">Police Vet Check Records</a></li>
            </ul>
            <ul>
                <li><a href="sops.php">Manage SOPs</a></li>
            </ul>
            <ul>
                <li><a href="offtake_agreements.php">Offtake Agreements</a></li>
            </ul>
        </section>

        <section>
            <h2>System Management</h2>
            <ul>
                <li><a href="add_new_user.php">Add New User</a></li>
                <li><a href="delete_user.php">Delete User</a></li>
            </ul>
        </section>
    </main>

    <script src="js/growcart.js"></script> 
</body>
</html>
