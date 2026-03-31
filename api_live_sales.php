<?php
include "connection.php";

$query = "SELECT o.*, p.price, (o.quantity * p.price) as total_amount 
          FROM orders o 
          LEFT JOIN products p ON o.product_name = p.product_name 
          ORDER BY o.order_date DESC LIMIT 50";
$sales = $conn->query($query);

if($sales->num_rows > 0):
    while($row = $sales->fetch_assoc()): ?>
    <tr style="border-bottom: 1px solid #f1f5f9;">
        <td style="padding: 1.25rem; font-weight: 700; color: #cbd5e1;">#<?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?></td>
        <td style="padding: 1.25rem;">
            <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['customer_name']); ?></div>
            <div style="font-size: 0.75rem; color: #64748b;"><?php echo htmlspecialchars($row['phone']); ?></div>
        </td>
        <td style="padding: 1.25rem;">
            <span style="font-weight: 500; font-size: 0.85rem;"><?php echo htmlspecialchars($row['product_name']); ?></span>
        </td>
        <td style="padding: 1.25rem; font-weight: 600;">× <?php echo $row['quantity']; ?></td>
        <td style="padding: 1.25rem; font-weight: 700; color: #2e7d32;">Ksh <?php echo number_format($row['total_amount'], 2); ?></td>
        <td style="padding: 1.25rem;">
            <span style="padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.7rem; font-weight: 700; background: #f0fdf4; color: #166534; text-transform: uppercase;">
                <?php echo $row['payment_method']; ?>
            </span>
        </td>
        <td style="padding: 1.25rem; color: #94a3b8; font-size: 0.85rem;">
            <?php echo date('M d, Y | H:i', strtotime($row['order_date'])); ?>
        </td>
    </tr>
    <?php 
    endwhile; 
else: ?>
    <tr><td colspan="7" style="padding: 4rem; text-align: center; color: #94a3b8;">No sale records found.</td></tr>
<?php endif; ?>
