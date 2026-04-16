<?php
include "../db_config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $book_id = $_POST['book_id'];
    $new_date = $_POST['new_return_date'];

    // check renew count
    $check = mysqli_query($con,"
        SELECT renew_count 
        FROM issue 
        WHERE book_id='$book_id'
        AND status!='Returned'
    ");

    $data = mysqli_fetch_assoc($check);

    if ($data['renew_count'] >= 2) {

        echo json_encode([
            "status" => "error",
            "message" => "Renew limit reached"
        ]);
        exit;
    }

    // update renew
    $query = mysqli_query($con,"
        UPDATE issue 
        SET 
            return_date='$new_date',
            renew_count = renew_count + 1
        WHERE book_id='$book_id'
        AND status!='Returned'
    ");

    if ($query) {
        sendLibraryMail($userEmail, $userName, $bookName, $newStatus, date("d M Y", strtotime($returnDate)), $fine);
        echo json_encode([
            "status" => "success"
        ]);
    } else {
        echo json_encode([
            "status" => "error"
        ]);
    }
}
?>