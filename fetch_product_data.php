<?php
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "meat room data base"; // Make sure this matches your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all products including the image source
$query = "SELECT product_id, name, price_description, image FROM products";
$result = $conn->query($query);

$products = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[$row['product_id']] = [
            'name' => $row['name'],
            'price_description' => $row['price_description'],
            'image' => $row['image'] // Add the image source to the array
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($products);
$conn->close();
?>
