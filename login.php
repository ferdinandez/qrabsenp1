<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errorMessage = '';
$usernameValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $usernameValue = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

    // Connect langsung ke Render API
    $apiUrl = 'https://attendx-t6ow.onrender.com/api/login';
    $postData = json_encode(['email' => $username, 'password' => $password]);
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Dewaweb compatibility
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['token'])) {
            $_SESSION['token'] = $data['token'];
            $_SESSION['user_id'] = $data['user']['id'];
            $_SESSION['username'] = $data['user']['name'];
            $_SESSION['email'] = $data['user']['email'];
            $_SESSION['role'] = $data['user']['role'];
            header('Location: dashboard.php');
            exit;
        }
    }
    
    $errorMessage = 'Email atau password salah.';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AttendX Enterprise</title>
    
    <!-- PWA Meta Tags -->
    <?php include_once 'includes/pwa-head.php'; ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #0a0e27;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 20px 0;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(45deg, transparent 30%, rgba(0, 102, 255, 0.03) 30%, rgba(0, 102, 255, 0.03) 70%, transparent 70%),
                linear-gradient(-45deg, transparent 30%, rgba(59, 130, 246, 0.03) 30%, rgba(59, 130, 246, 0.03) 70%, transparent 70%);
            background-size: 100px 100px;
            animation: backgroundShift 20s linear infinite;
        }
        
        @keyframes backgroundShift {
            0% { background-position: 0 0, 0 0; }
            100% { background-position: 100px 100px, -100px 100px; }
        }
        
        /* Gradient Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s ease-in-out infinite;
        }
        
        .orb-1 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #0066FF, #00C9FF);
            top: -250px;
            right: -250px;
            animation-delay: 0s;
        }
        
        .orb-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #667EEA, #764BA2);
            bottom: -200px;
            left: -200px;
            animation-delay: 5s;
        }
        
        .orb-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #F093FB, #F5576C);
            top: 50%;
            left: 50%;
            animation-delay: 10s;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        
        .login-container {
            width: 100%;
            max-width: 1100px;
            min-height: 600px;
            display: flex;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 
                0 0 0 1px rgba(255, 255, 255, 0.1),
                0 30px 80px rgba(0, 0, 0, 0.4);
            position: relative;
            z-index: 1;
            margin: 20px;
            backdrop-filter: blur(10px);
        }
        
        .login-left {
            flex: 1.1;
            background: linear-gradient(135deg, #1a1f3a 0%, #0a0e27 100%);
            padding: 50px 50px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            min-height: 600px;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -150px;
            right: -150px;
        }
        
        .login-left::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -100px;
            left: -100px;
        }
        
        .login-left-content {
            position: relative;
            z-index: 1;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 40px;
        }
        
        .logo-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #0066FF 0%, #00A3FF 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 26px;
            box-shadow: 0 8px 24px rgba(0, 102, 255, 0.3);
        }
        
        .logo-text {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .logo-text span {
            color: #0066FF;
            font-weight: 800;
        }
        
        .login-left h1 {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 16px;
            letter-spacing: -1px;
        }
        
        .login-left .subtitle {
            font-size: 15px;
            opacity: 0.85;
            line-height: 1.6;
            margin-bottom: 35px;
            font-weight: 300;
        }
        
        .features {
            list-style: none;
        }
        
        .features li {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: 400;
        }
        
        .features li i {
            width: 32px;
            height: 32px;
            background: rgba(0, 102, 255, 0.15);
            border: 1px solid rgba(0, 102, 255, 0.3);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #00A3FF;
        }
        
        .login-footer {
            position: relative;
            z-index: 1;
            opacity: 0.6;
            font-size: 13px;
            font-weight: 300;
        }
        
        .login-right {
            flex: 1;
            padding: 50px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
            min-height: 600px;
        }
        
        .login-header {
            margin-bottom: 36px;
        }
        
        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #0a0e27;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .login-header p {
            color: #64748b;
            font-size: 15px;
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 22px;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }
        
        .form-control {
            width: 100%;
            padding: 16px 18px 16px 52px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            outline: none;
            background: #f8fafc;
            color: #0a0e27;
        }
        
        .form-control:focus {
            border-color: #0066FF;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.08);
        }
        
        .form-control::placeholder {
            color: #94a3b8;
        }
        
        .btn-login {
            width: 100%;
            padding: 17px;
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 102, 255, 0.25);
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(0, 102, 255, 0.35);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login i {
            margin-right: 10px;
        }
        
        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .alert-danger {
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            color: #dc2626;
        }
        
        .alert i {
            font-size: 18px;
        }
        
        /* Password Toggle Button */
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 8px;
            font-size: 16px;
            transition: all 0.2s ease;
            z-index: 2;
        }
        
        .password-toggle:hover {
            color: #0066FF;
        }
        
        .password-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        /* Input Focus Animation */
        .input-wrapper.focused .fa-envelope,
        .input-wrapper.focused .fa-lock {
            color: #0066FF;
        }
        
        .input-wrapper.focused .form-control {
            border-color: #0066FF;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.08);
        }
        
        /* Loading State */
        .btn-login.loading {
            position: relative;
            color: transparent;
            pointer-events: none;
        }
        
        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spinner 0.6s linear infinite;
        }
        
        @keyframes spinner {
            to { transform: rotate(360deg); }
        }
        
        /* Touch Feedback */
        .btn-login:active {
            transform: scale(0.98);
        }
        
        /* Smooth Input Transitions */
        .form-control,
        .input-wrapper i,
        .password-toggle {
            transition: all 0.3s ease;
        }
        
        @media (max-width: 968px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .login-left {
                padding: 40px 30px;
                min-height: auto;
            }
            
            .login-right {
                padding: 40px 30px;
                min-height: auto;
            }
            
            .login-left h1 {
                font-size: 28px;
            }
            
            .login-header h2 {
                font-size: 24px;
            }
            
            .features {
                margin-bottom: 30px;
            }
            
            .login-footer {
                margin-top: 30px;
            }
            
            /* Mobile-friendly tap targets */
            .btn-login {
                padding: 18px;
                font-size: 16px;
            }
            
            .form-control {
                padding: 16px 18px 16px 52px;
                font-size: 16px; /* Prevent zoom on iOS */
            }
            
            .password-toggle {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    
    <div class="login-container">
        <div class="login-left">
            <div class="login-left-content">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="logo-text">Attend<span>X</span></div>
                </div>
                
                <h1>Enterprise Attendance Solution</h1>
                <p class="subtitle">Solusi absensi modern dengan teknologi QR Code, Geofencing, dan Analytics terintegrasi untuk meningkatkan produktivitas perusahaan Anda.</p>
                
                <ul class="features">
                    <li>
                        <i class="fas fa-shield-halved"></i>
                        <span>Advanced Security & Encryption</span>
                    </li>
                    <li>
                        <i class="fas fa-chart-line"></i>
                        <span>Real-time Analytics Dashboard</span>
                    </li>
                    <li>
                        <i class="fas fa-mobile-screen"></i>
                        <span>Cross-Platform Accessibility</span>
                    </li>
                    <li>
                        <i class="fas fa-cloud-arrow-up"></i>
                        <span>Cloud-Based Infrastructure</span>
                    </li>
                </ul>
            </div>
            
            <div class="login-footer">
                © 2026 AttendX Enterprise. All rights reserved.
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Enter your credentials to access your account</p>
            </div>
            
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span><?php echo $errorMessage; ?></span>
                </div>
            <?php endif; ?>
            
            <form method="post" action="login.php" id="loginForm">
                <div class="form-group">
                    <label for="username">Email Address</label>
                    <div class="input-wrapper" id="emailWrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="username" name="username" class="form-control" value="<?php echo $usernameValue; ?>" placeholder="john.doe@company.com" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper" id="passwordWrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    Sign In
                </button>
            </form>
        </div>
    </div>
    
    <script>
        // Password Toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Input Focus Effects
        const emailWrapper = document.getElementById('emailWrapper');
        const passwordWrapper = document.getElementById('passwordWrapper');
        const emailInput = document.getElementById('username');
        
        emailInput.addEventListener('focus', () => emailWrapper.classList.add('focused'));
        emailInput.addEventListener('blur', () => emailWrapper.classList.remove('focused'));
        
        passwordInput.addEventListener('focus', () => passwordWrapper.classList.add('focused'));
        passwordInput.addEventListener('blur', () => passwordWrapper.classList.remove('focused'));
        
        // Form Submit Loading State
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        
        loginForm.addEventListener('submit', function() {
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
        });
        
        // Mobile: Prevent double-tap zoom on buttons
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
</body>
</html>