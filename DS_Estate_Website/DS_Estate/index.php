<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DS Estate</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="js/script.js" defer></script>
</head>
<body>

<nav>
    <div class="container">
        <div class="menu-icon">
            <img src="images/hamburger-icon.png" alt="Menu Icon">
        </div>
        <ul id="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="feed.php">Feed</a></li>
            <li><a href="create_listing.php">Create Listing</a></li>
            <?php
            session_start();
            if (isset($_SESSION["username"])) {
                echo '<li><a href="logout.php">Logout</a></li>';
            } else {
                echo '<li><a href="login.php">Login</a></li>';
            }
            ?>
        </ul>
    </div>
</nav>

<div class="container main-content">
    <h1>Welcome to DS Estate</h1>
    <p>Your trusted platform for short-term property rentals.</p>
    <div class="home-image">
        <img src="images/homes.png" >
    </div>
</div>

<footer>
    <div class="container footer-grid">
        <div>
            <p>Contact us:</p>
            <p><a href="tel:+1234567890" style="color: #fff;">+1234567890</a></p>
            <p><a href="mailto:info@dsestate.com" style="color: #fff;">info@dsestate.com</a></p>
        </div>
        <div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3130.946650408834!2d23.64492471584656!3d37.94145727972473!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14a1bc0b24abbdff%3A0x8c5a1c85c5e7ad2a!2sPiraeus%2C%20Greece!5e0!3m2!1sen!2sus!4v1623497455293!5m2!1sen!2sus" width="300" height="200" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
        </div>
    </div>
</footer>


<script src="js/script.js"></script>
</body>
</html>
