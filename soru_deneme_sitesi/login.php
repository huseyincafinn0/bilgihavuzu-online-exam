<?php
require_once 'database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_key = trim($_POST['login_key'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($login_key) || empty($password)) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login_key, $login_key]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Kullanıcı adı veya şifre hatalı.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | Bilgihavuzu Siber Sürüm</title>
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

        /* Checkbox Custom Styling */
        .neon-checkbox:checked + div {
            border-color: #00f2fe;
            background: rgba(0, 242, 254, 0.1);
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
                Geri <br>
                <span class="text-gradient">Hoş Geldin!</span>
            </h1>
            
            <p class="text-gray-400 text-lg leading-relaxed font-medium">
                Yapay zeka destekli analizler ve binlerce güncel mock exam seni bekliyor. Kaldığın yerden başarıya devam et.
            </p>

            <div class="pt-8 flex items-center justify-center gap-4">
                <div class="flex -space-x-4">
                    <div class="w-10 h-10 rounded-full border-2 border-[#07080d] bg-gray-700 flex items-center justify-center overflow-hidden"><img src="https://i.pravatar.cc/100?img=1" alt="User"></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#07080d] bg-gray-700 flex items-center justify-center overflow-hidden"><img src="https://i.pravatar.cc/100?img=2" alt="User"></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#07080d] bg-gray-700 flex items-center justify-center overflow-hidden"><img src="https://i.pravatar.cc/100?img=3" alt="User"></div>
                    <div class="w-10 h-10 rounded-full border-2 border-[#07080d] bg-cyan-500/20 flex items-center justify-center text-xs font-bold text-cyan-400">+12K</div>
                </div>
                <div class="text-sm text-gray-400 font-medium">aktif kullanıcı içeride</div>
            </div>
        </div>
    </div>

    <!-- Right Screen: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative z-20">
        <!-- Floating decorative shape -->
        <div class="absolute top-10 right-10 w-32 h-32 bg-cyan-500/20 rounded-full blur-[60px] pointer-events-none"></div>

        <div class="w-full max-w-md glass-panel rounded-3xl p-8 sm:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
            
            <!-- Breadcrumb & Logo Header -->
            <div class="flex items-center justify-between mb-8">
                <a href="index.php" class="flex items-center gap-2 text-sm text-gray-400 hover:text-cyan-400 transition-colors group font-semibold">
                    <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Ana Sayfa
                </a>
                <div class="flex items-center gap-3">
                    <span class="font-outfit text-xl font-bold text-white tracking-wide">Bilgihavuzu</span>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-400 to-purple-500 flex items-center justify-center shadow-[0_0_15px_rgba(0,242,254,0.3)]">
                        <i class="fa-solid fa-layer-group text-white text-base"></i>
                    </div>
                </div>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white mb-2">Giriş Yap</h2>
                <p class="text-sm text-gray-400">Tekrar hoş geldin, başarıya kaldığın yerden devam et.</p>
            </div>

            <!-- PHP Error Alert -->
            <?php if ($error): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-medium flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>
            
            <!-- Original Login Form -->
            <form method="post" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Kullanıcı Adı veya E-posta</label>
                    <input type="text" name="login_key" class="w-full px-4 py-3.5 rounded-xl neon-input text-white placeholder-gray-600 font-medium" placeholder="Örn: aliyilmaz99 veya ornek@mail.com" required>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Şifre</label>
                    <input type="password" name="password" class="w-full px-4 py-3.5 rounded-xl neon-input text-white placeholder-gray-600 font-medium" placeholder="••••••••" required>
                </div>

                <!-- NEW: Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center justify-center w-5 h-5 rounded border border-white/10 bg-white/5 transition-colors group-hover:border-cyan-400/50">
                            <input type="checkbox" name="remember" class="opacity-0 absolute inset-0 cursor-pointer neon-checkbox">
                            <div class="absolute inset-0 rounded transition-all duration-200 pointer-events-none"></div>
                            <i class="fa-solid fa-check text-cyan-400 text-[10px] opacity-0 transition-opacity duration-200 z-10"></i>
                        </div>
                        <span class="text-sm text-gray-400 group-hover:text-gray-200 transition-colors">Beni Hatırla</span>
                    </label>
                    <style>
                        /* Checkbox checked state visibility */
                        .neon-checkbox:checked ~ i { opacity: 1; }
                        .neon-checkbox:checked ~ div { border-color: #00f2fe; background: rgba(0, 242, 254, 0.1); box-shadow: 0 0 8px rgba(0,242,254,0.3); }
                    </style>
                    
                    <a href="#" class="text-sm font-semibold text-purple-400 hover:text-cyan-400 transition-all duration-300 drop-shadow-[0_0_5px_rgba(243,85,218,0.3)] hover:drop-shadow-[0_0_8px_rgba(0,242,254,0.8)]">
                        Şifremi Unuttum?
                    </a>
                </div>

                <button type="submit" class="w-full py-4 mt-4 rounded-xl neon-btn-primary text-white font-bold text-lg tracking-wide uppercase">
                    Giriş Yap
                </button>
            </form>

            <!-- TEST HESAPLARI BİLGİSİ -->
            <div class="mt-8 p-5 rounded-2xl" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.15);">
                <h4 class="text-xs font-bold text-cyan-400 uppercase tracking-widest mb-4 text-center"><i class="fa-solid fa-users me-2"></i>Sistemdeki Kullanıcılar</h4>
                <div class="space-y-3">
                    <?php 
                    $demo_stmt = $db->query("SELECT full_name, username, role FROM users LIMIT 5");
                    while($demo_user = $demo_stmt->fetch()): 
                        $badge_color = $demo_user['role'] == 'admin' ? 'bg-purple-500/20 text-purple-400 border-purple-500/30' : 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30';
                        
                        // Kullanıcı adına göre varsayılan şifreyi belirle
                        $demo_pass = "123456"; // Default
                        if(strtolower($demo_user['username']) === 'admin') $demo_pass = 'admin123';
                        if(strtolower($demo_user['username']) === 'ogrenci') $demo_pass = 'ogrenci123';
                    ?>
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center text-xs p-3 rounded-xl bg-black/40 border border-white/5 transition-all hover:border-cyan-500/30 hover:bg-black/60">
                        <div class="mb-2 sm:mb-0">
                            <span class="text-gray-200 font-semibold text-sm"><?= htmlspecialchars($demo_user['full_name']) ?></span>
                            <span class="inline-block px-2 py-0.5 ml-2 rounded text-[10px] font-bold border <?= $badge_color ?> uppercase shadow-sm"><?= $demo_user['role'] ?></span>
                        </div>
                        <div class="text-left sm:text-right bg-white/5 p-2 rounded-lg sm:bg-transparent sm:p-0">
                            <div class="text-gray-400 mb-1">K.Adı: <span class="text-white font-mono bg-black/50 px-2 py-0.5 rounded border border-white/10"><?= htmlspecialchars($demo_user['username']) ?></span></div>
                            <div class="text-gray-400">Şifre: <span class="text-white font-mono bg-black/50 px-2 py-0.5 rounded border border-white/10"><?= $demo_pass ?></span></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Social Auth Divider -->
            <div class="my-8 flex items-center justify-between">
                <div class="h-px bg-white/5 flex-1"></div>
                <span class="text-[10px] text-gray-500 px-4 uppercase tracking-widest font-bold">Veya şununla giriş yap</span>
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

            <!-- Form Footer -->
            <div class="mt-8 text-center text-sm text-gray-400 font-medium">
                Hesabınız yok mu? 
                <a href="register.php" class="text-white font-bold transition-all duration-300 hover:text-cyan-400 hover:drop-shadow-[0_0_8px_rgba(0,242,254,0.8)] ml-1">
                    Hemen Kayıt Ol
                </a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
