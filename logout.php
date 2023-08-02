<?php
session_start();
// Clear all session variables
$_SESSION = array();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://media.gettyimages.com/id/157188156/photo/rainy-evening-morning-at-the-airport.jpg?s=612x612&w=0&k=20&c=omWq5zfqW31F7EIYYi7iYe94OTv2i_abFQyjN12cHXE=');
            background-size: cover;
            background-position: center;
            z-index: -1;
        }
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            z-index: 9999;
        }
    </style>
    <script>
        // Redirect the user to the login page after 5 seconds
        setTimeout(function() {
            window.location.href = 'index.html';
        }, 5000);
    </script>
</head>
<body>
    <div class="background"></div>
    <div class="popup">
        <h1>Thank you for visiting the website.</h1>
    </div>
</body>
</html>
