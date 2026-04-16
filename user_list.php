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
    <title>User List | Library System</title>
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

        .active {
            background: #dcfce7;
            color: #166534;
        }

        .inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        img.cover {
            width: 55px;
            /* Bigger image */
            height: 55px;
            object-fit: cover;
            border-radius: 100%;
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

        .roleSpan.user {
            background: #e0f2fe;
            color: #075985;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .roleSpan.librarian {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .roleSpan.admin {
            background: #ede9fe;
            color: #5b21b6;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
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
            <span class="current">User List</span>
        </nav>
    </div>
    <div class="container">
        <div class="card">
            <div class="top-actions">
                <div class="title-area">
                    <h3>User Details</h3>
                    <div class="subtitle">Manage your user data</div>
                </div>

                <div class="advanced-filters">
                    <div class="filter-box">
                        <label>First Name</label>
                        <input type="text" id="filterFirstName" placeholder="Filter by First Name">
                    </div>
                    <div class="filter-box">
                        <label>Last Name</label>
                        <input type="text" id="filterLastName" placeholder="Filter by Last Name">
                    </div>
                    <div class="filter-box">
                        <label>Contact Number</label>
                        <input type="text" id="filterContactNumber" placeholder="Filter by Contact Number">
                    </div>
                    <div class="filter-box">
                        <label>Gender</label>
                        <select id="filterGender">
                            <option value="">All Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="filter-box">
                        <label>Role</label>
                        <select id="filterRole">
                            <option value="">All Roles</option>
                            <option value="User">User</option>
                            <option value="Librarian">Librarian</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div class="filter-box">
                        <label>Status</label>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="filter-box btn-area">
                        <button class="btn btn-add" onclick="resetFilters()">Reset</button>
                    </div>
                </div>

            </div>

            <table id="bookTable" class="display">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Profile Image</th>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email ID</th>
                        <th>Contact Number</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user = mysqli_query($con, "SELECT * FROM user");
                    $i = 1;
                    foreach ($user as $row) {

                        // Dynamic Status Logic
                        if ($row['status'] == "Active") {
                            $statusClass = "active";
                            $statusText  = "Active";
                            $buttonText = "Inactive";
                        } else {
                            $statusClass = "inactive";
                            $statusText  = "Inactive";
                            $buttonText = "Active";
                        }

                        $roleClass = strtolower($row['role']);

                        echo "<tr>
                        <td>1</td>
                        <td><img src='../image/{$row['image']}' class='cover'></td>
                        <td>{$row['user_id']}</td>
                        <td>{$row['first_name']}</td>
                        <td>{$row['last_name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['contact_no']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['address']}</td>
                        <!-- ROLE COLUMN -->
                        <td>
                            <span id='role_{$row['user_id']}' 
                                class='roleSpan {$roleClass}'>
                                {$row['role']}
                            </span>
                        </td>

                        <td><span id='status_{$row['user_id']}' 
                                class='status {$statusClass}'>
                                {$statusText}
                            </span>
                        </td>

                        <!-- ACTIONS -->

                        <td>
                            <a href='edit_user.php?user_id={$row['user_id']}'><button class='btn btn-edit'>Edit</button></a>
                            <button class='btn btn-delete' onclick='openDeleteModal({$row['user_id']})'>Delete</button><br>
                            <button 
                                class='btn btn-toggle'
                                onclick='toggleStatus({$row['user_id']}, " . json_encode($statusText) . ", this)'>
                                {$buttonText}
                            </button>
                            <!-- ROLE SELECT -->
                            
                                <select class='btn btn-role'
                                        onchange='changeRole({$row['user_id']}, this.value)'>

                                    <option disabled selected>Change Role</option>

                                    <option value='Admin'>Admin</option>
                                    <option value='User'>User</option>
                                    <option value='Librarian'>Librarian</option>

                                </select>
                                

                        </td>
                    </tr>";
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
                    <h3>Delete User Record</h3>
                </div>

                <div class="detail" style="display:none;">
                    <input type="hidden" name="userId" id="userId">
                </div>

                <div class="modal-body">
                    <p>⚠️ Are you sure you want to delete this user record?</p>
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
        $user_id = intval($_POST['userId']);

        $select_query = "SELECT image FROM user WHERE user_id = $user_id";
        $select_result = mysqli_query($con, $select_query);

        if ($select_result && mysqli_num_rows($select_result) > 0) {
            $user_data = mysqli_fetch_assoc($select_result);
            $image_name = $user_data['image'];

            $image_deleted = true;

            if (!empty($image_name) && $image_name != 'no-image.png' && $image_name != 'default_profile.png') {
                $image_path = "../image/" . $image_name;

                if (file_exists($image_path)) {
                    $image_deleted = unlink($image_path);
                }
            }

            if ($image_deleted) {
                $delete_query = "DELETE FROM user WHERE user_id = $user_id";

                if (mysqli_query($con, $delete_query)) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function(){
                            Swal.fire({
                                toast: true,
                                position: 'top',
                                icon: 'success',
                                title: 'User deleted successfully!',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.href = 'user_list.php';
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
                                title: 'Failed to delete user record.',
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
                    targets: 0,
                    orderable: false,
                    searchable: false
                },
                {
                    targets: [9, 10], // Role and Status column
                    render: function(data, type, row) {
                        if (type === 'filter') {
                            return $(data).text().trim();
                        }
                        return data;
                    }
                }
            ],

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
            var value = this.value;

            if (value) {
                table.column(10).search('^' + value + '$', true, false).draw();
            } else {
                table.column(10).search('').draw();
            }
        });
        // Role filter
        $('#filterRole').on('change', function() {
            var value = this.value;

            if (value) {
                table.column(9).search('^' + value + '$', true, false).draw();
            } else {
                table.column(9).search('').draw();
            }
        });

        // Gender filter
        $('#filterGender').on('change', function() {
            var value = this.value;

            if (value) {
                table.column(7).search('^' + value + '$', true, false).draw();
            } else {
                table.column(7).search('').draw();
            }
        });

        // LOCATION filter
        $('#filterFirstName').on('keyup', function() {
            table.column(3).search(this.value).draw();
        });

        // OWNER filter
        $('#filterLastName').on('keyup', function() {
            table.column(4).search(this.value).draw();
        });

        // CATEGORY filter
        $('#filterContactNumber').on('keyup', function() {
            table.column(6).search(this.value).draw();
        });

        // RESET filters
        function resetFilters() {
            $('#filterStatus').val('');
            $('#filterRole').val('');
            $('#filterFirstName').val('');
            $('#filterLastName').val('');
            $('#filterContactNumber').val('');

            table.columns().search('').draw();
        }

        const deleteModal = document.getElementById("deleteModal");

        function openDeleteModal(id) {
            document.getElementById("userId").value = id;
            deleteModal.style.display = "flex";
        }

        function closeDeleteModal() {
            deleteModal.style.display = "none";
        }
    </script>

    <script>
        function toggleStatus(user_id, current_status, btn) {

            fetch("user_status_update.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "user_id=" + user_id + "&current_status=" + current_status
                })
                .then(response => response.json())
                .then(data => {

                    if (data.status === "success") {

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Status Updated Successfully!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                        // 🔥 Update Button Text Instantly
                        btn.innerText = data.buttonText;

                        // Update onclick with new status
                        btn.setAttribute("onclick",
                            "toggleStatus(" + user_id + ", '" + data.newStatus + "', this)");

                        // ✅ Update Status Column Text
                        let statusSpan = document.getElementById("status_" + user_id);
                        statusSpan.innerText = data.newStatus;

                        // ✅ Update Status Badge Class
                        statusSpan.classList.remove("active", "inactive");

                        if (data.newStatus === "Active") {
                            statusSpan.classList.add("active");
                        } else {
                            statusSpan.classList.add("inactive");
                        }

                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'error',
                            title: 'Failed to update status!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }

                });
        }
    </script>

    <script>
        function changeRole(user_id, role, btn) {

            fetch("user_role_update.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "user_id=" + user_id + "&role=" + role
                })
                .then(response => response.json())
                .then(data => {

                    if (data.status === "success") {

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Role Updated Successfully!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                        // Update role text
                        let roleSpan = document.getElementById("role_" + user_id);
                        roleSpan.innerText = data.newRole;

                        // Update role badge class
                        roleSpan.classList.remove("admin", "user", "librarian");
                        roleSpan.classList.add(data.newRole.toLowerCase());

                    } else {

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'error',
                            title: 'Failed to update role!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                    }

                });

        }
    </script>

</body>

</html>