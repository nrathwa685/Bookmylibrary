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
    <title>Review & Rating List | Library System</title>
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

        .paid {
            background: #dcfce7;
            color: #166534;
        }

        .unpaid {
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

        /* Role pill (Blue) */
        .pill-role-librarian {
            background-color: #fef3c7;
            color: #92400e;
        }

        .pill-role-user {
            background-color: #e0f2fe;
            color: #075985;
        }

        .pill-role-admin {
            background-color: #ede9fe;
            color: #5b21b6;
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
            grid-template-columns: 180px 1fr;
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

        /* Backdrop */
        .l-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        /* Card */
        .l-modal-card {
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
        .l-modal-header-p {
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .l-modal-header-p h3 {
            font-size: 18px;
            color: #0f172a;
        }

        .l-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Pills container */
        .l-pill-group {
            display: flex;
            gap: 8px;
        }

        /* Base pill */
        .l-pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
        }

        /* Status pills */
        .l-pill-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .l-pill-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Close */
        .l-close-icon {
            font-size: 22px;
            cursor: pointer;
            color: #64748b;
        }

        .l-close-icon:hover {
            color: #ef4444;
        }

        /* Body */
        .l-modal-body-p {
            display: grid;
            grid-template-columns: 580px 1fr;
            gap: 20px;
            padding: 20px;
        }

        /* Image */
        .l-book-image img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        /* Details */
        .l-book-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .l-detail span {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
        }

        .l-detail p {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        /* Footer */
        .l-modal-footer {
            padding: 14px 20px;
            border-top: 1px solid #e5e7eb;
            text-align: right;
        }

        /* Buttons */
        .l-btn-secondary {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #cbd5f5;
            background: #f8fafc;
            color: #1e293b;
            cursor: pointer;
        }

        .l-btn-secondary:hover {
            background: #e0e7ff;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .l-modal-body {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .l-book-details {
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

        #reviewTooltip {
            position: absolute;
            background: #111827;
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            max-width: 300px;
            display: none;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            line-height: 1.4;
        }

        .review-cell {
            cursor: pointer;
        }
    </style>

</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb">
            <a href="home.php" class="dashboard">Dashboard</a>
            <span class="separator">›</span>
            <span class="current">Review & Rating List</span>
        </nav>
    </div>
    <div class="container">
        <div class="card">
            <div class="top-actions">
                <div class="title-area">
                    <h3>Review & Rating</h3>
                    <div class="subtitle">Manage your Review & Rating data</div>
                </div>
                <div class="advanced-filters">
                    <div class="filter-box">
                        <label>Rating</label>
                        <select id="filterRating">
                            <option value="">All Rating</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>

                    <div class="filter-box btn-area">
                        <button class="btn btn-add" onclick="resetFilters()">Reset</button>
                    </div>
                </div>
                <div></div>
            </div>

            <table id="bookTable" class="display">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Review ID</th>
                        <th>Book ID</th>
                        <th>User ID</th>
                        <th>Review Comment</th>
                        <th>Rating</th>
                        <th>Review Date</th>
                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_id = $_SESSION['id'];
                    $library_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT library_id FROM library WHERE user_id = $user_id"));
                    $rating = mysqli_query($con, "SELECT * FROM rating WHERE library_id = {$library_id['library_id']}");
                    $i = 1;

                    foreach ($rating as $row) {

                        $full_description = $row['description'];

                        // get first 40 characters
                        $short_description = substr($full_description, 0, 40);

                        // add "..." if longer than 40
                        if (strlen($full_description) > 40) {
                            $short_description .= "...";
                        }

                        $book_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM book_list WHERE book_id = '{$row['book_id']}'"));
                        $library_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM library WHERE library_id = '{$row['library_id']}'"));
                        $user_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM user WHERE user_id = '{$row['user_id']}'"));

                        echo "<tr>
                                    <td>{$i}</td>
                                    <td>{$row['rating_id']}</td>
                                    <td>
                                    <span class='model-link'
                                    onclick=\"openBookModal(
                                    '{$book_data['book_id']}',
                                    '../book_images/{$book_data['image']}',
                                    '" . htmlspecialchars($book_data['title'], ENT_QUOTES) . "',
                                    '" . htmlspecialchars($book_data['author'], ENT_QUOTES) . "',
                                    '" . htmlspecialchars($book_data['category'], ENT_QUOTES) . "',
                                    '{$book_data['year']}',
                                    '" . htmlspecialchars($library_data['library_name'], ENT_QUOTES) . "'
                                    )\">
                                    {$row['book_id']}
                                    </span>
                                    </td>

                                    <td>
                                        <span class='model-link'
                                        onclick=\"openUserModal(
                                        '{$user_data['user_id']}',
                                        '../image/{$user_data['image']}',
                                        '" . htmlspecialchars($user_data['first_name'], ENT_QUOTES) . "',
                                        '" . htmlspecialchars($user_data['last_name'], ENT_QUOTES) . "',
                                        '" . htmlspecialchars($user_data['email'], ENT_QUOTES) . "',
                                        '" . htmlspecialchars($user_data['contact_no'], ENT_QUOTES) . "',
                                        '" . htmlspecialchars($user_data['address'], ENT_QUOTES) . "',
                                        '" . htmlspecialchars($user_data['role'], ENT_QUOTES) . "',
                                        '" . htmlspecialchars($user_data['status'], ENT_QUOTES) . "'
                                        )\">
                                        {$row['user_id']}
                                        </span>
                                    </td>
                                    
                                    <td class='review-cell'
                                        data-full='" . htmlspecialchars($full_description, ENT_QUOTES) . "'>
                                        {$short_description}
                                    </td>

                                    <td>{$row['rating']}</td>
                                    <td>{$row['rating_date']}</td>
                                </tr>";

                        $i++;
                    }
                    ?>

                </tbody>
            </table>
            <div id="reviewTooltip"></div>
        </div>
    </div>

    <div class="modal-backdrop" id="bookModal">
        <div class="modal-card">

            <div class="modal-header-p">
                <h3>Book Details</h3>
                <span class="close-icon" onclick="closeBookModal()">×</span>
            </div>

            <div class="modal-body-p">
                <div class="book-image">
                    <img id="modalBookImage" src="" alt="Book Image">
                </div>

                <div class="book-details">
                    <div class="detail">
                        <span>Book ID</span>
                        <p id="modalBookId"></p>
                    </div>
                    <div class="detail">
                        <span>Title</span>
                        <p id="modalBookTitle"></p>
                    </div>
                    <div class="detail">
                        <span>Author</span>
                        <p id="modalBookAuthor"></p>
                    </div>
                    <div class="detail">
                        <span>Category</span>
                        <p id="modalBookCategory"></p>
                    </div>
                    <div class="detail">
                        <span>Publish Year</span>
                        <p id="modalBookYear"></p>
                    </div>
                    <div class="detail">
                        <span>Library Name</span>
                        <p id="modalBookLibrary"></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeBookModal()">Close</button>
            </div>

        </div>
    </div>

    <div class="modal-backdrop" id="userModal">
        <div class="modal-card">

            <div class="modal-header-p">
                <div class="header-left">
                    <h3>User Details</h3>

                    <div class="pill-group">
                        <span id="modalUserRole" class="pill"></span>
                        <span id="modalUserStatus" class="pill"></span>
                        <!-- <span class="pill pill-inactive">Inactive</span> -->
                    </div>
                </div>

                <span class="close-icon" onclick="closeUserModal()">×</span>
            </div>

            <div class="modal-body-p">
                <div class="book-image">
                    <img id="modalUserImage" src="" alt="User Image">
                </div>

                <div class="book-details">
                    <div class="detail">
                        <span>User ID</span>
                        <p id="modalUserId"></p>
                    </div>
                    <div class="detail">
                        <span>First Name</span>
                        <p id="modalUserFirstName"></p>
                    </div>
                    <div class="detail">
                        <span>Last Name</span>
                        <p id="modalUserLastName"></p>
                    </div>
                    <div class="detail">
                        <span>Email ID</span>
                        <p id="modalUserEmail"></p>
                    </div>
                    <div class="detail">
                        <span>Contact Number</span>
                        <p id="modalUserContact"></p>
                    </div>
                    <div class="detail">
                        <span>Address</span>
                        <p id="modalUserAddress"></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeUserModal()">Close</button>
            </div>

        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Delete Fine Record</h3>
            </div>

            <div class="modal-body">
                <p>⚠️ Are you sure you want to delete this fine record?</p>
                <span>This action cannot be undone.</span>
            </div>

            <div class="modal-actions">
                <button class="btn cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn delete-btn" onclick="confirmDelete()">Yes, Delete</button>
            </div>
        </div>
    </div>

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
                        columns: [0, 1, 2, 3, 4, 5, 6] // column indexes you want
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
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

        // Rating filter
        $('#filterRating').on('change', function() {
            var value = this.value.toLowerCase();

            table.column(5).search(value ? '^' + value + '$' : '', true, false).draw();
        });

        // RESET filters
        function resetFilters() {
            $('#filterRating').val('');

            table.columns().search('').draw();
        }

        const deleteModal = document.getElementById("deleteModal");

        function openDeleteModal() {
            deleteModal.style.display = "flex";
        }

        function closeDeleteModal() {
            deleteModal.style.display = "none";
        }

        function confirmDelete() {
            closeDeleteModal();
            alert("Fine record deleted successfully!");
            // Here you can remove the row or call backend later
        }

        function openBookModal(bookId, image, title, author, category, year, library) {
            document.getElementById("modalBookId").innerText = bookId;
            document.getElementById("modalBookImage").src = image;
            document.getElementById("modalBookTitle").innerText = title;
            document.getElementById("modalBookAuthor").innerText = author;
            document.getElementById("modalBookCategory").innerText = category;
            document.getElementById("modalBookYear").innerText = year;
            document.getElementById("modalBookLibrary").innerText = library;

            document.getElementById("bookModal").style.display = "flex";
        }

        function closeBookModal() {
            document.getElementById("bookModal").style.display = "none";
        }

        function openUserModal(id, image, first, last, email, contact, address, role, status) {

            document.getElementById("modalUserId").innerText = id;
            document.getElementById("modalUserFirstName").innerText = first;
            document.getElementById("modalUserLastName").innerText = last;
            document.getElementById("modalUserEmail").innerText = email;
            document.getElementById("modalUserContact").innerText = contact;
            document.getElementById("modalUserAddress").innerText = address;
            document.getElementById("modalUserImage").src = image;

            /* ROLE */
            let roleElement = document.getElementById("modalUserRole");
            roleElement.innerText = role;
            roleElement.className = "pill";

            if (role === "Librarian") {
                roleElement.classList.add("pill-role-librarian");
            } else if (role === "User") {
                roleElement.classList.add("pill-role-user");
            } else if (role === "Admin") {
                roleElement.classList.add("pill-role-admin");
            }

            /* STATUS */
            let statusElement = document.getElementById("modalUserStatus");
            statusElement.innerText = status;
            statusElement.className = "pill";

            if (status === "Active") {
                statusElement.classList.add("pill-active");
            } else if (status === "Inactive") {
                statusElement.classList.add("pill-inactive");
            }

            document.getElementById("userModal").style.display = "flex";
        }

        function closeUserModal() {
            document.getElementById("userModal").style.display = "none";
        }

        const tooltip = document.getElementById("reviewTooltip");

        document.querySelectorAll(".review-cell").forEach(cell => {

            cell.addEventListener("mouseenter", function(e) {
                tooltip.textContent = this.dataset.full;
                tooltip.style.display = "block";
            });

            cell.addEventListener("mousemove", function(e) {
                tooltip.style.top = (e.pageY + 10) + "px";
                tooltip.style.left = (e.pageX + 10) + "px";
            });

            cell.addEventListener("mouseleave", function() {
                tooltip.style.display = "none";
            });

        });
    </script>

</body>

</html>