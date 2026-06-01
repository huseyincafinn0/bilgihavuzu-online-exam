<?php
require_once '../database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soru Deneme - Yönetim Paneli</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Style -->
    <link href="assets/css/admin-style.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Master Dark Cyberpunk Override to Bypass Cache -->
    <style>
        body, .wrapper, #content, .bg-light {
            background-color: #07080d !important;
            color: #ffffff !important;
        }
        .admin-card, .modal-content, .bg-white {
            background: rgba(13, 15, 24, 0.75) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1) !important;
        }
        .text-dark, .text-navy {
            color: #ffffff !important;
            text-shadow: 0 0 5px rgba(255,255,255,0.2) !important;
        }
        .table {
            color: #ffffff !important;
        }
        .table-light, .table thead th {
            background-color: rgba(0, 242, 254, 0.05) !important;
            color: #00f2fe !important;
            border-bottom: 2px solid #00f2fe !important;
        }
        .table tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.01) !important;
        }
        .table tbody tr:nth-of-type(even) {
            background-color: rgba(255, 255, 255, 0.03) !important;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 242, 254, 0.05) !important;
            box-shadow: inset 5px 0 0 #00f2fe !important;
            color: #fff !important;
        }
        .table td, .table th {
            border-color: rgba(255, 255, 255, 0.06) !important;
            background-color: transparent !important;
        }
    </style>
</head>
<body>
<div class="wrapper">
