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
    <title>Book List | Library System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="icon" href="../image/title_image.png" type="image/png">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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

        .container {
            width: 100%;
            /* Full width */
            max-width: 100%;
            /* Remove restriction */
            padding: 40px;
        }

        .card {
            background: #ffffff;
            padding: 35px;
            /* Increased padding */
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .title-area h3 {
            margin: 0;
            font-size: 24px;
            /* Larger heading */
            font-weight: 700;
            color: #1f2937;
        }

        .subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-top: 5px;
        }

        .btn {
            padding: 10px 18px;
            /* Bigger buttons */
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .btn-add {
            background: #16a34a;
            color: #fff;
        }

        .btn-add:hover {
            background: #15803d;
        }

        .btn-edit {
            background: #facc15;
            color: #1f2937;
            display: inline-block;
        }

        .btn-edit:hover {
            background: #eab308;
        }

        .btn-delete {
            background: #ef4444;
            color: #fff;
            display: inline-block;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        td {
            white-space: nowrap;
            /* ⬅ Prevent line break */
        }

        .model-link {
            color: #2660de;
            cursor: pointer;
            text-decoration: underline;
        }

        table.dataTable {
            width: 100% !important;
            font-size: 14px;
            /* Bigger text */
        }

        table.dataTable thead th {
            background: #f9fafb;
            color: #374151;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 12px;
            /* More header height */
        }

        table.dataTable tbody td {
            padding: 14px 12px;
            /* Increase row height */
            vertical-align: middle;
        }

        table.dataTable tbody tr {
            transition: background 0.2s;
        }

        table.dataTable tbody tr:hover {
            background: #f3f4f6;
        }

        .status {
            padding: 6px 16px;
            /* Bigger badge */
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
        }

        .available {
            background: #dcfce7;
            color: #166534;
        }

        .issued {
            background: #fee2e2;
            color: #991b1b;
        }

        img.cover {
            width: 55px;
            /* Bigger image */
            height: 75px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        /* DataTable Buttons */
        .dt-buttons .dt-button {
            background: #f3f4f6 !important;
            border: 1px solid #e5e7eb !important;
            color: #374151 !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 13px !important;
            margin-right: 6px;
        }

        .dt-buttons .dt-button:hover {
            background: #e5e7eb !important;
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

        /* Overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        /* Modal Box */
        .modal-box {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 14px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.25s ease;
        }

        /* Header */
        .modal-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }

        /* Body */
        .modal-body p {
            font-size: 15px;
            font-weight: 600;
            color: #dc2626;
            margin-bottom: 6px;
        }

        .modal-body span {
            font-size: 13px;
            color: #6b7280;
        }

        /* Buttons */
        .modal-actions {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .modal-actions .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .cancel-btn {
            background: #f1f5f9;
            color: #334155;
        }

        .cancel-btn:hover {
            background: #e2e8f0;
        }

        .delete-btn {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            color: #fff;
        }

        .delete-btn:hover {
            opacity: 0.9;
        }

        .status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status.available {
            background: #dcfce7;
            color: #166534;
        }

        .status.unavailable {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-toggle {
            background: #16a34a;
            color: #fff;

            display: inline-block;
            padding: 10px 18px;
            margin-top: 10px;
            /* Bigger buttons */
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .btn-toggle:hover {
            background: #15803d;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }


        @media (max-width: 768px) {
            .top-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            img.cover {
                width: 45px;
                height: 60px;
            }

            .breadcrumb {
                font-size: 13px;
            }
        }

        .book-id {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .book-id:hover {
            text-decoration: underline;
        }

        /* Backdrop */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        /* Card */
        .modal-card {
            background: #ffffff;
            width: 700px;
            max-width: 95%;
            border-radius: 14px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: fadeSlide 0.25s ease;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .modal-header-p {
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header-p h3 {
            font-size: 18px;
            color: #0f172a;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Pills container */
        .pill-group {
            display: flex;
            gap: 8px;
        }

        /* Base pill */
        .pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
        }

        /* Status pills */
        .pill-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .pill-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Close */
        .close-icon {
            font-size: 22px;
            cursor: pointer;
            color: #64748b;
        }

        .close-icon:hover {
            color: #ef4444;
        }

        /* Body */
        .modal-body-p {
            display: grid;
            grid-template-columns: 580px 1fr;
            gap: 20px;
            padding: 20px;
        }

        /* Image */
        .book-image img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        /* Details */
        .book-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .detail span {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
        }

        .detail p {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        /* Footer */
        .modal-footer {
            padding: 14px 20px;
            border-top: 1px solid #e5e7eb;
            text-align: right;
        }

        /* Buttons */
        .btn-secondary {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #cbd5f5;
            background: #f8fafc;
            color: #1e293b;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #e0e7ff;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .modal-body {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .book-details {
                grid-template-columns: 1fr;
            }
        }

        .advanced-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .advanced-filters input,
        .advanced-filters select {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            min-width: 180px;
        }

        .filter-box {
            display: flex;
            flex-direction: column;
        }

        .filter-box label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .btn-area {
            justify-content: flex-end;
        }
    </style>

</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb">
            <a href="home.php" class="dashboard">Dashboard</a>
            <span class="separator">›</span>
            <span class="current">Book List</span>
        </nav>
    </div>

    <div class="container">
        <div class="card">
            <div class="top-actions">
                <div class="title-area">
                    <h3>Book Details</h3>
                    <div class="subtitle">Manage your book data</div>
                </div>
                <div class="advanced-filters">
                    <div class="filter-box">
                        <label>Title</label>
                        <input type="text" id="filterTitle" placeholder="Filter by Title">
                    </div>
                    <div class="filter-box">
                        <label>Author</label>
                        <input type="text" id="filterAuthor" placeholder="Filter by Author">
                    </div>
                    <div class="filter-box">
                        <label>Category</label>
                        <select id="filterCategory">
                            <option value="">All Categories</option>
                            <?php
                            $categories = mysqli_query($con, "SELECT * FROM category WHERE status = 'Active'");

                            foreach ($categories as $row) {
                                echo "<option value='{$row['category_name']}'>
                                             {$row['category_name']}
                                          </option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-box">
                        <label>Status</label>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>

                    <div class="filter-box btn-area">
                        <button class="btn btn-add" onclick="resetFilters()">Reset</button>
                    </div>
                </div>
                <a href="add_book.php"><button class="btn btn-add">➕ Add Book</button></a>
            </div>

            <table id="bookTable" class="display">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Image</th>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Year</th>
                        <th>Language</th>
                        <th>Total Copy</th>
                        <th>Available Copy</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $user_id = $_SESSION['id'];
                    $library_id = mysqli_query($con, "SELECT * FROM library WHERE user_id=$user_id");
                    $library_data = mysqli_fetch_assoc($library_id);
                    $books = mysqli_query($con, "SELECT * FROM book_list WHERE library_id='{$library_data['library_id']}'");
                    $i = 1;
                    foreach ($books as $row) {

                        // Dynamic Status Logic
                        if ($row['available_copy'] == 0) {
                            $statusClass = "unavailable";
                            $statusText  = "Unavailable";
                        } else {
                            $statusClass = "available";
                            $statusText  = "Available";
                        }

                        $library_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM library WHERE library_id = '{$row['library_id']}'"));


                        echo "<tr>
                        <td>{$i}</td>
                        <td><img src='../book_images/{$row['image']}' class='cover'></td>
                        <td>{$row['book_id']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['author']}</td>
                        <td>{$row['category']}</td>
                        <td>{$row['year']}</td>
                        <td>{$row['language']}</td>
                        <td>{$row['total_copy']}</td>
                        <td>{$row['available_copy']}</td>
                        <td>{$row['rating']}</td>
                        <td><span class='status {$statusClass}'>{$statusText}</span></td>
                        <td>
                            <a href='edit_book.php?book_id={$row['book_id']}'><button class='btn btn-edit'>Edit</button></a>
                            <button class='btn btn-delete' onclick='openDeleteModal({$row['book_id']})'>Delete</button>
                        </td>
                    </tr>";

                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <form method="post">
            <div class="modal-box">
                <div class="modal-header">
                    <h3>Delete Book Record</h3>
                </div>

                <div class="detail" style="display:none;">
                    <input type="hidden" name="bookId" id="bookId">
                </div>

                <div class="modal-body">
                    <p>⚠️ Are you sure you want to delete this book record?</p>
                    <span>This action cannot be undone.</span>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn delete-btn" name="delete_btn">Yes, Delete</button>
                </div>
            </div>
        </form>
    </div>

    <?php
    if (isset($_POST['delete_btn'])) {
        $book_id = intval($_POST['bookId']);

        $select_query = "SELECT image FROM book_list WHERE book_id = $book_id";
        $select_result = mysqli_query($con, $select_query);

        if ($select_result && mysqli_num_rows($select_result) > 0) {
            $book_data = mysqli_fetch_assoc($select_result);
            $image_name = $book_data['image'];

            $image_deleted = true;

            if (!empty($image_name) && $image_name != 'no-image.png') {
                $image_path = "../book_images/" . $image_name;

                if (file_exists($image_path)) {
                    $image_deleted = unlink($image_path);
                }
            }

            if ($image_deleted) {
                $delete_query = "DELETE FROM book_list WHERE book_id = $book_id";

                if (mysqli_query($con, $delete_query)) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function(){
                            Swal.fire({
                                toast: true,
                                position: 'top',
                                icon: 'success',
                                title: 'Book deleted successfully!',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.href = 'book_list.php';
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
                                title: 'Failed to delete book record.',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                        });
                    </script>";
                }
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function(){
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'error',
                            title: 'Image could not be deleted.',
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
    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Export Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        var table = $('#bookTable').DataTable({
            responsive: true,
            dom: 'Brtip',
            columnDefs: [{
                targets: 0, // Sr No column
                orderable: false,
                searchable: false
            }],

            order: [
                [1, 'asc']
            ],
            buttons: [{
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10] // column indexes you want
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                }
            ],
            pageLength: 5,
            scrollY: "500px",
            scrollX: true,
            scrollCollapse: true
        });

        // ✅ AUTO UPDATE SERIAL NUMBER
        table.on('order.dt search.dt draw.dt', function() {
            table.column(0, {
                    search: 'applied',
                    order: 'applied'
                })
                .nodes()
                .each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
        }).draw();

        // STATUS filter
        $('#filterStatus').on('change', function() {
            var value = this.value.toLowerCase();

            table.column(11).search(value ? '^' + value + '$' : '', true, false).draw();
        });

        // LOCATION filter
        $('#filterTitle').on('keyup', function() {
            table.column(3).search(this.value).draw();
        });

        // OWNER filter
        $('#filterCategory').on('change', function() {
            var value = this.value.toLowerCase();

            table.column(5).search(value ? '^' + value + '$' : '', true, false).draw();
        });

        // CATEGORY filter
        $('#filterAuthor').on('keyup', function() {
            table.column(4).search(this.value).draw();
        });

        // RESET filters
        function resetFilters() {
            $('#filterStatus').val('');
            $('#filterTitle').val('');
            $('#filterAuthor').val('');
            $('#filterCategory').val('');

            table.columns().search('').draw();
        }

        const deleteModal = document.getElementById("deleteModal");

        function openDeleteModal(id) {
            document.getElementById("bookId").value = id;
            deleteModal.style.display = "flex";
        }

        function closeDeleteModal() {
            deleteModal.style.display = "none";
        }
    </script>

</body>

</html>