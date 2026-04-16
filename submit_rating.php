<?php
session_start();
include "../db_config.php";

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request"
    ]);
    exit;
}

$issue_id = mysqli_real_escape_string($con, $_POST['issue_id']);
$book_id = mysqli_real_escape_string($con, $_POST['book_id']);
$rating = (int) $_POST['rating'];
$review = mysqli_real_escape_string($con, $_POST['review']);
$rating_date = date("Y-m-d");

if (!isset($_SESSION['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "User not logged in"
    ]);
    exit;
}

$user_id = $_SESSION['id'];

if ($rating < 1 || $rating > 5) {
    echo json_encode([
        "status" => "error",
        "message" => "Rating must be between 1 and 5"
    ]);
    exit;
}

// check issue details
$issue_query = mysqli_query($con, "SELECT * FROM issue WHERE issue_id='$issue_id'");
$issue_data = mysqli_fetch_assoc($issue_query);

if (!$issue_data) {
    echo json_encode([
        "status" => "error",
        "message" => "Issue not found"
    ]);
    exit;
}

if ($issue_data['status'] != "Returned") {
    echo json_encode([
        "status" => "error",
        "message" => "Only returned books can be rated"
    ]);
    exit;
}

if ($issue_data['is_rated'] == 1) {
    echo json_encode([
        "status" => "info",
        "message" => "Already rated"
    ]);
    exit;
}

$library_id = $issue_data['library_id'];

// generate unique rating_id
do {
    $rating_id = rand(10000, 99999);
    $check_rating_id = mysqli_query($con, "SELECT rating_id FROM rating WHERE rating_id='$rating_id'");
} while (mysqli_num_rows($check_rating_id) > 0);

// start transaction
mysqli_begin_transaction($con);

try {
    // 1. update issue
    $update_issue = mysqli_query($con, "
        UPDATE issue 
        SET is_rated=1
        WHERE issue_id='$issue_id'
    ");

    if (!$update_issue) {
        throw new Exception("Failed to update issue");
    }

    // 2. insert into rating table
    $insert_rating = mysqli_query($con, "
        INSERT INTO rating (
            rating_id,
            book_id,
            library_id,
            user_id,
            description,
            rating,
            rating_date
        ) VALUES (
            '$rating_id',
            '$book_id',
            '$library_id',
            '$user_id',
            '$review',
            '$rating',
            '$rating_date'
        )
    ");

    if (!$insert_rating) {
        throw new Exception("Failed to insert rating");
    }

    // 3. get current book rating
    $book_query = mysqli_query($con, "
        SELECT rating, rating_count 
        FROM book_list 
        WHERE book_id='$book_id'
    ");
    $book = mysqli_fetch_assoc($book_query);

    if (!$book) {
        throw new Exception("Book not found");
    }

    $current_rating = (float)$book['rating'];
    $current_count = (int)$book['rating_count'];

    $new_count = $current_count + 1;
    $new_rating = (($current_rating * $current_count) + $rating) / $new_count;
    $new_rating = round($new_rating, 1);

    // 4. update book_list
    $update_book = mysqli_query($con, "
        UPDATE book_list 
        SET rating='$new_rating', rating_count='$new_count'
        WHERE book_id='$book_id'
    ");

    if (!$update_book) {
        throw new Exception("Failed to update book rating");
    }

    mysqli_commit($con);

    echo json_encode([
        "status" => "success",
        "message" => "Rating submitted successfully"
    ]);
} catch (Exception $e) {
    mysqli_rollback($con);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>