<?php
require "../session_check.php";
require "../db_config.php";


if ($_SESSION['role'] != "User") {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['id']);

/* ---------------------------------------------------
   AUTO EXPIRE OLD BOOKINGS
--------------------------------------------------- */
mysqli_query($con, "
    UPDATE library_chairs lc
    JOIN chair_bookings cb ON cb.chair_id = lc.chair_id
    SET lc.status = 'available', cb.status = 'expired'
    WHERE cb.status = 'active'
      AND cb.end_time <= NOW()
");

/* ---------------------------------------------------
   HELPER: JSON RESPONSE
--------------------------------------------------- */
function jsonResponse($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode([
        "status" => $status,
        "message" => $message
    ]);
    exit;
}

/* ---------------------------------------------------
   HELPER: CHAIR POSITION
--------------------------------------------------- */
function getChairPosition($index, $total)
{
    $chairsPerSide = ceil($total / 4);

    if ($index <= $chairsPerSide) {
        return ["side" => "top", "ratio" => $index / ($chairsPerSide + 1)];
    }

    if ($index <= $chairsPerSide * 2) {
        $sideIndex = $index - $chairsPerSide;
        return ["side" => "right", "ratio" => $sideIndex / ($chairsPerSide + 1)];
    }

    if ($index <= $chairsPerSide * 3) {
        $sideIndex = $index - ($chairsPerSide * 2);
        return ["side" => "bottom", "ratio" => $sideIndex / ($chairsPerSide + 1)];
    }

    $sideIndex = $index - ($chairsPerSide * 3);
    return ["side" => "left", "ratio" => $sideIndex / ($chairsPerSide + 1)];
}

/* ---------------------------------------------------
   BOOK CHAIR FOR 2 HOUR
--------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book_chair') {

    $library_id = intval($_POST['library_id'] ?? 0);
    $table_id   = intval($_POST['table_id'] ?? 0);
    $chair_id   = intval($_POST['chair_id'] ?? 0);

    if ($library_id <= 0 || $table_id <= 0 || $chair_id <= 0) {
        jsonResponse("error", "Invalid booking data.");
    }

    mysqli_begin_transaction($con);

    try {
        /* re-expire old bookings inside transaction */
        mysqli_query($con, "
            UPDATE chair_bookings
            SET status = 'expired'
            WHERE status = 'active' AND end_time <= NOW()
        ");

        /* check chair exists */
        $chairCheck = mysqli_query($con, "
            SELECT * FROM library_chairs
            WHERE chair_id = '$chair_id'
              AND table_id = '$table_id'
              AND library_id = '$library_id'
            LIMIT 1
        ");

        if (mysqli_num_rows($chairCheck) == 0) {
            throw new Exception("Chair not found.");
        }

        /* check active booking already exists */
        $activeCheck = mysqli_query($con, "
            SELECT * FROM chair_bookings
            WHERE chair_id = '$chair_id'
              AND table_id = '$table_id'
              AND library_id = '$library_id'
              AND status = 'active'
              AND NOW() < end_time
            LIMIT 1
        ");

        if (mysqli_num_rows($activeCheck) > 0) {
            throw new Exception("This chair is already booked.");
        }

        /* optional: prevent same user from booking multiple chairs at same time */
        $userActiveCheck = mysqli_query($con, "
            SELECT * FROM chair_bookings
            WHERE user_id = '$user_id'
              AND status = 'active'
              AND NOW() < end_time
            LIMIT 1
        ");

        if (mysqli_num_rows($userActiveCheck) > 0) {
            throw new Exception("You already have an active chair booking.");
        }

        date_default_timezone_set('Asia/Kolkata');
        mysqli_query($con, "SET time_zone = '+05:30'");

        $start_time   = date("Y-m-d H:i:s");
        $end_time     = date("Y-m-d H:i:s", strtotime($start_time . " +2 hour"));
        $booking_date = date("Y-m-d");

        $insert = mysqli_query($con, "INSERT INTO chair_bookings
                    (library_id, table_id, chair_id, user_id, booking_date, start_time, end_time, status)
                    VALUES
                    ('$library_id', '$table_id', '$chair_id', '$user_id', '$booking_date', '$start_time', '$end_time', 'active')
                ");

        if (!$insert) {
            throw new Exception(mysqli_error($con));
        }

        $updateChair = mysqli_query($con, "UPDATE library_chairs
                    SET status = 'booked'
                    WHERE chair_id = '$chair_id'
                    AND table_id = '$table_id'
                    AND library_id = '$library_id'
                ");

        if (!$updateChair) {
            throw new Exception(mysqli_error($con));
        }

        mysqli_commit($con);

        /* ---------------- EMAIL SEND AFTER SUCCESSFUL BOOKING ---------------- */

        $userQuery = mysqli_query($con, "SELECT first_name, last_name, email FROM user WHERE user_id = '$user_id'");
        $userData  = mysqli_fetch_assoc($userQuery);

        $libraryQuery = mysqli_query($con, "SELECT library_name FROM library WHERE library_id = '$library_id'");
        $libraryData  = mysqli_fetch_assoc($libraryQuery);

        $userName    = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
        $userEmail   = $userData['email'] ?? '';
        $libraryName = $libraryData['library_name'] ?? 'Library';

        $tableQuery = mysqli_query($con, "
                SELECT table_name 
                FROM library_tables 
                WHERE table_id = '$table_id'
            ");
        $tableData = mysqli_fetch_assoc($tableQuery);
        $tableName = $tableData['table_name'] ?? 'Table';

        $chairQuery = mysqli_query($con, "
                SELECT chair_no 
                FROM library_chairs 
                WHERE chair_id = '$chair_id'
            ");
        $chairData = mysqli_fetch_assoc($chairQuery);
        $chairName = "C" . ($chairData['chair_no'] ?? '0');

        if (!empty($userEmail)) {
            require_once "../send_mail.php"; // adjust path if needed

            $mailSent = sendChairBookingMail(
                $userEmail,
                $userName,
                $libraryName,
                $tableName,
                $chairName,
                $start_time,
                $end_time
            );

            // Optional: if mail fails, just log it, do not cancel booking
            if (!$mailSent) {
                error_log("Chair booking mail failed for user_id: $user_id");
            }
        }

        jsonResponse("success", "Chair booked successfully for 2 hour.");
    } catch (Exception $e) {
        mysqli_rollback($con);
        jsonResponse("error", $e->getMessage());
    }
}

/* ---------------------------------------------------
   LOAD LIBRARIES
--------------------------------------------------- */
$libraries = [];
$libraryQuery = mysqli_query($con, "SELECT library_id, library_name FROM library ORDER BY library_name ASC");
while ($row = mysqli_fetch_assoc($libraryQuery)) {
    $libraries[] = $row;
}

$selected_library_id = isset($_GET['library_id']) ? intval($_GET['library_id']) : 0;

/* ---------------------------------------------------
   LOAD TABLES + CHAIRS FOR SELECTED LIBRARY
--------------------------------------------------- */
$tables = [];

if ($selected_library_id > 0) {

    $tableQuery = mysqli_query($con, "
        SELECT * FROM library_tables
        WHERE library_id = '$selected_library_id'
        ORDER BY table_id ASC
    ");

    while ($tableRow = mysqli_fetch_assoc($tableQuery)) {
        $table_id = $tableRow['table_id'];

        $chairs = [];

        $chairQuery = mysqli_query($con, "
            SELECT 
                lc.*,
                CASE 
                    WHEN EXISTS (
                        SELECT 1
                        FROM chair_bookings cb
                        WHERE cb.chair_id = lc.chair_id
                          AND cb.status = 'active'
                          AND NOW() < cb.end_time
                    ) THEN 'booked'
                    ELSE 'available'
                END AS current_status
            FROM library_chairs lc
            WHERE lc.table_id = '$table_id'
              AND lc.library_id = '$selected_library_id'
            ORDER BY lc.chair_no ASC
        ");

        while ($chairRow = mysqli_fetch_assoc($chairQuery)) {
            $chairs[] = $chairRow;
        }

        $tableRow['chairs'] = $chairs;
        $tables[] = $tableRow;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Table & Chair View | Library System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/title_image.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
            font-family: "JetBrains Mono", "Fira Code", Consolas, monospace;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            color: #fff;
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #cbd5f5;
        }

        .legend-box {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 2px solid;
        }

        .legend-available {
            border-color: #38bdf8;
            background: #094b67;
        }

        .legend-selected {
            border-color: #38bdf8;
            background: #38bdf8;
        }

        .legend-sold {
            background: #344767;
            border-color: #64748b;
        }

        .hall {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 40px;
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }

        .table-unit {
            position: relative;
            width: 100%;
            max-width: 266px;
            aspect-ratio: 1/1;
            margin: 10px auto;
            padding: 10px;
        }

        .table {
            position: absolute;
            inset: 50% auto auto 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            height: 60%;
            background: #1e293b;
            border-radius: 12px;
            border: 2px solid #38bdf8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: clamp(10px, 2.5vw, 13px);
            font-weight: 700;
            color: #38bdf8;
            text-align: center;
        }

        .table span {
            font-size: clamp(9px, 2vw, 11px);
            color: #cbd5f5;
        }

        .chair {
            position: absolute;
            border: 2px solid #38bdf8;
            background: #094b67;
            color: #fff;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
        }

        .chair:hover {
            background: #2e9dcd;
            color: #032635;
        }

        .chair.selected {
            background: #38bdf8;
            color: #032635;
            border-color: #38bdf8;
        }

        .chair.booked {
            background: #344767;
            border-color: #64748b;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .btn {
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
            color: #020617;
            padding: 12px 30px;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            cursor: pointer;
            max-width: 90%;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .breadcrumb-wrapper {
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 15px;
            margin-top: 10px;
        }

        .breadcrumb {
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        .breadcrumb .dashboard {
            color: #ef4444;
            font-weight: 600;
        }

        .breadcrumb .separator {
            color: #9ca3af;
        }

        .breadcrumb a {
            text-decoration: none;
            color: #ef4444;
        }

        .breadcrumb .current {
            color: #fff;
            font-weight: 600;
        }

        .library-select {
            background: #fff;
            color: #000;
            padding: 10px 14px;
            border-radius: 8px;
            min-width: 250px;
        }

        @media (max-width: 600px) {
            .hall {
                gap: 25px;
                padding: 12px;
            }

            .table-unit {
                max-width: 140px;
            }

            .btn {
                width: 100%;
            }

            .breadcrumb {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb">
            <a href="home.php" class="dashboard">Dashboard</a>
            <span class="separator">›</span>
            <span class="current">View Table & Chair</span>
        </nav>
    </div>

    <div class="legend">
        <div class="legend-item">
            <div class="legend-box legend-available"></div> Available
        </div>
        <div class="legend-item">
            <div class="legend-box legend-selected"></div> Selected
        </div>
        <div class="legend-item">
            <div class="legend-box legend-sold"></div> Booked
        </div>
    </div>

    <div class="actions">
        <label style="margin-right:10px;font-weight:600;">Select Library:</label>

        <select id="librarySelect" class="library-select">
            <option value="">-- Choose Library --</option>
            <?php foreach ($libraries as $library): ?>
                <option value="<?php echo $library['library_id']; ?>" <?php echo ($selected_library_id == $library['library_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($library['library_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($selected_library_id > 0): ?>
        <div class="hall">
            <?php foreach ($tables as $tableRow): ?>
                <div class="table-unit" data-table-id="<?php echo $tableRow['table_id']; ?>">
                    <?php
                    foreach ($tableRow['chairs'] as $chair) {
                        $pos = getChairPosition($chair['chair_no'], $tableRow['chair_count']);
                        $statusClass = ($chair['current_status'] === 'booked') ? 'booked' : '';
                        $style = '';

                        if ($pos['side'] === 'top') {
                            $style = "top:-10%; left:" . ($pos['ratio'] * 100) . "%; transform:translateX(-50%);";
                        } elseif ($pos['side'] === 'right') {
                            $style = "right:-10%; top:" . ($pos['ratio'] * 100) . "%; transform:translateY(-50%);";
                        } elseif ($pos['side'] === 'bottom') {
                            $style = "bottom:-10%; left:" . ($pos['ratio'] * 100) . "%; transform:translateX(-50%);";
                        } else {
                            $style = "left:-10%; top:" . ($pos['ratio'] * 100) . "%; transform:translateY(-50%);";
                        }
                    ?>
                        <div class="chair <?php echo $statusClass; ?>"
                            style="<?php echo $style; ?>"
                            data-chair-id="<?php echo $chair['chair_id']; ?>"
                            data-table-id="<?php echo $tableRow['table_id']; ?>"
                            data-library-id="<?php echo $selected_library_id; ?>">
                            C<?php echo $chair['chair_no']; ?>
                        </div>
                    <?php } ?>

                    <div class="table">
                        <?php echo htmlspecialchars($tableRow['table_name']); ?><br>
                        <span><?php echo $tableRow['chair_count']; ?> Chairs</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="actions">
            <button class="btn" onclick="confirmBooking()">Confirm Booking (2 Hour)</button>
        </div>
    <?php endif; ?>

    <?php include 'footer.php'; ?>

    <script>
        document.getElementById("librarySelect").addEventListener("change", function() {
            const libraryId = this.value;
            if (libraryId) {
                window.location.href = "?library_id=" + libraryId;
            } else {
                window.location.href = "view_table_chair.php";
            }
        });

        document.querySelectorAll(".chair").forEach(chair => {
            chair.addEventListener("click", function() {
                if (this.classList.contains("booked")) return;

                document.querySelectorAll(".chair.selected").forEach(c => c.classList.remove("selected"));
                this.classList.add("selected");
            });
        });

        function confirmBooking() {
            const selected = document.querySelector(".chair.selected");

            if (!selected) {
                Swal.fire("Error", "Please select one available chair.", "error");
                return;
            }

            const libraryId = selected.dataset.libraryId;
            const tableId = selected.dataset.tableId;
            const chairId = selected.dataset.chairId;
            const chairName = selected.innerText.trim();
            const tableName = selected.closest(".table-unit").querySelector(".table").innerText.split("\n")[0];

            Swal.fire({
                title: "Confirm Booking?",
                html: `
                    <b>${tableName}</b><br>
                    Chair: <b>${chairName}</b><br><br>
                    Duration: <b>2 Hour</b>
                `,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Book Now"
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append("action", "book_chair");
                    formData.append("library_id", libraryId);
                    formData.append("table_id", tableId);
                    formData.append("chair_id", chairId);

                    fetch("", {
                            method: "POST",
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "success") {
                                Swal.fire({
                                    icon: "success",
                                    title: "Booked",
                                    text: data.message
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire("Error", data.message, "error").then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(() => {
                            Swal.fire("Error", "Something went wrong.", "error");
                        });
                }
            });
        }
    </script>
</body>

</html>