<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("location:login.php");
    exit();
}

include "connection.php";

// Fetch counts for dashboard
$products_count = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$orders_count = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];

// Calculate Revenue Analytics
$revenue_query = $conn->query("SELECT SUM(o.quantity * p.price) as total_revenue 
                               FROM orders o 
                               JOIN products p ON o.product_name = p.product_name");
$revenue = $revenue_query->fetch_assoc()['total_revenue'] ?? 0;

$top_product_query = $conn->query("SELECT product_name, COUNT(*) as count 
                                   FROM orders GROUP BY product_name 
                                   ORDER BY count DESC LIMIT 1");
$top_product = $top_product_query->fetch_assoc()['product_name'] ?? "No data";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Uziuzi Agrovet</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2e7d32;
            --primary-light: #4caf50;
            --secondary: #1e293b;
            --accent: #81c784;
            --bg: #f8fafc;
            --text: #1b5e20;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0,0,0,0.06);
            --border: #e2e8f0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            margin: 0;
            color: #334155;
        }

        header {
            background: var(--primary);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-actions {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .btn-view-shop {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            opacity: 0.9;
        }

        .btn-view-shop:hover { opacity: 1; text-decoration: underline; }

        header h1 { margin: 0; font-size: 1.5rem; font-weight: 600; }

        .btn-logout {
            background: rgba(255,255,255,0.15);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .btn-logout:hover { background: rgba(255,255,255,0.25); }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 4px; height: 100%;
            background: var(--primary);
        }

        .stat-info span {
            display: block;
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .stat-info b {
            font-size: 1.5rem;
            color: var(--secondary);
        }

        .revenue-card::after { background: #f59e0b; }
        .revenue-card b { color: #f59e0b; }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 2.5rem 0 1.25rem;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 3rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .form-group { margin-bottom: 1.5rem; }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.6rem;
            color: #475569;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.85rem;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-family: inherit;
            box-sizing: border-box;
            font-size: 0.95rem;
        }

        .btn-submit {
            background: var(--primary);
            color: white;
            padding: 0.9rem 2.2rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(46, 125, 50, 0.2);
        }

        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(46, 125, 50, 0.3); }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); }

        .product-img { width: 100%; height: 220px; object-fit: cover; background: #f1f5f9; }

        .product-content { padding: 1.5rem; }

        .tag {
            font-size: 0.72rem;
            background: #f0fdf4;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            color: #166534;
            font-weight: 600;
            text-transform: uppercase;
        }

        .price-stock { display: flex; justify-content: space-between; margin: 1.25rem 0; }
        .price { color: var(--primary); font-size: 1.25rem; font-weight: 600; }
        .stock { color: #94a3b8; font-size: 0.9rem; }

        .btn-delete {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
            padding: 0.6rem;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: all 0.2s;
        }

        .btn-delete:hover { background: #fecdd3; }

        .table-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 1.25rem; text-align: left; font-size: 0.85rem; color: #64748b; border-bottom: 1px solid var(--border); }
        td { padding: 1.25rem; border-bottom: 1px solid var(--border); font-size: 0.92rem; vertical-align: middle; }
    </style>
</head>
<body>

<header>
    <h1>Uziuzi Agrovet Control Panel</h1>
    <div class="header-actions">
        <a href="view.php" target="_blank" class="btn-view-shop">View Shop Catalog ↗</a>
        <a href="logout.php" class="btn-logout">Logout Admin</a>
    </div>
</header>

<div class="container">
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-info">
                <span>Inventory Size</span>
                <b><?php echo $products_count; ?> Items</b>
            </div>
            <div style="font-size: 2rem;">📦</div>
        </div>
        <div class="stat-grid">
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="value">Ksh <?php echo number_format($revenue['total_revenue'], 2); ?></div>
                <div class="trend" style="color: #16a34a;">↑ 12% vs last month</div>
            </div>
            <!-- Additional Stat Cards -->
        </div>

        <section style="margin-bottom: 5rem;">
            <h2 class="section-title">📈 Weekly Revenue Intelligence</h2>
            <div class="table-card" style="padding: 2rem; background: white;">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </section>

        <!-- Chart JS CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [<?php 
                        $days = [];
                        for($i=6; $i>=0; $i--) $days[] = "'".date('D', strtotime("-$i days"))."'";
                        echo implode(',', $days);
                    ?>],
                    datasets: [{
                        label: 'Gross Sales (Ksh)',
                        data: [<?php 
                            $sales_data = [];
                            for($i=6; $i>=0; $i--){
                                $d = date('Y-m-d', strtotime("-$i days"));
                                $res = $conn->query("SELECT SUM(o.quantity * p.price) as d_revenue FROM orders o JOIN products p ON o.product_name = p.product_name WHERE DATE(o.order_date) = '$d'")->fetch_assoc();
                                $sales_data[] = $res['d_revenue'] ?? 0;
                            }
                            echo implode(',', $sales_data);
                        ?>],
                        borderColor: '#2e7d32',
                        backgroundColor: 'rgba(46, 125, 32, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
                }
            });
        </script>
    <div class="stat-info">
                <span>Total Orders</span>
                <b><?php echo $orders_count; ?> Received</b>
            </div>
            <div style="font-size: 2rem;">🛒</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <span>Top Selling</span>
                <b><?php echo $top_product; ?></b>
            </div>
            <div style="font-size: 2rem;">⭐️</div>
        </div>
    </div>

    <section>
        <h2 class="section-title">📊 Inventory Management</h2>
        <div class="form-card">
            <form action="process.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Internal Product Name</label>
                    <input type="text" name="product_name" required placeholder="e.g. Certified Seedlings">
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Department / Category</label>
                        <select name="category" required>
                            <option value="Seeds">Seeds & Seedlings</option>
                            <option value="Fertilizers">Fertilizers & Growth</option>
                            <option value="Pesticides">Pesticides & Insecticides</option>
                            <option value="Herbicides">Herbicides & Weed Control</option>
                            <option value="Animal Feeds">Animal & Poultry Feeds</option>
                            <option value="Veterinary">Veterinary Medicine</option>
                            <option value="Farming Tools">Tools & Equipment</option>
                            <option value="Storage">Bags & Storage</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sale Price (Single Unit)</label>
                        <input type="number" step="0.01" name="price" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Opening Stock</label>
                        <input type="number" name="quantity" required placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Product Display Image</label>
                        <input type="file" name="image" required>
                    </div>
                </div>
                <button name="add" class="btn-submit">Register New Inventory</button>
            </form>
        </div>
    </section>

    <section>
        <div class="products-grid">
            <?php
            $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
            while($row = $result->fetch_assoc()){
            ?>
            <div class="product-card">
                <img src="images/<?php echo $row['image']; ?>" class="product-img" onerror="this.src='https://via.placeholder.com/300?text=Agrovet'">
                <div class="product-content">
                    <span class="tag"><?php echo $row['category']; ?></span>
                    <h3 style="margin: 0.75rem 0 0;"><?php echo $row['product_name']; ?></h3>
                    <div class="price-stock">
                        <div class="price">Ksh <?php echo number_format($row['price'], 2); ?></div>
                        <div class="stock">In Stock: <b><?php echo $row['quantity']; ?></b></div>
                    </div>

                    <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn-delete">Clear Item</button>

                    <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; font-size: 0.85rem;">
                        <span style="font-weight: 600; color: #64748b; margin-bottom: 0.75rem; display: block;">Quick Order System</span>
                        <form action="process.php" method="POST">
                            <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
                            <input type="text" name="customer_name" placeholder="Full Customer Name" required style="margin-bottom: 0.5rem; width: 100%; padding:0.6rem; border-radius:6px; border:1px solid #e2e8f0; box-sizing:border-box;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap:0.5rem; margin-bottom: 0.75rem;">
                                <input type="text" name="phone" placeholder="Phone No." required style="width: 100%; padding:0.6rem; border-radius:6px; border:1px solid #e2e8f0; box-sizing:border-box;">
                                <input type="number" name="quantity" placeholder="Qty" required min="1" style="width: 100%; padding:0.6rem; border-radius:6px; border:1px solid #e2e8f0; box-sizing:border-box;">
                            </div>
                            <select name="payment_method" style="width: 100%; padding:0.6rem; border-radius:6px; border:1px solid #e2e8f0; margin-bottom: 0.75rem;">
                                <option>M-Pesa</option>
                                <option>Cash on Delivery</option>
                            </select>
                            <button name="order" class="btn-submit" style="width:100%; padding: 0.6rem;">Complete Sale</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>

    <section style="margin-bottom: 6rem;">
        <h2 class="section-title">📊 Business Sales Hub (Receipt Records)</h2>
        <div class="table-card" style="background: white; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 1.25rem; text-align: left; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">RECIPE ID</th>
                        <th style="padding: 1.25rem; text-align: left; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">FARMER DETAILS</th>
                        <th style="padding: 1.25rem; text-align: left; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">ITEM(S) PURCHASED</th>
                        <th style="padding: 1.25rem; text-align: left; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">QTY</th>
                        <th style="padding: 1.25rem; text-align: left; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">TOTAL PAID (KSH)</th>
                        <th style="padding: 1.25rem; text-align: left; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">SETTLEMENT</th>
                        <th style="padding: 1.25rem; text-align: left; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">DATE & TIME</th>
                    </tr>
                </thead>
                <tbody id="sales-feed">
                    <?php
                    // Initial load from server-side PHP
                    $query = "SELECT o.*, p.price, (o.quantity * p.price) as total_amount 
                             FROM orders o 
                             LEFT JOIN products p ON o.product_name = p.product_name 
                             ORDER BY o.order_date DESC LIMIT 100";
                    $sales = $conn->query($query);
                    
                    if($sales->num_rows > 0):
                        while($row = $sales->fetch_assoc()):
                    ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 1.25rem; font-weight: 700; color: #cbd5e1;">#<?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?></td>
                        <td style="padding: 1.25rem;">
                            <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                            <div style="font-size: 0.75rem; color: #64748b;"><?php echo htmlspecialchars($row['phone']); ?></div>
                        </td>
                        <td style="padding: 1.25rem;">
                            <span style="font-weight: 500;"><?php echo htmlspecialchars($row['product_name']); ?></span>
                        </td>
                        <td style="padding: 1.25rem; font-weight: 600;">× <?php echo $row['quantity']; ?></td>
                        <td style="padding: 1.25rem; font-weight: 700; color: var(--primary);">Ksh <?php echo number_format($row['total_amount'], 2); ?></td>
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
                    else:
                    ?>
                    <tr><td colspan="7" style="padding: 4rem; text-align: center; color: #94a3b8;">No sale records found. All orders will appear here automatically.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
function confirmDelete(id) {
    if(confirm('🚨 WARNING: Are you sure you want to PERMANENTLY remove this item from your digital inventory? This cannot be undone.')) {
        window.location.href = 'process.php?delete=' + id;
    }
}

// 🕒 LIVE SALES FEED REFRESHER
function refreshSalesFeed() {
    fetch('api_live_sales.php')
        .then(response => response.text())
        .then(data => {
            const feed = document.getElementById('sales-feed');
            if(feed) feed.innerHTML = data;
        })
        .catch(err => console.error('Sales Feed Error:', err));
}

// Auto-refresh every 5 seconds
setInterval(refreshSalesFeed, 5000);
</script>

</body>
</html>