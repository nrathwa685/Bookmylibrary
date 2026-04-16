<?php
session_start();
include("../db_config.php");

$response = [];

if (isset($_FILES['image'])) {

    $file = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $newName = time() . "_" . $file;

    $path = "../image/" . $newName;

    move_uploaded_file($tmp, $path);

    $user_id = $_SESSION['id'];

    $result = mysqli_query($con, "SELECT image FROM user WHERE user_id='$user_id'");
    $row = mysqli_fetch_assoc($result);

    if ($row['image'] != "default_profile.png") {
        unlink("../image/" . $row['image']);
    }

    mysqli_query($con, "UPDATE user SET image='$newName' WHERE user_id='$user_id'");

    $response['status'] = "success";
    $response['image'] = $newName;
}

echo json_encode($response);
