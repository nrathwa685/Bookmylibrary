<?php
session_start();
include "../db_config.php";
include "../send_mail.php";

if (!isset($_GET['issue_id'])) {
    header("Location: issued_book.php?icon=error&msg=" . urlencode("Invalid request"));
    exit;
}

$issue_id = mysqli_real_escape_string($con, $_GET['issue_id']);

// get issue details
$issue_query = mysqli_query($con, "SELECT * FROM issue WHERE issue_id='$issue_id'");
$issue_data = mysqli_fetch_assoc($issue_query);

if (!$issue_data) {
    header("Location: issued_book.php?icon=error&msg=" . urlencode("Issue record not found"));
    exit;
}

$status = $issue_data['status'];
$book_id = $issue_data['book_id'];
$user_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM user WHERE user_id = {$issue_data['user_id']}"));
$book_name = mysqli_fetch_assoc(mysqli_query($con, "SELECT title FROM book_list WHERE book_id = $book_id"));

// already returned
if ($status == "Returned") {
    header("Location: issued_book.php?icon=info&msg=" . urlencode("Book already returned"));
    exit;
}

// if overdue → check payment
if ($status == "Overdue") {
    $payment_query = mysqli_query($con, "SELECT * FROM payment_history WHERE issue_id='$issue_id' ORDER BY payment_date DESC LIMIT 1");

    $payment_data = mysqli_fetch_assoc($payment_query);

    if (!$payment_data || $payment_data['payment_status'] != "Paid") {
        header("Location: issued_book.php?icon=warning&msg=" . urlencode("Please pay fine before returning"));
        exit;
    }
}

// update return
$update = mysqli_query($con, "UPDATE issue SET status='Returned' WHERE issue_id='$issue_id'");

if ($update) {
    mysqli_query($con, "UPDATE book_list SET available_copy = available_copy + 1 WHERE book_id='$book_id' ");

    sendLibraryMail($user_data['email'], $user_data['first_name'] . " " . $user_data['last_name'], $book_name['title'], "Returned", date("d M Y"));
    
    mysqli_query($con, "UPDATE issue SET last_mailed_status = 'Returned' WHERE issue_id='$issue_id'");

    header("Location: issued_book.php?icon=success&msg=" . urlencode("Book returned successfully"));
    exit;
} else {
    header("Location: issued_book.php?icon=error&msg=" . urlencode("Failed to return book"));
    exit;
}
