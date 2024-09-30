<?php
session_start(); // Start the session to track logged-in users

// Database connection details
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "meat room data base"; 

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Display error message if the connection fails
}

// Ensure the user is logged in by checking if the user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if user is not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Retrieve the logged-in user's ID from the session
$order_placed = false; // Flag to indicate if the order has been placed

// Define the time window for combining orders (e.g., 10 minutes)
$time_window = 10 * 60; // 10 minutes converted to seconds

// Handle form submission when the user confirms the order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    // Retrieve and sanitize special instructions and email input from the user
    $special_instructions = $conn->real_escape_string($_POST['special_instructions']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Calculate the total price by summing up the price * quantity for each item in the cart
    $cart_query = "
        SELECT SUM(p.price * c.quantity) AS total
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = $user_id
    ";
    $cart_result = $conn->query($cart_query);
    $cart_data = $cart_result->fetch_assoc();
    $total_price = $cart_data['total'];
    
    // Check if the user has placed another order within the time window
    $order_query = "
        SELECT order_id, total_price
        FROM orders
        WHERE user_id = $user_id
        AND TIMESTAMPDIFF(SECOND, order_date, NOW()) <= $time_window
        ORDER BY order_date DESC
        LIMIT 1
    ";
    $order_result = $conn->query($order_query);
    
    // If there is an existing order within the time window, combine the current order with it
    if ($order_result->num_rows > 0) {
        $existing_order = $order_result->fetch_assoc();
        $order_id = $existing_order['order_id'];
        $total_price += $existing_order['total_price']; // Add the new total to the existing order total
        
        // Update the total price and other order details of the existing order
        $update_order_query = "
            UPDATE orders
            SET total_price = $total_price, special_instructions = '$special_instructions', email = '$email'
            WHERE order_id = $order_id
        ";
        $conn->query($update_order_query);
    } else {
        // If no recent order exists, create a new order
        $insert_order_query = "
            INSERT INTO orders (user_id, total_price, special_instructions, email)
            VALUES ($user_id, $total_price, '$special_instructions', '$email')
        ";
        $conn->query($insert_order_query);
        $order_id = $conn->insert_id; // Get the ID of the newly created order
    }
    
    // Insert or update the products in the order_items table based on the current cart
    $cart_items_query = "
        SELECT c.product_id, c.quantity, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = $user_id
    ";
    $cart_items_result = $conn->query($cart_items_query);
    
    // Loop through each product in the cart and update or insert it into the order_items table
    while ($item = $cart_items_result->fetch_assoc()) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        
        // Insert or update the order items, combining quantities if they already exist
        $order_item_query = "
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES ($order_id, $product_id, $quantity, $price)
            ON DUPLICATE KEY UPDATE quantity = quantity + $quantity, price = $price
        ";
        $conn->query($order_item_query);
        
        // Reduce the stock quantity for each ordered product in the products table
        $update_stock_query = "
            UPDATE products
            SET stock_quantity = stock_quantity - $quantity
            WHERE product_id = $product_id
        ";
        $conn->query($update_stock_query);
    }
    
    // Clear the user's cart after placing the order
    $clear_cart_query = "DELETE FROM cart WHERE user_id = $user_id";
    $conn->query($clear_cart_query);

    // Set the order placed flag to true to show the confirmation message
    $order_placed = true;
}

// Fetch all items in the cart for display in the checkout page
$cart_query = "
    SELECT c.product_id, p.name, p.price AS price, c.quantity, (p.price * c.quantity) AS subtotal, p.image
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = $user_id
";
$cart_result = $conn->query($cart_query);

$cart_items = []; // Initialize an array to hold cart items
$total = 0; // Initialize total price

