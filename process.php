<?php
session_start();
include "connection.php";

// 🛡️ Security Check: Ensure only logged-in admins can add or delete products
function check_admin() {
    if(!isset($_SESSION['admin'])){
        header("location:admin_login.php");
        exit();
    }
}

// 📦 ADD PRODUCT
if(isset($_POST['add'])){
    check_admin();

    $name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    // Ensure images directory exists
    if (!is_dir('images')) {
        mkdir('images', 0777, true);
    }

    move_uploaded_file($tmp, "images/".$image);

    $stmt = $conn->prepare("INSERT INTO products (product_name, category, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $name, $category, $price, $quantity, $image);
    $stmt->execute();
    $stmt->close();

    header("location:index.php");
}

// 🗑️ DELETE PRODUCT
if(isset($_GET['delete'])){
    check_admin();

    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("location:index.php");
}

// 📧 EMAIL NOTIFICATION (Foundation)
function send_admin_notification($customer, $product, $qty) {
    $to = "admin@uziuziagrovet.com"; // Change to actual admin email
    $subject = "🚨 New Order Received: " . $product;
    $message = "You have a new order!\n\n" . 
               "Customer: " . $customer . "\n" .
               "Product: " . $product . "\n" .
               "Quantity: " . $qty . "\n\n" .
               "Check the dashboard for details.";
    $headers = "From: no-reply@uziuziagrovet.com";

    // Note: mail() requires a configured SMTP server on your local machine
    @mail($to, $subject, $message, $headers);
}

// 🛒 PLACE ORDER
if(isset($_POST['order'])){
    $customer = $_POST['customer_name'];
    $phone = $_POST['phone'];
    $product = $_POST['product_name'];
    $qty = $_POST['quantity'];
    $payment = $_POST['payment_method'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, product_name, quantity, payment_method, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiis", $customer, $phone, $product, $qty, $payment, $user_id);
    
    if($stmt->execute()){
        send_admin_notification($customer, $product, $qty);
    }
    $stmt->close();

    // REMOVED Redundant admin insertion logic

    echo "<div style='font-family:Arial; text-align:center; padding:50px;'>";
    echo "<h2>Order Successful!</h2>";
    echo "<p>Please complete payment via <b>".$payment."</b></p>";
    echo "<hr width='300px'>";
    echo "<p><b>M-Pesa Details:</b><br>Paybill: 123456<br>Account: Your Phone Number</p>";
    echo "<a href='index.php'><button style='background:green; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;'>Back to Shop</button></a>";
    echo "</div>";
}
?>