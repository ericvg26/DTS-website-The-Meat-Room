<?php

session_start(); // Start the session to track user activity and store user-specific data
print_r($_SESSION);  // Debugging: Print all session variables for monitoring purposes

// Database connection details
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "meat room data base"; 

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Terminate the script if connection fails
}

// SQL query to fetch all products from the 'products' table
$query = "SELECT product_id, name, price_description AS price, image FROM products";
$result = $conn->query($query); // Execute the query and store the result

$products = []; // Initialize an empty array to store products
if ($result->num_rows > 0) { // Check if there are any products in the result
    while($row = $result->fetch_assoc()) { // Fetch each product as an associative array
        $products[] = $row; // Append each product to the $products array
    }
}

// Close the database connection after retrieving all product data
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Meat Room</title>
    <link rel="stylesheet" href="css/stylesheet.css"> <!-- Include external CSS file -->
    <link rel="stylesheet" href="https://use.typekit.net/rpj3trr.css"> <!-- Include external font stylesheet may not be needed -->
    
    <style>
        /* Slideshow container styles */
        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin: auto;
        }

        /* Hide all slides by default */
        .mySlides {
            display: none;
            text-align: center;
        }

        /* Styling for individual product content within a slide */
        .product-content {
            display: inline-block;
            text-align: center;
            padding: 15px;
        }

        /* Styling for product images */
        .productimg {
            width: 100%;
            height: auto;
            max-width: 250px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        /* Navigation buttons for the slideshow */
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none; /* Disable text selection */
            z-index: 1; /* Ensure buttons are on top of other elements */
        }

        /* Style for the left navigation button */
        .prev {
            border-radius: 3px 0 0 3px;
        }

        /* Style for the right navigation button */
        .next {
            border-radius: 0 3px 3px 0;
        }

        /* Hover effect for navigation buttons */
        .prev:hover, .next:hover {
            background-color: rgba(74, 72, 72, 0.8);
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <div>
        <div class="top-bar">
            <h1>The Meat Room</h1>
            <h6>Premium butcher</h6>
        </div>
        
        <div class="nav">
            <!-- Logo that links to the home page -->
            <a href="home.php">
                <img class="logoimg" src="assets/imgs/LOGO.png" alt="Logo">
            </a>
            
            <!-- Hamburger menu icon for mobile navigation -->
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <!-- Main navigation links -->
            <ul>
                <li><a href="home.php">HOME</a></li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li class="dropdown">
                    <a href="shop.php">SHOP</a> <!-- Dropdown for shop categories -->
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
                    <a href="#">ACCOUNT</a> <!-- Dropdown for account-related options -->
                    <ul class="dropdown-content">
                        <?php if (isset($_SESSION['user_id'])): ?> <!-- If the user is logged in -->
                            <li><a href="log_out.php">Log out</a></li>
                        <?php else: ?> <!-- If the user is not logged in -->
                            <li><a href="login.php">Log in</a></li>
                            <li><a href="sign_up.php">Sign up</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php if (isset($_SESSION['user_id'])): ?> <!-- If the user is logged in -->
                    <li class="dropdown">
                        <a href="cart.php">CART</a> <!-- Dropdown for cart and order history -->
                        <ul class="dropdown-content">
                            <li><a href="order_history.php">Orders</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <!-- JavaScript for the hamburger menu toggle -->
    <script>
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav ul').classList.toggle('active');
        });
    </script>

    <!-- Slideshow container for displaying popular products -->
    <div>
        <div class="product-section2">
            <h1>POPULAR PRODUCTS</h1>
            <div class="slideshow-container">
                <?php
                // Set the number of products to display per slide
                $products_per_slide = 4;
                $total_products = count($products); // Total number of products fetched
                $total_slides = ceil($total_products / $products_per_slide); // Calculate total slides needed

                // Loop through each slide
                for ($slide = 0; $slide < $total_slides; $slide++) {
                    echo '<div class="mySlides">'; // Begin a new slide
                    // Loop through the products in the current slide
                    for ($i = $slide * $products_per_slide; $i < ($slide + 1) * $products_per_slide && $i < $total_products; $i++) {
                        $product = $products[$i]; // Get the current product
                        echo '<div class="product-content">'; // Product container
                        echo '<a href="product.php?id=' . $product['product_id'] . '">'; // Link to the product page
                        echo '<img class="productimg" src="' . $product['image'] . '" alt="' . $product['name'] . '">'; // Product image
                        echo '</a>';
                        echo '<div class="tabletext1">' . $product['name'] . '</div>'; // Product name
                        echo '<div class="tabletext2">' . $product['price'] . '</div>'; // Product price
                        echo '</div>';
                    }
                    echo '</div>'; // End of the slide
                }
                ?>
                
                <!-- Navigation buttons for the slideshow -->
                <a class="prev" onclick="plusSlides(-1)">&#10094;</a> <!-- Left arrow -->
                <a class="next" onclick="plusSlides(1)">&#10095;</a> <!-- Right arrow -->
            </div>
        </div>
    </div>

    <!-- Footer Section with Contact Information -->
    <div class="footer">
        <h2>Contact Info:</h2>
        <p>Instagram - @Themeatroom</p>
        <p>FaceBook - @Themeatroomnz</p>
        <p>Twitter - @Themeatroomnz</p>
        <p>Themeatroom@gmail.com | 0283969863 | 26 Anzac Road, Browns Bay</p>
    </div>

    <!-- JavaScript for controlling the slideshow -->
    <script>
        let slideIndex = 0;
        showSlides(slideIndex); // Initialize the slideshow

        // Function to move to the next/previous slide
        function plusSlides(n) {
            slideIndex += n;
            showSlides(slideIndex);
        }

        // Function to show the current slide
        function showSlides(n) {
            const slides = document.getElementsByClassName("mySlides"); // Get all slides
            if (n >= slides.length) {
                slideIndex = 0; // If the index exceeds the number of slides, start over
            }
            if (n < 0) {
                slideIndex = slides.length - 1; // If the index is negative, go to the last slide
            }
            // Hide all slides
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            // Display the current slide
            slides[slideIndex].style.display = "block";
        }

        // Initialize the slideshow on page load
        showSlides(slideIndex);
    </script>
</body>
</html>
