<?php
include 'config.php';
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $area = $_POST['area'];
    $rooms = $_POST['rooms'];
    $price_per_night = $_POST['price_per_night'];
    $image_path = '';

    // Validate inputs
    if (!preg_match("/^[a-zA-Z\s]*$/", $title)) {
        $message = "Title should contain only letters and spaces.";
    } elseif (!preg_match("/^[a-zA-Z\s]*$/", $area)) {
        $message = "Location should contain only letters and spaces.";
    } elseif (!filter_var($rooms, FILTER_VALIDATE_INT)) {
        $message = "Number of rooms should be an integer.";
    } elseif (!filter_var($price_per_night, FILTER_VALIDATE_INT)) {
        $message = "Price per night should be an integer.";
    } elseif ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
        $message = "Error uploading image.";
    } else {
        // Handle image upload
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;

            // Insert into database
            $sql = "INSERT INTO listings (title, area, rooms, price_per_night, image_path)
                    VALUES ('$title', '$area', '$rooms', '$price_per_night', '$image_path')";

            if ($conn->query($sql) === TRUE) {
                $message = "Listing created successfully!";
                header("Location: feed.php");
                exit();
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $message = "Error uploading image.";
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
    <title>Create Listing</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        function validateForm() {
            var title = document.getElementById("title").value;
            var area = document.getElementById("area").value;
            var rooms = document.getElementById("rooms").value;
            var price_per_night = document.getElementById("price_per_night").value;
            var image = document.getElementById("image").value;

            var titlePattern = /^[a-zA-Z\s]*$/;
            var areaPattern = /^[a-zA-Z\s]*$/;

            if (!titlePattern.test(title)) {
                alert("Title should contain only letters and spaces.");
                return false;
            }

            if (!areaPattern.test(area)) {
                alert("Location should contain only letters and spaces.");
                return false;
            }

            if (!Number.isInteger(Number(rooms)) || rooms <= 0) {
                alert("Number of rooms should be a positive integer.");
                return false;
            }

            if (!Number.isInteger(Number(price_per_night)) || price_per_night <= 0) {
                alert("Price per night should be a positive integer.");
                return false;
            }

            if (image == "") {
                alert("Please upload an image.");
                return false;
            }

            return true;
        }
    </script>
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
    <h2>Create Listing</h2>
    <?php if($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" action="create_listing.php" enctype="multipart/form-data" onsubmit="return validateForm()">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="area">Location:</label>
        <input type="text" id="area" name="area" required>

        <label for="rooms">Number of Rooms:</label>
        <input type="number" id="rooms" name="rooms" required>

        <label for="price_per_night">Price per Night:</label>
        <input type="number" id="price_per_night" name="price_per_night" required>

        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" required>

        <input type="submit" value="Create Listing">
    </form>
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
