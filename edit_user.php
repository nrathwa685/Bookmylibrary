<?php
require "../session_check.php";

if ($_SESSION['role'] != "Admin") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User | Library System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../image/title_image.png" type="image/png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "JetBrains Mono", "Fira Code", Consolas, monospace;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(120deg, #0f172a, #1e3a8a);
            color: #333;
        }

        /* ================= MAIN ================= */
        .main-content {
            flex: 1;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
        }

        .page-header p {
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* ================= FORM ================= */
        .edit-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            width: 100%;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 30px;
        }

        /* Image Upload */
        .image-box {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: 0.2s ease;
            height: fit-content;
        }

        .image-box:hover {
            border-color: #2563eb;
            background: #f8fafc;
        }

        .image-box img {
            width: 160px;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 10px;
        }

        .image-box span {
            font-size: 13px;
            color: #64748b;
        }

        .image-box input {
            display: none;
        }

        /* Fields */
        .form-fields {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        input,
        select {
            padding: 11px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            outline: none;
            font-size: 14px;
            transition: 0.2s ease;
        }

        input:focus,
        select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
            display: none;
        }

        .valid {
            border-color: #22c55e !important;
        }

        .invalid {
            border-color: #ef4444 !important;
        }

        /* Buttons */
        .actions {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn {
            padding: 11px 20px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #334155;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .btn-save {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #ffffff;
        }

        .btn-save:hover {
            opacity: 0.9;
        }

        /* Breadcrumb Container */
        .breadcrumb-wrapper {
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 15px;
            margin-top: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        /* Breadcrumb Layout */
        .breadcrumb {
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        /* Dashboard */
        .breadcrumb .dashboard {
            color: #ef4444;
            font-weight: 600;
        }

        /* Separator */
        .breadcrumb .separator {
            color: #9ca3af;
        }

        /* Links */
        .breadcrumb a {
            text-decoration: none;
            color: #ef4444;
            transition: 0.2s ease;
        }

        .breadcrumb a:hover {
            text-decoration: none;
        }

        /* Current Page */
        .breadcrumb .current {
            color: #ffffffff;
            font-weight: 600;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1024px) {
            .form-fields {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-fields {
                grid-template-columns: 1fr;
            }

            .breadcrumb {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <?php include 'navbar.php'; ?>

    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb">
            <a href="home.php" class="dashboard">Dashboard</a>
            <span class="separator">›</span>
            <a href="user_list.php"><span class="dashboard">User List</span></a>
            <span class="separator">›</span>
            <span class="current">Edit User</span>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <?php
        $user_id = intval($_GET['user_id']);
        $user = mysqli_query($con, "SELECT * FROM user WHERE user_id = $user_id");
        $data = mysqli_fetch_assoc($user);

        ?>

        <div class="edit-card">
            <form id="editUserForm" action="#" method="POST" enctype="multipart/form-data">
                <div class="page-header">
                    <h2>Edit User</h2>
                    <p>Edit user details in your library system</p>
                </div>
                <div class="form-grid">

                    <!-- Image -->
                    <label class="image-box">
                        <img id="previewImage" src="../image/<?php echo $data['image']; ?>"><br>
                        <span>Click to upload profile image</span>
                        <input type="file" accept="image/*" id="imageInput" value="../book_images/<?php echo $data['image']; ?>" name="image">
                        <div class="error"></div>
                    </label>

                    <!-- Fields -->
                    <div class="form-fields">

                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" id="first_name" value="<?php echo $data['first_name']; ?>" name="first_name">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="last_name" value="<?php echo $data['last_name']; ?>" name="last_name">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="email" value="<?php echo $data['email']; ?>" name="email" disabled>
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="tel" id="contact_number" value="<?php echo $data['contact_no']; ?>" name="contact_number" maxlength="10">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <select id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" <?php if ($data['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if ($data['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                <option value="Other" <?php if ($data['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                            </select>
                            <div class="error"></div>
                        </div>


                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" id="address" value="<?php echo $data['address']; ?>" name="address">
                            <div class="error"></div>
                        </div>

                    </div>
                </div>

                <!-- Buttons -->
                <div class="actions">
                    <button type="reset" class="btn btn-cancel">Cancel</button>
                    <button type="submit" class="btn btn-save" name="save_btn">Save Change</button>
                </div>

            </form>
        </div>

    </div>

    <?php
    if (isset($_POST['save_btn'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];

        $upload_dir = '../image/';

        $old_image = $data['image'];

        // 🔹 Step 2: Check if new image uploaded
        if (!empty($_FILES['image']['name'])) {

            // Generate new image name
            $image = uniqid() . $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];

            // Delete old image if exists
            $oldImagePath = $upload_dir . $old_image;

            if (!empty($old_image) && file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            // Upload new image
            move_uploaded_file($image_tmp, $upload_dir . $image);
        } else {
            // If no new image uploaded, keep old image
            $image = $old_image;
        }

        $update_query = "UPDATE user SET 
                            first_name = '$first_name', 
                            last_name = '$last_name', 
                            email = '$email', 
                            contact_no = '$contact_number', 
                            gender = '$gender', 
                            address = '$address', 
                            image = '$image' 
                         WHERE user_id = $user_id";

        if (mysqli_query($con, $update_query)) {
            echo "<script>
                    previewImage.src = '../image/$image';
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'success',
                        title: 'User details updated successfully!',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didClose: () => {
                            window.location.href = 'user_list.php';
                        }
                    });
                </script>";
        } else {
            echo "<script>
                    previewImage.src = '../image/$image';
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'Failed to update user details. Please try again.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                </script>";
        }
    }
    ?>

    <!-- FOOTER -->
    <?php include 'footer.php'; ?>

    <script>
        const form = document.getElementById("editUserForm");
        const first_name = document.getElementById("first_name");
        const last_name = document.getElementById("last_name");
        const email = document.getElementById("email");
        const contact_number = document.getElementById("contact_number");
        const address = document.getElementById("address");
        const imageInput = document.getElementById("imageInput");
        const previewImage = document.getElementById("previewImage");
        const imageError = imageInput.parentElement.querySelector(".error");

        function showError(input, message) {
            const error = input.nextElementSibling;
            error.textContent = message;
            error.style.display = "block";
            input.classList.add("invalid");
            input.classList.remove("valid");
        }

        function showSuccess(input) {
            const error = input.nextElementSibling;
            error.style.display = "none";
            input.classList.remove("invalid");
            input.classList.add("valid");
        }

        function validateText(input) {
            const value = input.value.trim();
            const regex = /^[A-Za-z\s]+$/;

            if (value === "") {
                showError(input, "This field is required");
                return false;
            } else if (value.length < 2) {
                showError(input, "Minimum 2 characters required");
                return false;
            } else if (!regex.test(value)) {
                showError(input, "Only letters are allowed");
                return false;
            } else {
                showSuccess(input);
                return true;
            }
        }

        function validateEmail() {
            const emailValue = email.value.trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (emailValue === "") {
                showError(email, "Email is required");
                return false;
            } else if (!regex.test(emailValue)) {
                showError(email, "Enter a valid email address");
                return false;
            } else {
                showSuccess(email);
                return true;
            }
        }

        function validateContactNumber() {
            const contactValue = contact_number.value.trim();
            const regex = /^[0-9]+$/; // only digits

            if (contactValue === "") {
                showError(contact_number, "Contact number is required");
                return false;
            } else if (contactValue.length !== 10) {
                showError(contact_number, "Contact number must be 10 digits");
                return false;
            } else if (!regex.test(contactValue)) {
                showError(contact_number, "Only digits are allowed");
                return false;
            } else {
                showSuccess(contact_number);
                return true;
            }
        }

        function validateAddress() {
            const addressValue = address.value.trim();
            const regex = /^[a-zA-Z0-9\s,():-]+$/;

            if (addressValue === "") {
                showError(address, "Address is required");
                return false;
            } else if (addressValue.length < 5) {
                showError(address, "Address is not ot be too short");
                return false;
            } else if (!regex.test(addressValue)) {
                showError(address, "Enter valid address");
                return false;
            } else {
                showSuccess(address);
                return true;
            }
        }

        // show error in label
        function showImageError(message) {
            imageError.textContent = message;
            imageError.style.display = "block";
        }

        // clear error
        function clearImageError() {
            imageError.textContent = "";
            imageError.style.display = "none";
        }

        function validateImage(file) {
            const allowedTypes = /^(image\/jpeg|image\/jpg|image\/png)$/;
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!file) {
                showError(imageInput, "Please select an image");
                return false;
            }

            if (!allowedTypes.test(file.type)) {
                showError(imageInput, "Only JPG and PNG images are allowed");
                return false;
            }

            if (file.size > maxSize) {
                showError(imageInput, "Image size must be less than 2MB");
                return false;
            }

            showSuccess(imageInput);
            return true;
        }

        function validateSelect(input) {
            if (input.value === "") {
                showError(input, "Please select an option");
                return false;
            } else {
                showSuccess(input);
                return true;
            }
        }

        first_name.addEventListener("input", () => validateText(first_name));
        last_name.addEventListener("input", () => validateText(last_name));
        email.addEventListener("input", () => validateEmail(email));
        contact_number.addEventListener("input", () => validateContactNumber(contact_number));
        gender.addEventListener("change", () => validateSelect(gender));
        address.addEventListener("input", () => validateAddress(address));

        imageInput.addEventListener("change", function(event) {
            const reader = new FileReader();
            reader.onload = () => previewImage.src = reader.result;
            reader.readAsDataURL(event.target.files[0]);
        });

        form.addEventListener("submit", function(e) {


            const isValid =
                validateText(first_name) &
                validateText(last_name) &
                validateEmail(email) &
                validateContactNumber(contact_number) &
                validateAddress(address) &
                validateSelect(gender);

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>

</body>

</html>