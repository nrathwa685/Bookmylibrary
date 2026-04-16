<!DOCTYPE html>
<html lang="en">
<?php 
include '../db_config.php';
include '../update_status.php'; 
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ---------- NAVBAR ---------- */
        .navbar {
            background: rgba(15, 23, 42, 0.95);
            color: #fff;
            padding: 12px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
            font-family: "JetBrains Mono", "Fira Code", Consolas, monospace;

        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-left img {
            height: 80px;
            width: 130px;
        }

        .nav-center {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .nav-center a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 15px;
        }

        .nav-center a:hover {
            color: #38bdf8;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
        }

        .profile img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid #38bdf8;
            object-fit: cover;
        }

        .logout-btn {
            padding: 6px 14px;
            border-radius: 6px;
            background: #ef4444;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        /* Dropdown container */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Dropdown link */
        .dropdown>a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 15px;
            cursor: pointer;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Dropdown link */
        .dropdown>a.dropbtn {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 15px;
            cursor: pointer;
        }

        /* Dropdown content */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: rgba(15, 23, 42, 0.95);
            min-width: 160px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 100;
            top: 35px;
        }

        /* Links inside dropdown */
        .dropdown-content a {
            color: #fff;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            transition: background 0.2s;
        }

        .dropdown-content a:hover {
            color: #38bdf8;
            /* darker blue */
        }

        /* Show dropdown when active */
        .dropdown-content.show {
            display: block;
        }

        .dropdown-content hr {
            width: 100%;
            margin: 6px 0;
            border: none;
            height: 1px;
            background: linear-gradient(to right, transparent, #999, transparent);
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 10px;
            }

            .nav-center {
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav-right {
                justify-content: center;
            }

            .nav-center {
                flex-direction: column;
                gap: 8px;
            }

            .dropdown-content {
                position: relative;
                top: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <?php
    $user_id = $_SESSION['id'];
    $user = mysqli_query($con, "SELECT * FROM user WHERE user_id = $user_id");
    $data = mysqli_fetch_assoc($user);
    ?>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="nav-left">
            <a href="home.php">
                <img src="../image/BookMyLibrary.png" alt="Library Logo">
            </a>
        </div>

        <div class="nav-center">
            <a href="home.php">Dashboard</a>

            <!-- Books Dropdown -->
            <!-- <div class="dropdown">
                <a href="javascript:void(0);" class="dropbtn">Book <i class="fa-solid fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="book_list.php">Book List</a>
                    <a href="issued_book.php">Issued Book</a>
                </div>
            </div> -->

            <a href="book.php">Book</a>

            <a href="issued_book.php">Issued Book</a>

            <!-- <a href="user_list.php">User</a> -->

            <!-- Library Dropdown -->
            <!-- <a href="library_list.php">Library</a> -->

            <div class="dropdown">
                <a href="javascript:void(0);" class="dropbtn">Fine <i class="fa-solid fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="fine_list.php">Fine List</a>
                    <a href="pending_fine_list.php">Fine Pending</a>
                </div>
            </div>

            <!-- Category Dropdown -->
            <!-- <a href="category_list.php">Category</a> -->

            <!-- <a href="review&rating_list.php">Review & Rating</a> -->

            <a href="view_table&chair.php">View Table & Chair</a>

            <a href="request_librarian.php">Request</a>
        </div>

        <div class="nav-right">
            <div class="profile">
                <img src="../image/<?php echo $data['image']; ?>" alt="Profile">
                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropbtn"><?php echo $data['first_name'] . " " . $data['last_name']; ?></a>
                    <div class="dropdown-content">
                        <a href="profile.php">My Profile</a>
                        <a href="../change_password.php">Change Password</a>
                        <!-- <a href="#">Screen Lock</a> -->
                        <hr>
                        <a href="../login.php" style="color:#ef4444;">Logout</a>

                    </div>
                </div>
            </div>
            <a href="../login.php" class="logout-btn">Logout</a>
        </div>
    </nav>
</body>
<script>
    // Select all dropdown buttons
    const dropdowns = document.querySelectorAll('.dropbtn');

    dropdowns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent page jump
            const content = this.nextElementSibling;

            // Toggle current dropdown
            content.classList.toggle('show');

            // Close other dropdowns
            dropdowns.forEach(other => {
                if (other !== this) {
                    other.nextElementSibling.classList.remove('show');
                }
            });
        });
    });

    // Close dropdown if clicked outside
    window.addEventListener('click', function(e) {
        if (!e.target.matches('.dropbtn')) {
            document.querySelectorAll('.dropdown-content').forEach(dc => dc.classList.remove('show'));
        }
    });
</script>

</html>