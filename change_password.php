<?php
ob_start();
session_start();
include "db_config.php";
require "session_check.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $oldPassword = trim($_POST['oldPassword'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');

    if ($oldPassword === "" || $password === "" || $confirmPassword === "") {
        $message = "All fields are required";
        $message_type = "error";
    } elseif (!preg_match('/^[0-9]{6}$/', $oldPassword)) {
        $message = "Old password must be exactly 6 digits";
        $message_type = "error";
    } elseif (!preg_match('/^[0-9]{6}$/', $password)) {
        $message = "New password must be exactly 6 digits";
        $message_type = "error";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match";
        $message_type = "error";
    } elseif ($oldPassword === $password) {
        $message = "New password must be different from old password";
        $message_type = "error";
    } else {

        $user_id = $_SESSION['id'] ?? '';

        if ($user_id == '') {
            $message = "Session expired. Please login again.";
            $message_type = "error";
        } else {
            $select = mysqli_query($con, "SELECT password FROM user WHERE user_id='$user_id' LIMIT 1");

            if ($select && mysqli_num_rows($select) > 0) {
                $row = mysqli_fetch_assoc($select);
                $dbPassword = $row['password'];

                $isOldPasswordCorrect = false;

                // Supports both hashed password and old plain-text password
                if (password_verify($oldPassword, $dbPassword)) {
                    $isOldPasswordCorrect = true;
                } elseif ($oldPassword === $dbPassword) {
                    $isOldPasswordCorrect = true;
                }

                if (!$isOldPasswordCorrect) {
                    $message = "Old password is incorrect";
                    $message_type = "error";
                } else {
                    $hashed = password_hash($password, PASSWORD_BCRYPT);
                    $update = mysqli_query($con, "UPDATE user SET password='$hashed' WHERE user_id='$user_id'");

                    if ($update) {
                        $message = "Password changed successfully!";
                        $message_type = "success";
                    } else {
                        $message = "Failed to change password";
                        $message_type = "error";
                    }
                }
            } else {
                $message = "User not found";
                $message_type = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password | Library System</title>
    <link rel="icon" href="../image/title_image.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "JetBrains Mono", "Fira Code", Consolas, monospace;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #0f172a, #1e3a8a);
            display: flex;
            align-items: center;
            justify-content: center;
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
            position: relative;
        }

        .login-card h1 {
            text-align: center;
            color: #0f172a;
        }

        .close-btn {
            position: absolute;
            top: 12px;
            right: 15px;
            font-size: 22px;
            font-weight: bold;
            color: #555;
            cursor: pointer;
            transition: 0.3s;
        }

        .close-btn:hover {
            color: #ef4444;
            transform: scale(1.2);
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

        .error {
            color: #e63946;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .form-group input.error-input {
            border: 2px solid #e63946;
            background: #fff5f5;
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

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 45px;
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

        .back-to-login {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }

        .back-to-login a {
            color: #2563eb;
            text-decoration: none;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }

        form button {
            margin-top: 20px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">
            <span class="close-btn" onclick="goBack()">×</span>
            <p class="subtitle">Change Password to your library account</p>

            <form id="loginForm" method="POST">

                <div class="form-group password-group">
                    <label>Current Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="oldPassword" maxlength="6" name="oldPassword"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <i class="fa-solid fa-eye eye-icon" id="toggleOldPassword"></i>
                    </div>
                    <small class="error" id="oldPasswordError"></small>
                </div>

                <div class="form-group password-group">
                    <label>New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" maxlength="6" name="password"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <i class="fa-solid fa-eye eye-icon" id="togglePassword"></i>
                    </div>
                    <small class="error" id="passwordError"></small>
                </div>

                <div class="form-group password-group">
                    <label>Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirmPassword" maxlength="6" name="confirmPassword"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <i class="fa-solid fa-eye eye-icon" id="toggleConfirmPassword"></i>
                    </div>
                    <small class="error" id="confirmPasswordError"></small>
                </div>

                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const oldPassword = document.getElementById("oldPassword");
    const password = document.getElementById("password");
    const confirmpassword = document.getElementById("confirmPassword");

    const oldPasswordError = document.getElementById("oldPasswordError");
    const passwordError = document.getElementById("passwordError");
    const confirmPasswordError = document.getElementById("confirmPasswordError");

    const toggleOldPassword = document.getElementById("toggleOldPassword");
    const togglePassword = document.getElementById("togglePassword");
    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

    function validateOldPassword() {
        const value = oldPassword.value.trim();
        const digitPattern = /^[0-9]{6}$/;

        if (value === "") {
            oldPasswordError.textContent = "Old password is required";
            oldPassword.classList.add("error-input");
            return false;
        } else if (!digitPattern.test(value)) {
            oldPasswordError.textContent = "Old password must be exactly 6 digits";
            oldPassword.classList.add("error-input");
            return false;
        } else {
            oldPasswordError.textContent = "";
            oldPassword.classList.remove("error-input");
            return true;
        }
    }

    function validatePassword() {
        const value = password.value.trim();
        const digitPattern = /^[0-9]{6}$/;

        if (value === "") {
            passwordError.textContent = "New password is required";
            password.classList.add("error-input");
            return false;
        } else if (!digitPattern.test(value)) {
            passwordError.textContent = "New password must be exactly 6 digits";
            password.classList.add("error-input");
            return false;
        } else if (value === oldPassword.value.trim() && value !== "") {
            passwordError.textContent = "New password must be different from old password";
            password.classList.add("error-input");
            return false;
        } else {
            passwordError.textContent = "";
            password.classList.remove("error-input");
            return true;
        }
    }

    function validateConfirmPassword() {
        const value = confirmpassword.value.trim();

        if (value === "") {
            confirmPasswordError.textContent = "Confirm Password is required";
            confirmpassword.classList.add("error-input");
            return false;
        } else if (value !== password.value.trim()) {
            confirmPasswordError.textContent = "Confirm Passwords do not match";
            confirmpassword.classList.add("error-input");
            return false;
        } else {
            confirmPasswordError.textContent = "";
            confirmpassword.classList.remove("error-input");
            return true;
        }
    }

    oldPassword.addEventListener("input", () => {
        validateOldPassword();
        validatePassword();
    });

    password.addEventListener("input", () => {
        validatePassword();
        validateConfirmPassword();
    });

    confirmpassword.addEventListener("input", validateConfirmPassword);

    toggleOldPassword.addEventListener("click", () => {
        if (oldPassword.type === "password") {
            oldPassword.type = "text";
            toggleOldPassword.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            oldPassword.type = "password";
            toggleOldPassword.classList.replace("fa-eye-slash", "fa-eye");
        }
    });

    togglePassword.addEventListener("click", () => {
        if (password.type === "password") {
            password.type = "text";
            togglePassword.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            password.type = "password";
            togglePassword.classList.replace("fa-eye-slash", "fa-eye");
        }
    });

    toggleConfirmPassword.addEventListener("click", () => {
        if (confirmpassword.type === "password") {
            confirmpassword.type = "text";
            toggleConfirmPassword.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            confirmpassword.type = "password";
            toggleConfirmPassword.classList.replace("fa-eye-slash", "fa-eye");
        }
    });

    document.getElementById("loginForm").addEventListener("submit", function(e) {
        const isOldPasswordValid = validateOldPassword();
        const isPasswordValid = validatePassword();
        const isConfirmPasswordValid = validateConfirmPassword();

        if (!(isOldPasswordValid && isPasswordValid && isConfirmPasswordValid)) {
            e.preventDefault();
        }
    });

    function goBack() {
        window.history.back();
    }
</script>

<?php if ($message !== ""): ?>
    <script>
        Swal.fire({
            toast: true,
            position: 'top',
            icon: '<?php echo $message_type; ?>',
            title: '<?php echo $message; ?>',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didClose: () => {
                <?php if ($message_type === "success"): ?>
                    setTimeout(() => {
                        window.location.href = "login.php";
                    }, 300);
                <?php endif; ?>
            }
        });
    </script>
<?php endif; ?>

</html>
<?php ob_end_flush(); ?>