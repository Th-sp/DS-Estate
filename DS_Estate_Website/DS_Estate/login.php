<?php
include 'config.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            $_SESSION["username"] = $row["username"];
            header("Location: index.php");
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "No user found with username: $username";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<nav>
    <div class="container">
        <div class="menu-icon" onclick="toggleMenu()">
            <img src="images/hamburger-icon.png" alt="Menu Icon" style="width:30px;">
        </div>
        <ul id="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="feed.php">Feed</a></li>
            <li><a href="create_listing.php">Create Listing</a></li>
            <?php
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
    <h2>Login</h2>
    <?php if($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Login">
    </form>
    <p>Not registered? <a href="register.php">Register here</a>.</p>
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
