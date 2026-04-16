<?php
require "../session_check.php";

if ($_SESSION['role'] != "User") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Issued Book List | Library System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- DataTables CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"> -->
    <link rel="icon" href="../image/title_image.png" type="image/png">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- <script src="https://checkout.razorpay.com/v1/checkout.js"></script> -->

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "JetBrains Mono", "Fira Code", Consolas, monospace;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(120deg, #0f172a, #1e3a8a);
            color: #333;
        }

        .container {
            width: 100%;
            /* Full width */
            max-width: 100%;
            /* Remove restriction */
            padding: 40px;
        }

        .card {
            background: #ffffff;
            padding: 35px;
            /* Increased padding */
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .title-area h3 {
            margin: 0;
            font-size: 24px;
            /* Larger heading */
            font-weight: 700;
            color: #1f2937;
        }

        .subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-top: 5px;
        }

        .btn {
            padding: 10px 18px;
            /* Bigger buttons */
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .btn-add {
            background: #16a34a;
            color: #fff;
        }

        .btn-add:hover {
            background: #15803d;
        }

        .btn-renew {
            background: #facc15;
            color: #1f2937;
            display: inline-block;
        }

        .btn-renew:hover {
            background: #eab308;
        }

        .btn-pay {
            background: #16a34a;
            color: #fff;
            display: inline-block;
        }

        .btn-pay:hover {
            background: #15803d;
        }

        td {
            white-space: nowrap;
            /* ⬅ Prevent line break */
        }

        .model-link {
            color: #2660de;
            cursor: pointer;
            text-decoration: underline;
        }

        table.dataTable {
            width: 100% !important;
            font-size: 14px;
            /* Bigger text */
        }

        table.dataTable thead th {
            background: #f9fafb;
            color: #374151;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 12px;
            /* More header height */
        }

        table.dataTable tbody td {
            padding: 14px 12px;
            /* Increase row height */
            vertical-align: middle;
        }

        table.dataTable tbody tr {
            transition: background 0.2s;
        }

        table.dataTable tbody tr:hover {
            background: #f3f4f6;
        }

        .status {
            padding: 6px 16px;
            /* Bigger badge */
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
        }

        .pending {
            background: #fee2e2;
            color: #991b1b;
        }

        .issued {
            background: #dcfce7;
            color: #166534;
        }

        .returned {
            background: #dbeafe;
            color: #1e3a8a;
        }

        .yet-to-return {
            background: #fef9c3;
            color: #854d0e;
        }

        .overdue {
            background: #fee2e2;
            color: #991b1b;
        }

        .return-at-library {
            background: #fff7ed;
            color: #c2410c;
        }

        img.cover {
            width: 55px;
            /* Bigger image */
            height: 75px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        /* DataTable Buttons */
        .dt-buttons .dt-button {
            background: #f3f4f6 !important;
            border: 1px solid #e5e7eb !important;
            color: #374151 !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 13px !important;
            margin-right: 6px;
        }

        .dt-buttons .dt-button:hover {
            background: #e5e7eb !important;
        }

        /* Breadcrumb Container */
        .breadcrumb-wrapper {
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 15px;
            margin-top: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        /* Breadcrumb Layout */
        .breadcrumb {
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        /* Dashboard */
        .breadcrumb .dashboard {
            color: #ef4444;
            font-weight: 600;
        }

        /* Separator */
        .breadcrumb .separator {
            color: #9ca3af;
        }

        /* Links */
        .breadcrumb a {
            text-decoration: none;
            color: #ef4444;
            transition: 0.2s ease;
        }

        .breadcrumb a:hover {
            text-decoration: none;
        }

        /* Current Page */
        .breadcrumb .current {
            color: #ffffffff;
            font-weight: 600;
        }

        /* Overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        /* Modal Box */
        .modal-box {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 14px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.25s ease;
        }

        /* Header */
        .modal-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }

        /* Body */
        .modal-body p {
            font-size: 15px;
            font-weight: 600;
            color: #dc2626;
            margin-bottom: 6px;
        }

        .modal-body span {
            font-size: 13px;
            color: #6b7280;
        }

        /* Buttons */
        .modal-actions {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .modal-actions .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .cancel-btn {
            background: #f1f5f9;
            color: #334155;
        }

        .cancel-btn:hover {
            background: #e2e8f0;
        }

        .delete-btn {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            color: #fff;
        }

        .delete-btn:hover {
            opacity: 0.9;
        }

        .status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status.available {
            background: #dcfce7;
            color: #166534;
        }

        .status.unavailable {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-toggle {
            background: #16a34a;
            color: #fff;

            display: inline-block;
            padding: 10px 18px;
            margin-top: 10px;
            /* Bigger buttons */
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .btn-toggle:hover {
            background: #15803d;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }


        @media (max-width: 768px) {
            .top-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            img.cover {
                width: 45px;
                height: 60px;
            }

            .breadcrumb {
                font-size: 13px;
            }
        }

        .book-id {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .book-id:hover {
            text-decoration: underline;
        }

        /* Backdrop */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        /* Card */
        .modal-card {
            background: #ffffff;
            width: 700px;
            max-width: 95%;
            border-radius: 14px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: fadeSlide 0.25s ease;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .modal-header-p {
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header-p h3 {
            font-size: 18px;
            color: #0f172a;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Pills container */
        .pill-group {
            display: flex;
            gap: 8px;
        }

        /* Base pill */
        .pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
        }

        /* Role pill (Blue) */
        .pill-role-librarian {
            background-color: #fef3c7;
            color: #92400e;
        }

        .pill-role-user {
            background-color: #e0f2fe;
            color: #075985;
        }

        .pill-role-admin {
            background-color: #ede9fe;
            color: #5b21b6;
        }

        /* Status pills */
        .pill-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .pill-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Close */
        .close-icon {
            font-size: 22px;
            cursor: pointer;
            color: #64748b;
        }

        .close-icon:hover {
            color: #ef4444;
        }

        /* Body */
        .modal-body-p {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 20px;
            padding: 20px;
        }

        /* Image */
        .book-image img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        /* Details */
        .book-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .detail span {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
        }

        .detail p {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        /* Footer */
        .modal-footer {
            padding: 14px 20px;
            border-top: 1px solid #e5e7eb;
            text-align: right;
        }

        /* Buttons */
        .btn-secondary {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #cbd5f5;
            background: #f8fafc;
            color: #1e293b;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #e0e7ff;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .modal-body {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .book-details {
                grid-template-columns: 1fr;
            }
        }

        /* Backdrop */
        .l-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        /* Card */
        .l-modal-card {
            background: #ffffff;
            width: 700px;
            max-width: 95%;
            border-radius: 14px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: fadeSlide 0.25s ease;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .l-modal-header-p {
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .l-modal-header-p h3 {
            font-size: 18px;
            color: #0f172a;
        }

        .l-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Pills container */
        .l-pill-group {
            display: flex;
            gap: 8px;
        }

        /* Base pill */
        .l-pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
        }

        /* Status pills */
        .l-pill-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .l-pill-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Close */
        .l-close-icon {
            font-size: 22px;
            cursor: pointer;
            color: #64748b;
        }

        .l-close-icon:hover {
            color: #ef4444;
        }

        /* Body */
        .l-modal-body-p {
            display: grid;
            grid-template-columns: 580px 1fr;
            gap: 20px;
            padding: 20px;
        }

        /* Image */
        .l-book-image img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        /* Details */
        .l-book-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .l-detail span {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
        }

        .l-detail p {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        /* Footer */
        .l-modal-footer {
            padding: 14px 20px;
            border-top: 1px solid #e5e7eb;
            text-align: right;
        }

        /* Buttons */
        .l-btn-secondary {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #cbd5f5;
            background: #f8fafc;
            color: #1e293b;
            cursor: pointer;
        }

        .l-btn-secondary:hover {
            background: #e0e7ff;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .l-modal-body {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .l-book-details {
                grid-template-columns: 1fr;
            }
        }

        .advanced-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .advanced-filters input,
        .advanced-filters select {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            min-width: 180px;
        }

        .filter-box {
            display: flex;
            flex-direction: column;
        }

        .filter-box label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .btn-area {
            justify-content: flex-end;
        }

        .upi-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(6px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeIn .25s ease;
        }

        .upi-card {
            width: 360px;
            background: linear-gradient(145deg, #ffffff, #f4f4f4);
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .3);
            animation: slideUp .3s ease;
        }

        .upi-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .upi-header h2 {
            font-size: 18px;
            font-weight: 600;
        }

        .upi-close {
            font-size: 22px;
            cursor: pointer;
        }

        .upi-body {
            text-align: center;
        }

        .upi-qr-box {
            background: #fff;
            padding: 12px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .15);
        }

        .upi-qr-box img {
            width: 220px;
        }

        .upi-amount {
            font-size: 26px;
            font-weight: 700;
            margin: 15px 0 5px;
        }

        .upi-text {
            color: #666;
            font-size: 13px;
        }

        .upi-apps {
            margin-top: 10px;
        }

        .upi-apps span {
            background: #eee;
            padding: 6px 10px;
            margin: 0 4px;
            border-radius: 6px;
            font-size: 12px;
        }

        .upi-footer {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .upi-cancel,
        .upi-paid {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .upi-cancel {
            background: #e5e5e5;
        }

        .upi-paid {
            background: #16a34a;
            color: #fff;
        }

        @keyframes slideUp {
            from {
                transform: translateY(40px);
                opacity: 0
            }

            to {
                transform: translateY(0);
                opacity: 1
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        .renew-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .renew-card {
            width: 360px;
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .3);
        }

        .renew-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .renew-body label {
            font-size: 13px;
            margin-top: 10px;
            display: block;
        }

        .renew-body input {
            width: 100%;
            padding: 9px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .renew-footer {
            margin-top: 18px;
            display: flex;
            gap: 10px;
        }

        .renew-confirm {
            flex: 1;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 8px;
        }

        .renew-cancel {
            flex: 1;
            background: #e5e5e5;
            border: none;
            padding: 10px;
            border-radius: 8px;
        }

        .renew-error {
            color: red;
            font-size: 12px;
        }

        .return-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .return-card {
            width: 360px;
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .3);
        }

        .return-body label {
            display: block;
            margin-top: 10px;
            font-size: 13px;
        }

        .return-body input {
            width: 100%;
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
        }

        .return-footer {
            display: flex;
            gap: 10px;
            margin-top: 18px;
        }

        .return-confirm {
            flex: 1;
            background: #16a34a;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 8px;
        }

        .return-cancel {
            flex: 1;
            background: #e5e5e5;
            border: none;
            padding: 10px;
            border-radius: 8px;
        }

        .return-error {
            color: red;
            font-size: 12px;
        }

        .rating-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .rating-card {
            background: #fff;
            padding: 25px;
            border-radius: 14px;
            text-align: center;
            width: 320px;
        }

        .rating-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .skip-btn {
            flex: 1;
            background: #e5e5e5;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        .submit-btn {
            flex: 1;
            background: #16a34a;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        .stars {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .stars span {
            font-size: 30px;
            cursor: pointer;
            color: #bbb;
            transition: 0.2s;
        }

        .stars span.active {
            color: gold;
        }

        .rating-card textarea {
            width: 100%;
            height: 70px;
            margin-top: 10px;
        }
    </style>

</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb">
            <a href="home.php" class="dashboard">Dashboard</a>
            <span class="separator">›</span>
            <span class="current">Issued Book List</span>
        </nav>
    </div>
    <div class="container">
        <div class="card">
            <div class="top-actions">
                <div class="title-area">
                    <h3>Issued Book Details</h3>
                    <div class="subtitle">See your issued book data</div>
                </div>
                <div class="advanced-filters">

                    <div class="filter-box">
                        <label>Issue Date</label>
                        <input type="date" id="filterIssueDate">
                    </div>

                    <div class="filter-box">
                        <label>Return Date</label>
                        <input type="date" id="filterReturnDate">
                    </div>

                    <div class="filter-box">
                        <label>Status</label>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="Issued">Issued</option>
                            <option value="Returned">Returned</option>
                            <option value="Yet to return">Yet to return</option>
                            <option value="Overdue">Overdue</option>
                            <option value="Return at library">Return at library</option>
                        </select>
                    </div>

                    <div class="filter-box btn-area">
                        <label>&nbsp;</label>
                        <button class="btn btn-add" onclick="resetFilters()">Reset</button>
                    </div>

                </div>
                <div></div>


            </div>

            <table id="bookTable" class="display">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Issue ID</th>
                        <th>Book ID</th>
                        <th>Issue Date</th>
                        <th>Return Date</th>
                        <th>Fine Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $user_id = $_SESSION['id'];
                    $issue = mysqli_query($con, "SELECT * FROM issue WHERE user_id = '$user_id' ORDER BY issue_date DESC");
                    $i = 1;
                    foreach ($issue as $row) {

                        // Dynamic Status Logic
                        if ($row['status'] == "Issued") {
                            $statusClass = "issued";
                            $statusText  = "Issued";
                        }
                        if ($row['status'] == "Returned") {
                            $statusClass = "returned";
                            $statusText  = "Returned";
                        }
                        if ($row['status'] == "Yet to return") {
                            $statusClass = "yet-to-return";
                            $statusText  = "Yet to return";
                        }
                        if ($row['status'] == "Overdue") {
                            $statusClass = "overdue";
                            $statusText  = "Overdue";
                        }
                        if ($row['status'] == "Return at library") {
                            $statusClass = "return-at-library";
                            $statusText  = "Return at library";
                        }
                        if ($row['status'] == "Pending") {
                            $statusClass = "pending";
                            $statusText  = "Pending";
                        }

                        $showRenew = false;

                        if ($statusText == "Yet to return" && $row['renew_count'] < 2) {
                            $showRenew = true;
                        }

                        $book_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM book_list WHERE book_id = '{$row['book_id']}'"));
                        $library_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM library WHERE library_id = '{$row['library_id']}'"));

                        echo "<tr>
                                <td>{$i}</td>
                                <td>{$row['issue_id']}</td>
                                <td>
                                    <span class='model-link'
                                    onclick=\"openBookModal(
                                    '{$book_data['book_id']}',
                                    '../book_images/{$book_data['image']}',
                                    '" . htmlspecialchars($book_data['title'], ENT_QUOTES) . "',
                                    '" . htmlspecialchars($book_data['author'], ENT_QUOTES) . "',
                                    '" . htmlspecialchars($book_data['category'], ENT_QUOTES) . "',
                                    '{$book_data['year']}',
                                    '" . htmlspecialchars($library_data['library_name'], ENT_QUOTES) . "'
                                    )\">
                                    {$row['book_id']}
                                    </span>
                                </td>
                                <td>{$row['issue_date']}</td>
                                <td>{$row['return_date']}</td>
                                <td>₹ {$row['fine_amount']}</td>
                                <td><span class='status {$statusClass}'>{$statusText}</span></td>
                                <td>";

                        // 🔁 RENEW BUTTON
                        if ($showRenew) {
                            echo "<button class='btn btn-renew'
                                        data-book='{$row['book_id']}'
                                        data-return='{$row['return_date']}'
                                        data-renew='{$row['renew_count']}'>
                                        Renew
                                    </button>";
                        } elseif ($row['renew_count'] >= 2) {
                            echo "<button class='btn btn-renew' disabled style='cursor:not-allowed;'>
                                        Renew Limit Reached
                                    </button>";
                        }

                        // 💰 PAY BUTTON
                        if ($row['fine_amount'] > 0 && $row['status'] == 'Overdue') {
                            echo "<button class='btn btn-pay'
                                    data-issue='{$row['issue_id']}'
                                    data-book='{$row['book_id']}'
                                    data-library='{$row['library_id']}'
                                    data-amount='{$row['fine_amount']}'>
                                    Pay ₹{$row['fine_amount']} (UPI QR)
                                </button>";
                        }

                        // ⭐ RATING BUTTON (NEW)
                        if ($row['status'] == "Returned" && $row['is_rated'] == 0) {
                            echo "<button class='btn btn-rate'
                                        onclick='openRatingModal(\"{$row['issue_id']}\", \"{$row['book_id']}\")'>
                                        Rate
                                    </button>";
                        } elseif ($row['status'] == "Returned" && $row['is_rated'] == 1) {
                            echo "<span class='status returned'>Rated</span>";
                        }


                        echo "</td>
                                    </tr>";


                        $i++;
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-backdrop" id="bookModal">
        <div class="modal-card">

            <div class="modal-header-p">
                <h3>Book Details</h3>
                <span class="close-icon" onclick="closeBookModal()">×</span>
            </div>

            <div class="modal-body-p">
                <div class="book-image">
                    <img id="modalBookImage" src="" alt="Book Image">
                </div>

                <div class="book-details">
                    <div class="detail">
                        <span>Book ID</span>
                        <p id="modalBookId"></p>
                    </div>
                    <div class="detail">
                        <span>Title</span>
                        <p id="modalBookTitle"></p>
                    </div>
                    <div class="detail">
                        <span>Author</span>
                        <p id="modalBookAuthor"></p>
                    </div>
                    <div class="detail">
                        <span>Category</span>
                        <p id="modalBookCategory"></p>
                    </div>
                    <div class="detail">
                        <span>Publish Year</span>
                        <p id="modalBookYear"></p>
                    </div>
                    <div class="detail">
                        <span>Library Name</span>
                        <p id="modalBookLibrary"></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeBookModal()">Close</button>
            </div>

        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Delete Issued Book Record</h3>
            </div>

            <div class="modal-body">
                <p>⚠️ Are you sure you want to delete this issued book record?</p>
                <span>This action cannot be undone.</span>
            </div>

            <div class="modal-actions">
                <button class="btn cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn delete-btn" onclick="confirmDelete()">Yes, Delete</button>
            </div>
        </div>
    </div>

    <div id="upiModal" class="upi-modal">

        <div class="upi-card">

            <div class="upi-header">
                <h2>Complete Payment</h2>
                <span class="upi-close" onclick="closeUPIModal()">×</span>
            </div>

            <div class="upi-body">

                <div class="upi-qr-box">
                    <img id="upiQR" src="" alt="UPI QR">
                </div>

                <div class="upi-details">
                    <p class="upi-amount">₹ <span id="upiAmount">100</span></p>
                    <p class="upi-text">Scan QR using any UPI app</p>

                    <div class="upi-apps">
                        <span>GPay</span>
                        <span>PhonePe</span>
                        <span>Paytm</span>
                    </div>
                </div>

            </div>

            <p style="font-size:12px; color:#991b1b; margin-top:10px;">
                After payment, enter your UTR number and upload screenshot for verification.
            </p>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="issue_id" id="issueId">
                <input type="hidden" name="library_id" id="libraryId">
                <input type="hidden" name="amount" id="amount">

                <div style="padding: 15px 20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:bold;">UTR / Transaction ID</label>
                    <input type="text" name="utr_no" placeholder="Enter UTR / Transaction ID" required
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; margin-bottom:15px;">

                    <label style="display:block; margin-bottom:8px; font-weight:bold;">Upload Payment Screenshot</label>
                    <input type="file" name="payment_screenshot" accept="image/*,.pdf" required
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; background:#fff;">
                </div>

                <div class="upi-footer">
                    <button type="button" class="upi-cancel" onclick="closeUPIModal()">Cancel</button>
                    <button class="upi-paid" name="paid_btn">Submit Payment Proof</button>
                </div>
            </form>

        </div>

    </div>

    <?php

    if (isset($_POST['paid_btn'])) {

        $issue_id   = intval($_POST['issue_id']);
        $library_id = intval($_POST['library_id']);
        $amount     = intval($_POST['amount']);
        $utr_no     = trim($_POST['utr_no']);
        $user_id    = $_SESSION['id'];

        if ($utr_no == "") {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function(){
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Please enter UTR number.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });
        </script>";
            exit;
        }

        // Get issue + user + book details
        $details_query = mysqli_query($con, "
        SELECT 
            i.issue_id,
            i.return_date,
            i.book_id,
            u.user_id,
            u.first_name,
            u.last_name,
            u.email,
            b.title AS book_title
        FROM issue i
        INNER JOIN user u ON i.user_id = u.user_id
        INNER JOIN book_list b ON i.book_id = b.book_id
        WHERE i.issue_id = '$issue_id' AND i.user_id = '$user_id'
        LIMIT 1
    ");

        $details = mysqli_fetch_assoc($details_query);

        if (!$details) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function(){
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Issue details not found.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });
        </script>";
            exit;
        }

        // Check duplicate UTR
        $check_utr = mysqli_query($con, "SELECT * FROM payment_history WHERE utr_no='$utr_no' LIMIT 1");
        if (mysqli_num_rows($check_utr) > 0) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function(){
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'This UTR number is already used.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });
        </script>";
            exit;
        }

        // Upload screenshot
        $screenshot_name = "";
        if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] == 0) {
            $upload_dir = "../payment_screenshot/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_tmp  = $_FILES['payment_screenshot']['tmp_name'];
            $file_name = $_FILES['payment_screenshot']['name'];
            $file_size = $_FILES['payment_screenshot']['size'];

            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

            if (!in_array($ext, $allowed)) {
                echo "<script>
                document.addEventListener('DOMContentLoaded', function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'Only JPG, JPEG, PNG, PDF files allowed.',
                        showConfirmButton: false,
                        timer: 2200,
                        timerProgressBar: true
                    });
                });
            </script>";
                exit;
            }

            if ($file_size > 5 * 1024 * 1024) {
                echo "<script>
                document.addEventListener('DOMContentLoaded', function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'File size must be less than 5MB.',
                        showConfirmButton: false,
                        timer: 2200,
                        timerProgressBar: true
                    });
                });
            </script>";
                exit;
            }

            $screenshot_name = "payment_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $target_file = $upload_dir . $screenshot_name;

            if (!move_uploaded_file($file_tmp, $target_file)) {
                echo "<script>
                document.addEventListener('DOMContentLoaded', function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'Screenshot upload failed.',
                        showConfirmButton: false,
                        timer: 2200,
                        timerProgressBar: true
                    });
                });
            </script>";
                exit;
            }
        } else {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function(){
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Please upload payment screenshot.',
                    showConfirmButton: false,
                    timer: 2200,
                    timerProgressBar: true
                });
            });
        </script>";
            exit;
        }

        // Generate unique payment_id
        do {
            $payment_id = rand(10000, 99999);
            $check_query = mysqli_query($con, "SELECT payment_id FROM payment_history WHERE payment_id='$payment_id'");
        } while (mysqli_num_rows($check_query) > 0);

        $verify_status = "Pending";
        $payment_date   = date("Y-m-d");

        // Update    payment record
        $update_payment = mysqli_query($con, "
            UPDATE payment_history 
            SET 
                payment_method = 'UPI',
                payment_status = 'Paid',
                payment_date = '$payment_date',
                utr_no = '$utr_no',
                screenshot = '$screenshot_name',
                verify_status = 'Pending'
            WHERE issue_id = '$issue_id'
        ");

        if ($update_payment) {

            $formattedReturnDate = date("d M Y", strtotime($details['return_date']));

            $update = mysqli_query($con, "
            UPDATE issue 
            SET fine_amount = 0,
                status = 'Return at library',
                last_mailed_status = 'Return at library'
            WHERE issue_id = '$issue_id'
        ");

            if ($update) {

                sendLibraryMail(
                    $details['email'],
                    $details['first_name'] . ' ' . $details['last_name'],
                    $details['book_title'],
                    'Return at library',
                    $formattedReturnDate,
                    $amount
                );

                echo "<script>
                document.addEventListener('DOMContentLoaded', function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'success',
                        title: 'Payment proof submitted. Waiting for verification.',
                        showConfirmButton: false,
                        timer: 2200,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = 'fine_list.php';
                    });
                });
            </script>";
            } else {
                echo "<script>
                document.addEventListener('DOMContentLoaded', function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'Payment saved but issue update failed.',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                });
            </script>";
            }
        } else {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function(){
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Failed to save payment details.',
                    showConfirmButton: false,
                    timer: 2200,
                    timerProgressBar: true
                });
            });
        </script>";
        }
    }
    ?>


    <div id="renewModal" class="renew-modal">

        <div class="renew-card">

            <div class="renew-header">
                <h3>Renew Book</h3>
                <span class="upi-close" onclick="closeRenew()">×</span>
            </div>

            <div class="renew-body">

                <label>Book ID</label>
                <input type="text" name="renew_book_id" readonly>

                <label>Current Return Date</label>
                <input type="date" name="old_return_date" readonly>

                <label>New Return Date</label>
                <input type="date" name="new_return_date" readonly>

                <small class="renew-error"></small>

            </div>

            <div class="renew-footer">
                <button class="renew-cancel" onclick="closeRenew()">Cancel</button>
                <button type="button" class="renew-confirm" id="renewConfirmBtn">
                    Confirm Renew
                </button>
            </div>

        </div>

    </div>

    <div id="ratingModal" class="renew-modal" style="display:none;">
        <div class="renew-card">

            <div class="renew-header">
                <h3>Rate Book</h3>
                <span class="upi-close" onclick="closeRatingModal()">×</span>
            </div>

            <div class="renew-body">

                <input type="hidden" id="issue_id">
                <input type="hidden" id="book_id">
                <input type="hidden" id="selected_rating" value="0">

                <label>Select Rating</label>
                <div class="stars">
                    <span onclick="setRating(1)">★</span>
                    <span onclick="setRating(2)">★</span>
                    <span onclick="setRating(3)">★</span>
                    <span onclick="setRating(4)">★</span>
                    <span onclick="setRating(5)">★</span>
                </div>

                <label style="margin-top:10px;">Write Review</label>
                <textarea id="review_text" placeholder="Write your experience..." rows="3"></textarea>

            </div>

            <div class="renew-footer">
                <button class="renew-cancel" onclick="closeRatingModal()">Cancel</button>
                <button class="renew-confirm" onclick="submitRating()">Submit</button>
            </div>

        </div>
    </div>



    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Export Buttons -->
    <!-- <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> -->

    <script>
        var table = $('#bookTable').DataTable({
            responsive: true,
            dom: 'Brtip',
            columnDefs: [{
                targets: 0, // Sr No column
                orderable: false,
                searchable: false
            }],

            order: [
                [1, 'asc']
            ],
            buttons: [{
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6] // column indexes you want
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ],
            pageLength: 5,
            scrollY: "500px",
            scrollX: true,
            scrollCollapse: true
        });

        // ✅ AUTO UPDATE SERIAL NUMBER
        table.on('order.dt search.dt draw.dt', function() {
            table.column(0, {
                    search: 'applied',
                    order: 'applied'
                })
                .nodes()
                .each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
        }).draw();
        // STATUS filter
        $('#filterStatus').on('change', function() {
            var value = this.value.toLowerCase();

            table.column(6).search(value ? '^' + value + '$' : '', true, false).draw();
        });

        // Issue filter
        $('#filterIssueDate').on('change', function() {
            table.column(3).search(this.value).draw();
        });


        // Return filter
        $('#filterReturnDate').on('change', function() {
            table.column(4).search(this.value).draw();
        });


        // RESET filters
        function resetFilters() {
            $('#filterIssueDate').val('');
            $('#filterReturnDate').val('');
            $('#filterStatus').val('');

            table.columns().search('').draw();
        }

        const deleteModal = document.getElementById("deleteModal");

        function openDeleteModal() {
            deleteModal.style.display = "flex";
        }

        function closeDeleteModal() {
            deleteModal.style.display = "none";
        }

        function confirmDelete() {
            closeDeleteModal();
            alert("Issued book record deleted successfully!");
            // Here you can remove the row or call backend later
        }

        function openBookModal(bookId, image, title, author, category, year, library) {
            document.getElementById("modalBookId").innerText = bookId;
            document.getElementById("modalBookImage").src = image;
            document.getElementById("modalBookTitle").innerText = title;
            document.getElementById("modalBookAuthor").innerText = author;
            document.getElementById("modalBookCategory").innerText = category;
            document.getElementById("modalBookYear").innerText = year;
            document.getElementById("modalBookLibrary").innerText = library;

            document.getElementById("bookModal").style.display = "flex";
        }

        function closeBookModal() {
            document.getElementById("bookModal").style.display = "none";
        }
    </script>

    <script>
        document.querySelectorAll(".btn-pay").forEach(btn => {
            btn.addEventListener("click", function() {

                const amount = parseInt(this.dataset.amount);
                const issueId = parseInt(this.dataset.issue);
                const libraryId = parseInt(this.dataset.library);

                const upiLink = `upi://pay?pa=asodariyadhruvil80@pingpay&pn=BookMyLibrary&am=${amount}&cu=INR&tn=Library Fine Payment`;

                const qrURL = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" + encodeURIComponent(upiLink);

                document.getElementById("upiQR").src = qrURL;
                document.getElementById("upiAmount").textContent = amount;

                document.getElementById("issueId").value = issueId;
                document.getElementById("libraryId").value = libraryId;
                document.getElementById("amount").value = amount;

                document.getElementById("upiModal").style.display = "flex";
            });
        });

        function closeUPIModal() {
            document.getElementById("upiModal").style.display = "none";
        }
    </script>

    <script>
        const renewModal = document.getElementById("renewModal");
        const bookIdInput = renewModal.querySelector('[name="renew_book_id"]');
        const oldDateInput = renewModal.querySelector('[name="old_return_date"]');
        const newDateInput = renewModal.querySelector('[name="new_return_date"]');
        const renewBtn = document.getElementById("renewConfirmBtn");

        // OPEN MODAL
        document.querySelectorAll(".btn-renew").forEach(btn => {
            btn.addEventListener("click", function() {

                const renewCount = parseInt(this.dataset.renew);

                // 🚫 Limit reached
                if (renewCount >= 2) {

                    Swal.fire({
                        toast: true,
                        position: "top",
                        icon: "error",
                        title: "Renew limit reached (Max 2 renewals)",
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });

                    return;
                }

                // OPEN MODAL
                renewModal.style.display = "flex";

                bookIdInput.value = this.dataset.book;

                const oldDate = new Date(this.dataset.return);
                oldDateInput.value = formatDate(oldDate);

                const newDate = new Date(oldDate);
                newDate.setDate(newDate.getDate() + 7);

                newDateInput.value = formatDate(newDate);
            });
        });

        // FORMAT DATE
        function formatDate(dateObj) {
            const d = new Date(dateObj);
            return d.toISOString().split('T')[0];
        }

        // CLOSE MODAL
        function closeRenew() {
            renewModal.style.display = "none";
        }

        // CONFIRM RENEW
        renewBtn.addEventListener("click", confirmRenew);

        function confirmRenew() {

            const bookId = bookIdInput.value;
            const newDate = newDateInput.value;

            fetch("renew_book.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `book_id=${bookId}&new_return_date=${newDate}`
                })
                .then(res => res.json())
                .then(data => {

                    if (data.status === "success") {

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Book renewed successfully!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                        closeRenew();
                        setTimeout(() => location.reload(), 1200);

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: data.message
                        });
                    }

                })
                .catch(err => {
                    console.error(err);
                    alert("Server error");
                });
        }
    </script>

    <script>
        function openRatingModal(issue_id, book_id) {
            const modal = document.getElementById("ratingModal");
            const issueInput = document.getElementById("issue_id");
            const bookInput = document.getElementById("book_id");
            const ratingInput = document.getElementById("selected_rating");

            if (!modal || !issueInput || !bookInput || !ratingInput) {
                console.log("Rating modal elements not found");
                return;
            }

            issueInput.value = issue_id;
            bookInput.value = book_id;
            ratingInput.value = 0;

            document.querySelectorAll(".stars span").forEach(star => {
                star.classList.remove("active");
            });

            modal.style.display = "flex";
        }

        function closeRatingModal() {
            document.getElementById("ratingModal").style.display = "none";
        }

        function setRating(rating) {
            document.getElementById("selected_rating").value = rating;

            document.querySelectorAll(".stars span").forEach((star, index) => {
                if (index < rating) {
                    star.classList.add("active");
                } else {
                    star.classList.remove("active");
                }
            });
        }

        function submitRating() {
            const issue_id = document.getElementById("issue_id").value;
            const book_id = document.getElementById("book_id").value;
            const rating = document.getElementById("selected_rating").value;
            const review = document.getElementById("review_text").value;

            if (rating == 0) {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'warning',
                    title: 'Please select a rating',
                    showConfirmButton: false,
                    timer: 2000
                });
                return;
            }

            fetch("submit_rating.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `issue_id=${issue_id}&book_id=${book_id}&rating=${rating}&review=${encodeURIComponent(review)}`
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: data.status,
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    });

                    if (data.status === "success") {
                        closeRatingModal();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                });
        }
    </script>

</body>

</html>