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
    <title>Edit Category | Library System</title>
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
            grid-template-columns: 100% 1fr;
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
            <a href="category_list.php"><span class="dashboard">Category List</span></a>
            <span class="separator">›</span>
            <span class="current">Edit Category</span>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <?php
        $category_id = intval($_GET['category_id']);
        $category = mysqli_query($con, "SELECT * FROM category WHERE category_id = $category_id");
        $data = mysqli_fetch_assoc($category);

        ?>

        <div class="edit-card">
            <form id="editCategoryForm" method="POST">
                <div class="page-header">
                    <h2>Edit Category</h2>
                    <p>Edit category details in your library system</p>
                </div>
                <div class="form-grid">

                    <!-- Fields -->
                    <div class="form-fields">

                        <!-- <div class="form-group">
                            <label>Fine Amount</label>
                            <input type="number" id="fineAmount" value="200">
                            <div class="error">Fine Amount is required</div>
                        </div> -->

                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" id="categoryName" value="<?php echo $data['category_name']; ?>" name="categoryName">
                            <div class="error">Category Name is required</div>
                        </div>

                        <div class="form-group">
                            <label>Description(optional)</label>
                            <input type="text" id="description" value="<?php if($data['category_description'] == "N/A"){echo "";} else{echo $data['category_description'];} ?>" name="categoryDescription">
                            <div class="error">Description is required</div>
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
        $category_name = $_POST['categoryName'];

        if($_POST['categoryDescription'] == ""){
            $category_description = "N/A";
        }
        else{
            $category_description = $_POST['categoryDescription'];
        }

        $update_query = "UPDATE category SET 
                        category_name = '$category_name', 
                        category_description = '$category_description' 
                        WHERE category_id = $category_id";


        if (mysqli_query($con, $update_query)) {
            echo "<script>
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'success',
                        title: 'Category details updated successfully!',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didClose: () => {
                            window.location.href = 'category_list.php';
                        }
                    });
                </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'Failed to update category details. Please try again.',
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
        const form = document.getElementById("editCategoryForm");
        // const fineAmount = document.getElementById("fineAmount");
        const categoryName = document.getElementById("categoryName");
        const description = document.getElementById("description");

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

        function validateDescription() {
            const descriptionValue = description.value.trim();
            const regex = /^[a-zA-Z,. ]+$/; // only letters + comma + space

            if (descriptionValue === "") {
                showError(description, "Description is required");
                return false;
            } else if (descriptionValue.length < 3) {
                showError(description, "Description must be at least 3 characters");
                return false;
            } else if (!regex.test(descriptionValue)) {
                showError(description, "Only letters and comma allowed");
                return false;
            } else {
                showSuccess(description);
                return true;
            }
        }

        categoryName.addEventListener("input", () => validateText(categoryName));

        form.addEventListener("submit", function(e) {
        
            if(!isValid){
                e.preventDefault();
            }

            const isValid =
                // validFineAmount(fineAmount) &
                validateText(categoryName);

        });
    </script>

</body>

</html>