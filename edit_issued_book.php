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
    <title>Edit Issued Book | Library System</title>
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
            <a href="issued_book.php"><span class="dashboard">Issued Book List</span></a>
            <span class="separator">›</span>
            <span class="current">Edit Issued Book</span>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="edit-card">
            <form id="editIssuedBookForm" action="#" method="POST">
                <div class="page-header">
                    <h2>Edit Issued Book</h2>
                    <p>Edit issued book details in your library system</p>
                </div>
                <div class="form-grid">

                    <!-- Fields -->
                    <div class="form-fields">

                        <div class="form-group">
                            <label>Issue Date</label>
                            <input type="date" id="issueDate" value="2026-01-12">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Return Date</label>
                            <input type="date" id="returnDate" value="2026-03-01">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Fine Amount</label>
                            <input type="number" id="fineAmount" value="6562">
                            <div class="error"></div>
                        </div>

                    </div>
                </div>

                <!-- Buttons -->
                <div class="actions">
                    <button type="reset" class="btn btn-cancel">Cancel</button>
                    <button type="submit" class="btn btn-save">Save Change</button>
                </div>

            </form>
        </div>

    </div>

    <!-- FOOTER -->
    <?php include 'footer.php'; ?>

    <script>
        const form = document.getElementById("editIssuedBookForm");
        const issueDate = document.getElementById("issueDate");
        const returnDate = document.getElementById("returnDate");
        const fineAmount = document.getElementById("fineAmount");

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

        function validateDate() {
            const issue = new Date(issueDate.value);
            const ret = new Date(returnDate.value);

            // Issue date required
            if (issueDate.value.trim() === "") {
                showError(issueDate, "Issue date is required");
                return false;
            } else {
                showSuccess(issueDate);
            }

            // Return date must be after issue date
            if (returnDate.value.trim() !== "") {
                if (ret <= issue) {
                    showError(returnDate, "Return date must be after issue date");
                    return false;
                } else {
                    showSuccess(returnDate);
                }
            }

            return true;
        }

        function validateFine() {
            const fineValue = fineAmount.value.trim();
            const regex = /^[0-9]+$/; // only whole numbers

            if (fineValue === "") {
                showError(fineAmount, "This field is required");
                return false;
            } else if (!regex.test(fineValue)) {
                showError(fineAmount, "Enter valid number");
                return false;
            } else if (Number(fineValue) <= 0) {
                showError(fineAmount, "Fine must be greater than 0");
                return false;
            } else {
                showSuccess(fineAmount);
                return true;
            }
        }



        issueDate.addEventListener("input", validateDate);
        returnDate.addEventListener("input", validateDate);
        fineAmount.addEventListener("input", validateFine);

        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const isValid =
                validateDate(issueDate) &
                validateDate(returnDate) &
                validateFine(fineAmount);

            if (isValid) {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'success',
                    title: 'Issued book details updated successfully!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didClose: () => {
                        window.location.href = "issued_book.php";
                    }
                });
            }
        });
    </script>

</body>

</html>