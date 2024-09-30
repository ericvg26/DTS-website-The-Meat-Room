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

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$response = [];

if ($product_id > 0) {
    // Fetch the specific product details
    $query = "SELECT name, price_description AS price, description, image, category FROM products WHERE product_id = $product_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $response['product'] = $product;

        $category = $product['category'];

        // Fetch recommendations from the same category
        $recommendation_query = "SELECT product_id, name, price_description AS price, image FROM products WHERE category = '$category' AND product_id != $product_id LIMIT 3";
        $recommendation_result = $conn->query($recommendation_query);

        $recommendations = [];
        if ($recommendation_result->num_rows > 0) {
            while($row = $recommendation_result->fetch_assoc()) {
                $recommendations[] = $row;
            }
        }
        $response['recommendations'] = $recommendations;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>
