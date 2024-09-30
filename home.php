<?php
session_start(); // Start the session to track user login status and other session data
?>

<!DOCTYPE html>
<html>
<head>
    <title>The Meat Room</title>
    <!-- Link to the external stylesheet for styling the webpage -->
    <link rel="stylesheet" href="css/stylesheet.css">
    <!-- Importing an external font library for enhanced typography -->
    <link rel="stylesheet" href="https://use.typekit.net/rpj3trr.css">
</head>
<body>
    <!-- Main wrapper for the header section -->
    <div>
        <!-- Top bar containing the title and description of the website -->
        <div class="top-bar">
            <h1>The Meat Room</h1> <!-- Website title -->
            <h6>Premium butcher</h6> <!-- Short description or tagline -->
        </div>
        <!-- Navigation bar section -->
        <div class="nav">
            <!-- Logo linking back to the homepage -->
            <a href="home.php">
                <img class="logoimg" src="assets/imgs/LOGO.png" alt="Logo"> <!-- Image of the logo -->
            </a>
            
            <!-- Hamburger icon for mobile navigation (only visible on small screens) -->
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span> <!-- Three lines representing the hamburger icon -->
            </div>
            
            <!-- Navigation menu links -->
            <ul>
                <li><a href="home.php">HOME</a></li> <!-- Home link -->
                &nbsp;&nbsp;&nbsp;&nbsp; <!-- Adds spacing between links -->
                <li class="dropdown">
                    <a href="shop.php">SHOP</a> <!-- Shop dropdown link -->
                    <!-- Dropdown menu items for different product categories -->
                    <ul class="dropdown-content">
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
                    <a href="#">ACCOUNT</a> <!-- Account dropdown link -->
                    <ul class="dropdown-content">
                        <!-- If the user is logged in, show "Log out" link -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="log_out.php">Log out</a></li>
                        <!-- If the user is not logged in, show "Log in" and "Sign up" links -->
                        <?php else: ?>
                            <li><a href="login.php">Log in</a></li>
                            <li><a href="sign_up.php">Sign up</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <!-- If the user is logged in, show the "Cart" and "Orders" links -->
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
    
    <!-- JavaScript to toggle the mobile navigation menu when the hamburger icon is clicked -->
    <script>
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav ul').classList.toggle('active'); // Adds/removes the 'active' class to show/hide the menu
        });
    </script>
    
    <!-- First large image with a description below -->
    <img class="cover" src="assets/imgs/rope-tied-salted-peppered-piece-meat-ready-smoke-wooden-table-herbs-spices-wooden.jpg">
    <div class="content">
        <!-- Section for the "About Us" information -->
        <h1 class="header1style">About us -</h1>
        <p>
            Shan Moulder, is a third generation butcher from South Africa. At the young age of 13, Shan already knew that he wanted to be a butcher and learn the great skills and specialized knowledge required. He started at a young age helping in his dad's shop (Alberton Meat Market which was established in 1979) on weekends and in school holidays, watching and learning from his dad and grandfather doing what they do best. Shan brings along with him his grandfather's boerewors and pork sausage recipe that originated in 1943, as well as making biltong the same way in which his grandfather taught him. Both of these family recipes are a huge part of the success of his dad's shop which is still going strong.
        </p>
    </div>
    
    <!-- Second large image with another content section below -->
    <img class="cover" src="assets/imgs/tattooed-butcher-hands-black-gloves-keep-knife-cut-slice-grilled-meat-wooden-board.jpg">
    <div class="content">
        <!-- Section for more information about Shan's background -->
        <h1 class="header1style">Info -</h1>
        <p>
            Shan had 13 years of butchery experience before coming to NZ in 2010, where he started his career at NOSH food market. Working his way up to butcher manager and managing 5 of the NOSH butchery departments. In October 2014 Shan embarked on the journey of opening his own shop which has been running ever since and proud to share THE MEAT ROOM with you. Shan takes pride in what he does and is always willing to share his knowledge and passion of good quality meat and a few cooking tips. Shan and the team are all about; tradition, passion and quality and can't wait to build more relationships with customers and provide them with the best quality and service that he and the team can.
        </p>
    </div>
    
    <!-- Third large image -->
    <img class="cover" src="assets/imgs/grilled-sirloin-marbled-perfection-juicy-aromatic-generated-by-ai.jpg">
    
    <!-- Footer section containing contact information -->
    <div class="footer">
        <h1>Contact Info:</h1>
        <p>Instagram - @Themeatroom</p>
        <p>Facebook - @Themeatroomnz</p>
        <p>Twitter - @Themeatroomnz</p>
        <p>Themeatroom@gmail.com | 0283969863 | 26 Anzac Road, Browns Bay</p> <!-- Contact details for The Meat Room -->
    </div>
</body>
</html>
