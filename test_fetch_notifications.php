<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Fetch Notifications</title>
</head>
<body>
    <h2>Test Fetch Notifications</h2>
    
    <h3>Session Information:</h3>
    <p>User ID: <?php echo $_SESSION['user_id'] ?? 'NOT SET'; ?></p>
    <p>Logged in: <?php echo $_SESSION['logged_in'] ?? 'NOT SET'; ?></p>
    <p>User role: <?php echo $_SESSION['user_role'] ?? 'NOT SET'; ?></p>
    
    <h3>Test Fetch Results:</h3>
    <div id="results">Loading...</div>
    
    <script>
        fetch('functions/fetch_notifications.php')
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Fetched data:', data);
                document.getElementById('results').innerHTML = 
                    '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('results').innerHTML = 
                    '<p style="color: red;">Error: ' + error.message + '</p>';
            });
    </script>
</body>
</html>
