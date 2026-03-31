<?php
session_start();
include "connection.php";
include "mpesa.php"; // Include M-Pesa handler

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : "";
$cart = $_SESSION['cart'] ?? [];

$total_bill = 0;
foreach($cart as $item) $total_bill += ($item['price'] * $item['qty']);

$payment_status = "idle"; // idle, pending, success

if(isset($_POST['place_order'])){
    $customer = $_POST['customer_name'];
    $phone = $_POST['phone'];
    $payment = $_POST['payment_method'];
    $user_id = $is_logged_in ? $_SESSION['user_id'] : null;

    // 🚀 MPESA STK PUSH LOGIC
    if($payment == 'M-Pesa Online'){
        $mpesa = new MpesaHandler();
        // Since we are in SANDBOX/DEMO, we'll simulate the successful push sent
        $payment_status = "pending";
        $_SESSION['pending_order'] = $_POST;
    } else {
        // Standard non-mpesa checkout
        processOrder($conn, $cart, $customer, $phone, $payment, $user_id);
    }
}

// 🛒 Order Processor Helper
function processOrder($conn, $cart, $customer, $phone, $payment, $user_id) {
    foreach($cart as $id => $item){
        $p_name = $item['name'];
        $qty = $item['qty'];
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, product_name, quantity, payment_method, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiis", $customer, $phone, $p_name, $qty, $payment, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Log Activity (Safely)
    $log_stmt = $conn->prepare("INSERT INTO activity_log (user_name, activity_type, activity_detail) VALUES (?, 'Sale', ?)");
    if($log_stmt){
        $detail = "Completed purchase via " . $payment;
        $log_stmt->bind_param("ss", $customer, $detail);
        $log_stmt->execute();
        $log_stmt->close();
    }

    $_SESSION['cart'] = [];
    header("Location: success.php");
    exit();
}

// ⚡ Handle Simulated Completion (for demo purposes)
if(isset($_GET['complete_mpesa'])){
    $data = $_SESSION['pending_order'];
    processOrder($conn, $cart, $data['customer_name'], $data['phone'], 'M-Pesa (Paid)', $is_logged_in ? $_SESSION['user_id'] : null);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Uziuzi Agrovet</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2e7d32;
            --primary-light: #4caf50;
            --bg: #f8fafc;
            --white: #ffffff;
            --shadow: 0 10px 40px rgba(0,0,0,0.06);
            --border: #e2e8f0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            margin: 0;
            color: #334155;
            padding-top: 2rem;
        }

        /* 📱 M-Pesa Waiting Overlay */
        .mpesa-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8); backdrop-filter: blur(8px);
            z-index: 5000; display: flex; align-items: center; justify-content: center;
        }

        .mpesa-card {
            background: var(--white); padding: 3rem; border-radius: 30px;
            text-align: center; max-width: 400px; width: 90%;
        }

        .spinner {
            width: 50px; height: 50px; border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary); border-radius: 50%;
            animation: spin 1s linear infinite; margin: 0 auto 2rem;
        }

        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1.5rem 5rem;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 768px) { .container { grid-template-columns: 1fr; } }

        .card {
            background: var(--white);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        h2 { margin-top: 0; color: #1e293b; font-size: 1.5rem; }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .cart-item:last-child { border-bottom: none; }

        .item-img { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; }

        .item-info { flex-grow: 1; }
        .item-info b { display: block; font-size: 1rem; }
        .item-info span { font-size: 0.85rem; color: #64748b; }

        .btn-remove { color: #ef4444; text-decoration: none; font-size: 1.25rem; font-weight: 700; padding: 0.5rem; }

        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; }
        .form-group input, .form-group select {
            width: 100%; padding: 0.8rem; border: 1.5px solid var(--border); border-radius: 12px; font-family: inherit; box-sizing: border-box;
        }

        .summary-row { display: flex; justify-content: space-between; margin-bottom: 1rem; }
        .total-row { border-top: 2px solid #f1f5f9; padding-top: 1.5rem; margin-top: 1.5rem; font-size: 1.25rem; font-weight: 700; color: var(--primary); }

        .btn-pay {
            width: 100%; padding: 1.2rem; background: var(--primary); color: white; border: none; border-radius: 16px; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: all 0.3s; margin-top: 1.5rem;
        }

        .btn-pay:hover { background: var(--primary-light); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(46, 125, 50, 0.2); }

        .back-link { text-decoration: none; color: var(--primary); font-weight: 600; display: inline-block; margin-bottom: 1.5rem; }
    </style>
</head>
<body>

<?php if($payment_status == "pending"): ?>
<div class="mpesa-overlay">
    <div class="mpesa-card">
        <div class="spinner"></div>
        <h3 style="color:var(--primary);">🚀 M-Pesa Prompt Sent!</h3>
        <p>Please check your phone (<b><?php echo $_POST['phone']; ?></b>) and enter your M-Pesa PIN to complete the <b>Ksh <?php echo number_format($total_bill, 2); ?></b> payment.</p>
        <p style="font-size:0.8rem; color:#94a3b8;">Waiting for Safaricom confirmation...</p>
        <button onclick="window.location.href='?complete_mpesa=1'" class="btn-pay" style="padding:0.75rem; font-size:0.9rem; background:#1e293b;">Click here after you enter PIN (Demo)</button>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <div>
        <a href="view.php" class="back-link">← Continue Shopping</a>
        <div class="card">
            <h2>🛒 Your Shopping Cart</h2>
            <?php if(empty($cart)): ?>
                <p style="text-align:center; padding: 3rem; color: #94a3b8;">Your cart is empty. <a href="view.php">Go back to shop.</a></p>
            <?php else: ?>
                <?php foreach($cart as $id => $item): ?>
                <div class="cart-item">
                    <img src="images/<?php echo $item['image']; ?>" class="item-img" onerror="this.src='https://via.placeholder.com/60/eeeeee?text=Item'">
                    <div class="item-info">
                        <b><?php echo $item['name']; ?></b>
                        <span>Ksh <?php echo number_format($item['price'], 2); ?> × <?php echo $item['qty']; ?></span>
                    </div>
                    <div style="font-weight: 600;">Ksh <?php echo number_format($item['price'] * $item['qty'], 2); ?></div>
                    <a href="cart_actions.php?remove=<?php echo $id; ?>" class="btn-remove">&times;</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <div class="card" style="position: sticky; top: 2rem;">
            <h2>🧾 Order Summary</h2>
            <div class="summary-row">
                <span>Subtotal</span>
                <b>Ksh <?php echo number_format($total_bill, 2); ?></b>
            </div>
            <div class="summary-row">
                <span>Tax (VAT 0%)</span>
                <b>Ksh 0.00</b>
            </div>
            <div class="summary-row">
                <span>Delivery</span>
                <b style="color: #16a34a;">FREE</b>
            </div>
            <div class="summary-row total-row">
                <span>Grand Total</span>
                <span>Ksh <?php echo number_format($total_bill, 2); ?></span>
            </div>

            <?php if(!empty($cart)): ?>
            <form method="POST" style="margin-top: 2rem;">
                <div class="form-group">
                    <label>Full Delivery Name</label>
                    <input type="text" name="customer_name" required placeholder="Who is receiving?" value="<?php echo $user_name; ?>">
                </div>
                <div class="form-group">
                    <label>M-Pesa / Phone Number</label>
                    <input type="text" name="phone" required placeholder="07XXXXXXXX" value="<?php echo $_POST['phone'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Settlement Method</label>
                    <select name="payment_method">
                        <option>M-Pesa Online</option>
                        <option>Cash on Collection</option>
                    </select>
                </div>
                <button type="submit" name="place_order" class="btn-pay">Complete Purchase & Pay</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
