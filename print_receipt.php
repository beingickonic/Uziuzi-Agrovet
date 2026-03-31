<?php
session_start();
include "connection.php";

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : "Customer";

// Fetch the most recent orders for this session/user
$query = "SELECT o.*, p.price, (o.quantity * p.price) as total_amount 
          FROM orders o 
          LEFT JOIN products p ON o.product_name = p.product_name ";

if($is_logged_in){
    $u_id = $_SESSION['user_id'];
    $query .= " WHERE o.user_id = $u_id ";
} else {
    // If guest, just get very recent orders (last 5 mins)
    $query .= " WHERE o.order_date > (NOW() - INTERVAL 5 MINUTE) ";
}
$query .= " ORDER BY o.order_date DESC LIMIT 10";
$items = $conn->query($query);
$receipt_id = date('Ymd').rand(100,999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Receipt - Uziuzi Agrovet</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; padding: 20px; color: #333; max-width: 400px; margin: auto; border: 1px solid #eee; }
        .header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 1.5rem; margin: 5px 0; color: #2e7d32; }
        .info-row { display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 0.9rem; }
        th { text-align: left; border-bottom: 1px solid #eee; padding: 10px 0; }
        td { padding: 10px 0; }
        .total-section { border-top: 2px dashed #ccc; padding-top: 15px; }
        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1rem; }
        .footer { text-align: center; margin-top: 30px; font-size: 0.75rem; color: #666; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <div class="header">
        <h1>UZIUZI AGROVET</h1>
        <p>Your Trusted Farm Partner</p>
        <p style="font-size: 0.8rem;">P.O Box 123, Kenya<br>Contact: +254 785 695 840</p>
    </div>

    <div class="info-row"><span>Date:</span> <span><?php echo date('d/m/Y H:i'); ?></span></div>
    <div class="info-row"><span>Receipt #:</span> <span>UZ-<?php echo $receipt_id; ?></span></div>
    <div class="info-row"><span>Customer:</span> <span><?php echo htmlspecialchars($user_name); ?></span></div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grand_total = 0;
            while($row = $items->fetch_assoc()): 
                $grand_total += $row['total_amount'];
            ?>
            <tr>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td style="text-align: right;"><?php echo number_format($row['total_amount'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>GRAND TOTAL</span>
            <span>KSH <?php echo number_format($grand_total, 2); ?></span>
        </div>
        <p style="font-size: 0.7rem; margin-top: 5px;">Payment Method: M-PESA/CASH</p>
    </div>

    <div class="footer">
        <p>*** Thank you for your business! ***</p>
        <p>Goods once sold are not returnable.<br>Sustainable Farming for a Better Future.</p>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2e7d32; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Receipt</button>
        <br><br>
        <a href="view.php" style="color: #666; text-decoration: none; font-size: 0.8rem;">← Back to Shop</a>
    </div>

    <script>window.onload = function() { setTimeout(() => { /* Auto-print option removed for UX focus */ }, 500); }</script>
</body>
</html>
