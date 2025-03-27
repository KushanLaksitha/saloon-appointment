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

// Signup Processing
$signup_error = '';
$signup_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $username = sanitize_input($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation checks
    $errors = [];

    // Enhanced username validation
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters long";
    } elseif (strlen($username) > 50) {
        $errors[] = "Username cannot exceed 50 characters";
    } elseif (!preg_match("/^[a-zA-Z0-9_-]+$/", $username)) {
        $errors[] = "Username can only contain letters, numbers, underscores, and hyphens";
    }

    // Enhanced email validation
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } elseif (strlen($email) > 100) {
        $errors[] = "Email address is too long";
    }

    // Enhanced password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 12) {
        $errors[] = "Password must be at least 12 characters long";
    } elseif (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?]).{12,}$/", $password)) {
        $errors[] = "Password must include uppercase, lowercase, number, and special character";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if username or email already exists
    if (empty($errors)) {
        try {
            $check_stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? OR email = ?");
            $check_stmt->bind_param("ss", $username, $email);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                $errors[] = "Username or email already exists";
            }
            $check_stmt->close();
        } catch (Exception $e) {
            $errors[] = "Database error occurred";
        }
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Enhanced password hashing with additional parameters
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        try {
            // Prepare SQL to insert new admin user with additional security
            $insert_stmt = $conn->prepare("INSERT INTO admin_users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())");
            $insert_stmt->bind_param("sss", $username, $email, $password_hash);

            if ($insert_stmt->execute()) {
                $signup_success = "Registration successful! You can now log in.";
                
                // Optional: Log registration attempt
                error_log("New admin user registered: " . $username);
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
            $insert_stmt->close();
        } catch (Exception $e) {
            $errors[] = "Registration error: " . $e->getMessage();
        }
    }

    // Prepare error messages
    if (!empty($errors)) {
        $signup_error = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saloon Kavisha - Admin Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }

        .signup-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            transition: all var(--transition-speed) ease;
            transform-style: preserve-3d;
            transform: rotateX(-10deg) translateZ(50px);
        }

        .signup-container:hover {
            transform: rotateX(0) translateZ(80px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.3);
        }

        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            background: linear-gradient(to right, #ff4136, #ff851b, #2ecc40);
            transition: width var(--transition-speed) ease-in-out;
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

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .alert {
            border-radius: 10px;
            animation: pulse 1.5s infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-container">
            <h2 class="text-center mb-4">Admin Signup - Saloon Kavisha</h2>
            
            <?php if(!empty($signup_error)): ?>
                <div class="alert alert-danger"><?php echo $signup_error; ?></div>
            <?php endif; ?>

            <?php if(!empty($signup_success)): ?>
                <div class="alert alert-success"><?php echo $signup_success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="signupForm">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" 
                           required minlength="4" maxlength="50"
                           value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           required
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           required minlength="12" 
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?]).{12,}"
                           title="Must contain at least one number, one uppercase and lowercase letter, one special character, and be at least 12 characters long">
                    <div id="passwordStrength" class="password-strength"></div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                           required minlength="12">
                </div>
                <button type="submit" class="btn btn-primary w-100">Create Admin Account</button>
            </form>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('passwordStrength');
        
        // Enhanced password strength calculation
        let strength = 0;
        if (password.length >= 12) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?]+/)) strength++;

        // Update strength bar width and color dynamically
        strengthBar.style.width = `${strength * 20}%`;
        
        switch(strength) {
            case 0:
            case 1:
                strengthBar.style.background = 'linear-gradient(to right, #ff4136, #ff4136)';
                break;
            case 2:
            case 3:
                strengthBar.style.background = 'linear-gradient(to right, #ff4136, #ff851b)';
                break;
            case 4:
            case 5:
                strengthBar.style.background = 'linear-gradient(to right, #ff4136, #ff851b, #2ecc40)';
                break;
        }
    });

    document.getElementById('signupForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match');
        }
    });
    </script>
</body>
</html>