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

// already issued
if ($status == "Issued") {
    header("Location: issued_book.php?icon=info&msg=" . urlencode("Book already issued"));
    exit;
}

// update issue
$update = mysqli_query($con, "UPDATE issue SET status='Issued' WHERE issue_id='$issue_id'");

if ($update) {

    sendLibraryMail($user_data['email'], $user_data['first_name'] . " " . $user_data['last_name'], $book_name['title'], "Issued", date("d M Y"));
    
    mysqli_query($con, "UPDATE issue SET last_mailed_status = 'Issued' WHERE issue_id='$issue_id'");

    header("Location: issued_book.php?icon=success&msg=" . urlencode("Book issued successfully"));
    exit;
} else {
    header("Location: issued_book.php?icon=error&msg=" . urlencode("Failed to issue book"));
    exit;
}
