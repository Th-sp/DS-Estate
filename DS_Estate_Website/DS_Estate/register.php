<?php
include 'config.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (!preg_match("/^[a-zA-Z]*$/", $firstname)) {
        $message = "Name should contain only letters.";
    } elseif (!preg_match("/^[a-zA-Z]*$/", $lastname)) {
        $message = "Surname should contain only letters.";
    } elseif (strlen($password) < 4 || strlen($password) > 10 || !preg_match("/[0-9]+/", $password)) {
        $message = "Password must be between 4 and 10 characters long and contain at least one number.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        $sql = "SELECT id FROM users WHERE username='$username' OR email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $message = "Username or Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (firstname, lastname, username, password, email)
            VALUES ('$firstname', '$lastname', '$username', '$hashed_password', '$email')";

            if ($conn->query($sql) === TRUE) {
                $message = "Registration successful!";
                header("Location: login.php");
                exit();
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
    <h2>Register</h2>
    <?php if($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" action="register.php">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" required>

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" required>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <input type="submit" value="Register">
    </form>
    <p>Already registered? <a href="login.php">Login here</a>.</p>
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

