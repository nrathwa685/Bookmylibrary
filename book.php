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
    <title>Book | Library System</title>
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
            color: #fff;
        }

        /* container */
        .container {
            width: 95%;
            max-width: 1400px;
            margin-top: 20px;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
        }

        /* top bar */
        .top-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .top-bar input,
        .top-bar select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(15, 23, 42, 0.95);
            color: #fff;
            border-radius: 8px;
        }

        /* reset button */
        .reset-btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            background: #ef4444;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
        }

        .reset-btn:hover {
            background: #dc2626;
        }

        /* grid */
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            justify-items: center;
            /* center cards horizontally */
        }

        /* card */
        .book-card {
            width: 100%;
            /* NOT 220px */
            max-width: 220px;
            /* keeps design size */
            height: 300px;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            transition: 0.4s ease;
            cursor: pointer;
        }

        /* Netflix expand */
        .book-card:hover {
            transform: scale(1.08);
            z-index: 10;
        }

        /* Image */
        .book-img {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        /* Glass blur */
        .glass {
            position: absolute;
            inset: 0;
            backdrop-filter: blur(6px);
            background: rgba(0, 0, 0, 0.25);
            opacity: 0;
            transition: 0.4s;
        }

        .book-card:hover .glass {
            opacity: 1;
        }

        .book-card.available .glass {
            background: linear-gradient(to top,
                    rgba(16, 185, 129, 0.95),
                    rgba(34, 197, 94, 0.6),
                    transparent);
        }


        .book-card.unavailable .glass {
            background: linear-gradient(to top,
                    rgba(220, 38, 38, 0.95),
                    rgba(239, 68, 68, 0.6),
                    transparent);
        }


        /* Hidden text */
        .book-content {
            position: absolute;
            bottom: 0;
            padding: 14px;
            color: white;
            opacity: 0;
            transform: translateY(20px);
            transition: 0.4s;
        }

        /* Show text on hover */
        .book-card:hover .book-content {
            opacity: 1;
            transform: translateY(0);
        }

        /* Title */
        .book-title {
            font-size: 16px;
            font-weight: 600;
        }

        /* Animated stars */
        .stars {
            color: #ffd700;
            font-size: 16px;
            letter-spacing: 1px;
        }

        .rating-text {
            font-size: 13px;
            color: #fff;
        }

        .book-card:hover .stars {
            transform: scale(0.9);
            letter-spacing: 2px;
        }

        /* Info */
        .book-info {
            font-size: 12px;
            margin-top: 4px;
        }

        /* Status */
        .status-badge {
            padding: 3px 8px;
            font-size: 11px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 6px;
        }

        .available {
            color: #dcfce7;
            background: #166534;
        }

        .unavailable {
            color: #fee2e2;
            background: #991b1b;
        }

        /* Button */
        .book-btn {
            margin-top: 10px;
            width: 100%;
            padding: 8px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 12px;
            font-weight: 600;
        }


        /* responsive */
        @media(max-width:1200px) {
            .book-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media(max-width:900px) {
            .book-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:600px) {
            .book-grid {
                grid-template-columns: repeat(1, 1fr);
            }
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

        .book-details input {
            width: 100%;
            padding: 7px 10px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 13px;
            margin-top: 4px;
        }

        .date-error {
            color: #ef4444;
            font-size: 11px;
            display: block;
            margin-top: 3px;
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

        .tc-box {
            display: block;
            margin-bottom: 12px;
            font-size: 13px;
            color: #374151;
        }

        .tc-box input {
            margin-right: 6px;
            transform: scale(1.1);
        }

        .view-tc {
            color: #2563eb;
            cursor: pointer;
            font-weight: 600;
            margin-left: 4px;
        }

        .view-tc:hover {
            text-decoration: underline;
        }

        .tc-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .tc-box-card {
            width: 500px;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .tc-header {
            background: #f3f4f6;
            color: #1e293b;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            font-weight: 600;
        }

        .tc-body {
            padding: 18px;
            font-size: 14px;
            color: #374151;
        }

        .tc-body ul {
            padding-left: 18px;
            line-height: 1.8;
        }

        .tc-footer {
            padding: 14px;
            text-align: right;
            background: #f9fafb;
        }

        .tc-footer button {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #cbd5f5;
            background: #f8fafc;
            color: #000;
            cursor: pointer;
        }

        .tc-footer button:hover {
            background: #e0e7ff;
        }

        /* Buttons */
        .btn-secondary {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #cbd5f5;
            background: #f8fafc;
            color: #000;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #e0e7ff;
        }

        .btn-confirm {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #cbd5f5;
            background: #16a34a;
            color: #fff;
            cursor: pointer;
        }

        .btn-confirm:hover {
            background: #078937;
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
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>
    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb">
            <a href="home.php" class="dashboard">Dashboard</a>
            <span class="separator">›</span>
            <span class="current">Book</span>
        </nav>
    </div>

    <div class="container">

        <div class="top-bar">
            <input type="text" id="searchBook" placeholder="Search by title, author, year">

            <select id="categoryFilter">
                <option value="">All Categories</option>
                <?php
                $categorys = mysqli_query($con, "SELECT * FROM category WHERE status = 'Active'");

                foreach ($categorys as $row) {
                    echo "<option value='{$row['category_name']}'>
                                             {$row['category_name']}
                                          </option>";
                }
                ?>
            </select>

            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="Available">Available</option>
                <option value="Unavailable">Unavailable</option>
            </select>

            <select id="libraryFilter">
                <option value="">All Library</option>
                <?php
                $libraries = mysqli_query($con, "SELECT * FROM library WHERE status = 'Active'");

                foreach ($libraries as $row) {
                    echo "<option value='{$row['library_name']}'>
                                             {$row['library_name']}
                                          </option>";
                }
                ?>
            </select>

            <select id="languageFilter">
                <option value="">All Language</option>
                <option value="English">English</option>
                <option value="Hindi">Hindi</option>
            </select>

            <select id="ratingFilter">
                <option value="">All Rating</option>
                <option value="5">5 Star</option>
                <option value="4">4 Star & Above</option>
                <option value="3">3 Star & Above</option>
                <option value="2">2 Star & Above</option>
            </select>


            <button class="reset-btn" onclick="resetFilters()">Reset</button>
        </div>


        <div class="book-grid" id="bookGrid">

            <?php
            $books = mysqli_query($con, "SELECT * FROM book_list");

            foreach ($books as $row) {

                $statusClass = ($row['available_copy'] == 0) ? "unavailable" : "available";
                $statusText  = ($row['available_copy'] == 0) ? "Unavailable" : "Available";
                $library_name = mysqli_fetch_assoc(mysqli_query($con, "SELECT library_name FROM library WHERE library_id = {$row['library_id']}"));

                echo "
                        <div class='book-card " . strtolower($statusClass) . "'
                            data-id='{$row['book_id']}'
                            data-title='{$row['title']}'
                            data-author='{$row['author']}'
                            data-year='{$row['year']}'
                            data-category='{$row['category']}'
                            data-status='{$statusText}'
                            data-library='{$library_name['library_name']}'
                            data-language='{$row['language']}'
                            data-rating='{$row['rating']}'>

                            <div class='book-img'
                                style=\"background-image:url('../book_images/{$row['image']}')\">

                                <div class='glass'></div>

                                <div class='book-content'>
                                    <div class='status-badge {$statusClass}'>{$statusText}</div>

                                    <div class='book-title'>{$row['title']}</div>

                                    <div class='book-info'>Author: {$row['author']}</div>
                                    <div class='book-info'>Year: {$row['year']}</div>
                                    <div class='book-info'>Category: {$row['category']}</div>
                                    <div class='book-info'>Library: {$library_name['library_name']}</div>
                                    <div class='book-info'>Language: {$row['language']}</div>
                                    <div class='book-info'>Available Copy: {$row['available_copy']}</div>

                                    <div class='book-rating'>
                                        <span class='stars'></span>
                                        <span class='rating-text'>({$row['rating']})</span>
                                    </div>

                                    <button class='book-btn'
                                        data-status='{$statusText}'
                                        data-id='{$row['book_id']}'>
                                        Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                        ";
            }
            ?>

        </div>

    </div>


    <!-- BOOK NOW MODAL -->
    <div class="modal-backdrop" id="bookModal">
        <form method="post">
            <div class="modal-card">

                <div class="modal-header-p">
                    <h3>Book Details</h3>
                    <span class="close-icon" onclick="closeModal()">×</span>
                </div>

                <div class="modal-body-p">
                    <div class="book-image">
                        <img class="modalBookImage" src="" alt="Book Image">
                    </div>

                    <div class="book-details">

                        <div class="detail">
                            <span>Book ID</span>
                            <input type="text" name="book_id" readonly>
                        </div>

                        <div class="detail">
                            <span>Title</span>
                            <input type="text" name="title" readonly>
                        </div>

                        <div class="detail">
                            <span>Author</span>
                            <input type="text" name="author" readonly>
                        </div>

                        <div class="detail">
                            <span>Category</span>
                            <input type="text" name="category" readonly>
                        </div>

                        <div class="detail">
                            <span>Publish Year</span>
                            <input type="text" name="year" readonly>
                        </div>

                        <div class="detail">
                            <span>Library Name</span>
                            <input type="text" name="library" readonly>
                        </div>

                        <!-- NEW FIELD -->
                        <div class="detail">
                            <span>Issue Date</span>
                            <input type="date" name="issue_date">
                            <small class="date-error issue-error"></small>
                        </div>

                        <div class="detail">
                            <span>Return Date</span>
                            <input type="date" name="return_date">
                            <small class="date-error return-error"></small>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">

                    <label class="tc-box">
                        <input type="checkbox" name="agree_tc" id="agree_tc">
                        I agree to
                        <span class="view-tc" onclick="openTC()">Terms & Conditions</span>
                    </label>

                    <button type="button" class="btn-secondary" onclick="closeModal()">Close</button>
                    <button type="submit" class="btn-confirm" name="confirm_btn" id="confirm_btn" disabled>
                        Confirm Booking
                    </button>
                </div>

            </div>
        </form>
    </div>

    <?php
    include_once "../send_mail.php";
    if (isset($_POST['confirm_btn'])) {

        if (!isset($_POST['agree_tc'])) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'warning',
                        title: 'Please accept Terms & Conditions first!',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                });
              </script>";
        } else {

            // 🔁 Generate Unique Random ID
            do {
                $issue_id = rand(10000000, 99999999);

                $check_query = mysqli_query(
                    $con,
                    "SELECT issue_id FROM issue WHERE issue_id = '$issue_id'"
                );
            } while (mysqli_num_rows($check_query) > 0);

            $book_id = $_POST['book_id'];
            $user_id = $_SESSION['id'];
            $library_name = $_POST['library'];

            $query = "SELECT library_id FROM library WHERE library_name = '$library_name'";
            $result = mysqli_query($con, $query);
            $row = mysqli_fetch_assoc($result);

            $library_id = $row['library_id'];
            $issue_date = $_POST['issue_date'];
            $return_date = $_POST['return_date'];
            $fine_amount = 0;
            $status = "Pending";

            $user_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM user WHERE user_id = $user_id"));
            $book_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT title FROM book_list WHERE book_id = $book_id"));

            $insert_query = "INSERT INTO issue 
                        (issue_id, book_id, user_id, library_id, issue_date, return_date, fine_amount, status)
                        VALUES($issue_id, $book_id, $user_id, $library_id, '$issue_date', '$return_date', $fine_amount, '$status')";

            $checkIssue = mysqli_query($con, "SELECT * FROM issue WHERE book_id = '$book_id' AND user_id = $user_id AND status != 'Returned'");

            if (mysqli_num_rows($checkIssue) > 0) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function(){
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'warning',
                            title: 'Book is already issued and not returned!',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                    });
                </script>";
            } else {
                if (mysqli_query($con, $insert_query)) {

                    mysqli_query($con, "UPDATE book_list SET available_copy = available_copy - 1 WHERE book_id='$book_id'");

                    sendLibraryMail(
                        $user_data['email'],
                        $user_data['first_name'] . " " . $user_data['last_name'],
                        $book_data['title'],
                        "Pending",
                        $return_date
                    );

                    mysqli_query($con, "UPDATE issue SET last_mailed_status = 'Pending' WHERE issue_id = $issue_id");

                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function(){
                                Swal.fire({
                                    toast: true,
                                    position: 'top',
                                    icon: 'success',
                                    title: 'Request sent! Waiting for approval 📚',
                                    text: 'Collect the book within 24 hours after approval.',
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true,
                                    didClose: () => {
                                        window.location.href = 'issued_book.php';
                                    }
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
                                    title: 'Request failed ❌',
                                    text: 'Unable to process your request. Please try again.',
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                });
                            });
                        </script>";
                }
            }
        }
    }
    ?>

    <div class="tc-modal" id="tcModal">
        <div class="tc-box-card">

            <div class="tc-header">
                <h3>Library Terms & Conditions</h3>
                <span class="close-icon" onclick="closeTC()">×</span>
            </div>

            <div class="tc-body">
                <ul>
                    <li>Books must be returned within the assigned return date.</li>
                    <li>Late return will result in ₹5/day fine.</li>
                    <li>Damaged books must be replaced by the borrower.</li>
                    <li>Only registered users can issue books.</li>
                    <li>Maximum 2 books allowed per user.</li>
                    <li>Library ID must be shown while collecting book.</li>
                    <li>Maintain silence and discipline inside the library.</li>
                    <li>Books should be handled carefully and kept clean.</li>
                </ul>
            </div>

            <div class="tc-footer">
                <button onclick="closeTC()">Close</button>
            </div>

        </div>
    </div>



    <?php include 'footer.php'; ?>

