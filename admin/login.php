<?php
// admin/login.php - Updated with role-based redirect
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: dashboard-editor.php");
    }
    exit();
}

require_once '../config/database.php';

$error = '';
$success_message = '';

// Check for logout success message
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success_message = 'You have been successfully logged out.';
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user && password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();
            
            // Update last login
            $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Redirect based on role
            if($user['role'] == 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: dashboard-editor.php");
            }
            exit();
        } else {
            $error = "Invalid username or password";
            error_log("Failed login attempt for username: $username at " . date('Y-m-d H:i:s'));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Admin Login - Stella Maris College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a4d8c 0%, #2e7d32 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        
        .login-card {
            background: white;
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: fadeInUp 0.5s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .login-header img {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            margin-bottom: 16px;
            object-fit: cover;
        }
        
        .login-header h2 {
            font-size: 28px;
            margin: 0;
            color: #333;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
            margin-top: 8px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }
        
        .form-control {
            padding: 14px 16px 14px 48px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #1a4d8c;
            box-shadow: 0 0 0 3px rgba(26,77,140,0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1a4d8c 0%, #2e7d32 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-login:active {
            transform: scale(0.98);
        }
        
        .alert {
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .footer-links {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #eee;
        }
        
        .footer-links a {
            color: #1a4d8c;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .demo-credentials {
            margin-top: 20px;
            padding: 16px;
            background: #f5f5f5;
            border-radius: 12px;
            font-size: 13px;
        }
        
        .role-info {
            margin-top: 15px;
            padding: 12px;
            background: #e8f0fe;
            border-radius: 8px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="../assets/images/logo.png" alt="Stella Maris College">
                <h2>Admin Login</h2>
                <p>Stella Maris College Nsuube</p>
            </div>
            
            <?php if($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label>Username or Email</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" class="form-control" required autocomplete="off" autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <i class="fas fa-eye password-toggle" id="togglePassword" style="right: 16px; left: auto;"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="role-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Role Information:</strong><br>
                <span class="badge bg-danger">Admin</span> - Full access to all features<br>
                <span class="badge bg-info">Editor</span> - Can manage content only
            </div>
            
            <!--<div class="demo-credentials">
                <p><strong>Demo Credentials:</strong></p>
                <p><strong>Admin:</strong> admin / Admin@123</p>
                <p><strong>Editor:</strong> editor / Admin@123 (after creating)</p>
            </div>-->
            
            <div class="footer-links">
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Website</a>
            </div>
        </div>
    </div>
    
    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>