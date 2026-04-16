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
    <title>Add Library | Library System</title>
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

        @keyframes shake {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-4px);
            }

            50% {
                transform: translateX(4px);
            }

            75% {
                transform: translateX(-4px);
            }

            100% {
                transform: translateX(0);
            }
        }

        .form-group input.error-input {
            animation: shake 0.25s;
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
            <a href="request_librarian.php"><span class="dashboard">Request</span></a>
            <span class="separator">›</span>
            <span class="current">Add Library</span>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">



        <div class="edit-card">
            <form id="editLibraryForm" action="#" method="POST">
                <div class="page-header">
                    <h2>Add Library</h2>
                    <p>Add library details in your library system</p>
                </div>
                <div class="form-grid">

                    <!-- Fields -->
                    <div class="form-fields">

                        <div class="form-group">
                            <label>Library Name</label>
                            <input type="text" id="libraryName" name="libraryName">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Library Owner Name</label>
                            <input type="text" id="libraryOwnerName" name="libraryOwnerName">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Library Email</label>
                            <input type="email" id="libraryEmail" name="libraryEmail">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Table Capacity</label>
                            <input type="number" id="tableCapacity" name="tableCapacity">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Chair Capacity</label>
                            <input type="number" id="chairCapacity" name="chairCapacity">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Open At</label>
                            <input type="time" id="openAt" name="openAt">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Close At</label>
                            <input type="time" id="closeAt" name="closeAt">
                            <div class="error"></div>
                        </div>

                        <div class="form-group">
                            <label>Library Location</label>
                            <input type="text" id="libraryLocation" name="libraryLocation">
                            <div class="error"></div>
                        </div>

                    </div>
                </div>

                <!-- Buttons -->
                <div class="actions">
                    <button type="reset" class="btn btn-cancel">Cancel</button>
                    <button type="submit" class="btn btn-save" name="add_library">Add Library</button>
                </div>

            </form>
        </div>

    </div>

    <?php
    if (isset($_POST['add_library'])) {
        // 🔁 Generate Unique Random ID
        do {
            $library_id = rand(10000000, 99999999);

            $check_query = mysqli_query(
                $con,
                "SELECT library_id FROM library WHERE library_id = '$library_id'"
            );
        } while (mysqli_num_rows($check_query) > 0);

        $user_id = $_SESSION['id'];
        $library_name = $_POST['libraryName'];
        $library_owner_name = $_POST['libraryOwnerName'];
        $library_email = $_POST['libraryEmail'];
        $table_capacity = $_POST['tableCapacity'];
        $chair_capacity = $_POST['chairCapacity'];
        $open_at = $_POST['openAt'];
        $close_at = $_POST['closeAt'];
        $library_location = $_POST['libraryLocation'];
        $status = "Active";

        $insert_query = "INSERT INTO library
                        (library_id, user_id, library_name, library_owner_name, library_email, table_capacity, chair_capacity, open_at, close_at, library_location, status)
                        VALUES($library_id, $user_id, '$library_name', '$library_owner_name', '$library_email', $table_capacity, $chair_capacity, '$open_at', '$close_at', '$library_location', '$status')";

        // Check if library already exists
        $check_library = mysqli_query($con, "SELECT library_name FROM library WHERE library_name='$library_name'");

        if (mysqli_num_rows($check_library) > 0) {

            echo "<script>
                    Swal.fire({
                        toast:true,
                        position:'top',
                        icon:'error',
                        title:'Library already exists!',
                        showConfirmButton:false,
                        timer:2000
                    });
                </script>";
        } else {
            if (mysqli_query($con, $insert_query)) {

                mysqli_query($con, "UPDATE user SET role = 'Librarian' WHERE user_id = $user_id");

                // 2. Distribute chairs among tables
                $base_chairs = intdiv($chair_capacity, $table_capacity);
                $extra_chairs = $chair_capacity % $table_capacity;

                // 3. Create tables and chairs automatically
                for ($i = 1; $i <= $table_capacity; $i++) {

                    // Extra 1 chair for first few tables
                    $chairs_for_this_table = $base_chairs + ($i <= $extra_chairs ? 1 : 0);

                    $table_name = "Table " . $i;

                    $insert_table = "
                        INSERT INTO library_tables (library_id, table_name, chair_count)
                        VALUES ('$library_id', '$table_name', '$chairs_for_this_table')
                    ";

                    if (!mysqli_query($con, $insert_table)) {
                        throw new Exception("Table insert failed: " . mysqli_error($con));
                    }

                    $table_id = mysqli_insert_id($con);

                    // Insert chairs for current table
                    for ($j = 1; $j <= $chairs_for_this_table; $j++) {
                        $insert_chair = "
                            INSERT INTO library_chairs (table_id, library_id, chair_no, status, booked_by)
                            VALUES ('$table_id', '$library_id', '$j', 'available', NULL)
                        ";

                        if (!mysqli_query($con, $insert_chair)) {
                            throw new Exception("Chair insert failed: " . mysqli_error($con));
                        }
                    }
                }

                echo "<script>
                    document.addEventListener('DOMContentLoaded', function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'success',
                        title: 'Library Added Successfully!',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = '../login.php';
                    });
                });
            </script>";
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'Failed to add library. Please try again.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                });
            </script>";
            }
        }
    }
    ?>

    <!-- FOOTER -->
    <?php include 'footer.php'; ?>

    <script>
        const form = document.getElementById("editLibraryForm");
        const libraryName = document.getElementById("libraryName");
        const libraryOwnerName = document.getElementById("libraryOwnerName");
        const libraryEmail = document.getElementById("libraryEmail");
        const tableCapacity = document.getElementById("tableCapacity");
        const chairCapacity = document.getElementById("chairCapacity");
        const openAt = document.getElementById("openAt");
        const closeAt = document.getElementById("closeAt");
        const libraryLocation = document.getElementById("libraryLocation");

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

        function validateEmail(input) {
            let email = input.value.trim();
            let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

            if (email === "") {
                showError(input, "Email is required");
                return false;
            } else if (!emailPattern.test(email)) {
                showError(input, "Enter valid email");
                return false;
            } else {
                showSuccess(input);
                return true;
            }
        }

        function validateTable() {
            const tableValue = tableCapacity.value.trim();
            const chairValue = chairCapacity.value.trim();

            // Table validation
            if (tableValue === "") {
                showError(tableCapacity, "Table count is required");
                return false;
            } else if (!/^[0-9]+$/.test(tableValue)) {
                showError(tableCapacity, "Only numbers allowed");
                return false;
            } else {
                showSuccess(tableCapacity);
            }

            // Chair validation
            const maxChairs = Number(tableCapacity.value) * 12
            if (chairValue === "") {
                showError(chairCapacity, "Chair count is required");
                return false;
            } else if (!/^[0-9]+$/.test(chairValue)) {
                showError(chairCapacity, "Only numbers allowed");
                return false;
            } else if (Number(chairValue) < tableCapacity.value) {
                showError(chairCapacity, `Enter at least ${tableCapacity.value} chair(s)`);
                return false;
            } else if (Number(chairValue) > maxChairs) {
                showError(chairCapacity, `Only ${maxChairs} chairs allowed for ${tableCapacity.value} table(s)`);
                return false;
            } else {
                showSuccess(chairCapacity);
                return true;
            }
        }

        function validateTime() {
            const openTime = openAt.value;
            const closeTime = closeAt.value;

            // Open time
            if (!openTime) {
                showError(openAt, "Open time is required");
                return false;
            } else {
                showSuccess(openAt);
            }

            // Close time
            if (!closeTime) {
                showError(closeAt, "Close time is required");
                return false;
            } else if (openTime >= closeTime) {
                showError(closeAt, "Close time must be after open time");
                return false;
            } else {
                showSuccess(closeAt);
            }

            return true;
        }

        function validateLocation() {
            const locationValue = libraryLocation.value.trim();
            const regex = /^[a-zA-Z0-9\s,:()\-]+$/;

            if (locationValue === "") {
                showError(libraryLocation, "Location is required");
                return false;
            } else if (locationValue.length < 3) {
                showError(libraryLocation, "Location must be at least 3 characters");
                return false;
            } else if (!regex.test(locationValue)) {
                showError(libraryLocation, "Invalid location format");
                return false;
            } else {
                showSuccess(libraryLocation);
                return true;
            }
        }

        libraryName.addEventListener("input", () => validateText(libraryName));
        libraryOwnerName.addEventListener("input", () => validateText(libraryOwnerName));
        libraryEmail.addEventListener("input", () => validateEmail(libraryEmail));
        tableCapacity.addEventListener("input", validateTable);
        chairCapacity.addEventListener("input", validateTable);
        openAt.addEventListener("input", validateTime);
        closeAt.addEventListener("input", validateTime);
        libraryLocation.addEventListener("input", validateLocation);

        form.addEventListener("submit", function(e) {


            const isValid =
                validateText(libraryName) &
                validateText(libraryOwnerName) &
                validateEmail(libraryEmail) &
                validateTable() &
                validateTable() &
                validateTime() &
                validateTime() &
                validateLocation();

            if (!isValid) {
                e.preventDefault();
            }

            // if (isValid) {
            //     Swal.fire({
            //         toast: true,
            //         position: 'top',
            //         icon: 'success',
            //         title: 'Library details added successfully!',
            //         showConfirmButton: false,
            //         timer: 2000,
            //         timerProgressBar: true,
            //         didClose: () => {
            //             window.location.href = "library_list.php";
            //         }
            //     });
            // }
        });
    </script>

</body>

</html>