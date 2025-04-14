<?php
session_start();

// Include necessary files (e.g., database connection, helper functions)
// require_once 'path_to_file.php';

// Set headers if needed
header('Content-Type: text/html; charset=UTF-8');

// Your PHP logic goes here

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACCESS PORTAL</title>
    <link rel="stylesheet" href="../style/portal.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="../script/portal.js" defer></script>
</head>
<body>
    <div class="main_container">

        <div class="choice_container active_show">
            <div class="box">
                <div class="logo">
                    LOGO
                </div>
                <div class="choice">
                    <div class="choice_item" id="login_choice" onclick="toggleChoice('login')">Login</div>
                    <h3 style="user-select: none;">or</h3>
                    <div class="choice_item" id="register_choice" onclick="toggleChoice('register')">Register</div>
                </div>
                <a href="index.html" class="home_button">Continue as Guest</a>
            </div>
        </div>
        <div class="recaptcha_container">
            <div class="box">
                <!--Recaptcha for .php layout-->
            </div>
        </div>
        <div class="login_container">
            <div class="box">
                <div class="text_box">
                    <span class="label">Welcome</span>
                    <span class="sublabel">Login to your create account</span>
                </div>
                <div class="input_box">
                    <div class="input_field">
                        <label for="username"><i class="fas fa-user"></i></label>
                        <input type="text" id="username" placeholder="Enter your username" required>
                    </div>
                    <div class="input_field">
                        <label for="password"><i class="fas fa-lock"></i></label>
                        <input type="password" id="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="choice_item submit_button" onclick="Checkstatus()">Login</button>
                </div>
                <div class="back_button" onclick="toggleChoice('choice')">Back</div>
            </div>
        </div>
        <div class="register_container">    
            <div class="box">
                <div class="text_box">
                    <span class="label">Welcome</span>
                    <span class="sublabel">Create your account</span>
                </div>
                <div class="input_box">
                    <div class="input_field">
                        <label for="username"><i class="fas fa-user"></i></label>
                        <input type="text" id="username" placeholder="Enter your username" required>
                    </div>
                    <div class="input_field">
                        <label for="email"><i class="fas fa-envelope"></i></label>
                        <input type="email" id="email" placeholder="Enter your email" required>
                    </div>
                    <div class="input_field">
                        <label for="password"><i class="fas fa-lock"></i></label>
                        <input type="password" id="password" placeholder="Enter your password" required>
                    </div>
                    <div class="input_field">
                        <label for="confirm_password"><i class="fas fa-lock"></i></label>
                        <input type="password" id="confirm_password" placeholder="Confirm your password" required>
                    </div>
                    <button type="submit" class="choice_item submit_button" onclick="Checkstatus()">Register</button>
                </div>
                <div class="back_button" onclick="toggleChoice('choice')">Back</div>
            </div>
        </div>
        <div class="status_container">
            <div class="box">
                <div class="text_box">
                    <span class="sublabel">NOTICE</span>
                    <div class="label_status">
                        <p>Button is pressed here</p>
                    </div>
                </div>
                <div class="back_button" onclick="Checkstatus()">Close Notice</div>
            </div>
        </div>
    </div>
</body>
</html>