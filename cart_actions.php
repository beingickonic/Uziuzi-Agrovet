<?php
session_start();

// 🛒 CART INITIALIZATION
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

// 📦 ADD TO CART
if(isset($_POST['add_to_cart'])){
    $p_id = $_POST['p_id'];
    $p_name = $_POST['p_name'];
    $p_price = $_POST['p_price'];
    $p_image = $_POST['p_image'];
    $qty = (int)$_POST['qty'];

    // Check if already in cart
    if(isset($_SESSION['cart'][$p_id])){
        $_SESSION['cart'][$p_id]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$p_id] = [
            'name' => $p_name,
            'price' => $p_price,
            'image' => $p_image,
            'qty' => $qty
        ];
    }
    
    // Quick redirect back to gallery
    header("Location: view.php?added=" . urlencode($p_name));
    exit();
}

// 🗑️ REMOVE FROM CART
if(isset($_GET['remove'])){
    $rid = $_GET['remove'];
    unset($_SESSION['cart'][$rid]);
    header("Location: checkout.php");
    exit();
}

// 🧹 CLEAR CART
if(isset($_GET['clear'])){
    $_SESSION['cart'] = [];
    header("Location: view.php");
    exit();
}
?>
