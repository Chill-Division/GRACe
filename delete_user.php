<?php require_once 'auth.php'; ?>
<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">   
    <link rel="stylesheet" href="css/growcart.css"> 
    <title>GRACe - Delete User</title> 
</head>
<body>
    <header class="container-fluid">
        <?php require_once 'nav.php'; ?>
    </header>

    <main class="container">
        <h1>Delete User</h1>

        <table id="userList" class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Surname</th>
                    <th>Last Logged In</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </main>

    <script src="js/growcart.js"></script> 
<script>
    const userList = document.getElementById('userList').getElementsByTagName('tbody')[0];

    fetch('get_users.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(users => {
            users.sort((a, b) => a.username.localeCompare(b.username));
            users.forEach(user => {
                const row = userList.insertRow();
                row.insertCell().textContent = user.username;
                row.insertCell().textContent = user.first_name;
                row.insertCell().textContent = user.surname;
                row.insertCell().textContent = user.last_logged_in || 'Never';
                
                const actionCell = row.insertCell();
                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.classList.add('button'); 
                deleteButton.addEventListener('click', () => {
                    if (confirm(`Are you sure you want to delete user ${user.username}?`)) {
                        fetch('handle_delete_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ username: user.username })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                row.remove();
                            } else {
                                console.error('Error from server:', data.message);
                                alert('An error occurred while deleting the user: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error during delete request:', error);
                            alert('An error occurred. Please check the console for details.');
                        });
                    }
                });
                actionCell.appendChild(deleteButton);
            });
        })
        .catch(error => {
            console.error('Error fetching user data:', error);
            userList.innerHTML = `<tr><td colspan="5">Error loading user data: ${error.message}</td></tr>`;
        });
</script></body>
</html>
