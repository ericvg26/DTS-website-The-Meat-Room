<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Meat Room</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.typekit.net/rpj3trr.css">
    <link rel="stylesheet" href='css/contactStyle.css'>
    
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
        <script>
            document.querySelector('.hamburger').addEventListener('click', function() {
                document.querySelector('.nav ul').classList.toggle('active');
            });
        </script>
    
    <div class="content">
        <!-- Top Section: Image -->
        <div class="top-image"></div>

        <!-- Bottom Left Section: Google Map -->
        <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1599.1128967797729!2d174.7463746!3d-36.7171381!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6d0d3a5b82fe1e55%3A0x3891d055e7e7f55f!2sThe%20Meat%20Room!5e0!3m2!1sen!2snz!4v1724579313528!5m2!1sen!2snz" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <!-- Bottom Middle Section: Contact Information -->
        <div class="contact-info">
            <h2>Contact</h2>
            <p>Phone - 09 973 1989</p>
            <p>Email - themeatroom@outlook.nz</p>
            <p>Facebook - @Themeatroomnz</p>
            <p>We are located at 26 Anzac Road, Browns Bay</p>
        </div>

        <!-- Bottom Right Section: Opening Hours -->
        <div class="opening-hours">
            <h2>Opening Hours</h2>
            <p>Monday-Thursday & Saturday: 7am - 5.30pm</p>
            <p>Friday: 7am - 6pm</p>
            <p>Sunday: 7am - 4pm</p>
            <p>Please contact us if you have any questions or would like to place an order. Check our Facebook page for any updates.</p>
        </div>
    </div>
</body>
<footer>
    <!-- Footer content here -->
</footer>
</html>
