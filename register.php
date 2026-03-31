<?php
session_start();
include "connection.php";

$error = "";
$success = "";

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if($password !== $confirm){
        $error = "Passwords do not match!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed);
        
        if($stmt->execute()){
            $success = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "Email already exists!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Uziuzi Agrovet</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2e7d32;
            --primary-light: #4caf50;
            --bg: #f8fafc;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0,0,0,0.06);
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
        }

        .auth-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            border: 1px solid var(--border);
        }

        .auth-card h2 {
            margin: 0 0 0.5rem;
            color: var(--primary);
            font-size: 1.75rem;
        }

        .auth-card p {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 2rem;
        }

        .form-group { margin-bottom: 1.25rem; }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #475569;
        }

        input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
        }

        .btn-register {
            width: 100%;
            padding: 0.9rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
            transition: transform 0.2s, background 0.3s;
        }

        .btn-register:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .error-msg { background: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem; border: 1px solid #fecdd3; }
        .success-msg { background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem; border: 1px solid #bbf7d0; }

        .auth-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
        }

        .auth-footer a { color: var(--primary); font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>

    <div class="auth-card">
        <h2>Join Agrovet</h2>
        <p>Start managing your farm essentials today.</p>

        <?php if($error): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
        <?php if($success): ?><div class="success-msg"><?php echo $success; ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com">
            </div>

            <div class="form-group">
                <label>Secure Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="••••••••">
            </div>

            <button type="submit" name="register" class="btn-register">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Log In</a>
        </div>
    </div>

</body>
</html>
