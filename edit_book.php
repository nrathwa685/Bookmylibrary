<?php
require "../session_check.php";

if ($_SESSION['role'] != "Librarian") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Book | Library System</title>
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
            grid-template-columns: repeat(3, 1fr);
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
            <a href="book_list.php"><span class="dashboard">Book List</span></a>
            <span class="separator">›</span>
            <span class="current">Edit Book</span>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <?php
        $book_id = intval($_GET['book_id']);
        $book = mysqli_query($con, "SELECT * FROM book_list WHERE book_id = $book_id");
        $data = mysqli_fetch_assoc($book);

        ?>

        <div class="edit-card">
            <form id="editBookForm" method="POST" enctype="multipart/form-data">
                <div class="page-header">
                    <h2>Edit Book</h2>
                    <p>Edit book details in your library system</p>
                </div>
                <div class="form-grid">

                    <!-- Image -->
                    <label class="image-box">
                        <img id="previewImage" src="../book_images/<?php echo $data['image']; ?>"><br>
                        <span>Click to upload book image</span>
                        <input type="file" accept="image/*" id="imageInput" value="../book_images/<?php echo $data['image']; ?>" name="image">
                    </label>

                    <!-- Fields -->
                    <div class="form-fields">

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" id="title" value="<?php echo $data['title']; ?>" name="title">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Author</label>
                            <input type="text" id="author" value="<?php echo $data['author']; ?>" name="author">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <select id="category" name="category">
                                <option value="">Select Category</option>
                                <option value="Programming" <?php if ($data['category'] == 'Programming') echo 'selected'; ?>>Programming</option>
                                <option value="Science" <?php if ($data['category'] == 'Science') echo 'selected'; ?>>Science</option>
                                <option value="Database" <?php if ($data['category'] == 'Database') echo 'selected'; ?>>Database</option>
                                <option value="Mathematics" <?php if ($data['category'] == 'Mathematics') echo 'selected'; ?>>Mathematics</option>
                                <option value="History" <?php if ($data['category'] == 'History') echo 'selected'; ?>>History</option>
                                <option value="Fiction" <?php if ($data['category'] == 'Fiction') echo 'selected'; ?>>Fiction</option>
                            </select>
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" id="year" value="<?php echo $data['year']; ?>" name="year">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Total Copies</label>
                            <input type="number" id="total" value="<?php echo $data['total_copy']; ?>" name="total_copy">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Available Copies</label>
                            <input type="number" id="available" value="<?php echo $data['available_copy']; ?>" name="available_copy">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Language</label>
                            <select id="language" name="language">
                                <option value="">Select Language</option>
                                <option value="English" <?php if ($data['language'] == 'English') echo 'selected'; ?>>English</option>
                                <option value="Hindi" <?php if ($data['language'] == 'Hindi') echo 'selected'; ?>>Hindi</option>
                                <option value="Gujarati" <?php if ($data['language'] == 'Gujarati') echo 'selected'; ?>>Gujarati</option>
                            </select>
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
        $title = $_POST['title'];
        $author = $_POST['author'];
        $category = $_POST['category'];
        $year = $_POST['year'];
        $language = $_POST['language'];
        $total_copy = $_POST['total_copy'];
        $available_copy = $_POST['available_copy'];
        if ($available_copy == 0) {
            $status = "Unavailable";
        } else {
            $status = "Available";
        }
        $upload_dir = '../book_images/';

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

        $update_query = "UPDATE book_list SET 
                            title = '$title', 
                            author = '$author', 
                            category = '$category', 
                            year = $year, 
                            language = '$language', 
                            total_copy = $total_copy, 
                            available_copy = $available_copy, 
                            status = '$status',
                            image = '$image' 
                         WHERE book_id = $book_id";

        if (mysqli_query($con, $update_query)) {
            echo "<script>
                    previewImage.src = '../book_images/$image';
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'success',
                        title: 'Book details updated successfully!',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didClose: () => {
                            window.location.href = 'book_list.php';
                        }
                    });
                </script>";
        } else {
            echo "<script>
                    previewImage.src = '../book_images/$image';
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'Failed to update book details. Please try again.',
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const form = document.getElementById("editBookForm");
        const title = document.getElementById("title");
        const author = document.getElementById("author");
        const category = document.getElementById("category");
        const year = document.getElementById("year");
        const total = document.getElementById("total");
        const available = document.getElementById("available");
        const language = document.getElementById("language");
        const imageInput = document.getElementById("imageInput");
        const previewImage = document.getElementById("previewImage");

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

        function validateSelect(input) {
            if (input.value === "") {
                showError(input, "Please select an option");
                return false;
            } else {
                showSuccess(input);
                return true;
            }
        }

        function validateYear() {
            const currentYear = new Date().getFullYear();
            if (year.value === "") {
                showError(year, "This field is required");
                return false;
            } else if (year.value < 0) {
                showError(year, "Enter a valid year");
                return false;
            } else if (year.value.length !== 4 || isNaN(year.value)) {
                showError(year, "Enter a valid 4-digit year");
                return false;
            } else if (year.value > currentYear) {
                showError(year, "Year cannot be in the future");
                return false;
            } else {
                showSuccess(year);
                return true;
            }
        }

        function validateCopies() {
            const totalValue = total.value.trim();
            const availableValue = available.value.trim();
            const regex = /^[0-9]+$/; // only whole numbers

            // TOTAL validation
            if (totalValue === "") {
                showError(total, "This field is required");
                return false;
            } else if (!regex.test(totalValue)) {
                showError(total, "Enter valid number");
                return false;
            } else if (Number(totalValue) <= 0) {
                showError(total, "Total must be greater than 0");
                return false;
            } else {
                showSuccess(total);
            }

            // AVAILABLE validation
            if (availableValue === "") {
                showError(available, "This field is required");
                return false;
            } else if (!regex.test(availableValue)) {
                showError(available, "Enter valid number");
                return false;
            } else if (Number(availableValue) < 0 || Number(availableValue) > Number(totalValue)) {
                showError(available, `Available must be between 0 and ${totalValue}`);
                return false;
            } else {
                showSuccess(available);
                return true;
            }
        }


        title.addEventListener("input", () => validateText(title));
        author.addEventListener("input", () => validateText(author));
        category.addEventListener("change", () => validateSelect(category));
        language.addEventListener("change", () => validateSelect(language));
        year.addEventListener("input", validateYear);
        total.addEventListener("input", validateCopies);
        available.addEventListener("input", validateCopies);

        imageInput.addEventListener("change", function(event) {
            const reader = new FileReader();
            reader.onload = () => previewImage.src = reader.result;
            reader.readAsDataURL(event.target.files[0]);
        });

        form.addEventListener("submit", function(e) {

            const isValid =
                validateText(title) &&
                validateText(author) &&
                validateSelect(category) &&
                validateYear() &&
                validateSelect(language) &&
                validateCopies();

            if (!isvalid) {
                e.preventDefault();
            }

            // if (isValid) {
            // Swal.fire({
            //     toast: true,
            //     position: 'top',
            //     icon: 'success',
            //     title: 'Book details updated successfully!',
            //     showConfirmButton: false,
            //     timer: 2000,
            //     timerProgressBar: true,
            //     didClose: () => {
            //         window.location.href = "book_list.php";
            //     }
            // });
            previewImage.src = "https://via.placeholder.com/160x220?text=Book+Cover";
            // }
        });
    </script>

</body>

</html>