<?php
require "../session_check.php";

if ($_SESSION['role'] != "User") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Library System</title>
    <link rel="icon" href="../image/title_image.png" type="image/png">


    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "JetBrains Mono", "Fira Code", Consolas, monospace;
        }

        body {
            background: linear-gradient(120deg, #0f172a, #1e3a8a);
        }

        /* Main Layout */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            display: flex;
            gap: 30px;
        }

        /* LEFT PROFILE */
        .profile-left {
            width: 300px;
            text-align: center;
            border-right: 1px solid #ddd;
            padding-right: 20px;
        }

        .profile-img img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Modal background */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
        }

        /* Popup card */
        .modal-content {
            background: #fff;
            width: 380px;
            margin: 6% auto;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Close button */
        .close {
            float: right;
            font-size: 22px;
            cursor: pointer;
        }

        /* Drag area */
        .drop-area {
            border: 2px dashed #16a34a;
            padding: 25px;
            border-radius: 10px;
            margin-top: 15px;
            background: #f8fafc;
            transition: 0.3s;
        }

        .drop-area.dragover {
            background: #eef2ff;
            border-color: #16a34a;
        }

        .drop-area p {
            margin: 0;
            font-weight: 600;
        }

        .drop-area span {
            display: block;
            margin: 10px 0;
            color: #777;
        }

        /* Hidden file input */
        #fileInput {
            display: none;
        }

        /* Browse button */
        .browse-btn {
            background: #16a34a;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-block;
        }

        /* Preview */
        .preview-area img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-top: 15px;
            object-fit: cover;
            display: none;
        }

        /* Save button */
        .save-btn {
            margin-top: 15px;
            padding: 10px 18px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .cancel-btn {
            margin-top: 15px;
            padding: 10px 18px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .profile-left h2 {
            margin-top: 15px;
        }

        .profile-left p {
            color: #555;
            margin-bottom: 15px;
        }

        .change-btn {
            background: #16a34a;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
        }

        .remove-btn {
            background: #ef4444;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            margin-top: 5px;
            color: white;
            cursor: pointer;
        }

        /* RIGHT SIDE */
        .profile-right {
            flex: 1;
        }

        /* Card Sections */
        .card {
            background: #fff;
            padding: 28px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card h3 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .sub {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
        }

        .p {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            width: 205.5px;
        }

        /* validation */
        .error {
            color: red;
            font-size: 12px;
            margin-top: 4px;
        }

        input.invalid {
            border: 1px solid red;
        }

        input.valid {
            border: 1px solid green;
        }

        /* Edit Button */
        .edit-btn {
            background: #2563eb;
            color: white;
            padding: 11px 22px;
            border-radius: 10px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
            float: right;
        }

        input:disabled {
            background: #f1f3f5;
            cursor: not-allowed;
        }

        /* Responsive */
        @media(max-width:900px) {
            .container {
                flex-direction: column;
            }

            .profile-left {
                border-right: none;
                border-bottom: 1px solid #ddd;
                padding-bottom: 20px;
            }

            orm-grid {
                grid-template-columns: 1fr;
            }
        }

        .edit-card {
            background: #ffffff;
            padding: 28px;
            border-radius: 14px;
        }

        /* Header */
        .edit-header h3 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .edit-header p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        /* Form Layout */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #000000;
        }

        /* Inputs */
        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 12px 14px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            font-size: 14px;
            transition: 0.2s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #2563eb;
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }

        /* Full width address */
        .full-width {
            grid-column: span 2;
        }

        /* Buttons */
        .edit-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 25px;
        }

        .save-btn {
            background: #2563eb;
            color: white;
            padding: 11px 22px;
            border-radius: 10px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }


        .cancel-btn {
            background: #e5e7eb;
            color: #111;
            padding: 11px 22px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
        }

        /* Validation */
        .error {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
        }

        /* Responsive */
        @media(max-width:700px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <?php
    $user_id = $_SESSION['id'];
    $user = mysqli_query($con, "SELECT * FROM user WHERE user_id = $user_id");
    $data = mysqli_fetch_assoc($user);
    ?>

    <div class="container">

        <div class="profile-left">
            <div class="profile-img">
                <img src="../image/<?php echo $data['image'] ? $data['image'] : 'default_profile.png'; ?>" id="profileImage">
            </div>

            <h2><?php echo $data['first_name'] . " " . $data['last_name']; ?></h2>
            <p><?php echo $data['role'] ?></p>

            <button class="change-btn" onclick="openPopup()">Change Profile</button>
            <button class="remove-btn" id="removeProfileButton" onclick="removeProfile()">Remove Profile</button>
        </div>


        <!-- Popup -->
        <div class="modal" id="profileModal">
            <div class="modal-content">
                <span class="close" onclick="closePopup()">×</span>

                <h3>Upload Profile Photo</h3>

                <!-- Drag & Drop Area -->
                <form id="profileForm" enctype="multipart/form-data">

                    <div class="drop-area" id="dropArea">
                        <p>Drag & Drop Image Here</p>
                        <span>OR</span>

                        <input type="file" id="fileInput" name="image" accept="image/*">

                        <label for="fileInput" class="browse-btn">Browse File</label>
                    </div>

                    <div class="preview-area">
                        <img id="previewImage" style="display:none;">
                    </div>

                </form>

                <button class="save-btn" onclick="saveProfile()">Save Photo</button>
            </div>
        </div>


        <!-- RIGHT DETAILS -->
        <div class="profile-right">

            <!-- Personal Details -->
            <div class="card" id="viewProfile">

                <h3>Personal Details</h3>
                <p class="sub">Update your profile information</p>

                <div class="form-grid">

                    <div class="form-group">
                        <label>First Name</label>
                        <p class="p" id="viewFirstName"><?php echo $data['first_name']; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <p class="p" id="viewLastName"><?php echo $data['last_name']; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <p class="p" id="viewEmail"><?php echo $data['email']; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <p class="p" id="viewPhone"><?php echo $data['contact_no']; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Gender</label>
                        <p class="p" id="viewGender"><?php echo $data['gender']; ?></p>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <p class="p" id="viewAddress"><?php echo $data['address']; ?></p>
                    </div>

                </div>

                <div class="edit-actions">
                    <button class="edit-btn" onclick="openEditForm()">Edit profile</button>
                </div>
            </div>
            <div class="card edit-card" id="editProfile" style="display:none;">

                <div class="edit-header">
                    <h3>Edit Personal Details</h3>
                    <p>Update your profile information</p>
                </div>

                <form method="POST" onsubmit="return validateForm();">
                    <div class="form-grid">

                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" id="editFirstName" placeholder="Enter first name" name="first_name">
                            <span id="firstNameError" class="error"></span>
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="editLastName" placeholder="Enter last name" name="last_name">
                            <span id="lastNameError" class="error"></span>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="editEmail" placeholder="Enter email" disabled>
                            <span id="emailError" class="error"></span>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" id="editPhone" placeholder="Enter phone number" name="contact_no">
                            <span id="phoneError" class="error"></span>
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <select id="editGender" name="gender">
                                <option value="">Select gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                            <span id="genderError" class="error"></span>
                        </div>

                        <div class="form-group full-width">
                            <label>Address</label>
                            <textarea id="editAddress" rows="3" placeholder="Enter address" name="address"></textarea>
                            <span id="addressError" class="error"></span>
                        </div>

                    </div>

                    <div class="edit-actions">
                        <button class="cancel-btn" onclick="cancelEdit()">Cancel</button>
                        <button type="submit" class="save-btn" onclick="saveEdit()" name="save_btn">Save Changes</button>
                    </div>
                </form>

            </div>



        </div>
    </div>

    <?php
    if (isset($_POST['save_btn'])) {

        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $contact_no = $_POST['contact_no'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];

        $update_query = "UPDATE user SET 
                            first_name='$first_name',
                            last_name='$last_name',
                            contact_no='$contact_no',
                            gender='$gender',
                            address='$address'
                        WHERE user_id=$user_id";

        if (mysqli_query($con, $update_query)) {
            echo "<script>
                    Swal.fire({
                        toast:true,
                        position:'top',
                        icon:'success',
                        title:'Profile Updated Successfully',
                        showConfirmButton:false,
                        timer:2000,
                        timerProgressBar:true
                    }).then(()=>{
                        window.location.href='profile.php';
                    });
                </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        toast:true,
                        position:'top',
                        icon:'error',
                        title:'Failed to update profile',
                        showConfirmButton:false,
                        timer:2000,
                        timerProgressBar:true
                    });
                </script>";
        }
    }
    ?>
    <?php include 'footer.php'; ?>

    <script>
        const defaultImage = "../image/default_profile.png";

        const fileInput = document.getElementById("fileInput");
        const previewImage = document.getElementById("previewImage");
        const profileImage = document.getElementById("profileImage");
        const removeBtn = document.getElementById("removeProfileButton");
        const dropArea = document.getElementById("dropArea");

        let selectedFile = null;


        /* Page Load */
        window.onload = function() {
            toggleRemoveButton();
        };


        /* Show/Hide Remove Button */
        function toggleRemoveButton() {

            let currentImg = profileImage.getAttribute("src");

            if (currentImg.includes("default_profile.png")) {
                removeBtn.style.display = "none";
            } else {
                removeBtn.style.display = "inline-block";
            }

        }


        /* Open Popup */
        function openPopup() {
            document.getElementById("profileModal").style.display = "block";
        }


        /* Close Popup */
        function closePopup() {
            document.getElementById("profileModal").style.display = "none";
        }



        /* File Input Preview */
        fileInput.addEventListener("change", function() {
            handleFile(this.files[0]);
        });



        /* Drag Over */
        dropArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropArea.classList.add("dragover");
        });


        /* Drag Leave */
        dropArea.addEventListener("dragleave", () => {
            dropArea.classList.remove("dragover");
        });


        /* Drop File */
        dropArea.addEventListener("drop", (e) => {

            e.preventDefault();
            dropArea.classList.remove("dragover");

            const file = e.dataTransfer.files[0];
            handleFile(file);

        });



        /* Handle Image */
        function handleFile(file) {

            if (!file) return;

            selectedFile = file;

            const reader = new FileReader();

            reader.onload = function(e) {

                previewImage.src = e.target.result;
                previewImage.style.display = "block";

            };

            reader.readAsDataURL(file);

        }



        /* Save Profile Image */
        function saveProfile() {

            if (!selectedFile) {

                Swal.fire({
                    toast: true,
                    position: "top",
                    icon: "warning",
                    title: "Please select an image first",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                return;
            }

            if (!selectedFile.type.startsWith("image/")) {
                Swal.fire({
                    toast: true,
                    position: "top",
                    icon: "error",
                    title: "Please upload an image file.",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                return;
            }

            if (selectedFile.size > 2 * 1024 * 1024) {
                Swal.fire({
                    toast: true,
                    position: "top",
                    icon: "error",
                    title: "Image must be less than 2MB.",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                return;
            }

            let formData = new FormData();
            formData.append("image", selectedFile);

            fetch("upload_profile.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {

                    if (data.status === "success") {

                        profileImage.src = "../image/" + data.image + "?" + new Date().getTime();

                        toggleRemoveButton();
                        closePopup();

                        // Toast Message
                        Swal.fire({
                            toast: true,
                            position: "top",
                            icon: "success",
                            title: "Profile photo updated successfully",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                    } else {

                        Swal.fire({
                            toast: true,
                            position: "top",
                            icon: "error",
                            title: "Upload failed",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                    }

                });

        }



        /* Remove Profile Image */
        function removeProfile() {

            Swal.fire({
                title: "Remove Profile Photo?",
                text: "Your profile image will be deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, remove it"
            }).then((result) => {

                if (result.isConfirmed) {

                    fetch("remove_profile.php", {
                            method: "POST"
                        })
                        .then(res => res.json())
                        .then(data => {

                            if (data.status === "success") {

                                profileImage.src = defaultImage + "?" + new Date().getTime();

                                toggleRemoveButton();

                                // Toast message
                                Swal.fire({
                                    toast: true,
                                    position: "top",
                                    icon: "success",
                                    title: "Profile photo removed",
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });

                            } else {

                                Swal.fire({
                                    toast: true,
                                    position: "top",
                                    icon: "error",
                                    title: "Failed to remove image",
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });

                            }

                        });

                }

            });

        }
    </script>

    <script>
        /* OPEN EDIT FORM */
        function openEditForm() {

            document.getElementById("viewProfile").style.display = "none";
            document.getElementById("editProfile").style.display = "block";

            // Fill edit fields from view
            document.getElementById("editFirstName").value =
                document.getElementById("viewFirstName").innerText;

            document.getElementById("editLastName").value =
                document.getElementById("viewLastName").innerText;

            document.getElementById("editEmail").value =
                document.getElementById("viewEmail").innerText;

            document.getElementById("editPhone").value =
                document.getElementById("viewPhone").innerText;

            document.getElementById("editGender").value =
                document.getElementById("viewGender").innerText;

            document.getElementById("editAddress").value =
                document.getElementById("viewAddress").innerText;
        }

        /* CANCEL EDIT */
        function cancelEdit() {
            document.getElementById("editProfile").style.display = "none";
            document.getElementById("viewProfile").style.display = "block";
        }

        document.getElementById("editFirstName").addEventListener("input", validateFirstName);
        document.getElementById("editLastName").addEventListener("input", validateLastName);
        document.getElementById("editEmail").addEventListener("input", validateEmail);
        document.getElementById("editPhone").addEventListener("input", validatePhone);
        document.getElementById("editGender").addEventListener("change", validateGender);
        document.getElementById("editAddress").addEventListener("input", validateAddress);

        /* SAVE PROFILE */
        function saveEdit() {

            if (!validateForm()) return;

            // Update view values
            document.getElementById("viewFirstName").innerText =
                document.getElementById("editFirstName").value;

            document.getElementById("viewLastName").innerText =
                document.getElementById("editLastName").value;

            document.getElementById("viewEmail").innerText =
                document.getElementById("editEmail").value;

            document.getElementById("viewPhone").innerText =
                document.getElementById("editPhone").value;

            document.getElementById("viewGender").innerText =
                document.getElementById("editGender").value;

            document.getElementById("viewAddress").innerText =
                document.getElementById("editAddress").value;

            cancelEdit(); // go back to view
        }

        /* VALIDATION */

        function validateFirstName() {
            let firstName = document.getElementById("editFirstName");

            let regex = /^[A-Za-z\s]+$/; // only letters + space

            if (firstName.value.trim() === "") {
                showError(firstName, "firstNameError", "First name is required");
                return false;
            } else if (firstName.value.trim().length < 2) {
                showError(firstName, "firstNameError", "First name must be at least 2 characters");
                return false;
            } else if (!regex.test(firstName.value.trim())) {
                showError(firstName, "firstNameError", "Only letters allowed");
                return false;
            }

            showSuccess(firstName, "firstNameError");
            return true;
        }

        function validateLastName() {
            let lastName = document.getElementById("editLastName");

            let regex = /^[A-Za-z\s]+$/; // only letters + space

            if (lastName.value.trim() === "") {
                showError(lastName, "lastNameError", "Last name is required");
                return false;
            } else if (lastName.value.trim().length < 2) {
                showError(lastName, "lastNameError", "Last name must be at least 2 characters");
                return false;
            } else if (!regex.test(lastName.value.trim())) {
                showError(lastName, "lastNameError", "Only letters allowed");
                return false;
            }

            showSuccess(lastName, "lastNameError");
            return true;
        }

        function validateEmail() {
            let email = document.getElementById("editEmail");
            let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

            if (email.value.trim() === "") {
                showError(email, "emailError", "Email is required");
                return false;
            } else if (!emailPattern.test(email.value.trim())) {
                showError(email, "emailError", "Enter valid email");
                return false;
            }

            showSuccess(email, "emailError");
            return true;
        }

        function validatePhone() {
            let phone = document.getElementById("editPhone");
            let value = phone.value.trim();

            if (value === "") {
                showError(phone, "phoneError", "Phone number is required");
                return false;
            } else if (!/^[0-9]+$/.test(value)) {
                showError(phone, "phoneError", "Phone must contain only numbers");
                return false;
            } else if (value.length !== 10) {
                showError(phone, "phoneError", "Phone number must be exactly 10 digits");
                return false;
            }

            showSuccess(phone, "phoneError");
            return true;
        }

        function validateGender() {
            let gender = document.getElementById("editGender");

            if (!gender.value) {
                showError(gender, "genderError", "Select gender");
                return false;
            }
            showSuccess(gender, "genderError");
            return true;
        }

        function validateAddress() {
            let address = document.getElementById("editAddress");
            let value = address.value.trim();

            // allow letters, numbers, space, comma, (), :, -
            let regex = /^[a-zA-Z0-9\s,():-]+$/;

            if (value === "") {
                showError(address, "addressError", "Address is required");
                return false;
            } else if (value.length < 5) {
                showError(address, "addressError", "Enter full address");
                return false;
            } else if (!regex.test(value)) {
                showError(address, "addressError", "Only letters, numbers, space, , ( ) : - allowed");
                return false;
            }

            showSuccess(address, "addressError");
            return true;
        }


        function validateForm() {
            return (
                validateFirstName() &&
                validateLastName() &&
                validateEmail() &&
                validatePhone() &&
                validateGender() &&
                validateAddress()
            );
        }

        /* HELPERS */

        function showError(input, errorId, message) {
            document.getElementById(errorId).innerText = message;
            input.classList.add("invalid");
        }

        function showSuccess(input, errorId) {
            document.getElementById(errorId).innerText = "";
            input.classList.remove("invalid");
        }
    </script>




</body>

</html>