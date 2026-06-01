<?php
require_once 'database.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soru Deneme Sitesi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Master Cyberpunk Student Portal Override -->
    <style>
        body {
            background-color: #07080d !important;
            color: #ffffff !important;
        }
        .navbar-custom {
            background: rgba(13, 15, 24, 0.75) !important;
            backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid rgba(157, 78, 221, 0.15) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1) !important;
        }
        .navbar-brand, .nav-link, .text-dark, .text-muted, h2, h3, h4, h5, label {
            color: #ffffff !important;
        }
        .nav-link:hover {
            color: #00f2fe !important;
            text-shadow: 0 0 5px rgba(0, 242, 254, 0.4);
        }
        .glass-card, .card {
            background: rgba(13, 15, 24, 0.75) !important;
            backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
            color: #fff !important;
        }
        .bg-light, .bg-white {
            background: transparent !important;
        }
        /* Custom Stepper & Selects */
        .form-select, .form-control {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #fff !important;
        }
        .form-select:focus, .form-control:focus {
            border-color: #00f2fe !important;
            box-shadow: 0 0 15px rgba(0, 242, 254, 0.4) !important;
            background: rgba(255, 255, 255, 0.06) !important;
        }
        .form-select option {
            background: #07080d !important;
            color: #fff !important;
        }
        /* Tables */
        .table { color: #fff !important; }
        .table td, .table th { border-color: rgba(255,255,255,0.06) !important; background-color: transparent !important; }
        .table tbody tr { border-bottom: 1px solid rgba(255,255,255,0.06) !important; }
        /* Stepper Buttons */
        .stepper-btn {
            background: rgba(0, 242, 254, 0.1);
            border: 1px solid #00f2fe;
            color: #00f2fe;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
        }
        .stepper-btn:hover {
            background: #00f2fe;
            color: #07080d;
            box-shadow: 0 0 15px rgba(0, 242, 254, 0.6);
        }
        .stepper-input {
            width: 50px;
            text-align: center;
            background: transparent !important;
            border: none !important;
            color: #fff !important;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .stepper-input:focus { outline: none; }
        /* Remove arrows from number input */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        /* Skeleton Loader */
        @keyframes cyberPulse {
            0% { background-color: rgba(255, 255, 255, 0.02); }
            50% { background-color: rgba(157, 78, 221, 0.05); }
            100% { background-color: rgba(255, 255, 255, 0.02); }
        }
        .skeleton-box {
            animation: cyberPulse 1.5s infinite ease-in-out;
            border-radius: 8px;
            min-height: 20px;
        }
        /* Toast specific */
        #globalCyberToast {
            transition: opacity 0.4s ease, transform 0.4s ease;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom mb-5">
    <div class="container">
        <a class="navbar-brand text-dark fw-bold" href="dashboard.php">
            <i class="fa-solid fa-book-open text-primary me-2"></i>Bilgihavuzu
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="create_exam.php">Deneme Oluştur</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="my_exams.php">Geçmiş Denemelerim</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if($_SESSION['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="admin/index.php">Admin Paneli</a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php"><?= htmlspecialchars($_SESSION['full_name']) ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Çıkış Yap</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Global Cyberpunk Toast Notification -->
<div id="globalCyberToast" class="position-fixed top-0 end-0 p-4" style="z-index: 9999; opacity: 0; transform: translateY(-20px); pointer-events: none; margin-top: 60px;">
    <div class="toast show align-items-center text-white border-0" id="globalToastBody" style="background: rgba(13, 15, 24, 0.95); backdrop-filter: blur(12px); border-left: 4px solid #00f2fe !important; box-shadow: 0 0 25px rgba(0, 242, 254, 0.3); border-radius: 8px;">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="globalCyberToastMsg" style="font-size: 1.1rem; font-family: 'Inter', sans-serif;"></div>
            <button type="button" class="btn-close btn-close-white me-3 m-auto" onclick="document.getElementById('globalCyberToast').style.opacity='0'"></button>
        </div>
    </div>
</div>

<script>
window.showCyberToast = function(msg, type = 'cyan') {
    const toastEl = document.getElementById('globalCyberToast');
    const toastBody = document.getElementById('globalToastBody');
    const msgEl = document.getElementById('globalCyberToastMsg');
    
    let color = '#00f2fe';
    let icon = 'fa-circle-info';
    let shadow = 'rgba(0, 242, 254, 0.3)';
    
    if (type === 'magenta' || type === 'error' || type === 'warning') {
        color = '#f355da';
        icon = 'fa-triangle-exclamation';
        shadow = 'rgba(243, 85, 218, 0.4)';
    } else if (type === 'success') {
        color = '#00ff88';
        icon = 'fa-check';
        shadow = 'rgba(0, 255, 136, 0.3)';
    }

    toastBody.style.borderLeftColor = color;
    toastBody.style.boxShadow = `0 0 25px ${shadow}`;
    msgEl.innerHTML = `<i class="fa-solid ${icon} me-2" style="color: ${color};"></i>` + msg;
    
    toastEl.style.opacity = '1';
    toastEl.style.transform = 'translateY(0)';
    toastEl.style.pointerEvents = 'auto';
    
    if(window.toastTimer) clearTimeout(window.toastTimer);
    window.toastTimer = setTimeout(() => { 
        toastEl.style.opacity = '0';
        toastEl.style.transform = 'translateY(-20px)';
        toastEl.style.pointerEvents = 'none';
    }, 3000);
}
</script>

<div class="container pb-5">
