<?php
session_start();

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "meat room data base"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;
$recommendations = [];
$success_message = "";

// Handle Add to Cart request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);

        // Check if the product is already in the cart
        $check_cart_query = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
        $check_cart_result = $conn->query($check_cart_query);

        if ($check_cart_result->num_rows > 0) {
            // If product is already in the cart, set an error message
            $error_message = "Product is already in the cart. Please update the quantity in the cart.";
        } else {
            // Fetch the stock quantity for validation
            $stock_query = "SELECT stock_quantity FROM products WHERE product_id = $product_id";
            $stock_result = $conn->query($stock_query);
            $stock_data = $stock_result->fetch_assoc();

            if ($quantity > 0 && $quantity <= $stock_data['stock_quantity']) {
                // Insert the product into the cart with the chosen quantity
                $insert_cart_query = "INSERT INTO cart (user_id, product_id, quantity, added_at) VALUES ($user_id, $product_id, $quantity, NOW())";
                $conn->query($insert_cart_query);

                // Set success message
                $_SESSION['success_message'] = "Product successfully added to cart!";
                // Redirect to avoid form resubmission
                header("Location: product.php?id=$product_id");
                exit();
            } else {
                // If the quantity is not valid, handle it (e.g., set an error message)
                $error_message = "Invalid quantity. Please choose a quantity between 1 and " . $stock_data['stock_quantity'] . ".";
            }
        }
    } else {
        // If user is not logged in, redirect to login page
        header("Location: login.php");
        exit();
    }
}

