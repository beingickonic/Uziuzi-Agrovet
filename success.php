<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmed | Uziuzi Agrovet</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #334155;
            padding: 2rem;
        }

        .success-card {
            background: var(--white);
            padding: 4rem 3rem;
            border-radius: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .check-icon {
            font-size: 5rem;
            color: #16a34a;
            margin-bottom: 2rem;
            display: block;
        }

        h1 {
            color: var(--primary);
            font-size: 2.25rem;
            margin: 0 0 1rem;
            font-weight: 700;
        }

        p {
            font-size: 1.1rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .order-badge {
            display: inline-block;
            background: #f0fdf4;
            color: #166534;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            border: 1px solid #bbf7d0;
        }

        .btn-return {
            display: block;
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 1.25rem;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(46, 125, 50, 0.2);
        }

        .btn-return:hover {
            background: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(46, 125, 50, 0.3);
        }

        .social-note {
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="success-card">
        <span class="check-icon">✓</span>
        <div class="order-badge">Order Processed Successfully</div>
        <h1>Payment Confirmed!</h1>
        <p>Thank you for choosing <b>Uziuzi Agrovet</b>. Your farm supplies are being prepared and will be delivered shortly.</p>
        
        <a href="view.php" class="btn-return">Return to Farm Shop catalog</a>
        <br>
        <a href="print_receipt.php" target="_blank" style="display:inline-block; margin-top:1.5rem; color:#2e7d32; text-decoration:none; font-weight:600; border-bottom: 2px solid #2e7d32; padding-bottom:2px;">📥 Download Official Receipt (PDF)</a>
        
        <div class="social-note">A receipt or confirmation has been sent to your dashboard.</div>
    </div>

</body>
</html>
