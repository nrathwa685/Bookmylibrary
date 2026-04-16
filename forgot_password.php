<?php
session_start();
include "db_config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

/* =========================
   MAIL FUNCTION
========================= */
function sendOtpMail($to, $otp)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dasodariya899@rku.ac.in';   // your gmail
        $mail->Password   = 'impt ujku nrtp taee';      // your gmail app password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('dasodariya899@rku.ac.in', 'Book My Library');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'OTP for Forgot Password';
        $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 650px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; background: #ffffff;'>

                    <div style='background: linear-gradient(135deg, #0f172a, #1e3a8a); color: #fff; padding: 20px; text-align: center;'>
                        <h2 style='margin: 0;'>🔐 Password Reset OTP</h2>
                    </div>

                    <div style='padding: 20px; color: #333;'>

                        <p>Hello,</p>
                        <p>You requested to reset your password for <strong>Book My Library</strong>.</p>

                        <div style='margin: 25px 0; text-align: center;'>
                            <p style='margin-bottom: 10px;'>Your OTP is:</p>
                            <div style='display: inline-block; padding: 15px 25px; font-size: 24px; font-weight: bold; letter-spacing: 5px; background: #f1f5f9; border: 2px dashed #1e3a8a; border-radius: 8px; color: #1e3a8a;'>
                                $otp
                            </div>
                        </div>

                        <p style='text-align: center; color: #555;'>
                            This OTP is valid for <strong>1 minute</strong>.
                        </p>

                        <div style='margin-top: 25px; padding: 15px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 8px; color: #856404;'>
                            ⚠️ Do not share this OTP with anyone. If you did not request this, please ignore this email.
                        </div>

                        <p style='margin-top: 25px;'>Thank you,<br><strong>Book My Library</strong></p>

                    </div>
                </div>
                ";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

function sendPasswordChangedMail($to)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dasodariya899@rku.ac.in';
        $mail->Password   = 'impt ujku nrtp taee';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('dasodariya899@rku.ac.in', 'Book My Library');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Password Changed Successfully';
        $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 650px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; background: #ffffff;'>

                    <div style='background: linear-gradient(135deg, #0f172a, #1e3a8a); color: #fff; padding: 20px; text-align: center;'>
                        <h2 style='margin: 0;'>🔐 Password Updated</h2>
                    </div>

                    <div style='padding: 20px; color: #333;'>
                        <p>Hello,</p>

                        <p>Your password has been successfully changed.</p>

                        <div style='margin-top: 20px; padding: 15px; background: #e6fffa; border: 1px solid #b2f5ea; border-radius: 8px;'>
                            ✅ You can now login using your new password.
                        </div>

                        <div style='margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 8px; color: #856404;'>
                            ⚠️ For security reasons, we do not display your password.
                        </div>

                        <p style='margin-top: 25px;'>Thank you,<br><strong>Book My Library</strong></p>
                    </div>
                </div>
                ";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