</body>

<script>
    function openModel() {
        document.getElementById("bookModal").style.display = "flex";
    }

    const modal = document.getElementById("bookModal");

    // OPEN MODAL
    document.querySelectorAll(".book-btn").forEach(btn => {
        btn.addEventListener("click", function() {

            const status = this.dataset.status;

            if (status.toLowerCase() === "unavailable") {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'This book is currently unavailable',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                return;
            }

            const card = this.closest(".book-card");

            modal.querySelector('[name="book_id"]').value = card.dataset.id || "N/A";
            modal.querySelector('[name="title"]').value = card.dataset.title;
            modal.querySelector('[name="author"]').value = card.dataset.author;
            modal.querySelector('[name="category"]').value = card.dataset.category;
            modal.querySelector('[name="year"]').value = card.dataset.year;
            modal.querySelector('[name="library"]').value = card.dataset.library;

            const bg = card.querySelector(".book-img").style.backgroundImage;
            const url = bg.slice(5, -2);
            modal.querySelector(".modalBookImage").src = url;

            const today = new Date().toISOString().split('T')[0];
            modal.querySelector('[name="issue_date"]').value = today;

            const returnDate = new Date();
            returnDate.setDate(returnDate.getDate() + 7);
            modal.querySelector('[name="return_date"]').value = returnDate.toISOString().split('T')[0];

            // reset checkbox every time modal opens
            modal.querySelector('[name="agree_tc"]').checked = false;

            validateForm();
            modal.style.display = "flex";
        });
    });

    function closeModal() {
        modal.style.display = "none";
    }

    window.onclick = function(e) {
        if (e.target === modal) modal.style.display = "none";
    };

    function openTC() {
        document.getElementById("tcModal").style.display = "flex";
    }

    function closeTC() {
        document.getElementById("tcModal").style.display = "none";
    }

    const issueInput = modal.querySelector('[name="issue_date"]');
    const returnInput = modal.querySelector('[name="return_date"]');
    const agreeTC = modal.querySelector('[name="agree_tc"]');

    const issueError = modal.querySelector('.issue-error');
    const returnError = modal.querySelector('.return-error');

    const confirmBtn = modal.querySelector('.btn-confirm');

    function validateForm() {
        const issueValue = issueInput.value;
        const returnValue = returnInput.value;

        const issueDate = issueValue ? new Date(issueValue) : null;
        const returnDate = returnValue ? new Date(returnValue) : null;
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        let valid = true;

        // ISSUE DATE VALIDATION
        if (issueValue === "") {
            issueError.textContent = "Please select issue date";
            valid = false;
        } else if (issueDate < today) {
            issueError.textContent = "Issue date cannot be in past";
            valid = false;
        } else {
            issueError.textContent = "";
        }

        // RETURN DATE VALIDATION
        if (returnValue === "") {
            returnError.textContent = "Please select return date";
            valid = false;
        } else if (returnDate <= issueDate) {
            returnError.textContent = "Return date must be after issue date";
            valid = false;
        } else {
            returnError.textContent = "";
        }

        // T&C must be checked
        if (!agreeTC.checked) {
            valid = false;
        }

        confirmBtn.disabled = !valid;
        return valid;
    }

    function confirmBooking(event) {
        if (!validateForm()) {
            event.preventDefault();

            if (!agreeTC.checked) {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'warning',
                    title: 'Please agree to Terms & Conditions',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        }
    }

    issueInput.addEventListener("change", validateForm);
    returnInput.addEventListener("change", validateForm);
    agreeTC.addEventListener("change", validateForm);

    function printStars(rating) {
        let full = Math.floor(rating);
        let half = rating % 1 >= 0.5 ? 1 : 0;
        let empty = 5 - full - half;

        return "★".repeat(full) + (half ? "⯪" : "") + "☆".repeat(empty);
    }

    let ratingValue = 3.9;

    document.querySelector(".stars").textContent = printStars(ratingValue);
    document.querySelector(".rating-text").textContent = "(" + ratingValue + ")";

    document.querySelectorAll(".book-card").forEach(card => {
        let rating = parseFloat(card.dataset.rating);
        let starsEl = card.querySelector(".stars");
        let textEl = card.querySelector(".rating-text");

        starsEl.textContent = printStars(rating);
        textEl.textContent = "(" + rating + ")";
    });
</script>

<script>
    const searchInput = document.getElementById("searchBook");
    const categoryFilter = document.getElementById("categoryFilter");
    const statusFilter = document.getElementById("statusFilter");
    const libraryFilter = document.getElementById("libraryFilter");
    const languageFilter = document.getElementById("languageFilter");
    const ratingFilter = document.getElementById("ratingFilter");

    const books = document.querySelectorAll(".book-card");

    function filterBooks() {

        let searchValue = searchInput.value.toLowerCase();
        let categoryValue = categoryFilter.value.toLowerCase();
        let statusValue = statusFilter.value.toLowerCase();
        let libraryValue = libraryFilter.value.toLowerCase();
        let languageValue = languageFilter.value.toLowerCase();
        let ratingValue = ratingFilter.value;

        books.forEach(book => {

            let title = book.dataset.title.toLowerCase();
            let author = book.dataset.author.toLowerCase();
            let year = book.dataset.year.toLowerCase();
            let category = book.dataset.category.toLowerCase();
            let status = book.dataset.status.toLowerCase();
            let library = book.dataset.library.toLowerCase();
            let language = book.dataset.language.toLowerCase();
            let rating = parseFloat(book.dataset.rating);

            let matchesSearch =
                title.includes(searchValue) ||
                author.includes(searchValue) ||
                year.includes(searchValue);

            let matchesCategory = !categoryValue || category === categoryValue;
            let matchesStatus = !statusValue || status === statusValue;
            let matchesLibrary = !libraryValue || library === libraryValue;
            let matchesLanguage = !languageValue || language === languageValue;

            /* Rating filter */
            let matchesRating = !ratingValue || rating >= parseFloat(ratingValue);

            if (matchesSearch && matchesCategory && matchesStatus &&
                matchesLibrary && matchesLanguage && matchesRating) {
                book.style.display = "block";
            } else {
                book.style.display = "none";
            }
        });
    }


    /* Event listeners */
    searchInput.addEventListener("keyup", filterBooks);
    categoryFilter.addEventListener("change", filterBooks);
    statusFilter.addEventListener("change", filterBooks);
    libraryFilter.addEventListener("change", filterBooks);
    languageFilter.addEventListener("change", filterBooks);
    ratingFilter.addEventListener("change", filterBooks);


    /* Reset */
    function resetFilters() {
        searchInput.value = "";
        categoryFilter.value = "";
        statusFilter.value = "";
        libraryFilter.value = "";
        languageFilter.value = "";
        ratingFilter.value = "";
        filterBooks();
    }
</script>


</html>