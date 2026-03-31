<?php
session_start();
include "connection.php";

$error = "";

// 🛡️ AUTHENTICATION LOGIC
if(isset($_POST['login'])){
    $role = $_POST['role']; // 'admin' or 'customer'
    
    if($role == 'admin'){
        $username = $_POST['id_input']; // For admin it is 'username'
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT password FROM admin WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()){
            if(password_verify($password, $row['password'])){
                $_SESSION['admin'] = $username;
                header("Location: index.php");
                exit();
            } else { $error = "Invalid Admin Credentials"; }
        } else { $error = "Invalid Admin Credentials"; }
    } else {
        $email = $_POST['id_input']; // For customer it is 'email'
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()){
            if(password_verify($password, $row['password'])){
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                
                // 📝 Log Activity (Safely)
                $log_stmt = $conn->prepare("INSERT INTO activity_log (user_name, activity_type, activity_detail) VALUES (?, 'Login', 'Farmer authenticated and entered shop')");
                if($log_stmt){
                    $log_stmt->bind_param("s", $row['name']);
                    $log_stmt->execute();
                    $log_stmt->close();
                }
                
                header("Location: view.php");
                exit();
            } else { $error = "Invalid Customer Credentials"; }
        } else { $error = "Customer account not found"; }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal | Uziuzi Agrovet</title>
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
            align-items: center; justify-content: center;
            min-height: 100vh;
            color: #334155;
        }

        .portal-card {
            background: var(--white);
            padding: 3rem;
            border-radius: 30px;
            box-shadow: var(--shadow);
            width: 100%; max-width: 440px;
            text-align: center; border: 1px solid var(--border);
        }

        .portal-header h2 { font-size: 2rem; color: var(--primary); margin: 0 0 0.5rem; }
        .portal-header p { font-size: 0.95rem; color: #64748b; margin-bottom: 2rem; }

        /* Role Switcher Styles */
        .role-switcher {
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .role-btn {
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: inherit;
            background: transparent;
            color: #64748b;
        }

        .role-btn.active {
            background: var(--white);
            color: var(--primary);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .form-group { text-align: left; margin-bottom: 1.5rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: #475569; }
        .form-group input { 
            width: 100%; padding: 0.85rem 1.1rem; border: 1.5px solid var(--border); 
            border-radius: 12px; font-family: inherit; box-sizing: border-box; 
        }

        .btn-portal {
            width: 100%; padding: 1rem; background: var(--primary); color: white;
            border: none; border-radius: 12px; font-weight: 700; font-size: 1.05rem;
            cursor: pointer; transition: all 0.3s; margin-top: 1rem;
        }

        .btn-portal:hover { background: var(--primary-light); transform: translateY(-3px); }

        .error-box { background: #ffebee; color: #c62828; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.85rem; }

        .footer-links { margin-top: 2rem; font-size: 0.9rem; color: #64748b; }
        .footer-links a { color: var(--primary); font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>

    <div class="portal-card">
        <div class="portal-header">
            <h2>Uziuzi Portal</h2>
            <p id="sub-msg">Please select your account type below.</p>
        </div>

        <?php if($error): ?><div class="error-box"><?php echo $error; ?></div><?php endif; ?>

        <div class="role-switcher">
            <button type="button" id="btn-customer" class="role-btn active" onclick="switchRole('customer')">Farmer Login</button>
            <button type="button" id="btn-admin" class="role-btn" onclick="switchRole('admin')">Staff / Admin</button>
        </div>

        <form method="POST">
            <input type="hidden" name="role" id="role-input" value="customer">
            
            <div class="form-group">
                <label id="id-label">Customer Email Address</label>
                <input type="text" name="id_input" id="id_field" required placeholder="name@example.com">
            </div>

            <div class="form-group">
                <label>Access Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" name="login" class="btn-portal" id="btn-text">Log In to Farm</button>
        </form>

        <div class="footer-links" id="signup-area">
            Don't have a farm account? <a href="register.php">Sign Up Here</a>
        </div>
    </div>

    <script>
        function switchRole(role) {
            const roleInput = document.getElementById('role-input');
            const idLabel = document.getElementById('id-label');
            const idField = document.getElementById('id_field');
            const btnText = document.getElementById('btn-text');
            const subMsg = document.getElementById('sub-msg');
            const signupArea = document.getElementById('signup-area');
            
            const btnAdmin = document.getElementById('btn-admin');
            const btnCustomer = document.getElementById('btn-customer');

            if(role === 'admin') {
                btnAdmin.classList.add('active');
                btnCustomer.classList.remove('active');
                roleInput.value = 'admin';
                idLabel.innerText = 'Admin Username';
                idField.placeholder = 'Enter staff ID / username';
                btnText.innerText = 'Unlock Admin Dashboard';
                subMsg.innerText = 'Administrator and Inventory Control.';
                signupArea.style.display = 'none';
            } else {
                btnCustomer.classList.add('active');
                btnAdmin.classList.remove('active');
                roleInput.value = 'customer';
                idLabel.innerText = 'Customer Email Address';
                idField.placeholder = 'name@example.com';
                btnText.innerText = 'Log In to Farm';
                subMsg.innerText = 'Order supplies and review your purchases.';
                signupArea.style.display = 'block';
            }
        }
    </script>
</body>
</html>
