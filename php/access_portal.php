<?php
session_start();

include 'import/database.php';

// Set headers if needed
header('Content-Type: text/html; charset=UTF-8');

$error_message = ""; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'login') {
        $username = $_POST['log_username'];
        $password = $_POST['log_password'];

        // Search all_accounts view for username (name) and password
        $stmt = $conn->prepare("SELECT * FROM all_accounts WHERE name = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            // Redirect based on id prefix
            if (strpos($user['id'], 'C') === 0) {
                header("Location: customer/page1.php");
                exit;
            } elseif (strpos($user['id'], 'E') === 0) {
                // Redirect based on employee role
                if ($user['role'] === 'Manager') {
                    header("Location: employee/Einv.php");
                    exit;
                } elseif ($user['role'] === 'Cashier') {
                    header("Location: employee/Ecash.php");
                    exit;
                } elseif ($user['role'] === 'Admin') {
                    header("Location: employee/Eacc.php");
                    exit;
                } else {
                    header("Location: employee/page1.php"); // fallback for unknown role
                    exit;
                }
            } else {
                // fallback
                header("Location: dashboard.php");
                exit;
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } elseif ($action === 'register') {
        $username = $_POST['reg_username'];
        $email = $_POST['reg_email'];
        $phone = isset($_POST['reg_phone']) ? $_POST['reg_phone'] : '';
        $password = $_POST['reg_password'];
        $confirm_password = $_POST['reg_confirm_password'];

        if ($password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        } else {
            try {
                // Store password as plain text
                $stmt = $conn->prepare("INSERT INTO customer_accounts (name, password, email, phone_number) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$username, $password, $email, $phone])) {
                    echo "<script>console.log('Registration complete');</script>";
                    header("Location: access_portal.php");
                    exit;
                } else {
                    $error_message = "Error: Unable to register. Please try again later.";
                }
            } catch (PDOException $e) {
                $error_message = "Error: Unable to register. Please try again later.";
            }
        }
    } elseif ($action === 'guest') {
        // Login as guest_account (C101)
        $stmt = $conn->prepare("SELECT * FROM all_accounts WHERE id = 'C101' AND name = 'guest_account' AND password = 'password123'");
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: customer/page1.php");
            exit;
        } else {
            $error_message = "Guest account not found.";
        }
    }
}

// Ensure error message persists in the status container
if (!empty($error_message)) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusContainer = document.querySelector('.status_container');
            if (statusContainer) {
                statusContainer.classList.add('show_status');
            }
        });
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ACCESS PORTAL</title>
        <link rel="icon" href="../style/ToMart_Logo.png" type="image/x-icon">
        <link rel="stylesheet" href="../style/portal.css">
        <script src="https://www.google.com/recaptcha/enterprise.js?render=6LeD9xcrAAAAABd3Q1NbdwgV0iF4Adpj1NNzFlC1"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="../script/portal.js" defer></script>
    </head>
    <body>
        <div class="main_container">
            <div class="choice_container active_show">
                <div class="box">
                    <div class="logo">
                        <div class="logo_icon"></div>
                    </div>
                    <div class="sublabel">Access Portal</div>
                    <div class="choice">
                        <div class="choice_item" id="login_choice" onclick="toggleChoice('login')">Login</div>
                        <h3 style="user-select: none;">or</h3>
                        <div class="choice_item" id="register_choice" onclick="toggleChoice('register')">Register</div>
                    </div>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="action" value="guest">
                        <button type="submit" class="home_button" style="border:none;background:none;padding:0;cursor:pointer;">Continue as Guest</button>
                    </form>
                    <!--
                        TARGET: URGENT IF PAGE IS FINISHED
                        CODE: 00F02
                    -->
                </div>
            </div>
            <div class="recaptcha_container">
                <div class="box">
                    recaptcha
                    <!--Recaptcha for .php layout-->
                </div>
            </div>
            <div class="login_container">
                <div class="box">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="login">
                        <div class="text_box">
                            <span class="label">Welcome</span>
                            <span class="sublabel">Login to your account</span>
                        </div> 
                        <div class="input_box">
                            <div class="input_field">
                                <label for="log_username"><i class="fas fa-user"></i></label>
                                <input type="text" id="log_username" name="log_username" placeholder="Enter your username" required>
                            </div>
                            <div class="input_field">
                                <label for="password"><i class="fas fa-lock"></i></label>
                                <input type="password" id="log_password" name="log_password" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="choice_item submit_button">Login</button>
                        </div>
                    </form>
                    <div class="back_button" onclick="toggleChoice('choice')">Back</div>
                </div>
            </div>
            <div class="register_container">    
                <div class="box">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="register">
                        <div class="text_box">
                            <span class="label">Welcome</span>
                            <span class="sublabel">Create your account</span>
                        </div>
                        <div class="input_box">
                            <div class="input_field">
                                <label for="reg_username"><i class="fas fa-user"></i></label>
                                <input type="text" id="reg_username" name="reg_username" placeholder="Enter your username" required>
                            </div>
                            <div class="input_field">
                                <label for="reg_email"><i class="fas fa-envelope"></i></label>
                                <input type="email" id="reg_email" name="reg_email" placeholder="Enter your email" required>
                            </div>
                            <div class="input_field">
                                <label for="reg_phone"><i class="fas fa-phone"></i></label>
                                <input type="text" id="reg_phone" name="reg_phone" placeholder="Enter your phone number" required>
                            </div>
                            <div class="input_field">
                                <label for="reg_password"><i class="fas fa-lock"></i></label>
                                <input type="password" id="reg_password" name="reg_password" placeholder="Enter your password" required>
                            </div>
                            <div class="input_field">
                                <label for="reg_confirm_password"><i class="fas fa-lock"></i></label>
                                <input type="password" id="reg_confirm_password" name="reg_confirm_password" placeholder="Confirm your password" required>
                            </div>
                            <button type="submit" class="choice_item submit_button">Register</button>
                        </div>
                    </form>
                    <div class="back_button" onclick="toggleChoice('choice')">Back</div>
                </div>
            </div>
            <div class="status_container">
                <div class="box">
                    <div class="text_box">
                        <span class="sublabel">NOTICE</span>
                        <div class="label_status">
                            <?php if (!empty($error_message)): ?>
                                <p><?php echo htmlspecialchars($error_message); ?></p>
                                <script>
                                    Checkstatus();
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="back_button" onclick="Checkstatus()">Close Notice</div>
                </div>
            </div>
        </div>
    </body>
</html>