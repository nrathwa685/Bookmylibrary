<?php
include("../db_config.php"); // your connection file

if (isset($_POST['user_id'])) {

    $user_id = $_POST['user_id'];
    $current_status = $_POST['current_status'];

    $new_status = ($current_status == "Active") ? "Inactive" : "Active";

    $update = mysqli_query(
        $con,
        "UPDATE user SET status='$new_status' WHERE user_id='$user_id'"
    );

    if ($update) {

        $buttonText = ($new_status == "Active") ? "Inactive" : "Active";

        echo json_encode([
            "status" => "success",
            "newStatus" => $new_status,
            "buttonText" => $buttonText
        ]);

    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>