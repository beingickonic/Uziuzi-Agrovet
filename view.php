<?php
session_start();
include "connection.php";

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : "Customer";

// 🧮 Cart Counter
$cart_count = 0;
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $item) $cart_count += $item['qty'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop | Uziuzi Agrovet</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2e7d32;
            --primary-light: #4caf50;
            --bg: #f8fafc;
            --white: #ffffff;
            --shadow: 0 10px 40px rgba(0,0,0,0.08);
            --border: #e2e8f0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            margin: 0;
            color: #334155;
            padding-bottom: 80px; /* Space for floating cart */
        }

        .navbar {
            background: var(--white);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar h1 { margin: 0; font-size: 1.5rem; color: var(--primary); }

        .search-area {
            flex: 1;
            max-width: 400px;
            margin: 0 2rem;
        }

        .search-area input {
            width: 100%;
            padding: 0.6rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: 20px;
            font-family: inherit;
        }

        .hero {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .product-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); /* Softer shadow */
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .product-card:hover { 
            transform: translateY(-12px) scale(1.02); 
            box-shadow: 0 20px 40px rgba(46, 125, 50, 0.15);
            border-color: var(--primary-light);
        }

        /* 👑 ENTERPRISE BADGES */
        .verified-badge {
            position: absolute; top: 15px; right: 15px;
            background: rgba(255,255,255,0.9);
            padding: 0.4rem 0.8rem; border-radius: 50px;
            font-size: 0.7rem; font-weight: 700; color: #1e293b;
            display: flex; align-items: center; gap: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #f1f5f9;
            z-index: 50;
        }

        .stock-dot {
            width: 8px; height: 8px; background: #22c55e; border-radius: 50%;
            display: inline-block; box-shadow: 0 0 10px #22c55e;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1.2); box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        .product-image { width: 100%; height: 200px; object-fit: cover; }

        .product-info { padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column; }

        .category-tag { font-size: 0.75rem; color: var(--primary); font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem; }

        .product-title { font-size: 1.15rem; margin: 0 0 1rem; font-weight: 600; }

        .product-price { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem; }

        .add-cart-form { display: grid; grid-template-columns: 60px 1fr; gap: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 1.25rem; }

        .qty-input { padding: 0.5rem; border: 1.5px solid var(--border); border-radius: 8px; text-align: center; }

        .btn-add-cart {
            background: linear-gradient(135deg, var(--primary) 0%, #1b5e20 100%);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            animation: pulse-btn 3s infinite;
        }

        .btn-add-cart:hover { 
            animation: none; 
            background: var(--primary-light); 
            transform: scale(1.05); 
        }

        /* 🔥 FIRE ANIMATION FOR HOT DEALS */
        .hot-deal-badge {
            position: absolute; top: 15px; left: 15px;
            background: linear-gradient(135deg, #ff4d00 0%, #ff8c00 100%);
            color: white; padding: 0.4rem 0.8rem; border-radius: 50px;
            font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
            box-shadow: 0 0 20px rgba(255, 77, 0, 0.6);
            z-index: 60; border: 1.5px solid rgba(255,255,255,0.4);
            animation: flicker-glow 1.5s infinite alternate;
        }

        @keyframes flicker-glow {
            0% { transform: scale(1); box-shadow: 0 0 15px #ff4d00, 0 0 30px #ff8c00; }
            50% { transform: scale(1.05); box-shadow: 0 0 25px #ff4d00, 0 0 50px #ff8c00; }
            100% { transform: scale(1); box-shadow: 0 0 15px #ff4d00, 0 0 30px #ff8c00; }
        }

        .hot-price { color: #f97316 !important; animation: price-vibrate 0.3s infinite alternate; }
        @keyframes price-vibrate {
            from { transform: translateX(0); }
            to { transform: translateX(1.5px); }
        }

        @keyframes pulse-btn {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); box-shadow: 0 0 20px rgba(46, 125, 50, 0.4); }
            100% { transform: scale(1); }
        }

        /* Floating Mini-Cart Banner */
        .floating-cart {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.4);
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            z-index: 2000;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .floating-cart:hover { transform: scale(1.05); }

        .cart-tag {
            background: white;
            color: var(--primary);
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem;
        }

        .notification {
            position: fixed;
            top: 100px; right: 25px;
            background: #dcfce7;
            color: #166534;
            padding: 1rem 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid #bbf7d0;
            z-index: 1000;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>Uziuzi Agrovet</h1>
    <div class="search-area">
        <form method="GET">
            <input type="text" name="q" placeholder="Search seeds, tools, or feeds..." value="<?php echo $_GET['q'] ?? ''; ?>">
        </form>
    </div>
    <div class="nav-links">
        <?php if($is_logged_in): ?>
            <p style="font-size: 0.8rem;">P.O Box 123, Kenya<br>Contact: +254 785 695 840</p>
            <span style="font-size:0.9rem; color:#64748b; margin-right:0.5rem;">Farmer: <b><?php echo $user_name; ?></b></span>
            <a href="logout.php" style="background: #fee2e2; color: #b91c1c !important; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 600; text-decoration: none;">🚪 Logout Account</a>
        <?php else: ?>
            <a href="login.php" class="btn-auth">Customer Login</a>
            <a href="register.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

<div class="hero">
    <div class="farm-animation">
        <div class="floating-item item-1"></div>
        <div class="floating-item item-2"></div>
        <div class="floating-item item-3"></div>
    </div>
    
    <div class="hero-card">
        <div class="prestige-badge">
            <span class="flag-dot"></span> 🇰🇪 OFFICIAL KENYAN SUPPLIER
        </div>
        
        <h2 class="hero-title">
            <span class="gold-text">Certified</span> Farm Essentials
        </h2>
        
        <p class="hero-subtitle">
            Premium Agricultural Supplies Delivered directly to your Farm gate across the nation.
        </p>

        <div class="hero-stats">
            <div class="hero-stat"><b>100%</b><br><span>Organic & Certified</span></div>
            <div class="hero-stat"><b>FREE</b><br><span>National Delivery</span></div>
            <div class="hero-stat"><b>254+</b><br><span>Verified Farmers</span></div>
        </div>
    </div>
</div>

<style>
    .hero-card {
        position: relative; z-index: 20;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 3rem; border-radius: 40px;
        max-width: 800px; margin: 0 auto;
        box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        animation: hero-slide-up 1.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    @keyframes hero-slide-up {
        0% { transform: translateY(50px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }

    .prestige-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(0,0,0,0.3); padding: 0.6rem 1.2rem;
        border-radius: 50px; font-size: 0.85rem; font-weight: 700;
        color: white; border: 1px solid rgba(255,255,255,0.2);
        margin-bottom: 2rem; animation: badge-glow 3s infinite;
    }

    @keyframes badge-glow {
        0%, 100% { box-shadow: 0 0 10px rgba(255,255,255,0.1); }
        50% { box-shadow: 0 0 25px rgba(255,255,255,0.4); }
    }

    .hero-title { font-size: 3.5rem !important; line-height: 1.1; margin-bottom: 1.5rem; text-shadow: none !important; }
    .gold-text { color: #facc15; text-shadow: 0 0 20px rgba(250, 204, 21, 0.5); }

    .hero-subtitle { font-size: 1.15rem; color: rgba(255,255,255,0.8); max-width: 600px; margin: 0 auto 2.5rem; }

    .hero-stats { display: flex; justify-content: center; gap: 3rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; }
    .hero-stat b { display: block; font-size: 1.5rem; color: white; }
    .hero-stat span { font-size: 0.75rem; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 1px; }

    .flag-dot { width: 10px; height: 10px; background: #e11d48; border-radius: 50%; box-shadow: 0 0 10px #e11d48; }
</style>

<div class="container">
    <?php if(isset($_GET['added'])): ?>
        <div class="notification" id="notify">🎉 Added <b><?php echo $_GET['added']; ?></b> to your cart!</div>
        <script>setTimeout(() => { document.getElementById('notify').style.display = 'none'; }, 3000);</script>
    <?php endif; ?>

    <div class="products-grid">
        <?php
        $search = $_GET['q'] ?? '';
        $sql = "SELECT * FROM products";
        if($search) $sql .= " WHERE product_name LIKE '%$search%' OR category LIKE '%$search%'";
        $sql .= " ORDER BY id DESC";
        $result = $conn->query($sql);
        
        while($row = $result->fetch_assoc()){
            $is_hot = ($row['price'] >= 1500); // ⚡ HOT DEAL Condition
        ?>
        <div class="product-card">
            <?php if($is_hot): ?>
                <div class="hot-deal-badge">🔥 HOT DEAL</div>
            <?php endif; ?>
            <div class="verified-badge">✅ Verified Supplier</div>
            <img src="images/<?php echo $row['image']; ?>" class="product-image" onerror="this.src='https://via.placeholder.com/300x200?text=Agrovet+Item'">
            <div class="product-info">
                <span class="category-tag"><div class="stock-dot"></div> Live Inventory - <?php echo $row['category']; ?></span>
                <h3 class="product-title"><?php echo $row['product_name']; ?></h3>
                <div class="product-price <?php echo $is_hot ? 'hot-price' : ''; ?>">Ksh <?php echo number_format($row['price'], 2); ?></div>
                
                <form action="cart_actions.php" method="POST" class="add-cart-form">
                    <input type="hidden" name="p_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="p_name" value="<?php echo $row['product_name']; ?>">
                    <input type="hidden" name="p_price" value="<?php echo $row['price']; ?>">
                    <input type="hidden" name="p_image" value="<?php echo $row['image']; ?>">
                    <input type="number" name="qty" value="1" min="1" class="qty-input">
                    <button type="submit" name="add_to_cart" class="btn-add-cart">Add to Cart</button>
                </form>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<a href="https://wa.me/254785695840?text=Hello%20Uziuzi%20Agrovet!%20I%20have%20a%20question%20about%20your%20farm%20products." target="_blank" class="whatsapp-btn">
    <span style="font-size: 1.5rem;">📱</span>
    <span style="font-weight: 600;">Chat with Us</span>
</a>

<a href="checkout.php" class="floating-cart">
    <div class="cart-tag"><?php echo $cart_count; ?></div>
    <span style="font-weight: 600;">Go to Checkout 🛒</span>
</a>

<style>
    .whatsapp-btn {
        position: fixed; bottom: 25px; left: 25px;
        background: #25d366; color: white;
        padding: 0.8rem 1.5rem; border-radius: 50px;
        box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4);
        display: flex; align-items: center; gap: 0.75rem;
        text-decoration: none; z-index: 2000;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .whatsapp-btn:hover { transform: scale(1.05); }
</style>

</body>
</html>
