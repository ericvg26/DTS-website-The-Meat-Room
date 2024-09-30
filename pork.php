<?php
session_start();
print_r($_SESSION);  // This will print all session variables

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

// Fetch pork products only
$query = "SELECT product_id, name, price_description, image FROM products WHERE category = 'pork'";
$result = $conn->query($query);

$products = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[$row['product_id']] = [
            'name' => $row['name'],
            'price_description' => $row['price_description'],
            'image' => $row['image']
        ];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Meat Room - Pork</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <link rel="stylesheet" href="https://use.typekit.net/rpj3trr.css">
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
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav ul').classList.toggle('active');
        });
    </script>

    <div>
        <div class="product-section">
            <h1>PORK PRODUCTS</h1>
            <table style="padding-bottom:15px;padding-top:40px;padding-bottom:70px;" id="pork-products">
                <?php foreach ($products as $id => $product): ?>
                    <?php if ($id % 3 === 1): ?>
                        <tr>
                    <?php endif; ?>
                    
                    <td class="tablecontent">
                        <a href="product.php?id=<?php echo $id; ?>">
                            <img class="productimg" src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </a>
                        <div class="tabletext1 tablecontent"><?php echo $product['name']; ?></div>
                        <div class="tabletext2 tablecontent"><?php echo $product['price_description']; ?></div>
                    </td>
                    
                    <?php if ($id % 3 === 0): ?>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="footer">
        <h2>Contact Info:</h2>
        <p>Instagram - @Themeatroom</p>
        <p>FaceBook - @Themeatroomnz</p>
        <p>Twitter - @Themeatroomnz</p>
        <p>Themeatroom@gmail.com | 0283969863 | 26 Anzac Road, Browns Bay</p>
    </div>
</body>
</html>
