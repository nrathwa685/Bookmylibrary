<?php
include("../db_config.php"); // your connection file

if(isset($_POST['user_id']) && isset($_POST['role'])){

    $user_id = $_POST['user_id'];
    $role = $_POST['role'];

    $update = "UPDATE user SET role='$role' WHERE user_id='$user_id'";

    if(mysqli_query($con,$update)){

        echo json_encode([
            "status" => "success",
            "newRole" => $role
        ]);

    } else {

        echo json_encode([
            "status" => "error"
        ]);

    }

}
?>