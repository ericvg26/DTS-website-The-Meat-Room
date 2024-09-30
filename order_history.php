<?php
session_start(); // Start the session to access session variables like the user's logged-in status

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "meat room data base"; 

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // If the connection fails, output the error and stop the script
}

// Ensure the user is logged in by checking if the session contains the 'user_id'
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if the user is not logged in
    exit(); // Stop further script execution
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID from the session

// Query to fetch all orders made by the logged-in user, along with details of the products in those orders
$order_query = "
    SELECT o.order_id, o.total_price, o.order_date, oi.product_id, oi.quantity, p.name, p.image
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.user_id = $user_id
    ORDER BY o.order_date DESC
"; // This query retrieves the user's orders, including product details such as name, image, and quantity ordered

$order_result = $conn->query($order_query); // Execute the query

$orders = []; // Initialize an empty array to hold the orders

// If there are results from the query (meaning the user has made orders)
if ($order_result->num_rows > 0) {
    // Loop through the result set to organize the data
    while ($row = $order_result->fetch_assoc()) {
        $orders[$row['order_id']]['details'][] = $row; // Store the product details under the respective order
        $orders[$row['order_id']]['total_price'] = $row['total_price']; // Store the total price of the order
        $orders[$row['order_id']]['order_date'] = $row['order_date']; // Store the order date
    }
}

$conn->close(); // Close the database connection after fetching the data
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <link rel="stylesheet" href="https://use.typekit.net/rpj3trr.css">
    <style>
        /* Styling for the order history container */
        .order-history-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .order-history-container h2 {
            text-align: center;
            font-size: 36px;
            color: #c55b5b;
            margin-bottom: 30px;
        }

        /* Styling for each individual order */
        .order {
            background-color: #333;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .order h3 {
            color: #c55b5b;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .order p {
            color: #ddd;
            font-size: 18px;
        }

        /* Styling for the product list within each order */
        .product-list {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #444;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .product-item img {
            width: 100px;
            height: auto;
            border-radius: 10px;
            margin-right: 20px;
        }

        .product-info {
            flex-grow: 1;
        }

        .product-info h4 {
            color: #fff;
            font-size: 20px;
            margin: 0;
        }

        .product-info p {
            color: #ddd;
            margin: 5px 0;
        }

        .product-info span {
            font-weight: bold;
            color: #c55b5b;
        }

        .order-total {
            text-align: right;
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Page Header -->
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
            
            <!-- Navigation Menu -->
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
    
    <script>
        // JavaScript to toggle the navigation menu on smaller screens
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav ul').classList.toggle('active');
        });
    </script>

    <!-- Order History Container -->
    <div class="order-history-container">
        <h2>Your Orders</h2>

        <!-- Check if there are any orders to display -->
        <?php if (!empty($orders)): ?>
            <!-- Loop through each order and display its details -->
            <?php foreach ($orders as $order_id => $order): ?>
                <div class="order">
                    <h3>Order #<?php echo $order_id; ?> - Placed on <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></h3>
                    <div class="product-list">
                        <!-- Loop through each product in the order -->
                        <?php foreach ($order['details'] as $item): ?>
                            <div class="product-item">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="product-info">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Quantity: <span><?php echo $item['quantity']; ?></span></p>
                                    <p>Price: <span>$<?php echo number_format($item['quantity'] * $item['total_price'] / $item['quantity'], 2); ?></span></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Display the total price of the order -->
                    <p class="order-total">Total Price: $<?php echo number_format($order['total_price'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Message displayed if no orders are found -->
            <p>You have no previous orders.</p>
        <?php endif; ?>
    </div>
</body>
</html>
