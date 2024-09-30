<?php
session_start(); // Start the session to store user data

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meat room data base";

// Create a new connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection to the database was successful
if ($conn->connect_error) {
    // If the connection fails, terminate the script and display an error message
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ''; // Initialize an error message variable

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input to prevent SQL injection attacks
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Check if the password and confirm password match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if the username or email already exists in the database
        $check_user_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = $conn->query($check_user_query);

        // If a user with the same username or email exists, set an error message
        if ($result->num_rows > 0) {
            $error_message = "Username or email already exists.";
        } else {
            // Hash the password for secure storage in the database
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // SQL query to insert the new user into the users table
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

            // If the insertion is successful, log the user in and redirect to the home page
            if ($conn->query($sql) === TRUE) {
                $_SESSION['user_id'] = $conn->insert_id; // Store the user ID in the session
                header("Location: home.php"); // Redirect to the home page after successful sign-up
                exit(); // Stop further execution
            } else {
                // If an error occurs during the insertion, store the error message
                $error_message = "Error: " . $conn->error;
            }
        }
    }

    // If an error occurs, store the error message and redirect back to the form
    if ($error_message) {
        $_SESSION['error_message'] = $error_message;
        $_SESSION['form_data'] = ['username' => $username, 'email' => $email]; // Store form data so user doesn't have to re-enter it
        header("Location: sign_up.php");
        exit();
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - The Meat Room</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <style>
        /* Additional styles for the sign-up form */
        .sign-up-container {
            max-width: 500px;  /* Limit the width of the sign-up form */
            margin: 50px auto;  /* Center the form on the page */
            padding: 20px;
            background-color: rgb(21, 21, 21);  /* Dark background color */
            border-radius: 10px;  /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);  /* Drop shadow */
            text-align: center;  /* Center the content inside the form */
        }

        .sign-up-container h1 {
            font-family: "kepler-std", serif;  /* Font styling for the heading */
            color: #c55b5b;  /* Color matching the website's theme */
            margin-bottom: 20px;
            font-size: 36px;  /* Font size for the heading */
        }

        .sign-up-container label {
            display: block;  /* Ensure the label appears above the input field */
            margin-bottom: 10px;
            color: #ffffff;  /* White text color */
            font-family: "finalsix", sans-serif;
            text-align: left;  /* Align the label text to the left */
        }

        .sign-up-container input[type="text"],
        .sign-up-container input[type="email"],
        .sign-up-container input[type="password"] {
            width: 100%;  /* Input fields take up the full width */
            padding: 10px;
            margin-bottom: 20px;  /* Space between input fields */
            border: 1px solid #c55b5b;  /* Border color matching the theme */
            border-radius: 5px;  /* Rounded corners for input fields */
            background-color: #2a2a2a;  /* Dark background color for inputs */
            color: #ffffff;  /* White text color */
            font-family: "finalsix", sans-serif;
        }

        .sign-up-container button {
            width: 100%;  /* Button takes up the full width */
            padding: 10px;
            background-color: #c55b5b;  /* Button color matching the theme */
            border: none;
            border-radius: 5px;  /* Rounded button corners */
            color: #ffffff;  /* White button text */
            font-family: "finalsix", sans-serif;
            font-size: 16px;
            cursor: pointer;  /* Pointer cursor on hover */
            transition: background-color 0.3s ease;  /* Smooth transition on hover */
        }

        .sign-up-container button:hover {
            background-color: #a44444;  /* Darken button color on hover */
        }

        .sign-up-container p {
            margin-top: 20px;
            color: #ffffff;  /* White text color for the paragraph */
            font-family: "finalsix", sans-serif;
        }

        .sign-up-container p a {
            color: #c55b5b;  /* Link color matching the theme */
            text-decoration: none;  /* Remove underline from link */
        }

        .sign-up-container p a:hover {
            text-decoration: underline;  /* Add underline on hover */
        }

        /* Error message styling */
        .error-message {
            color: red;  /* Error messages are displayed in red */
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div>
        <!-- Top bar section with the website's name and slogan -->
        <div class="top-bar">
            <h1>The Meat Room</h1>
            <h6>Premium butcher</h6>
        </div>
        
        <!-- Navigation bar -->
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
                        <!-- If the user is logged in, show logout, otherwise show login/signup options -->
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

    <!-- JavaScript for handling the mobile hamburger menu -->
    <script>
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav ul').classList.toggle('active');
        });
    </script>

    <!-- Sign-up form container -->
    <div class="sign-up-container">
        <h1>Sign Up</h1>
        <!-- Display any error messages from the session -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']); // Clear the error message after displaying
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Sign-up form -->
        <form action="sign_up.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Log In</a></p>
    </div>
</body>
</html>
