<?php
session_start(); // Start the session to track user-specific data, such as user_id

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "meat room data base"; 

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // End the script if connection fails
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if the user is not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
$error_messages = []; // Array to store any error messages related to stock or updates

// Handle cart updates (e.g., quantity changes)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle cart updates
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            $quantity = intval($quantity); // Convert the quantity to an integer
            
            // Fetch the current stock for the product to ensure the requested quantity is valid
            $stock_query = "SELECT stock_quantity FROM products WHERE product_id = $product_id";
            $stock_result = $conn->query($stock_query);
            $stock_data = $stock_result->fetch_assoc();

            if ($quantity > 0) {
                // If the requested quantity exceeds the available stock, set the quantity to the available stock
                if ($quantity > $stock_data['stock_quantity']) {
                    $quantity = $stock_data['stock_quantity'];
                    $error_messages[$product_id] = "Requested quantity for product exceeds available stock. Quantity reverted to maximum available: $quantity."; // Add error message
                }
                
                // Update the cart with the new quantity for the product
                $update_query = "UPDATE cart SET quantity = $quantity WHERE user_id = $user_id AND product_id = $product_id";
                $conn->query($update_query);
            } else {
                // If the quantity is 0, remove the product from the cart
                $delete_query = "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id";
                $conn->query($delete_query);
            }
        }
    }

    // Handle item removal
    if (isset($_POST['remove_item'])) {
        $product_id = intval($_POST['remove_item']); // Get the product_id to be removed
        $delete_query = "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id"; // Delete the item from the cart
        $conn->query($delete_query); // Execute the delete query
    }
    
}

// Fetch the items in the user's cart
$cart_query = "
    SELECT c.product_id, p.name, p.price_description AS price, c.quantity, (p.price * c.quantity) AS subtotal, p.image
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = $user_id
";
$cart_result = $conn->query($cart_query);

$cart_items = []; // Array to store cart items
$total = 0; // Initialize the total price

if ($cart_result->num_rows > 0) {
    // Loop through each cart item and calculate the subtotal for each product
    while ($row = $cart_result->fetch_assoc()) {
        $cart_items[] = $row; // Add the product to the cart items array
        $total += $row['subtotal']; // Add the subtotal of each product to the total price
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <link rel="stylesheet" href="https://use.typekit.net/rpj3trr.css">
    <style>
        /* Styling for the cart page container */
        .cart-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        /* Cart title styling */
        .cart-container h2 {
            text-align: center;
            font-size: 36px;
            color: #c55b5b;
            margin-bottom: 30px;
        }

        /* Table styling for the cart items */
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

        /* Product image styling */
        .cart-table img {
            max-width: 100px;
            height: auto;
            border-radius: 10px;
        }

        /* Styling for the cart action buttons (update and checkout) */
        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .update-cart-button, .checkout-button {
            background-color: #c55b5b;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .update-cart-button:hover, .checkout-button:hover {
            background-color: #b44b4b;
        }

        /* Remove button styling for each cart item */
        .remove-button {
            background-color: #b44b5b;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .remove-button:hover {
            background-color: #a33a3a;
        }

        /* Styling for error messages */
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        p{
            text-align: center;
        }

        h3{
            color: #fff
        }

    </style>
</head>
<body>
    <!-- Navigation bar and logo -->
    <div>
        <div class="top-bar">
            <h1>The Meat Room</h1>
            <h6>Premium butcher</h6>
        </div>
        <div class="nav">
            <a href="home.php">
                <img class="logoimg" src="assets/imgs/LOGO.png" alt="Logo">
            </a>
            
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
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="dropdown">
                        <a href="cart.php">CART</a>
                        <ul class="dropdown-content">
                            <li><a href="order_history.php">Orders</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Mobile navigation menu toggle -->
    <script>
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav ul').classList.toggle('active'); // Toggle the visibility of the navigation menu for mobile
        });
    </script>

    <!-- Cart container displaying all items in the user's cart -->
    <div class="cart-container">
        <h2>Your Cart</h2>
        <?php if (!empty($cart_items)): ?> <!-- If there are items in the cart, display them -->
            <form action="cart.php" method="post">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?> <!-- Loop through each item in the cart -->
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"> <!-- Product image -->
                                    <?php echo htmlspecialchars($item['name']); ?> <!-- Product name -->
                                    <?php if (isset($error_messages[$item['product_id']])): ?> <!-- Display error message if any -->
                                        <div class="error-message"><?php echo $error_messages[$item['product_id']]; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input type="number" name="quantities[<?php echo $item['product_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="99" class="quantity-input"> <!-- Quantity input field -->
                                </td>
                                <td><?php echo htmlspecialchars($item['price']); ?></td> <!-- Display the product price -->
                                <td><?php echo htmlspecialchars($item['subtotal']); ?></td> <!-- Display the subtotal for the product -->
                                <td>
                                    <button type="submit" name="remove_item" value="<?php echo $item['product_id']; ?>" class="remove-button">Remove</button> <!-- Button to remove item from cart -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Cart actions: update cart or proceed to checkout -->
                <div class="cart-actions">
                    <button type="submit" name="update_cart" class="update-cart-button">Update Cart</button> <!-- Update cart button -->
                    <h3>Total: $<?php echo htmlspecialchars($total); ?></h3> <!-- Display total cart value -->
                    <a href="checkout.php" class="checkout-button">Proceed to Checkout</a> <!-- Proceed to checkout button -->
                </div>
            </form>
        <?php else: ?> <!-- If the cart is empty, display a message -->
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>