/* =========================
   AJAX REQUEST HANDLER
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header("Content-Type: application/json");

    $action = $_POST['action'];

    // SEND OTP
    if ($action === "send_otp") {
        $email = trim($_POST['email']);

        if (empty($email)) {
            echo json_encode(["status" => "error", "message" => "Email is required"]);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email format"]);
            exit;
        }

        // Change this query as per your table structure
        $check = mysqli_query($con, "SELECT * FROM user WHERE email='$email'");

        if (!$check || mysqli_num_rows($check) == 0) {
            echo json_encode(["status" => "error", "message" => "Email not found"]);
            exit;
        }

        $otp = rand(100000, 999999);
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 minute"));

        // Store in session
        $_SESSION['forgot_email'] = $email;
        $_SESSION['forgot_otp'] = $otp;
        $_SESSION['forgot_otp_expiry'] = $expires_at;
        $_SESSION['otp_verified'] = false;

        if (sendOtpMail($email, $otp)) {
            echo json_encode(["status" => "success", "message" => "OTP sent successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to send OTP"]);
        }
        exit;
    }

    // VERIFY OTP
    if ($action === "verify_otp") {
        $otp = trim($_POST['otp']);

        if (empty($otp)) {
            echo json_encode(["status" => "error", "message" => "OTP is required"]);
            exit;
        }

        if (!isset($_SESSION['forgot_otp']) || !isset($_SESSION['forgot_otp_expiry'])) {
            echo json_encode(["status" => "error", "message" => "Please send OTP first"]);
            exit;
        }

        if (date("Y-m-d H:i:s") > $_SESSION['forgot_otp_expiry']) {
            unset($_SESSION['forgot_otp'], $_SESSION['forgot_otp_expiry']);
            echo json_encode(["status" => "error", "message" => "OTP expired"]);
            exit;
        }

        if ($otp != $_SESSION['forgot_otp']) {
            echo json_encode(["status" => "error", "message" => "Invalid OTP"]);
            exit;
        }

        $_SESSION['otp_verified'] = true;
        echo json_encode(["status" => "success", "message" => "OTP verified successfully"]);
        exit;
    }

    // RESET PASSWORD
    // RESET PASSWORD
    if ($action === "reset_password") {
        $newPassword = trim($_POST['new_password']);
        $confirmPassword = trim($_POST['confirm_password']);

        if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
            echo json_encode(["status" => "error", "message" => "Please verify OTP first"]);
            exit;
        }

        if (empty($newPassword) || empty($confirmPassword)) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit;
        }

        if (!preg_match('/^\d{6}$/', $newPassword)) {
            echo json_encode(["status" => "error", "message" => "Password must be 6 digits"]);
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
            exit;
        }

        $email = $_SESSION['forgot_email'];

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $update = mysqli_query($con, "UPDATE user SET password='$hashed' WHERE email='$email'");

        if ($update) {

            // send password changed confirmation mail
            sendPasswordChangedMail($email);

            unset(
                $_SESSION['forgot_email'],
                $_SESSION['forgot_otp'],
                $_SESSION['forgot_otp_expiry'],
                $_SESSION['otp_verified']
            );

            echo json_encode(["status" => "success", "message" => "Password reset successful"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to reset password"]);
        }
        exit;
    }

    echo json_encode(["status" => "error", "message" => "Invalid action"]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Library System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/title_image.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "JetBrains Mono", "Fira Code", Consolas, monospace;
        }

        body {
            background: linear-gradient(120deg, #0f172a, #1e3a8a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            padding: 20px;
        }

        .login-card {
            max-width: 420px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 25px 40px rgba(0, 0, 0, 0.25);
        }

        .login-card h1 {
            text-align: center;
            color: #0f172a;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 30px;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2563eb;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 42px;
        }

        .eye-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #64748b;
        }

        .eye-icon:hover {
            color: #2563eb;
        }

        .error {
            color: red;
            display: block;
            margin-top: 5px;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #1e40af;
        }

        button:disabled {
            background: #9bbcf0;
            cursor: not-allowed;
        }

        #message {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }

        #otpTimer {
            text-align: center;
            margin-top: 10px;
            font-size: 13px;
            color: #d9534f;
        }

        .back-to-login {
            margin-top: 18px;
            text-align: center;
        }

        .back-to-login a {
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }

        .otp-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 8px;
        }

        .otp-box {
            width: 48px !important;
            height: 52px;
            padding: 0 !important;
            text-align: center;
            font-size: 22px !important;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            outline: none;
            background: #f8fafc;
            color: #0f172a;
            transition: 0.2s ease;
        }

        .otp-box:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            background: #fff;
        }

        .otp-box.filled {
            border-color: #2563eb;
            background: #eff6ff;
        }

        @media (max-width: 480px) {
            .otp-container {
                gap: 6px;
            }

            .otp-box {
                width: 40px !important;
                height: 46px;
                font-size: 18px !important;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h1>Forgot Password</h1>

            <!-- SEND OTP FORM -->
            <form id="forgotForm" novalidate>
                <p class="subtitle">Enter your registered email to receive OTP</p>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="email" name="email">
                    <small class="error" id="emailError"></small>
                </div>

                <button type="submit" id="sendOtpBtn">Send OTP</button>
                <div id="otpTimer"></div>
            </form>

            <!-- VERIFY OTP FORM -->
            <!-- VERIFY OTP FORM -->
            <form id="otpForm" style="display:none;" novalidate>
                <p class="subtitle">Enter OTP received on your email</p>

                <div class="form-group">
                    <label>Enter OTP</label>

                    <div class="otp-container">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric">
                    </div>

                    <input type="hidden" id="otp">
                    <small class="error" id="otpError"></small>
                </div>

                <button type="submit">Verify OTP</button>
            </form>

            <!-- RESET PASSWORD FORM -->
            <form id="resetForm" style="display:none;" novalidate>
                <p class="subtitle">Enter new password</p>

                <div class="form-group">
                    <label>New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="newPassword" maxlength="6" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <i class="fa-solid fa-eye eye-icon" id="toggleNewPassword"></i>
                    </div>
                    <small class="error" id="newPasswordError"></small>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirmPassword" maxlength="6" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <i class="fa-solid fa-eye eye-icon" id="toggleConfirmPassword"></i>
                    </div>
                    <small class="error" id="confirmPasswordError"></small>
                </div>

                <button type="submit">Reset Password</button>
            </form>

            <p id="message"></p>

            <p class="back-to-login">
                <a href="login.php"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
            </p>
        </div>
    </div>

    <script>
        const forgotForm = document.getElementById("forgotForm");
        const otpForm = document.getElementById("otpForm");
        const resetForm = document.getElementById("resetForm");

        const email = document.getElementById("email");
        const otp = document.getElementById("otp"); // hidden input
        const otpInputs = document.querySelectorAll(".otp-box");

        const newPassword = document.getElementById("newPassword");
        const confirmPassword = document.getElementById("confirmPassword");

        const emailError = document.getElementById("emailError");
        const otpError = document.getElementById("otpError");
        const newPasswordError = document.getElementById("newPasswordError");
        const confirmPasswordError = document.getElementById("confirmPasswordError");

        const message = document.getElementById("message");
        const otpTimer = document.getElementById("otpTimer");
        const sendOtpBtn = document.getElementById("sendOtpBtn");

        let timerInterval;
        let timeLeft = 60;

        // EMAIL VALIDATION
        email.addEventListener("input", function() {
            const value = email.value.trim();
            const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (value === "") {
                emailError.textContent = "Email is required";
            } else if (!pattern.test(value)) {
                emailError.textContent = "Enter valid email address";
            } else {
                emailError.textContent = "";
            }
        });

        // OTP FUNCTIONS
        function getOtpValue() {
            return Array.from(otpInputs).map(input => input.value).join("");
        }

        function validateOtpBoxes() {
            const value = getOtpValue();
            otp.value = value;

            if (value === "") {
                otpError.textContent = "OTP is required";
            } else if (!/^\d{6}$/.test(value)) {
                otpError.textContent = "OTP must be 6 digits";
            } else {
                otpError.textContent = "";
            }
        }

        otpInputs.forEach((input, index) => {
            input.addEventListener("input", function() {
                this.value = this.value.replace(/[^0-9]/g, "").slice(0, 1);

                if (this.value !== "") {
                    this.classList.add("filled");
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                } else {
                    this.classList.remove("filled");
                }

                validateOtpBoxes();
            });

            input.addEventListener("keydown", function(e) {
                if (e.key === "Backspace") {
                    if (this.value === "" && index > 0) {
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = "";
                        otpInputs[index - 1].classList.remove("filled");
                    } else {
                        this.value = "";
                        this.classList.remove("filled");
                    }
                    validateOtpBoxes();
                }

                if (e.key === "ArrowLeft" && index > 0) {
                    otpInputs[index - 1].focus();
                }

                if (e.key === "ArrowRight" && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener("paste", function(e) {
                e.preventDefault();

                const pastedData = (e.clipboardData || window.clipboardData)
                    .getData("text")
                    .replace(/[^0-9]/g, "")
                    .slice(0, 6);

                if (!pastedData) return;

                otpInputs.forEach((box, i) => {
                    box.value = pastedData[i] || "";
                    if (box.value !== "") {
                        box.classList.add("filled");
                    } else {
                        box.classList.remove("filled");
                    }
                });

                otp.value = getOtpValue();
                validateOtpBoxes();

                const nextIndex = Math.min(pastedData.length, otpInputs.length - 1);
                otpInputs[nextIndex].focus();
            });
        });

        // PASSWORD VALIDATION
        function validatePasswords() {
            const pass = newPassword.value.trim();
            const confirm = confirmPassword.value.trim();

            if (pass === "") {
                newPasswordError.textContent = "New password is required";
            } else if (!/^\d{6}$/.test(pass)) {
                newPasswordError.textContent = "Password must be 6 digits";
            } else {
                newPasswordError.textContent = "";
            }

            if (confirm === "") {
                confirmPasswordError.textContent = "Confirm password is required";
            } else if (pass !== confirm) {
                confirmPasswordError.textContent = "Passwords do not match";
            } else {
                confirmPasswordError.textContent = "";
            }
        }

        newPassword.addEventListener("input", validatePasswords);
        confirmPassword.addEventListener("input", validatePasswords);

        // SEND OTP
        forgotForm.addEventListener("submit", function(e) {
            e.preventDefault();

            const emailValue = email.value.trim();
            const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (emailValue === "") {
                emailError.textContent = "Email is required";
                return;
            }

            if (!pattern.test(emailValue)) {
                emailError.textContent = "Enter valid email address";
                return;
            }

            emailError.textContent = "";
            message.style.color = "";
            message.textContent = "";

            const formData = new FormData();
            formData.append("action", "send_otp");
            formData.append("email", emailValue);

            fetch("", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        message.style.color = "green";
                        message.textContent = data.message;

                        otpForm.style.display = "block";
                        resetForm.style.display = "none";

                        otpInputs.forEach(input => {
                            input.value = "";
                            input.classList.remove("filled");
                        });

                        otp.value = "";
                        otpError.textContent = "";
                        otpInputs[0].focus();

                        startTimer();
                    } else {
                        message.style.color = "red";
                        message.textContent = data.message;
                    }
                })
                .catch(() => {
                    message.style.color = "red";
                    message.textContent = "Something went wrong";
                });
        });

        // VERIFY OTP
        otpForm.addEventListener("submit", function(e) {
            e.preventDefault();

            const otpValue = getOtpValue();
            otp.value = otpValue;

            if (otpValue === "") {
                otpError.textContent = "OTP is required";
                return;
            }

            if (!/^\d{6}$/.test(otpValue)) {
                otpError.textContent = "OTP must be 6 digits";
                return;
            }

            otpError.textContent = "";

            const formData = new FormData();
            formData.append("action", "verify_otp");
            formData.append("otp", otpValue);

            fetch("", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        message.style.color = "green";
                        message.textContent = data.message;

                        otpForm.style.display = "none";
                        resetForm.style.display = "block";

                        clearInterval(timerInterval);
                        otpTimer.textContent = "";
                    } else {
                        message.style.color = "red";
                        message.textContent = data.message;
                    }
                })
                .catch(() => {
                    message.style.color = "red";
                    message.textContent = "Something went wrong";
                });
        });

        // RESET PASSWORD
        resetForm.addEventListener("submit", function(e) {
            e.preventDefault();

            validatePasswords();

            if (newPasswordError.textContent !== "" || confirmPasswordError.textContent !== "") {
                return;
            }

            const formData = new FormData();
            formData.append("action", "reset_password");
            formData.append("new_password", newPassword.value.trim());
            formData.append("confirm_password", confirmPassword.value.trim());

            fetch("", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        message.style.color = "green";
                        message.textContent = data.message;

                        setTimeout(() => {
                            window.location.href = "login.php";
                        }, 1500);
                    } else {
                        message.style.color = "red";
                        message.textContent = data.message;
                    }
                })
                .catch(() => {
                    message.style.color = "red";
                    message.textContent = "Something went wrong";
                });
        });

        // OTP TIMER
        function startTimer() {
            clearInterval(timerInterval);
            timeLeft = 60;
            sendOtpBtn.disabled = true;

            timerInterval = setInterval(() => {
                otpTimer.textContent = "OTP expires in " + timeLeft + " seconds";
                timeLeft--;

                if (timeLeft < 0) {
                    clearInterval(timerInterval);
                    otpTimer.textContent = "OTP expired. Please send OTP again.";
                    sendOtpBtn.disabled = false;
                }
            }, 1000);
        }

        // TOGGLE PASSWORD
        document.getElementById("toggleNewPassword").addEventListener("click", function() {
            togglePassword("newPassword", this);
        });

        document.getElementById("toggleConfirmPassword").addEventListener("click", function() {
            togglePassword("confirmPassword", this);
        });

        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>