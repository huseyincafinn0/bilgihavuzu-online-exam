<?php
require_once 'database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $error = "Lütfen tüm alanları doldurun.";
    } elseif ($password !== $password_confirm) {
        $error = "Şifreler eşleşmiyor.";
    } else {
        // Kullanıcı adı veya e-posta kullanımda mı?
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = "Bu kullanıcı adı veya e-posta zaten kullanımda.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (full_name, username, email, password, role) VALUES (?, ?, ?, ?, 'user')");
            if ($stmt->execute([$full_name, $username, $email, $hashed_password])) {
                $success = "Kayıt başarılı. Giriş yapabilirsiniz.";
            } else {
                $error = "Kayıt sırasında bir hata oluştu.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | Bilgihavuzu Siber Sürüm</title>
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #07080d;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(0, 242, 254, 0.05), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(243, 85, 218, 0.05), transparent 25%);
            margin: 0;
            min-height: 100vh;
        }

        h1, h2, h3, h4, .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphism Form Container */
        .glass-panel {
            background: rgba(13, 15, 24, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Input Fields styling */
        .neon-input {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
            color: #ffffff;
        }

        .neon-input:focus {
            border-color: #00f2fe;
            box-shadow: 0 0 12px rgba(0, 242, 254, 0.2);
            outline: none;
            background: rgba(255, 255, 255, 0.05);
        }

        /* Main Gradient Button */
        .neon-btn-primary {
            background: linear-gradient(135deg, #00f2fe, #f355da);
            box-shadow: 0 4px 15px rgba(243, 85, 218, 0.2);
            transition: all 0.3s ease;
            border: none;
        }

        .neon-btn-primary:hover {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(0, 242, 254, 0.5), 0 0 20px rgba(243, 85, 218, 0.5);
            text-shadow: 0 0 8px rgba(255,255,255,0.8);
        }

        /* Social Auth Buttons */
        .neon-btn-social {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }

        .neon-btn-social:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        /* Text Gradient */
        .text-gradient {
            background: linear-gradient(135deg, #00f2fe, #f355da);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Abstract Grid Pattern for Left Screen */
        .bg-cyber-grid {
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="text-white selection:bg-cyan-500/30">

<div class="flex w-full min-h-screen">
    
    <!-- Left Screen: Marketing (Hidden on Mobile, Visible on Desktop) -->
    <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-center items-center p-12 overflow-hidden bg-cyber-grid">
        <!-- Glowing background orb -->
        <div class="absolute w-[500px] h-[500px] bg-cyan-500/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500/10 rounded-full blur-[100px] pointer-events-none"></div>
        
        <div class="relative z-10 text-center space-y-8 max-w-lg">
            
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-cyan-500/30 text-xs font-bold text-cyan-400 bg-cyan-500/10 uppercase tracking-widest shadow-[0_0_15px_rgba(0,242,254,0.15)]">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                v2.0 Cyber Edition
            </div>
            
            <h1 class="text-5xl lg:text-6xl font-outfit font-extrabold leading-tight">
                Siber Dünyanın <br>
                <span class="text-gradient">Soru Havuzuna</span><br>
                Adım At
            </h1>
            
            <p class="text-gray-400 text-lg leading-relaxed font-medium">
                Yapay zeka destekli analizler, binlerce güncel mock exam ve tamamen dinamik bir test deneyimi. Hedefine ulaşmak için sadece bir adım kaldı.
            </p>

            <div class="pt-8 flex items-center justify-center gap-4">
                <div class="flex -space-x-4">
                    <div class="w-10 h-10 rounded-full border-2 border-[#07080d] bg-gray-700 flex items-center justify-center overflow-hidden"><img src="https://i.pravatar.cc/100?img=1" alt="User"></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#07080d] bg-gray-700 flex items-center justify-center overflow-hidden"><img src="https://i.pravatar.cc/100?img=2" alt="User"></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#07080d] bg-gray-700 flex items-center justify-center overflow-hidden"><img src="https://i.pravatar.cc/100?img=3" alt="User"></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#07080d] bg-cyan-500/20 flex items-center justify-center text-xs font-bold text-cyan-400">+12K</div>
                </div>
                <div class="text-sm text-gray-400 font-medium">aktif kullanıcı arasına katıl</div>
            </div>
        </div>
    </div>

    <!-- Right Screen: Registration Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative z-20">
        <!-- Floating decorative shape -->
        <div class="absolute top-10 right-10 w-32 h-32 bg-purple-500/20 rounded-full blur-[60px] pointer-events-none"></div>

        <div class="w-full max-w-md glass-panel rounded-3xl p-8 sm:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
            
            <!-- Logo Header -->
            <div class="flex items-center justify-center gap-3 mb-8">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-cyan-400 to-purple-500 flex items-center justify-center shadow-[0_0_15px_rgba(0,242,254,0.3)]">
                    <i class="fa-solid fa-layer-group text-white text-xl"></i>
                </div>
                <span class="font-outfit text-3xl font-bold text-white tracking-wide">Bilgihavuzu</span>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-white mb-2">Hesap Oluştur</h2>
                <p class="text-sm text-gray-400">Sisteme katıl ve başarı analizlerini hemen başlat.</p>
            </div>

            <!-- PHP Error / Success Alerts -->
            <?php if ($error): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-medium flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-sm font-medium flex flex-col gap-2 text-center">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                        <span><?= htmlspecialchars($success) ?></span>
                    </div>
                    <a href="login.php" class="inline-block py-2 px-4 bg-green-500/20 rounded-lg text-green-300 hover:bg-green-500/30 transition-colors font-bold">
                        Hemen Giriş Yap
                    </a>
                </div>
            <?php else: ?>

            <!-- Original Registration Form -->
            <form method="post" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Ad Soyad</label>
                    <input type="text" name="full_name" class="w-full px-4 py-3.5 rounded-xl neon-input text-white placeholder-gray-600 font-medium" placeholder="Örn: Ali Yılmaz" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Kullanıcı Adı</label>
                    <input type="text" name="username" class="w-full px-4 py-3.5 rounded-xl neon-input text-white placeholder-gray-600 font-medium" placeholder="Örn: aliyilmaz99" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">E-posta Adresi</label>
                    <input type="email" name="email" class="w-full px-4 py-3.5 rounded-xl neon-input text-white placeholder-gray-600 font-medium" placeholder="ornek@mail.com" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Şifre</label>
                        <input type="password" name="password" class="w-full px-4 py-3.5 rounded-xl neon-input text-white placeholder-gray-600 font-medium" placeholder="••••••••" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Şifre Tekrar</label>
                        <input type="password" name="password_confirm" class="w-full px-4 py-3.5 rounded-xl neon-input text-white placeholder-gray-600 font-medium" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 mt-2 rounded-xl neon-btn-primary text-white font-bold text-lg tracking-wide uppercase">
                    Kayıt Ol
                </button>
            </form>

            <!-- Social Auth Divider -->
            <div class="my-8 flex items-center justify-between">
                <div class="h-px bg-white/5 flex-1"></div>
                <span class="text-[10px] text-gray-500 px-4 uppercase tracking-widest font-bold">Veya şununla kayıt ol</span>
                <div class="h-px bg-white/5 flex-1"></div>
            </div>

            <!-- Social Auth Buttons -->
            <div class="grid grid-cols-2 gap-4">
                <button type="button" class="neon-btn-social flex items-center justify-center gap-2 py-3.5 rounded-xl text-sm font-semibold text-gray-300">
                    <i class="fa-brands fa-github text-lg text-white"></i> GitHub
                </button>
                <button type="button" class="neon-btn-social flex items-center justify-center gap-2 py-3.5 rounded-xl text-sm font-semibold text-gray-300">
                    <i class="fa-brands fa-google text-lg text-white"></i> Google
                </button>
            </div>

            <?php endif; ?>

            <!-- Form Footer -->
            <div class="mt-8 text-center text-sm text-gray-400 font-medium">
                Zaten hesabınız var mı? 
                <a href="login.php" class="text-white font-bold transition-all duration-300 hover:text-cyan-400 hover:drop-shadow-[0_0_8px_rgba(0,242,254,0.8)] ml-1">
                    Giriş Yap
                </a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
