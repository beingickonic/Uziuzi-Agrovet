<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "uziuzi-Agrovet";

// Create connection
$conn = new mysqli($host, $user, $pass, $db_name);

// Check connection
if($conn->connect_error){
    die("🚨 Connection Failed: " . $conn->connect_error);
}
?>
