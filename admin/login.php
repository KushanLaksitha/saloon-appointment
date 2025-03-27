<?php
session_start();
require_once '../includes/database.php';

// Enhanced input sanitization function
function sanitize_input($data) {
    // Trim whitespace from beginning and end
    $data = trim($data);
    
    // Remove backslashes
    $data = stripslashes($data);
    
    // Convert special characters to HTML entities
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

// Login Processing with Enhanced Security
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    $login_error = '';

    // Rate limiting and brute force protection
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5) {
        $login_error = "Too many failed attempts. Please try again later.";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password_hash, is_super_admin, failed_attempts, last_failed_attempt FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check if account is locked due to multiple failed attempts
            $current_time = time();
            $lockout_duration = 15 * 60; // 15 minutes
            
            if ($user['failed_attempts'] >= 5 && 
                ($current_time - strtotime($user['last_failed_attempt'])) < $lockout_duration) {
                $login_error = "Account temporarily locked. Please try again in 15 minutes.";
            } else {
                // Verify password
                if (password_verify($password, $user['password_hash'])) {
                    // Reset failed attempts on successful login
                    $reset_stmt = $conn->prepare("UPDATE admin_users SET failed_attempts = 0, last_failed_attempt = NULL WHERE id = ?");
                    $reset_stmt->bind_param("i", $user['id']);
                    $reset_stmt->execute();

                    // Login successful
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['is_super_admin'] = $user['is_super_admin'];
                    $_SESSION['login_attempts'] = 0;

                    // Update last login time
                    $update_stmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();

                    // Redirect to admin dashboard
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    // Increment failed attempts
                    $failed_attempts = ($user['failed_attempts'] + 1);
                    $update_stmt = $conn->prepare("UPDATE admin_users SET failed_attempts = ?, last_failed_attempt = NOW() WHERE id = ?");
                    $update_stmt->bind_param("ii", $failed_attempts, $user['id']);
                    $update_stmt->execute();

                    // Set login error
                    $login_error = "Invalid username or password";
                    
                    // Track login attempts in session
                    $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) 
                        ? $_SESSION['login_attempts'] + 1 
                        : 1;
                }
            }
        } else {
            // User not found
            $login_error = "Invalid username or password";
            
            // Track login attempts in session
            $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) 
                ? $_SESSION['login_attempts'] + 1 
                : 1;
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
    <title>Saloon Kavisha - Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --transition-speed: 0.3s;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
            margin: 0;
            perspective: 1000px;
            overflow: hidden;
        }

        /* Animated Background Bubbles */
        .background-bubbles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bubble {
            position: absolute;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-100vh) rotate(360deg); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            transition: all var(--transition-speed) ease;
            transform-style: preserve-3d;
            transform: rotateX(-10deg) translateZ(50px);
            position: relative;
            z-index: 10;
        }

        .login-container:hover {
            transform: rotateX(0) translateZ(80px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.3);
        }

        .form-control {
            border-radius: 20px;
            padding: 10px 15px;
            transition: all var(--transition-speed) ease;
        }

        .form-control:focus {
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.3);
            border-color: var(--primary-color);
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 25px;
            padding: 12px;
            transition: transform var(--transition-speed) ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: var(--primary-color);
        }

        .alert {
            border-radius: 10px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .signup-link {
            color: var(--primary-color);
            text-decoration: none;
            transition: color var(--transition-speed) ease;
        }

        .signup-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Animated Background Bubbles -->
    <div class="background-bubbles" id="bubbleContainer"></div>

    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">
                <i class="fas fa-user-shield me-2" style="color: var(--primary-color);"></i>
                Saloon Kavisha Admin
            </h2>

            <?php if(isset($login_error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Username" required 
                               pattern="[a-zA-Z0-9_-]+" 
                               title="Username can only contain letters, numbers, underscores, and hyphens">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="signup.php" class="signup-link">Sign up here</a></p>
            </div>
        </div>
    </div>

    <script>
    // Animated Background Bubbles
    function createBubbles() {
        const container = document.getElementById('bubbleContainer');
        const bubbleCount = 20;

        for (let i = 0; i < bubbleCount; i++) {
            const bubble = document.createElement('div');
            bubble.classList.add('bubble');
            
            // Random size between 20 and 100 pixels
            const size = Math.random() * 80 + 20;
            bubble.style.width = `${size}px`;
            bubble.style.height = `${size}px`;
            
            // Random horizontal position
            bubble.style.left = `${Math.random() * 100}%`;
            
            // Random animation duration
            bubble.style.animationDuration = `${Math.random() * 20 + 10}s`;
            
            // Stagger the start time
            bubble.style.animationDelay = `${Math.random() * -20}s`;
            
            container.appendChild(bubble);
        }
    }

    // Create bubbles when page loads
    createBubbles();
    </script>
</body>
</html>