if ($product_id > 0) {
    // Fetch the specific product details including stock_quantity
    $query = "SELECT name, price_description AS price, description, image, category, stock_quantity FROM products WHERE product_id = $product_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Fetch recommendations from the same category
        $category = $product['category'];
        $recommendation_query = "SELECT product_id, name, price_description AS price, image FROM products WHERE category = '$category' AND product_id != $product_id LIMIT 3";
        $recommendation_result = $conn->query($recommendation_query);

        if ($recommendation_result->num_rows > 0) {
            while($row = $recommendation_result->fetch_assoc()) {
                $recommendations[] = $row;
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
    <title>The Meat Room - Product Details</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <link rel="stylesheet" href="https://use.typekit.net/rpj3trr.css">
    <style>
        /* Styles for the product details container */
        .product-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        /* Styling the product title */
        .product-container h2 {
            text-align: left;
            font-size: 36px;
            color: #c55b5b;
            margin-bottom: 30px;
        }

        /* Layout for product details */
        .product-details {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 20px;
        }

        /* Styling for the product image */
        .product-image {
            display: flex;
            max-width: 400px;
            height: auto;
            border-radius: 10px;
            transition: transform 0.3s ease-in-out;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Hover effect on product image */
        .product-image:hover {
            transform: scale(1.05);
        }

        /* Styling for the product information */
        .info {
            flex: 1;
            text-align: left;
            padding: 20px;
        }

        /* Text styling for the product information */
        .info p {
            font-size: 18px;
            line-height: 1.6;
            color: #ddd;
            margin: 10px 0;
        }

        /* Styling for the product price */
        .info p.price {
            font-size: 28px;
            font-weight: bold;
            color: #c55b5b;
            margin-bottom: 20px;
        }

        /* Styling for the stock status */
        .info p.stock-status {
            font-size: 20px;
            font-weight: bold;
            color: #c55b5b;
            margin-bottom: 20px;
        }

        /* Styling for the quantity input */
        .quantity-input {
            width: 60px;
            padding: 5px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            text-align: center;
            font-size: 16px;
        }

        /* Styling for the Add to Cart button */
        .add-to-cart-button {
            background-color: #c55b5b;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-to-cart-button:hover {
            background-color: #b44b4b;
        }

        /* Error message styling */
        .error-message {
            color: red;
            font-size: 16px;
            margin-top: 10px;
            text-align: center;
        }

        /* Confirmation message styling */
        .confirmation-message {
            background-color: #28a745;
            color: #fff;
            padding: 10px;
            text-align: center;
            margin-top: 20px;
            border-radius: 5px;
            display: none;
        }

        .show-confirmation {
            display: block;
        }

        /* Footer styles */
        .footer {
            background-color: #222;
            color: #b4b4b4;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            border-top: 1px solid #444;
        }

        .footer h2 {
            font-size: 20px;
            color: #c55b5b;
            margin-bottom: 20px;
        }

        /* Styling for the recommended products section */
        .recommendations {
            margin-top: 50px;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba
            (0, 0, 0, 0.3);
            text-align: center;
        }

        .recommendations h3 {
            font-size: 24px;
            color: #c55b5b;
            margin-bottom: 20px;
        }

        /* Layout for the recommended products, responsive for different screen sizes */
        .recommended-products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        /* Styling for each recommended product */
        .recommended-product {
            text-align: center;
        }

        /* Styling for the images of recommended products, ensuring uniform size */
        .recommended-product img {
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
        }

        /* Hover effect on recommended product images */
        .recommended-product img:hover {
            transform: scale(1.05);
        }

        /* Styling for the text below recommended product images */
        .recommended-product p {
            font-size: 16px;
            color: #ddd;
            margin-top: 10px;
        }

        /* Styling for the price text below recommended product images */
        .recommended-product .price {
            font-size: 14px;
            color: #c55b5b;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Top bar with the website name and tagline -->
    <div>
        <div class="top-bar">
            <h1>The Meat Room</h1>
            <h6>Premium butcher</h6>
        </div>
        
        <!-- Navigation bar -->
        <div class="nav">
            <!-- Link to home page with a logo -->
            <a href="home.php">
                <img class="logoimg" src="assets/imgs/LOGO.png" alt="Logo">
            </a>
            
            <!-- Hamburger icon for mobile navigation -->
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <!-- Navigation links with dropdowns for Shop and Account sections -->
            <ul>
                <li><a href="home.php">HOME</a></li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <!-- Dropdown for the Shop categories -->
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
                <!-- Dropdown for Account options, checking if the user is logged in -->
                <li class="dropdown">
                    <a href="#">ACCOUNT</a>
                    <ul class="dropdown-content">
                        <?php if (isset($_SESSION['user_id'])): ?> <!-- If the user is logged in, show Log out -->
                            <li><a href="log_out.php">Log out</a></li>
                        <?php else: ?> <!-- Otherwise, show Log in and Sign up options -->
                            <li><a href="login.php">Log in</a></li>
                            <li><a href="sign_up.php">Sign up</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <!-- Display the Cart dropdown if the user is logged in -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="dropdown">
                        <a href="cart.php">CART</a>
                        <ul class="dropdown-content">
                            <li><a href="order_history.php">Orders</a></li> <!-- Link to view order history -->
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- JavaScript to handle the hamburger menu click for mobile navigation -->
    <script>
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav ul').classList.toggle('active'); // Toggle active class to show/hide the menu
        });
    </script>
    
    <!-- Confirmation message displayed after adding an item to the cart -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="confirmation-message show-confirmation">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?> <!-- Display the success message and then clear it from the session -->
        </div>
    <?php endif; ?>

    <!-- Display error message if the product is already in the cart -->
    <?php if (isset($error_message)): ?>
        <div class="error-message">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Main product container for displaying the product details -->
    <div class="product-container">
        <?php if ($product): ?> <!-- Check if the product exists -->

            <!-- Product details section -->
            <div class="product-details">
                
                <!-- Product info (name, price, description, stock status) -->
                <div class="info">
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2> <!-- Product name -->
                    <p class="price"><?php echo htmlspecialchars($product['price']); ?></p> <!-- Product price -->
                    <p><?php echo htmlspecialchars($product['description']); ?></p> <!-- Product description -->
                    
                    <!-- Display stock availability -->
                    <p class="stock-status">
                        <?php echo $product['stock_quantity'] . " available - " . (($product['stock_quantity'] > 0) ? 'In Stock' : 'Out of Stock'); ?>
                    </p>
                    
                    <!-- If the product is in stock, show the Add to Cart form -->
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <form action="product.php?id=<?php echo $product_id; ?>" method="post">
                            <!-- Hidden input to pass the product ID -->
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <!-- Input for quantity selection, limited by the stock available -->
                            <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                            <!-- Submit button to add the product to the cart -->
                            <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Product image -->
                <div class="image">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>"> <!-- Product image with alt text for accessibility -->
                </div>
            </div>

        <?php else: ?> <!-- If the product is not found, display a message -->
            <p>Product not found.</p>
        <?php endif; ?>
    </div>
   
    <!-- Recommendations section for related products -->
    <div class="recommendations">
        <h3>You may also like:</h3>
        <div class="recommended-products">
            <?php foreach ($recommendations as $recommended): ?> <!-- Loop through recommended products -->
                <div class="recommended-product">
                    <!-- Each recommended product links to its own product page -->
                    <a href="product.php?id=<?php echo $recommended['product_id']; ?>">
                        <!-- Display the recommended product image -->
                        <img src="<?php echo htmlspecialchars($recommended['image']); ?>" alt="<?php echo htmlspecialchars($recommended['name']); ?>">
                        <!-- Display the recommended product name -->
                        <p><?php echo htmlspecialchars($recommended['name']); ?></p>
                        <!-- Display the recommended product price -->
                        <p class="price"><?php echo htmlspecialchars($recommended['price']); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer section with contact information -->
    <div class="footer">
        <h2>Contact Info:</h2>
        <p>Instagram - @Themeatroom</p>
        <p>Facebook - @Themeatroomnz</p>
        <p>Twitter - @Themeatroomnz</p>
        <p>Themeatroom@gmail.com | 0283969863 | 26 Anzac Road, Browns Bay</p>
    </div>
</body>
</html>

