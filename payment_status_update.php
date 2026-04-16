<?php
include("../db_config.php"); // your connection file

if (isset($_POST['payment_id'])) {

    $payment_id = $_POST['payment_id'];
    $current_status = $_POST['current_status'];

    $new_status = ($current_status == "Approved") ? "Pending" : "Approved";

    $update = mysqli_query(
        $con,
        "UPDATE payment_history SET verify_status='$new_status' WHERE payment_id='$payment_id'"
    );

    if ($update) {

        echo json_encode([
            "status" => "success",
            "newStatus" => $new_status
        ]);

    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>