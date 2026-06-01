<?php 
// 404 Error Page
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Sayfa Bulunamadı | Bilgihavuzu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');
        body {
            background-color: #07080d;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 0;
            background-image: 
                radial-gradient(circle at 50% 50%, rgba(157, 78, 221, 0.1), transparent 50%),
                radial-gradient(circle at 10% 20%, rgba(0, 242, 254, 0.05), transparent 30%);
        }
        .glitch {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #00f2fe, #f355da);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 30px rgba(243, 85, 218, 0.4);
            margin-bottom: 0.5rem;
            line-height: 1;
        }
        .subtext {
            color: #a0aec0;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 2.5rem;
            line-height: 1.6;
        }
        .btn-pulse {
            background: rgba(13, 15, 24, 0.8);
            border: 1px solid #00f2fe;
            color: #00f2fe;
            padding: 12px 35px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 0 15px rgba(0, 242, 254, 0.2);
            animation: pulse-glow 2s infinite;
            display: inline-block;
        }
        .btn-pulse:hover {
            background: #00f2fe;
            color: #07080d;
            box-shadow: 0 0 25px rgba(0, 242, 254, 0.6);
        }
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 10px rgba(0, 242, 254, 0.2); }
            50% { box-shadow: 0 0 25px rgba(0, 242, 254, 0.5); }
            100% { box-shadow: 0 0 10px rgba(0, 242, 254, 0.2); }
        }
    </style>
</head>
<body>

<div class="text-center px-4">
    <div class="glitch">404</div>
    <h2 class="h3 fw-bold mb-3" style="color: #e2e8f0;">Siber Uzayda Kayboldun</h2>
    <p class="subtext">
        Aradığın soru veya veri, veri tabanından silinmiş veya hiç var olmamış olabilir. Lütfen sistemin güvenli bölgesine geri dön.
    </p>
    <a href="index.php" class="btn-pulse">
        Ana Sayfaya Dön
    </a>
</div>

</body>
</html>
