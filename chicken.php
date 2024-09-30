<?php
session_start(); // Start the session to track user-specific data
print_r($_SESSION);  // Debugging: Print all session variables for monitoring purposes

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "meat room data base"; 

// Create connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful, otherwise terminate the script
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch only products in the 'chicken' category
$query = "SELECT product_id, name, price_description, image FROM products WHERE category = 'chicken'";
$result = $conn->query($query); // Execute the query

$products = []; // Initialize an empty array to store chicken products

// If there are products in the result, loop through and store them in the $products array
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Store each product's details in an associative array
        $products[] = [
            'id' => $row['product_id'],
            'name' => $row['name'],
            'price_description' => $row['price_description'],
            'image' => $row['image']
        ];
    }
}
$conn->close(); // Close the database connection after fetching data
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Meat Room - Chicken</title>
    <link rel="stylesheet" href="css/stylesheet.css"> <!-- Link to the external stylesheet -->
    <link rel="stylesheet" href="https://use.typekit.net/rpj3trr.css"> <!-- Include external font stylesheet -->
</head>

<body>
    <!-- Navigation Bar and Logo Section -->
    <div>
        <div class="top-bar">
            <h1>The Meat Room</h1> <!-- Website title -->
            <h6>Premium butcher</h6> <!-- Website tagline -->
        </div>

        <!-- Navigation Links -->
        <div class="nav">
            <!-- Logo linked to the home page -->
            <a href="home.php">
                <img class="logoimg" src="assets/imgs/LOGO.png" alt="Logo">
            </a>
            
            <!-- Hamburger Icon for Mobile Navigation -->
            <!-- Toggles the mobile navigation menu when clicked -->
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <!-- Navigation menu items -->
            <ul>
                <li><a href="home.php">HOME</a></li> <!-- Home link -->
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li class="dropdown">
                    <a href="shop.php">SHOP</a> <!-- Shop link -->
                    <ul class="dropdown-content">
                        <!-- Links to specific product categories -->
                        <li><a href="beef.php">Beef</a></li>
                        <li><a href="chicken.php">Chicken</a></li>
                        <li><a href="pork.php">Pork</a></li>
                        <li><a href="lamb.php">Lamb</a></li>
                    </ul>
                </li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li><a href="contact.php">CONTACT</a></li> <!-- Contact link -->
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li class="dropdown">
                    <a href="#">ACCOUNT</a> <!-- Account dropdown -->
                    <ul class="dropdown-content">
                        <?php if (isset($_SESSION['user_id'])): ?> <!-- If the user is logged in -->
                            <li><a href="log_out.php">Log out</a></li> <!-- Logout link -->
                        <?php else: ?> <!-- If the user is not logged in -->
                            <li><a href="login.php">Log in</a></li> <!-- Login link -->
                            <li><a href="sign_up.php">Sign up</a></li> <!-- Sign up link -->
                        <?php endif; ?>
                    </ul>
                </li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php if (isset($_SESSION['user_id'])): ?> <!-- If the user is logged in, show cart options -->
                    <li class="dropdown">
                        <a href="cart.php">CART</a>
                        <ul class="dropdown-content">
                            <li><a href="order_history.php">Orders</a></li> <!-- Order history link -->
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <!-- JavaScript to toggle the mobile navigation menu -->
    <script>
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav ul').classList.toggle('active');
        });
    </script>

    <!-- Chicken Products Section -->
    <div>
        <div class="product-section">
            <h1>CHICKEN PRODUCTS</h1> <!-- Title for the chicken products section -->
            <table style="padding-bottom:15px;padding-top:40px;padding-bottom:70px;" id="chicken-products">
                <?php
                $count = 0; // Counter to track the number of products displayed

                // Loop through each product and display it in a table
                foreach ($products as $product):
                    // Start a new row after every third product
                    if ($count % 3 === 0): ?>
                        <tr>
                    <?php endif; ?>

                    <!-- Display each product in a table cell -->
                    <td class="tablecontent">
                        <a href="product.php?id=<?php echo $product['id']; ?>"> <!-- Link to the product page with the product ID as a query parameter -->
                            <img class="productimg" src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"> <!-- Product image -->
                        </a>
                        <div class="tabletext1 tablecontent"><?php echo $product['name']; ?></div> <!-- Product name -->
                        <div class="tabletext2 tablecontent"><?php echo $product['price_description']; ?></div> <!-- Product price description -->
                    </td>

                    <?php
                    $count++; // Increment the product counter
                    // End the row after every third product
                    if ($count % 3 === 0): ?>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php
                // If the number of products is not a multiple of 3, close the last row
                if ($count % 3 !== 0): ?>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <h2>Contact Info:</h2>
        <p>Instagram - @Themeatroom</p>
        <p>FaceBook - @Themeatroomnz</p>
        <p>Twitter - @Themeatroomnz</p>
        <p>Themeatroom@gmail.com | 0283969863 | 26 Anzac Road, Browns Bay</p>
    </div>
</body>
</html>
