<?php
session_start();
include("../db_config.php");

$user_id = $_SESSION['id'];

$result = mysqli_query($con,"SELECT image FROM user WHERE user_id='$user_id'");
$row = mysqli_fetch_assoc($result);

if($row['image'] != "default_profile.png"){
    unlink("../image/".$row['image']);
}

mysqli_query($con,"UPDATE user SET image='default_profile.png' WHERE user_id='$user_id'");

echo json_encode(["status"=>"success"]);