<!DOCTYPE html>
<html lang="en">
<?php include 'db_config.php'; ?>

<head>
    <meta charset="UTF-8">
    <title>Login | Library System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="image/title_image.png" type="image/png">
</head>

<?php
session_start();
$_SESSION = []; // clear previous sessions
if (isset($_POST['login_btn'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {

        $user_data = mysqli_fetch_assoc($result);
        if (password_verify($password, $user_data['password'])) {

            if ($user_data['status'] == "Active") {

                $_SESSION['id'] = $user_data['user_id'];
                $_SESSION['role'] = $user_data['role'];

                if ($user_data['role'] == "User") {
                    setcookie("success", "Login successful", time() + 2);
                    header("Location: user/home.php");
                    exit();
                } else if ($user_data['role'] == "Admin") {
                    setcookie("success", "Login successful", time() + 2);
                    header("Location: admin/home.php");
                    exit();
                } else {
                    setcookie("success", "Login successful", time() + 2);
                    header("Location: librarian/home.php");
                    exit();
                }
            } else {

                setcookie("error", "Your account is inactive. Please contact the administrator.", time() + 2);
                header("Location: login.php");
                exit();
            }
        } else {
            setcookie("error", "Invalid email or password", time() + 2);
            header("Location: login.php");
            exit();
        }
    } else {

        setcookie("error", "Invalid email or password", time() + 2);
        header("Location: login.php");
        exit();
    }
}
?>

<?php
if (isset($_GET['timeout'])) {
    echo "<script>
    alert('Session expired. Please login again.');
    </script>";
}
?>

<body>

    <?php
    if (isset($_COOKIE['success'])) {
    ?>
        <div class="alert-box alert-success">
            <span><?php echo $_COOKIE['success']; ?></span>
            <span class="alert-close" onclick="this.parentElement.remove()">&times;</span>
        </div>
    <?php
        setcookie("success", "", time() - 3600);
    }

    if (isset($_COOKIE['error'])) {
    ?>
        <div class="alert-box alert-error">
            <span><?php echo $_COOKIE['error']; ?></span>
            <span class="alert-close" onclick="this.parentElement.remove()">&times;</span>
        </div>
    <?php
        setcookie("error", "", time() - 3600);
    }
    ?>

    <div class="login-wrapper">
        <div class="login-card">
            <h1>Welcome Back</h1>
            <p class="subtitle">Login to your account</p>

            <form id="loginForm" method="POST">

                <!-- Email -->
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="email" name="email">
                    <small class="error" id="emailError"></small>
                </div>

                <!-- Password -->
                <div class="form-group password-group">
                    <label>Password</label>

                    <div class="password-wrapper">
                        <input type="password" id="password" maxlength="6" name="password">

                        <i class="fa-solid fa-eye eye-icon" id="togglePassword"></i>
                    </div>

                    <small class="error" id="passwordError"></small>
                </div>
                <!-- Remember & Forgot -->
                <div class="form-options">
                    <label class="remember">
                        <input type="checkbox" id="rememberMe">
                        Remember me
                    </label>

                    <a href="forgot_password.php" class="forgot">Forgot password?</a>
                </div>

                <button type="submit" name="login_btn">Login</button>

                <p class="back-to-login">
                    Don’t have an account?
                    <a href="register.php">Create one</a>
                </p>

            </form>
        </div>
    </div>

    <script>
        const email = document.getElementById("email");
        const password = document.getElementById("password");

        const emailError = document.getElementById("emailError");
        const passwordError = document.getElementById("passwordError");

        const togglePassword = document.getElementById("togglePassword");

        /* Email Validation */
        function validateEmail() {
            const value = email.value.trim();
            const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;

            if (value === "") {
                emailError.textContent = "Email is required";
                email.classList.add("error-input");
                return false;
            } else if (!emailPattern.test(value)) {
                emailError.textContent = "Enter a valid email address";
                email.classList.add("error-input");
                return false;
            } else {
                emailError.textContent = "";
                email.classList.remove("error-input");
                return true;
            }
        }

        /* Password Validation */
        function validatePassword() {
            const value = password.value.trim();
            const digitPattern = /^[0-9]{6}$/;

            if (value === "") {
                passwordError.textContent = "Password is required";
                password.classList.add("error-input");
                return false;
            } else if (!digitPattern.test(value)) {
                passwordError.textContent = "Password must be exactly 6 digits";
                password.classList.add("error-input");
                return false;
            } else {
                passwordError.textContent = "";
                password.classList.remove("error-input");
                return true;
            }
        }


        /* Live validation */
        email.addEventListener("input", validateEmail);
        password.addEventListener("input", validatePassword);

        /* Show / Hide password */
        togglePassword.addEventListener("click", () => {
            if (password.type === "password") {
                password.type = "text";
                togglePassword.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                password.type = "password";
                togglePassword.classList.replace("fa-eye-slash", "fa-eye");
            }
        });

        /* Submit Validation (SHOW ALL ERRORS) */
        document.getElementById("loginForm").addEventListener("submit", function(e) {

            const isEmailValid = validateEmail();
            const isPasswordValid = validatePassword();

            if (!isEmailValid || !isPasswordValid) {
                e.preventDefault(); // stop submit only if invalid
                return;
            }

        });

        setTimeout(() => {
            document.querySelectorAll(".alert-box").forEach(alert => {
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 400);
            });
        }, 5000);
    </script>
</body>


</html>