// If there are items in the cart, populate the cart items array and calculate the total price
if ($cart_result->num_rows > 0) {
    while ($row = $cart_result->fetch_assoc()) {
        $cart_items[] = $row;
        $total += $row['subtotal']; // Add subtotal of each item to the total
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
    <title>Checkout</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <link rel="stylesheet" href="https://use.typekit.net/rpj3trr.css">
    <style>
        /* Checkout container styling */
        .checkout-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .checkout-container h2 {
            text-align: center;
            font-size: 36px;
            color: #c55b5b;
            margin-bottom: 30px;
        }

        /* Cart item table styling */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cart-table th, .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .cart-table th {
            background-color: #444;
            color: #fff;
        }

        .cart-table td {
            background-color: #333;
            color: #ddd;
        }

        .cart-table img {
            max-width: 100px;
            height: auto;
            border-radius: 10px;
        }

        /* Special instructions styling */
        .special-instructions {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            color: #333;
        }

        /* Email input styling */
        .email-input {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            color: #333;
        }

        /* Confirm order button */
        .confirm-order-button {
            background-color: #c55b5b;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin-top: 20px;
            display: block;
            width: 100%;
            text-align: center;
        }

        .confirm-order-button:hover {
            background-color: #b44b4b;
        }

        /* Total styling */
        .total-price {
            font-size: 24px;
            font-weight: bold;
            color: #c55b5b;
            text-align: right;
            margin-top: 20px;
        }

        /* Success message */
        .success-message {
            text-align: center;
            font-size: 24px;
            color: #4CAF50;
            margin-top: 20px;
        }
    </style>
</head>

<body>
<div>
    <div class="top-bar">
        <!-- Website header with the store name and tagline -->
        <h1>The Meat Room</h1>
        <h6>Premium butcher</h6>
    </div>

    <!-- Navigation bar -->
    <div class="nav">
        <!-- Logo linking to the home page -->
        <a href="home.php">
            <img class="logoimg" src="assets/imgs/LOGO.png" alt="Logo"> <!-- Image logo for the brand -->
        </a>
        
        <!-- Hamburger Icon for Mobile Navigation -->
        <!-- Hamburger menu used for responsive mobile view -->
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <!-- Navigation links with dropdowns for shop and account options -->
        <ul>
            <!-- Home link -->
            <li><a href="home.php">HOME</a></li>
            &nbsp;&nbsp;&nbsp;&nbsp; <!-- Adds space between navigation items -->
            
            <!-- Shop dropdown menu with categories -->
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

            <!-- Contact link -->
            <li><a href="contact.php">CONTACT</a></li>
            &nbsp;&nbsp;&nbsp;&nbsp;

            <!-- Account dropdown for login, signup, or logout depending on session status -->
            <li class="dropdown">
                <a href="#">ACCOUNT</a>
                <ul class="dropdown-content">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- If user is logged in, show log out option -->
                        <li><a href="log_out.php">Log out</a></li>
                    <?php else: ?>
                        <!-- If user is not logged in, show login and sign up options -->
                        <li><a href="login.php">Log in</a></li>
                        <li><a href="sign_up.php">Sign up</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            &nbsp;&nbsp;&nbsp;&nbsp;

            <!-- Cart dropdown (only shown if user is logged in) -->
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

    <!-- Checkout section -->
    <div class="checkout-container">
        <!-- Heading for the checkout section -->
        <h2>Checkout</h2>
        
        <!-- Success message shown if order is placed -->
        <?php if ($order_placed): ?>
            <div class="success-message">
                <!-- Confirmation message to the user -->
                Thank you! Your order has been placed successfully. We will email you shortly. 
            </div>
        <?php else: ?>
            <!-- Cart table showing the products in the cart, quantity, price, and subtotal -->
            <table class="cart-table">
                <thead>
                    <tr>
                        <!-- Table headers for product details -->
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop through cart items to display each product, its quantity, price, and subtotal -->
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <!-- Display product image and name -->
                            <td><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"> <?php echo htmlspecialchars($item['name']); ?></td>
                            <!-- Display product quantity -->
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <!-- Display product price -->
                            <td><?php echo htmlspecialchars($item['price']); ?></td>
                            <!-- Display product subtotal -->
                            <td><?php echo htmlspecialchars($item['subtotal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Display the total price of all cart items -->
            <div class="total-price">
                Subtotal: NZD $<?php echo htmlspecialchars($total); ?>
            </div>
            
            <!-- Form to confirm the order and submit special instructions -->
            <form action="checkout.php" method="post" onsubmit="return confirm('Are you sure you want to place this order?');">
                <!-- Textarea for any special instructions for the order -->
                <textarea name="special_instructions" class="special-instructions" placeholder="Special instructions for the seller (Please fill in duplicate orders)"></textarea>
                <!-- Input field for user's email (required) -->
                <input type="email" name="email" class="email-input" placeholder="Enter your email" required>
                <!-- Button to confirm and place the order -->
                <button type="submit" name="confirm_order" class="confirm-order-button">Confirm Order</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
