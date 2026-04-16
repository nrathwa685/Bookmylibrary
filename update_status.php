<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db_config.php";
include "send_mail.php";
date_default_timezone_set('Asia/Kolkata');

$user_id = $_SESSION['id'];
$today = date("Y-m-d");
$now = date("Y-m-d H:i:s");

$issues = mysqli_query($con, "SELECT * FROM issue WHERE user_id='$user_id'");
$user_data = mysqli_query($con, "SELECT * FROM user WHERE user_id='$user_id'");
$user_data_fetch = mysqli_fetch_assoc($user_data);

while ($update_data = mysqli_fetch_assoc($issues)) {

    $issue_id = $update_data['issue_id'];
    $book_id = $update_data['book_id'];
    $oldStatus = $update_data['status'];
    $lastMailedStatus = $update_data['last_mailed_status'] ?? '';
    $returnDate = $update_data['return_date'];
    $graceDate = date("Y-m-d", strtotime($returnDate . " +2 days"));

    $book_title = mysqli_query($con, "SELECT * FROM book_list WHERE book_id='{$book_id}'");
    $book_title_fetch = mysqli_fetch_assoc($book_title);

    $bookName = $book_title_fetch['title'];
    $userName = $user_data_fetch['first_name'] . " " . $user_data_fetch['last_name'];
    $userEmail = $user_data_fetch['email'];

    $newStatus = $oldStatus;
    $fine = 0;

    /* =========================
       PENDING EXPIRE AFTER 24 HOURS
    ========================= */
    if ($oldStatus == 'Pending') {

        // change created_at to your actual datetime column name if different
        $pendingTime = $update_data['created_at'];

        if (!empty($pendingTime)) {

            $expireTime = date("Y-m-d H:i:s", strtotime($pendingTime . " +24 hours"));

            // DEBUG (optional)
            // echo "Now: $now | Expire: $expireTime <br>";

            if (strtotime($now) >= strtotime($expireTime)) {

                // ✅ Only delete AFTER 24 hours

                mysqli_query($con, "
                    UPDATE book_list 
                    SET available_copy = available_copy + 1 
                    WHERE book_id='$book_id'
                ");

                mysqli_query($con, "
                    DELETE FROM issue 
                    WHERE issue_id='$issue_id'
                ");

                // 📧 Send cancellation mail
                sendLibraryMail(
                    $userEmail,
                    $userName,
                    $bookName,
                    "Cancelled",   // 👈 new status for mail
                    date("d M Y", strtotime($pendingTime)),
                    0
                );

                continue;
            }
        }

        // keep last_mailed_status same as current status
        if ($lastMailedStatus != $oldStatus) {
            mysqli_query($con, "
                UPDATE issue
                SET last_mailed_status='$oldStatus'
                WHERE issue_id='$issue_id'
            ");
        }

        continue;
    }

    // Final statuses should not be changed again
    if ($oldStatus == 'Returned' || $oldStatus == 'Return at library') {

        if ($lastMailedStatus != $oldStatus) {
            mysqli_query($con, "
                UPDATE issue
                SET last_mailed_status='$oldStatus'
                WHERE issue_id='$issue_id'
            ");
        }

        continue;
    }

    // Decide new status
    if ($today < $returnDate) {
        $newStatus = 'Issued';
        $fine = 0;
    } elseif ($today >= $returnDate && $today <= $graceDate) {
        $newStatus = 'Yet to return';
        $fine = 0;
    } elseif ($today > $graceDate) {
        $newStatus = 'Overdue';

        $overdueDays = floor((strtotime($today) - strtotime($graceDate)) / (60 * 60 * 24));
        $finePerDay = 5;
        $fine = $overdueDays * $finePerDay;

        $payment_check = mysqli_query($con, "
            SELECT * FROM payment_history 
            WHERE issue_id='$issue_id' AND payment_status='Unpaid'
        ");

        if (mysqli_num_rows($payment_check) > 0) {
            $payment_data = mysqli_fetch_assoc($payment_check);

            if ($payment_data['amount'] != $fine) {
                mysqli_query($con, "
                    UPDATE payment_history 
                    SET amount='$fine' 
                    WHERE issue_id='$issue_id' AND payment_status='Unpaid'
                ");
            }
        } else {
            do {
                $payment_id = rand(10000, 99999);
                $check_query = mysqli_query($con, "
                    SELECT payment_id FROM payment_history WHERE payment_id='$payment_id'
                ");
            } while (mysqli_num_rows($check_query) > 0);

            $user_issue_id = $update_data['user_id'];
            $library_id = $update_data['library_id'];

            mysqli_query($con, "
                INSERT INTO payment_history
                (payment_id, issue_id, user_id, library_id, amount, payment_method, payment_status, payment_date)
                VALUES
                ('$payment_id', '$issue_id', '$user_issue_id', '$library_id', '$fine', '--', 'Unpaid', NULL)
            ");
        }
    }

    // Update issue only if needed
    if ($oldStatus != $newStatus || $update_data['fine_amount'] != $fine) {
        mysqli_query($con, "
            UPDATE issue 
            SET status='$newStatus', fine_amount='$fine'" . ($newStatus == 'Overdue' ? ", renew_count=0" : "") . "
            WHERE issue_id='$issue_id'
        ");
    }

    // Send mail only once when status changes
    if ($oldStatus != $newStatus && $lastMailedStatus != $newStatus) {

        if ($newStatus == "Yet to return") {
            sendLibraryMail($userEmail, $userName, $bookName, $newStatus, date("d M Y", strtotime($graceDate)), $fine);
        } else {
            sendLibraryMail($userEmail, $userName, $bookName, $newStatus, date("d M Y", strtotime($returnDate)), $fine);
        }

        mysqli_query($con, "UPDATE issue SET last_mailed_status='$newStatus' WHERE issue_id='$issue_id'");
    }
}
