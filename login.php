<?php
session_start(); // Start the session to store user information, like user ID, across different pages.

$servername = "localhost";  
$username = "root";  // The MySQL username
$password = "";  // The MySQL password
$dbname = "meat room data base";  

// Create a new connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection to the database was successful
if ($conn->connect_error) {
    // If the connection fails, terminate the script and display an error message
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted using the POST method (which happens when the user submits the login form)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the input to prevent SQL injection attacks by escaping special characters in the email and password
    $email = $conn->real_escape_string($_POST['email']);  // Escape special characters from the email input
    $password = $conn->real_escape_string($_POST['password']);  // Escape special characters from the password input

    // SQL query to fetch the user's ID, username, and hashed password from the database where the email matches the input
    $sql = "SELECT user_id, username, password FROM users WHERE email = '$email'";
    $result = $conn->query($sql);  // Execute the query and store the result

    // Check if any user with the provided email exists in the database
    if ($result->num_rows > 0) {
        // Fetch the user's details (user ID, username, and hashed password) as an associative array
        $user = $result->fetch_assoc();

        // Use password_verify() to compare the input password with the hashed password stored in the database
        if (password_verify($password, $user['password'])) {
            // If the password is correct, store the user's ID and username in session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // Redirect the user to the home page after a successful login
            header("Location: home.php");
            exit();  // Ensure no further code is executed
        } else {
            // If the password is incorrect, set an error message and redirect the user back to the login page
            $_SESSION['error_message'] = "Invalid password.";
            header("Location: login.php");
            exit();
        }
    } else {
        // If no user with the provided email is found, set an error message and redirect the user back to the login page
        $_SESSION['error_message'] = "No user found with that email.";
        header("Location: login.php");
        exit();
    }
}

// Close the database connection once the login process is complete
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - The Meat Room</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <style>
        /* Additional styles for the login form */
        .login-container {
            max-width: 500px;  /* Restrict the width of the login form */
            margin: 50px auto;  /* Center the form on the page */
            padding: 20px;
            background-color: rgb(21, 21, 21); /* Background color matching the theme */
            border-radius: 10px;  /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);  /* Add a shadow for some depth */
            text-align: center;  /* Center the content inside the form */
        }

        .login-container h1 {
            font-family: "kepler-std", serif;  /* Font styling */
            color: #c55b5b;  /* The main theme color */
            margin-bottom: 20px;  /* Space below the heading */
            font-size: 36px;  /* Heading font size */
        }

        .login-container label {
            display: block;  /* Ensure the label is on a separate line from the input field */
            margin-bottom: 10px;
            color: #ffffff;  /* White text color */
            font-family: "finalsix", sans-serif;
            text-align: left;  /* Align labels to the left */
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100%;  /* Make input fields take up the full width */
            padding: 10px;
            margin-bottom: 20px;  /* Space between fields */
            border: 1px solid #c55b5b;  /* Border color matching the theme */
            border-radius: 5px;  /* Rounded corners for input fields */
            background-color: #2a2a2a;  /* Input field background color */
            color: #ffffff;  /* Input text color */
            font-family: "finalsix", sans-serif;
        }

        .login-container button {
            width: 100%;  /* Make the button as wide as the input fields */
            padding: 10px;
            background-color: #c55b5b;  /* Button background color matching the theme */
            border: none;
            border-radius: 5px;  /* Rounded button corners */
            color: #ffffff;  /* Button text color */
            font-family: "finalsix", sans-serif;
            font-size: 16px;
            cursor: pointer;  /* Pointer cursor on hover */
            transition: background-color 0.3s ease;  /* Smooth transition on hover */
        }

        .login-container button:hover {
            background-color: #a44444;  /* Darker button color on hover */
        }

        .login-container p {
            margin-top: 20px;
            color: #ffffff;
            font-family: "finalsix", sans-serif;
            text-align: center;
        }

        .login-container p a {
            color: #c55b5b;  /* Links use the main theme color */
            text-decoration: none;
        }

        .login-container p a:hover {
            text-decoration: underline;  /* Underline on hover */
        }

        /* Error message styling */
        .error-message {
            color: red;  /* Error messages are red */
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div>
        <div class="top-bar">
            <h1>The Meat Room</h1>
            <h6>Premium butcher</h6>
        </div>
        <div class="nav">
            <a href="home.php">
                <img class="logoimg" src="assets/imgs/LOGO.png" alt="Logo">
            </a>
            
            <!-- Hamburger Icon for Mobile Navigation -->
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <ul>
                <li><a href="home.php">HOME</a></li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li class="dropdown">
                    <a href="shop.php">SHOP</a>
                    <ul class="dropdown-content">
                        <li><a href="beef.php">Beef</a></li>
                        <li><a href="chicken.php">Chicken</a></li>
                        <li><a href="pork.php">Pork</a></li>
                        <li><a href="lamb.php">Lamb</a></li>
                    </ul>
                </li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li><a href="contact.php">CONTACT</a></li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li class="dropdown">
                    <a href="#">ACCOUNT</a>
                    <ul class="dropdown-content">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="log_out.php">Log out</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Log in</a></li>
                            <li><a href="sign_up.php">Sign up</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <li><a href="cart.php">CART</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script>
        // Handle the hamburger menu click for mobile navigation
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav ul').classList.toggle('active');
        });
    </script>

    <!-- The login form container -->
    <div class="login-container">
        <h1>Login</h1>
        <!-- If there is an error message in the session, display it -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php 
                    // Display the error message
                    echo $_SESSION['error_message']; 
                    // Remove the error message after displaying
                    unset($_SESSION['error_message']); 
                ?>
            </div>
        <?php endif; ?>

        <!-- The login form -->
        <form action="login.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required> <!-- Email input field -->
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required> <!-- Password input field -->
            
            <button type="submit">Sign In</button> <!-- Submit button -->
        </form>

        <p>Don't have an account? <a href="sign_up.php">Create Account</a></p>
        <p><a href="home.php">Return to Store</a></p>
    </div>
</body>
</html>
