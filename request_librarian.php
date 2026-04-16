<?php
session_start();
include "../db_config.php";

if ($_SESSION['role'] != "User") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['id'];
$error = "";
$success = "";

// Get user data
$userQuery = mysqli_query($con, "SELECT * FROM user WHERE user_id = '$user_id'");
$userData = mysqli_fetch_assoc($userQuery);

if (!$userData) {
    die("User not found");
}

// Prevent librarian/admin from requesting
if ($userData['role'] == 'Librarian' || $userData['role'] == 'Admin') {
    echo "<script>
        alert('You already have elevated access.');
        window.location.href='home.php';
    </script>";
    exit();
}

// Check pending request
$checkPending = mysqli_query($con, "SELECT * FROM librarian_request WHERE user_id='$user_id' AND status='Pending'");
$pendingRequest = mysqli_num_rows($checkPending) > 0;

// Check approved request
$checkApproved = mysqli_query($con, "SELECT * FROM librarian_request WHERE user_id='$user_id' AND status='Approved'");
$approvedRequest = mysqli_num_rows($checkApproved) > 0;

if (isset($_POST['send_request'])) {
    $subject = mysqli_real_escape_string($con, trim($_POST['subject']));
    $message = mysqli_real_escape_string($con, trim($_POST['message']));

    // 🔁 Generate Unique Random ID
    do {
        $request_id = rand(1000, 9999);

        $check_query = mysqli_query(
            $con,
            "SELECT request_id FROM librarian_request WHERE request_id = '$request_id'"
        );
    } while (mysqli_num_rows($check_query) > 0);

    if ($subject == "" || $message == "") {
        $error = "Please fill all required fields.";
    } elseif ($pendingRequest) {
        $error = "You already have a pending request.";
    } elseif ($approvedRequest) {
        $error = "Your librarian request has already been approved.";
    } else {
        $insert = mysqli_query($con, "
            INSERT INTO librarian_request (request_id, user_id, subject, message, status, request_date)
            VALUES ($request_id,'$user_id', '$subject', '$message', 'Pending', NOW())
        ");

        if ($insert) {
            $success = "Your request has been sent successfully to admin.";
            $pendingRequest = true;
        } else {
            $error = "Failed to send request. Please try again.";
        }
    }
}

// Get latest request status
$latestRequestQuery = mysqli_query($con, "
    SELECT * FROM librarian_request 
    WHERE user_id = '$user_id' 
    ORDER BY request_id DESC 
    LIMIT 1
");
$latestRequest = mysqli_fetch_assoc($latestRequestQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Request Librarian Access | Book My Library</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/title_image.png" type="image/png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "JetBrains Mono", "Fira Code", Consolas, monospace;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            color: #fff;
        }

        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 80px);
            /* adjust based on navbar height */
        }

        .page-wrapper {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 24px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .left-panel {
            padding: 35px;
            position: relative;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(56, 189, 248, 0.15);
            color: #7dd3fc;
            border: 1px solid rgba(56, 189, 248, 0.25);
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 13px;
            margin-bottom: 22px;
        }

        .title {
            font-size: clamp(28px, 4vw, 42px);
            line-height: 1.2;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .title span {
            color: #38bdf8;
        }

        .subtitle {
            color: #cbd5e1;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 28px;
        }

        .feature-list {
            display: grid;
            gap: 16px;
            margin-top: 25px;
        }

        .feature-box {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .feature-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #0ea5e9, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.28);
        }

        .feature-box h4 {
            font-size: 15px;
            margin-bottom: 6px;
            color: #f8fafc;
        }

        .feature-box p {
            font-size: 13px;
            line-height: 1.6;
            color: #cbd5e1;
        }

        .right-panel {
            padding: 32px;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-header h2 {
            font-size: 28px;
            color: #f8fafc;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.7;
        }

        .user-box,
        .status-box {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #7dd3fc;
            font-size: 15px;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .user-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 14px;
            padding: 14px;
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .info-item span {
            font-size: 14px;
            color: #f8fafc;
            font-weight: 600;
            word-break: break-word;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }

        .pending {
            background: rgba(245, 158, 11, 0.18);
            color: #facc15;
            border: 1px solid rgba(245, 158, 11, 0.22);
        }

        .approved {
            background: rgba(34, 197, 94, 0.18);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.22);
        }

        .rejected {
            background: rgba(239, 68, 68, 0.18);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.22);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #e2e8f0;
            font-size: 14px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            outline: none;
            transition: 0.25s ease;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus {
            border-color: rgba(56, 189, 248, 0.55);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.10);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
            line-height: 1.7;
        }

        .helper-text {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 7px;
        }

        .btn-submit {
            width: 100%;
            border: none;
            padding: 15px 20px;
            border-radius: 16px;
            background: linear-gradient(90deg, #0ea5e9, #2563eb);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.24);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 32px rgba(37, 99, 235, 0.33);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        .note-box {
            margin-top: 18px;
            background: rgba(14, 165, 233, 0.09);
            border: 1px solid rgba(56, 189, 248, 0.18);
            color: #cfefff;
            padding: 14px 16px;
            border-radius: 14px;
            font-size: 13px;
            line-height: 1.7;
        }

        @media (max-width: 992px) {
            .page-wrapper {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 14px;
            }

            .left-panel,
            .right-panel {
                padding: 22px;
            }

            .user-grid {
                grid-template-columns: 1fr;
            }

            .form-header h2 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>

    <?php include_once "navbar.php"; ?>

    <div class="main-container">
        <div class="page-wrapper">

            <!-- LEFT PANEL -->
            <div class="glass-card left-panel">
                <div class="brand-badge">
                    <i class="fa-solid fa-book-open-reader"></i>
                    Book My Library Access Request
                </div>

                <h1 class="title">
                    Become a <span>Librarian</span> in your library system
                </h1>

                <p class="subtitle">
                    Submit a professional request to the administrator for librarian access.
                    Once approved, you will be able to manage books, handle issue-return activity,
                    and access table and chair related operations.
                </p>

                <div class="feature-list">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fa-solid fa-book"></i>
                        </div>
                        <div>
                            <h4>Book Management Access</h4>
                            <p>Manage books, issue-return records, renewals, and library inventory from your dashboard.</p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fa-solid fa-chair"></i>
                        </div>
                        <div>
                            <h4>Table & Chair Control</h4>
                            <p>Handle library seating, chair view, booking workflows, and table arrangements efficiently.</p>
                        </div>
                    </div>

                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h4>Admin Approval Process</h4>
                            <p>Your request is securely reviewed by the admin before any role change is applied to your account.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="glass-card right-panel">
                <div class="form-header">
                    <h2>Request Librarian Access</h2>
                    <p>
                        Fill in the details below and send your request to the administrator.
                        Make sure your reason is clear and professional.
                    </p>
                </div>

                <div class="user-box">
                    <div class="section-title">
                        <i class="fa-solid fa-user"></i>
                        User Information
                    </div>

                    <div class="user-grid">
                        <div class="info-item">
                            <label>Full Name</label>
                            <span><?php echo htmlspecialchars($userData['first_name'] . " " . $userData['last_name']); ?></span>
                        </div>

                        <div class="info-item">
                            <label>Email Address</label>
                            <span><?php echo htmlspecialchars($userData['email']); ?></span>
                        </div>

                        <div class="info-item">
                            <label>Current Role</label>
                            <span><?php echo htmlspecialchars($userData['role']); ?></span>
                        </div>

                        <div class="info-item">
                            <label>User ID</label>
                            <span><?php echo htmlspecialchars($userData['user_id']); ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($approvedRequest) { ?>
                    <div class="status-box">
                        <div class="section-title">
                            <i class="fa-solid fa-circle-check"></i>
                            Request Approved
                        </div>

                        <div class="note-box" style="margin-top:0;">
                            <i class="fa-solid fa-circle-info"></i>
                            <strong>Congratulations!</strong>
                            Your request for librarian access has been approved by the admin.
                            Now you need to enter your library details to complete your librarian setup.
                        </div>

                        <div style="margin-top: 18px;">
                            <a href="add_library.php" style="text-decoration:none;">
                                <button type="button" class="btn-submit">
                                    <i class="fa-solid fa-building"></i> Complete Library Details
                                </button>
                            </a>
                        </div>
                    </div>
                <?php } ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Request Subject</label>
                        <input
                            type="text"
                            name="subject"
                            class="form-control"
                            placeholder="Example: Request for Librarian Role Access"
                            value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>"
                            <?php echo ($pendingRequest || $approvedRequest) ? 'disabled' : ''; ?>>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Reason / Description</label>
                        <textarea
                            name="message"
                            class="form-control"
                            placeholder="Write why you need librarian access, what responsibilities you will handle, and why admin should approve your request..."
                            <?php echo ($pendingRequest || $approvedRequest) ? 'disabled' : ''; ?>><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        <div class="helper-text">
                            Write a clear explanation for faster admin review.
                        </div>
                    </div>

                    <button
                        type="submit"
                        name="send_request"
                        class="btn-submit"
                        <?php echo ($pendingRequest || $approvedRequest) ? 'disabled' : ''; ?>>
                        Send Request to Admin
                    </button>
                </form>

                <div class="note-box">
                    <i class="fa-solid fa-lightbulb"></i>
                    <strong>Note:</strong>
                    Once the admin approves your request, your role will be updated to
                    <strong>Librarian</strong> and you will gain access to librarian features.
                </div>
            </div>
        </div>
    </div>

    <?php if ($error != "") { ?>
        <script>
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'error',
                title: '<?php echo addslashes($error); ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    <?php } ?>

    <?php if ($success != "") { ?>
        <script>
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                title: '<?php echo addslashes($success); ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    <?php } ?>

</body>

</html>