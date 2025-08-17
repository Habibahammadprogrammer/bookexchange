<?php
require_once 'includes/config.php';
$errors = [];
$success = '';
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("Select Id, PasswordHash from users WHERE Email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userID, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $success = "Login Successful";
            session_start();
            $_SESSION["user_id"] = $userID;
            $_SESSION["email"]= $email;
            header("Location: home.php");
            exit();
        } else {
            $errors[]= "Incorrect Password";
        }
    } else {
        $errors[]= "User not found";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapter & Verse - Literary Authentication</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --cream: #F8F6F3;
            --charcoal: #2C2C2C;
            --orange: #E8A87C;
            --dark-charcoal: #1A1A1A;
            --muted-gray: #6B6B6B;
            --light-gray: #E5E5E5;
            --error: #D73027;
            --success: #1A7F37;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 1200px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(44, 44, 44, 0.1);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        .auth-visual {
            background: linear-gradient(135deg, var(--charcoal) 0%, var(--dark-charcoal) 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            overflow: hidden;
        }

        .auth-visual::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="books" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect width="2" height="15" x="2" y="2.5" fill="%23E8A87C" opacity="0.1"/><rect width="2" height="12" x="6" y="4" fill="%23E8A87C" opacity="0.08"/><rect width="2" height="18" x="10" y="1" fill="%23E8A87C" opacity="0.06"/><rect width="2" height="14" x="14" y="3" fill="%23E8A87C" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23books)"/></svg>') repeat;
            opacity: 0.3;
        }

        .visual-content {
            text-align: center;
            color: white;
            z-index: 2;
            position: relative;
        }

        .visual-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--orange);
        }

        .visual-content .tagline {
            font-size: 1.1rem;
            font-weight: 300;
            opacity: 0.9;
            max-width: 300px;
            margin: 0 auto;
            line-height: 1.5;
        }

        .quote {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(232, 168, 124, 0.3);
            font-style: italic;
            font-size: 0.95rem;
            opacity: 0.8;
        }

        .auth-form {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .breadcrumb {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 0.85rem;
            color: var(--muted-gray);
        }

        .breadcrumb a {
            color: var(--orange);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb a:hover {
            color: var(--charcoal);
        }

        .form-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--muted-gray);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--charcoal);
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            background: white;
            transition: all 0.3s ease;
            color: var(--charcoal);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(232, 168, 124, 0.1);
        }

        .form-control.error {
            border-color: var(--error);
        }

        .form-control.success {
            border-color: var(--success);
        }

        .error-message {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0 2rem;
            font-size: 0.9rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-wrapper input[type="checkbox"] {
            accent-color: var(--orange);
        }

        .forgot-link {
            color: var(--orange);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--charcoal);
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: var(--dark-charcoal);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            background: var(--charcoal);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 26, 26, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
        }
        .btn-primary a{
            color:white;
            text-decoration:none;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: var(--muted-gray);
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--light-gray);
        }

        .divider span {
            padding: 0 1rem;
        }

        .social-login {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 12px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            background: white;
            color: var(--charcoal);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            border-color: var(--orange);
            background: rgba(232, 168, 124, 0.05);
        }

        .form-footer {
            text-align: center;
            color: var(--muted-gray);
            font-size: 0.9rem;
        }

        .form-footer a {
            color: var(--orange);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--charcoal);
        }

        .form-toggle {
            margin-top: 1rem;
        }

        .privacy-links {
            margin-top: 1.5rem;
            font-size: 0.8rem;
            text-align: center;
        }

        .privacy-links a {
            color: var(--muted-gray);
            text-decoration: none;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }

        .privacy-links a:hover {
            color: var(--orange);
        }

        .hidden {
            display: none;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .auth-container {
                grid-template-columns: 1fr;
                max-width: 400px;
            }

            .auth-visual {
                padding: 40px 20px;
                min-height: 200px;
            }

            .visual-content h1 {
                font-size: 2rem;
            }

            .auth-form {
                padding: 40px 30px;
            }

            .form-header h2 {
                font-size: 1.8rem;
            }

            .social-login {
                grid-template-columns: 1fr;
            }

            .form-options {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }

        /* Animations */
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

        .auth-container {
            animation: fadeInUp 0.6s ease;
        }

        .form-group {
            animation: fadeInUp 0.6s ease;
            animation-fill-mode: both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .btn-primary { animation-delay: 0.5s; }

        /* Loading state */
        .btn-primary.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-primary.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="breadcrumb">
        <a href="#" onclick="goHome()">← Back to Chapter & Verse</a>
    </div>

    <div class="auth-container">
        <div class="auth-visual">
            <div class="visual-content">
                <h1>Chapter & Verse</h1>
                <p class="tagline">Where every story finds its reader, and every reader discovers their next adventure</p>
                <div class="quote">
                    "A reader lives a thousand lives before he dies... The man who never reads lives only one." — George R.R. Martin
                </div>
            </div>
        </div>

        <div class="auth-form">
            <!-- Login Form -->
            <form id="loginForm" class="auth-form-content" method="POST" action="login.php">
                <div class="form-header">
                    <h2>Welcome Back</h2>
                    <p>Continue your literary journey</p>
                </div>

                <div class="social-login">
                    <a href="#" class="social-btn" onclick="socialLogin('google')">
                        <i class="fab fa-google"></i>
                        Google
                    </a>
                    <a href="#" class="social-btn" onclick="socialLogin('apple')">
                        <i class="fab fa-apple"></i>
                        Apple
                    </a>
                </div>

                <div class="divider">
                    <span>or continue with email</span>
                </div>

                <div class="form-group">
                    <label for="loginEmail">Email Address</label>
                    <input  name="email" type="email" id="loginEmail" class="form-control" placeholder="your@email.com" required>
                    <div class="error-message" id="loginEmailError">Please enter a valid email address</div>
                </div>

                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input name="password" type="password" id="loginPassword" class="form-control" placeholder="Enter your password" required>
                    <div class="error-message" id="loginPasswordError">Password is required</div>
                </div>

                <div class="form-options">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="rememberMe">
                        <label for="rememberMe">Remember me</label>
                    </div>
                    <a href="#" class="forgot-link" onclick="showForgotPassword()">Forgot password?</a>
                </div>

                <button  name="login" type="submit" class="btn-primary" ><a>Sign In</a></button>

                <div class="form-footer">
                    <div class="form-toggle">
                        New to Chapter & Verse? <a href="register.php">Create an account</a>
                    </div>
                    <div class="privacy-links">
                        <a href="#" onclick="showPrivacy()">Privacy Policy</a>
                        <a href="#" onclick="showTerms()">Terms of Service</a>
                    </div>
                </div>
            </form>
            </div>
            </div>
            </body>
            </html>
