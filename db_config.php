<?php
//Step-1 : Craete connection

$con = mysqli_connect("localhost", "root", "", "bookmyshow");

if ($con) {
    // echo "Connection Successfull";
} else {
    echo "Connection Failed";
}
mysqli_query($con, "SET time_zone = '+05:30'");

//Step-2 : Create Database this is one time process so we can comment this code after creating database

// $create_db = "create database BookMyShow";

// if (mysqli_query($con, $create_db)) {
//     echo "Database Created Successfully";
// } else {
//     echo "Database Creation Failed";
// }

//Step-3 : Select Database
try {
    mysqli_select_db($con, "bookmyshow");
} catch (Exception) {
    echo "Error in connecting with DB";
}

//Step-4 : create Table is one time process so we can comment this code after creating database

//Table: Book_list

// $create_table = "CREATE TABLE book_list (
//     book_id INT PRIMARY KEY,
//     library_id INT,
//     title VARCHAR(100),
//     author VARCHAR(50),
//     category VARCHAR(25),
//     year INT(4),
//     language VARCHAR(25),
//     total_copy INT(3),
//     available_copy INT(3),
//     rating FLOAT(2,1),
//     status VARCHAR(25),
//     image VARCHAR(255),

//     CONSTRAINT fk_library
//     FOREIGN KEY (library_id)
//     REFERENCES library(library_id)
//     ON DELETE CASCADE
//     ON UPDATE CASCADE
// );";

// Table: category

// $create_table = "CREATE TABLE category (
//     category_id INT PRIMARY KEY,
//     category_name VARCHAR(100),
//     category_description VARCHAR(255),
//     status VARCHAR(10)
// );";

// Table: library

// $create_table = "CREATE TABLE library (
//     library_id INT PRIMARY KEY,
//     library_name VARCHAR(100),
//     library_owner_name VARCHAR(100),
//     table_capacity INT(3),
//     chair_capacity INT(3),
//     open_at TIME,
//     close_at TIME,
//     library_location VARCHAR(255),
//     status VARCHAR(15),
//     user_id INT 

//     FOREIGN KEY (user_id) REFERENCES user(user_id)
// );";

// Table: user

// $create_table = "CREATE TABLE user (
//     user_id INT PRIMARY KEY,
//     first_name VARCHAR(100),
//     last_name VARCHAR(100),
//     email VARCHAR(255),
//     contact_no INT(10),
//     gender VARCHAR(10),
//     address VARCHAR(255),
//     image VARCHAR(255),
//     role VARCHAR(10),
//     status VARCHAR(15)
// );";

// if (mysqli_query($con, $create_table)) {
//     echo "Table Created Successfully";
// } else {
//     echo "Table Creation Failed";
// }

// Table: issue

// $create_table = "CREATE TABLE issue (
//     issue_id INT PRIMARY KEY,
//     book_id INT,
//     user_id INT,
//     library_id INT,
//     issue_date DATE,
//     return_date DATE,
//     fine_amount INT(10),
//     status VARCHAR(15),

//     FOREIGN KEY (book_id) REFERENCES book_list(book_id),
//     FOREIGN KEY (user_id) REFERENCES user(user_id),
//     FOREIGN KEY (library_id) REFERENCES library(library_id)
// )";

//Table: payment_history

// $create_table = "CREATE TABLE payment_history (
//     payment_id INT PRIMARY KEY,
//     issue_id INT,
//     user_id INT,
//     library_id INT,
//     amount INT,
//     payment_method VARCHAR(50),
//     payment_status VARCHAR(50),
//     payment_date DATE,

//     CONSTRAINT fk_issue_payment
//     FOREIGN KEY (issue_id)
//     REFERENCES issue(issue_id)
//     ON DELETE CASCADE
//     ON UPDATE CASCADE,

//     CONSTRAINT fk_user_payment
//     FOREIGN KEY (user_id)
//     REFERENCES user(user_id)
//     ON DELETE CASCADE
//     ON UPDATE CASCADE,

//     CONSTRAINT fk_library_payment
//     FOREIGN KEY (library_id)
//     REFERENCES library(library_id)
//     ON DELETE CASCADE
//     ON UPDATE CASCADE
// )";

// Table: rating

// $create_table = "CREATE TABLE rating (
//     rating_id INT PRIMARY KEY,
//     book_id INT,
//     library_id INT,
//     user_id INT,
//     description TEXT,
//     rating INT,
//     rating_date DATE,
//     CONSTRAINT fk_rating_book FOREIGN KEY (book_id) REFERENCES book_list(book_id),
//     CONSTRAINT fk_rating_library FOREIGN KEY (library_id) REFERENCES library(library_id),
//     CONSTRAINT fk_rating_user FOREIGN KEY (user_id) REFERENCES user(user_id)
// );";

// Table: library_table

// $create_table = "CREATE TABLE library_tables (
//     table_id INT AUTO_INCREMENT PRIMARY KEY,
//     library_id INT NOT NULL,
//     table_name VARCHAR(100) NOT NULL,
//     chair_count INT NOT NULL,
//     status ENUM('Active','Inactive') DEFAULT 'Active',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// );";

// Table: library_chair

//  $create_table = "CREATE TABLE library_chairs (
//     chair_id INT AUTO_INCREMENT PRIMARY KEY,
//     table_id INT NOT NULL,
//     chair_name VARCHAR(20) NOT NULL,
//     position_side ENUM('top','right','bottom','left') NOT NULL,
//     position_order INT NOT NULL,
//     is_booked TINYINT(1) DEFAULT 0,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     FOREIGN KEY (table_id) REFERENCES library_tables(table_id) ON DELETE CASCADE
// );";

// if (mysqli_query($con, $create_table)) {
//     echo "Table Created Successfully";
// } else {
//     echo "Table Creation Failed";
// }
