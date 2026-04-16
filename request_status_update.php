<?php
include("../db_config.php"); // your connection file

if (isset($_POST['request_id'])) {

    $request_id = $_POST['request_id'];
    $current_status = $_POST['current_status'];

    $new_status = "Approved";
    $date = date("Y-m-d H:i:s");

    $update = mysqli_query(
        $con,
        "UPDATE librarian_request SET status='$new_status', approved_date = '$date' WHERE request_id='$request_id'"
    );

    if ($update) {


        echo json_encode([
            "status" => "success"
        ]);

    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>