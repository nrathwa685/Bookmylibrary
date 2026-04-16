<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendLibraryMail($email, $name, $book_name, $status, $returnDate, $fine = 0)
{
    $mail = new PHPMailer(true);

    try {

        $mail = new PHPMailer(true);

        $mail->CharSet = 'UTF-8';

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dasodariya899@rku.ac.in';
        $mail->Password   = 'impt ujku nrtp taee';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('dasodariya899@rku.ac.in', 'Book My Library');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);

        // Default values
        $title = "";
        $content = "";

        // 📧 Content Based on Status
        if ($status == "Issued") {

            $title = "📚 Book Issued Successfully";

            $content = "
            Your book <b>$book_name</b> has been issued successfully.<br>
            Please return it before <b>$returnDate</b>.
            ";
        } elseif ($status == "Yet to return") {

            $title = "⏰ Return Reminder";

            $content = "
            Your book <b>$book_name</b> is due soon.<br>
            Please return it before <b>$returnDate</b> to avoid fines.
            ";
        } elseif ($status == "Overdue") {

            $title = "⚠ Book Overdue";

            $content = "
            Your book <b>$book_name</b> is overdue.<br>
            Current fine amount: <b>₹$fine</b><br>
            Please return it immediately.
            ";
        } elseif ($status == "Return at library") {

            $title = "📖 Return Confirmation";

            $content = "
            Your book <b>$book_name</b> has been returned at the library.<br>
            Thank you for visiting us.
            ";
        } elseif ($status == "Returned") {

            $title = "✅ Book Returned";

            $content = "
            Thank you for returning the book <b>$book_name</b>.<br>
            We hope you enjoyed reading it.
            ";
        } elseif ($status == "Pending") {

            $title = "📚 Book Issue Pending";

            $content = "
            Your request for the book <b>$book_name</b> has been received successfully.<br>
            The issue process is currently <b>pending approval</b>.<br>
            You will be notified once it is approved.<br><br>

            After approval, please visit the library and collect the book <b>within 24 hours</b>.
            ";
        } elseif ($status == "Cancelled") {

            $title = "❌ Request Cancelled";

            $content = "
            Your request for the book <b>$book_name</b> has been <b>cancelled</b>.<br>
            The request was not approved within <b>24 hours</b>.<br><br>

            Please make a new request if you still want to issue this book.
            ";
        }

        // 📧 HTML Email Template
        $message = "
                    <div style='font-family: Arial, sans-serif; max-width: 650px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; background: #ffffff;'>

                        <div style='background: linear-gradient(135deg, #0f172a, #1e3a8a); color: #fff; padding: 20px; text-align: center;'>
                            <h2 style='margin: 0;'>🚀 Welcome to <span style=\"color:#ffcc70;\">Book My Library</span></h2>
                        </div>

                        <div style='padding: 20px; color: #333;'>

                            <p>Hello <strong>$name</strong>, we're glad to connect with you!</p>

                            <div style='background: #f8fafc; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-top: 20px; text-align: center;'>
                                <h3 style='margin-top: 0; color: #1e3a8a;'>$title</h3>
                                <p style='color: #444; font-size: 15px; line-height: 1.6; margin-bottom: 0;'>
                                    $content
                                </p>

                                <a href='http://localhost/BookMyLibrary/login.php'
                                style='display: inline-block; margin-top: 18px; background: #1e3a8a; color: #fff; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;'>
                                🔐 Visit Library
                                </a>
                            </div>

                            <div style='margin-top: 25px; padding: 18px; border: 1px solid #ddd; border-radius: 8px; background: #ffffff;'>
                                <h3 style='margin-top: 0; color: #1e3a8a;'>📚 Book My Library</h3>
                                <p style='margin: 10px 0; color: #444;'>
                                    Manage your books, borrowing history, and payments easily.
                                </p>
                                <p style='margin: 0; color: #666; font-style: italic;'>
                                    'Books are a uniquely portable magic.'
                                </p>
                            </div>

                            <div style='margin-top: 25px; padding: 18px; border: 1px solid #ddd; border-radius: 8px; background: #f8fafc; text-align: center;'>
                                <p style='margin: 0 0 8px 0; color: #333;'>Best Wishes,</p>
                                <p style='margin: 0; font-weight: bold; color: #1e3a8a;'>📖 The Book My Library Team</p>
                                <p style='margin-top: 10px; font-size: 13px; color: #666;'>
                                    📧 support@bookmylibrary.com
                                </p>
                            </div>

                        </div>
                    </div>
                    ";


        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        echo "Mail Error: " . $mail->ErrorInfo;
    }
}

function sendChairBookingMail($email, $name, $libraryName, $tableId, $chairId, $startTime, $endTime)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dasodariya899@rku.ac.in';
        $mail->Password   = 'impt ujku nrtp taee';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('dasodariya899@rku.ac.in', 'Book My Library');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Chair Booking Confirmation";

        $startFormatted = date("d-m-Y h:i A", strtotime($startTime));
        $endFormatted   = date("d-m-Y h:i A", strtotime($endTime));

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 650px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
                <div style='background: linear-gradient(135deg, #0f172a, #1e3a8a); color: #fff; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>Chair Booking Confirmation</h2>
                </div>

                <div style='padding: 20px; color: #333;'>
                    <p>Hello <strong>{$name}</strong>,</p>
                    <p>Your chair has been booked successfully.</p>

                    <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Library Name</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>{$libraryName}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Table</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>{$tableId}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Chair</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>{$chairId}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>Start Time</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>{$startFormatted}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #ddd;'><strong>End Time</strong></td>
                            <td style='padding: 10px; border: 1px solid #ddd;'>{$endFormatted}</td>
                        </tr>
                    </table>

                    <p style='margin-top: 20px;'>Please arrive on time and use your booked chair within the allotted duration.</p>

                    <p>Thank you,<br><strong>Book My Library</strong></p>
                </div>
            </div>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Chair Booking Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
