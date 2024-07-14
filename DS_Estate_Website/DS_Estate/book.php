<?php
include 'config.php';
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$message = '';
$property = null;
$user = null;
$step = 1;

// Fetch property details
if (isset($_GET['id'])) {
    $property_id = $_GET['id'];
    $sql = "SELECT * FROM listings WHERE id = $property_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
    } else {
        $message = "Property not found.";
    }
} else {
    header("Location: feed.php");
    exit();
}

// Fetch user details using username from session
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $message = "User not found.";
    }
} else {
    $message = "User not logged in.";
}

// Handle booking process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['step']) && $_POST['step'] == 1) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Check availability
        $sql = "SELECT * FROM reservations WHERE property_id = $property_id AND
                ((start_date <= '$end_date' AND end_date >= '$start_date'))";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<script>alert('Sorry, the property is not available for the selected dates.'); 
                    window.location.href='book.php?id=$property_id';</script>";
        } else {
            $step = 2;
        }
    } elseif (isset($_POST['step']) && $_POST['step'] == 2) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];

        // Calculate the final payment amount
        $date1 = new DateTime($start_date);
        $date2 = new DateTime($end_date);
        $nights = $date2->diff($date1)->format("%a");
        $initial_payment = $nights * $property['price_per_night'];
        $discount_rate = rand(10, 30) / 100;
        $final_payment = $initial_payment - ($initial_payment * $discount_rate);

        // Insert booking into database
        $user_id = $user['id']; // Fetch user_id from the user details
        $sql = "INSERT INTO reservations (property_id, user_id, start_date, end_date, name, surname, email, amount)
                VALUES ('$property_id', '$user_id', '$start_date', '$end_date', '$name', '$surname', '$email', '$final_payment')";

        if ($conn->query($sql) === TRUE) {
            $message = "Booking successful!";
            header("Location: feed.php");
            exit();
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
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
    <title>Book Property</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 500px;
            margin: auto;
        }
        .form-container form {
            width: 100%;
        }
        .form-container label {
            display: block;
            margin-top: 10px;
        }
        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="date"],
        .form-container input[type="number"],
        .form-container input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        .form-container input[type="submit"] {
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
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
    <h2>Book Property</h2>
    <?php if ($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if ($property): ?>
        <div class="property-details">
            <img src="<?php echo $property['image_path']; ?>" alt="Property Image" style="width:400px;" >
            <h3><?php echo $property['title']; ?></h3>
            <p>Location: <?php echo $property['area']; ?></p>
            <p>Rooms: <?php echo $property['rooms']; ?></p>
            <p>Price per night: $<?php echo number_format($property['price_per_night'], 2); ?></p>
        </div>
        <div class="form-container">
            <?php if ($step == 1): ?>
                <form method="post" action="book.php?id=<?php echo $property['id']; ?>">
                    <input type="hidden" name="step" value="1">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>

                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>

                    <input type="submit" value="Check Availability">
                </form>
            <?php elseif ($step == 2): ?>
                <form method="post" action="book.php?id=<?php echo $property['id']; ?>">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="start_date" value="<?php echo $_POST['start_date']; ?>">
                    <input type="hidden" name="end_date" value="<?php echo $_POST['end_date']; ?>">

                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($user['firstname']) ? $user['firstname'] : ''; ?>" required>

                    <label for="surname">Surname:</label>
                    <input type="text" id="surname" name="surname" value="<?php echo isset($user['lastname']) ? $user['lastname'] : ''; ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>" required>

                    <?php
                    // Calculate the final payment amount
                    $date1 = new DateTime($_POST['start_date']);
                    $date2 = new DateTime($_POST['end_date']);
                    $nights = $date2->diff($date1)->format("%a");
                    $initial_payment = $nights * $property['price_per_night'];
                    $discount_rate = rand(10, 30) / 100;
                    $final_payment = $initial_payment - ($initial_payment * $discount_rate);
                    ?>
                    <p>Final Payment Amount: $<?php echo number_format($final_payment, 2); ?></p>

                    <input type="submit" value="Confirm Booking">
                </form>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Property not found.</p>
    <?php endif; ?>
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
