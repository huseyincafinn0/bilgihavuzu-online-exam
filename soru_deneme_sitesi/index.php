<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilgihavuzu - Premium Online Test Platformu</title>
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap');
        
        :root {
            --bg-dark: #07080d;
            --neon-cyan: #00f2fe;
            --neon-purple: #f355da;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: #ffffff;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(0, 242, 254, 0.05), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(243, 85, 218, 0.05), transparent 25%);
        }

        h1, h2, h3, h4, .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphism */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .glass-nav {
            background: rgba(7, 8, 13, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
        }

        /* Neon Text Gradients */
        .text-gradient {
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .text-gradient-cyan {
            background: linear-gradient(135deg, #fff, var(--neon-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Neon Borders & Glows */
        .neon-button {
            position: relative;
            background: rgba(0, 242, 254, 0.05);
            color: #fff;
            border: 1px solid var(--neon-cyan);
            box-shadow: 0 0 10px rgba(0, 242, 254, 0.2), inset 0 0 10px rgba(0, 242, 254, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .neon-button:hover {
            background: rgba(0, 242, 254, 0.1);
            box-shadow: 0 0 20px rgba(0, 242, 254, 0.6), inset 0 0 15px rgba(0, 242, 254, 0.4);
            text-shadow: 0 0 8px rgba(255,255,255,0.8);
            border-color: #fff;
            transform: scale(1.02);
        }

        .neon-button-purple {
            position: relative;
            background: rgba(243, 85, 218, 0.05);
            color: #fff;
            border: 1px solid var(--neon-purple);
            box-shadow: 0 0 10px rgba(243, 85, 218, 0.2), inset 0 0 10px rgba(243, 85, 218, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .neon-button-purple:hover {
            background: rgba(243, 85, 218, 0.1);
            box-shadow: 0 0 20px rgba(243, 85, 218, 0.6), inset 0 0 15px rgba(243, 85, 218, 0.4);
            text-shadow: 0 0 8px rgba(255,255,255,0.8);
            border-color: #fff;
            transform: scale(1.02);
        }

        /* Animations */
        @keyframes float {
            0% { transform: translateY(0px) rotateY(-10deg) rotateX(5deg); }
            50% { transform: translateY(-15px) rotateY(-8deg) rotateX(7deg); }
            100% { transform: translateY(0px) rotateY(-10deg) rotateX(5deg); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float-simple {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float-delayed {
            animation: float-simple 6s ease-in-out 3s infinite;
        }

        @keyframes pulse-glow {
            0% { box-shadow: 0 0 15px rgba(0, 242, 254, 0.2); }
            50% { box-shadow: 0 0 30px rgba(0, 242, 254, 0.5); }
            100% { box-shadow: 0 0 15px rgba(0, 242, 254, 0.2); }
        }
        .pulse-glow {
            animation: pulse-glow 3s infinite;
        }

        /* Grid Cards */
        .exam-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: linear-gradient(180deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);
        }
        .exam-card:hover {
            transform: translateY(-8px) scale(1.01);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .pricing-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .pricing-card::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
            transform: skewX(-20deg);
            transition: 0.6s;
        }
        .pricing-card:hover::after {
            left: 150%;
        }
        .pricing-card:hover {
            border-color: rgba(255,255,255,0.15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            transform: translateY(-5px);
        }

        /* Cookie Banner */
        #cookie-banner {
            transform: translateY(150%);
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #cookie-banner.show {
            transform: translateY(0);
        }

        /* Abstract shapes */
        .glow-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            z-index: -1;
            opacity: 0.3;
        }
    </style>
</head>
<body class="antialiased selection:bg-cyan-500/30">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-400 to-purple-500 flex items-center justify-center pulse-glow">
                        <i class="fa-solid fa-layer-group text-white text-lg"></i>
                    </div>
                    <a href="index.php" class="flex flex-col">
                        <span class="font-outfit text-2xl font-bold tracking-tight text-white">Bilgihavuzu</span>
                        <span class="text-[10px] text-cyan-400 font-medium tracking-widest uppercase mt-[-4px]">Yeni Nesil Sınav</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#sinavlar" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Sınavlar</a>
                    <a href="#istatistikler" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">İstatistikler</a>
                    <a href="#market" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Elmas Market</a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="login.php" class="text-sm font-semibold text-gray-300 hover:text-white transition-colors">Giriş Yap</a>
                    <a href="register.php" class="neon-button px-5 py-2.5 rounded-lg text-sm font-bold tracking-wide">
                        Kayıt Ol <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="glow-shape bg-cyan-500/20 w-96 h-96 top-20 left-[-10%]"></div>
        <div class="glow-shape bg-purple-500/20 w-96 h-96 bottom-0 right-[-10%]"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                
                <!-- Left: Content -->
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border border-cyan-500/20 text-xs font-medium text-cyan-300 shadow-none">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                        V2.0 Cyber Güncellemesi Yayında
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-extrabold leading-tight">
                        Geleceğin <br/>
                        <span class="text-gradient">Online Test</span> Platformu
                    </h1>
                    
                    <p class="text-lg text-gray-400 max-w-xl leading-relaxed">
                        Yapay zeka destekli analizler, binlerce güncel soru ve oyunlaştırılmış sınav deneyimi. Rakiplerinin bir adım önüne geçmeye hazır mısın?
                    </p>
                    
                    <div class="glass-panel p-4 rounded-2xl border border-purple-500/20 inline-block shadow-none">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center border border-purple-500/30">
                                <i class="fa-solid fa-gem text-purple-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Elmas Sistemi Aktif</p>
                                <p class="text-lg font-bold text-white">15 Soruluk Test sadece <span class="text-purple-400">1 Elmas</span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-4 pt-4">
                        <a href="register.php" class="neon-button px-8 py-4 rounded-xl font-bold text-lg">
                            Hemen Başla
                        </a>
                        <a href="#market" class="glass-panel px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/5 transition-colors border-white/10 shadow-none hover:shadow-[0_0_15px_rgba(255,255,255,0.05)]">
                            Paketleri İncele
                        </a>
                    </div>
                </div>

                <!-- Right: Dashboard Preview -->
                <div class="relative lg:h-[600px] flex items-center justify-center" style="perspective: 1000px;">
                    <div class="glass-panel w-full max-w-md rounded-2xl border border-cyan-500/20 p-8 animate-float relative z-20 shadow-[0_15px_40px_rgba(0,0,0,0.4)]">
                        <div class="flex justify-between items-center mb-8 border-b border-white/10 pb-5">
                            <div>
                                <h3 class="font-outfit font-extrabold text-2xl text-white tracking-wide">YKS Deneme #42</h3>
                                <p class="text-sm text-cyan-400 mt-1 font-medium">Canlı Oturum - Sayısal Bölüm</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-white">45:00</div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider mt-1">Kalan Süre</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <p class="text-base text-gray-200 leading-relaxed font-medium">Soru 1: Limit ve Süreklilik kavramları dikkate alındığında, aşağıdakilerden hangisi daima doğrudur?</p>
                            <div class="space-y-4">
                                <div class="glass-panel p-4 rounded-xl border border-white/5 hover:border-cyan-500/40 hover:bg-cyan-500/5 cursor-pointer transition-all duration-300 ease-in-out flex items-center gap-4 shadow-none hover:shadow-[0_0_10px_rgba(0,242,254,0.1)]">
                                    <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-sm font-bold border border-white/10 transition-colors duration-300">A</div>
                                    <span class="text-base text-gray-300 transition-colors duration-300">Yalnız I</span>
                                </div>
                                <div class="glass-panel p-4 rounded-xl border border-cyan-500/60 shadow-[0_0_15px_rgba(0,242,254,0.15)] cursor-pointer transition-all duration-300 ease-in-out flex items-center gap-4 bg-cyan-500/10">
                                    <div class="w-8 h-8 rounded-full bg-cyan-500 flex items-center justify-center text-sm font-bold text-black transition-colors duration-300">B</div>
                                    <span class="text-base text-white font-medium transition-colors duration-300">I ve II</span>
                                </div>
                                <div class="glass-panel p-4 rounded-xl border border-white/5 hover:border-cyan-500/40 hover:bg-cyan-500/5 cursor-pointer transition-all duration-300 ease-in-out flex items-center gap-4 shadow-none hover:shadow-[0_0_10px_rgba(0,242,254,0.1)]">
                                    <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-sm font-bold border border-white/10 transition-colors duration-300">C</div>
                                    <span class="text-base text-gray-300 transition-colors duration-300">I, II ve III</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute top-10 right-4 glass-panel p-4 rounded-xl border border-purple-500/30 animate-float-delayed z-30 shadow-[0_10px_20px_rgba(0,0,0,0.2)]">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bolt text-purple-400"></i>
                            <div>
                                <p class="text-xs text-gray-400">Anlık Analiz</p>
                                <p class="text-sm font-bold text-white">+%15 Başarı Artışı</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute bottom-16 left-0 glass-panel p-4 rounded-xl border border-cyan-500/30 animate-float-delayed z-30 shadow-[0_10px_20px_rgba(0,0,0,0.2)]" style="animation-delay: 1.5s;">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-chart-pie text-cyan-400"></i>
                            <div>
                                <p class="text-xs text-gray-400">Konu Eksikleri</p>
                                <p class="text-sm font-bold text-white">Tespit Edildi</p>
                            </div>
                        </div>
                    </div>

                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-cyan-500/10 rounded-full blur-[100px] z-0 pointer-events-none"></div>
                </div>

            </div>
        </div>
    </section>

    <!-- Active Exam Grid -->
    <section id="sinavlar" class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold mb-4">Sınav <span class="text-gradient">Kategorileri</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Hedefine uygun sınavı seç ve binlerce soru arasından kendini test etmeye başla.</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- YKS -->
                <a href="quiz.php?type=YKS" class="block glass-panel rounded-2xl p-6 exam-card border border-white/5 text-center group cursor-pointer shadow-none">
                    <div class="w-16 h-16 mx-auto rounded-full bg-cyan-500/10 flex items-center justify-center mb-4 border border-cyan-500/30 group-hover:bg-cyan-500/20 group-hover:border-cyan-500/60 transition-all duration-300 shadow-[0_0_10px_rgba(0,242,254,0.05)] group-hover:shadow-[0_0_20px_rgba(0,242,254,0.3)]">
                        <i class="fa-solid fa-graduation-cap text-3xl text-cyan-300 drop-shadow-[0_0_5px_rgba(0,242,254,0.4)] group-hover:text-white group-hover:drop-shadow-[0_0_12px_rgba(0,242,254,0.9)] transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">YKS</h3>
                    <p class="text-xs text-gray-400">TYT & AYT Denemeleri</p>
                </a>
                <!-- LGS -->
                <a href="quiz.php?type=LGS" class="block glass-panel rounded-2xl p-6 exam-card border border-white/5 text-center group cursor-pointer shadow-none">
                    <div class="w-16 h-16 mx-auto rounded-full bg-purple-500/10 flex items-center justify-center mb-4 border border-purple-500/30 group-hover:bg-purple-500/20 group-hover:border-purple-500/60 transition-all duration-300 shadow-[0_0_10px_rgba(243,85,218,0.05)] group-hover:shadow-[0_0_20px_rgba(243,85,218,0.3)]">
                        <i class="fa-solid fa-school text-3xl text-purple-300 drop-shadow-[0_0_5px_rgba(243,85,218,0.4)] group-hover:text-white group-hover:drop-shadow-[0_0_12px_rgba(243,85,218,0.9)] transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">LGS</h3>
                    <p class="text-xs text-gray-400">Liselere Geçiş</p>
                </a>
                <!-- KPSS -->
                <a href="quiz.php?type=KPSS" class="block glass-panel rounded-2xl p-6 exam-card border border-white/5 text-center group cursor-pointer shadow-none">
                    <div class="w-16 h-16 mx-auto rounded-full bg-cyan-500/10 flex items-center justify-center mb-4 border border-cyan-500/30 group-hover:bg-cyan-500/20 group-hover:border-cyan-500/60 transition-all duration-300 shadow-[0_0_10px_rgba(0,242,254,0.05)] group-hover:shadow-[0_0_20px_rgba(0,242,254,0.3)]">
                        <i class="fa-solid fa-building-columns text-3xl text-cyan-300 drop-shadow-[0_0_5px_rgba(0,242,254,0.4)] group-hover:text-white group-hover:drop-shadow-[0_0_12px_rgba(0,242,254,0.9)] transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">KPSS</h3>
                    <p class="text-xs text-gray-400">Kamu Personeli Seçme</p>
                </a>
                <!-- YDT -->
                <a href="quiz.php?type=YDT" class="block glass-panel rounded-2xl p-6 exam-card border border-white/5 text-center group cursor-pointer shadow-none">
                    <div class="w-16 h-16 mx-auto rounded-full bg-purple-500/10 flex items-center justify-center mb-4 border border-purple-500/30 group-hover:bg-purple-500/20 group-hover:border-purple-500/60 transition-all duration-300 shadow-[0_0_10px_rgba(243,85,218,0.05)] group-hover:shadow-[0_0_20px_rgba(243,85,218,0.3)]">
                        <i class="fa-solid fa-language text-3xl text-purple-300 drop-shadow-[0_0_5px_rgba(243,85,218,0.4)] group-hover:text-white group-hover:drop-shadow-[0_0_12px_rgba(243,85,218,0.9)] transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">YDT</h3>
                    <p class="text-xs text-gray-400">Yabancı Dil Testi</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Dynamic Live Stats -->
    <section id="istatistikler" class="py-20 border-y border-white/5 bg-[#0a0b10]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-white/5">
                <div class="p-4">
                    <div class="text-5xl font-black text-gradient-cyan mb-2 drop-shadow-[0_0_10px_rgba(0,242,254,0.15)]">520+</div>
                    <div class="text-gray-400 font-medium text-lg">Yüksek Kalite Soru Havuzu</div>
                </div>
                <div class="p-4">
                    <div class="text-5xl font-black text-gradient mb-2 drop-shadow-[0_0_10px_rgba(243,85,218,0.15)]"><i class="fa-solid fa-microchip"></i></div>
                    <div class="text-gray-400 font-medium text-lg">Anında AI Analiz Raporu</div>
                </div>
                <div class="p-4">
                    <div class="text-5xl font-black text-gradient-cyan mb-2 drop-shadow-[0_0_10px_rgba(0,242,254,0.15)]">12K+</div>
                    <div class="text-gray-400 font-medium text-lg">Aktif Çözülen Deneme</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gamified Pricing Cards -->
    <section id="market" class="py-24 relative overflow-hidden">
        <div class="glow-shape bg-cyan-500/10 w-[600px] h-[600px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold mb-4">Elmas <span class="text-gradient">Paketleri</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Kendinize uygun paketi seçin ve sınavlarda öne geçmeye bugün başlayın.</p>
            </div>
            
            <!-- Changed items-center to items-stretch for equal heights -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto items-stretch">
                <!-- Basic -->
                <div class="glass-panel pricing-card rounded-3xl p-8 border border-white/5 flex flex-col h-full shadow-none hover:shadow-[0_15px_35px_rgba(0,0,0,0.5)]">
                    <div class="text-center mb-8">
                        <div class="text-gray-400 font-medium mb-2 uppercase tracking-widest text-xs">Başlangıç Paketi</div>
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <i class="fa-solid fa-gem text-cyan-400 text-2xl drop-shadow-[0_0_8px_rgba(0,242,254,0.4)]"></i>
                            <span class="text-4xl font-bold text-white">100</span>
                        </div>
                        <div class="text-3xl font-black text-white">100 <span class="text-sm text-gray-500 font-normal">TL</span></div>
                    </div>
                    <!-- flex-grow pushes the button to the bottom -->
                    <ul class="space-y-4 mb-10 flex-grow">
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-5 h-5 text-green-400 shrink-0 drop-shadow-[0_0_5px_rgba(74,222,128,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>~6 Deneme Sınavı Hakkı</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-5 h-5 text-green-400 shrink-0 drop-shadow-[0_0_5px_rgba(74,222,128,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Temel İstatistikler</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-500 opacity-50">
                            <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span>Yapay Zeka Analizi</span>
                        </li>
                    </ul>
                    <a href="register.php" class="block text-center w-full py-3 rounded-xl neon-button font-bold text-sm tracking-wide mt-auto">Satın Al</a>
                </div>

                <!-- Pro (Highlighted) -->
                <div class="glass-panel pricing-card rounded-3xl p-8 border border-purple-500/30 flex flex-col h-full relative transform md:-translate-y-4 shadow-[0_20px_40px_rgba(0,0,0,0.4)] bg-gradient-to-b from-purple-900/10 to-transparent">
                    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-cyan-400 to-purple-500"></div>
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-cyan-400 to-purple-500 text-white text-[10px] font-bold px-4 py-1.5 rounded-full whitespace-nowrap shadow-[0_0_15px_rgba(243,85,218,0.5)] tracking-wider">
                        PREMIUM PASS
                    </div>
                    <div class="text-center mb-8 mt-4">
                        <div class="text-purple-400 font-bold mb-2 uppercase tracking-widest text-xs drop-shadow-[0_0_5px_rgba(243,85,218,0.3)]">Avantaj Paketi</div>
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <i class="fa-solid fa-gem text-purple-400 text-3xl drop-shadow-[0_0_10px_rgba(243,85,218,0.6)]"></i>
                            <span class="text-5xl font-black text-white">500</span>
                        </div>
                        <div class="text-4xl font-black text-white">450 <span class="text-sm text-gray-500 font-normal">TL</span></div>
                    </div>
                    <ul class="space-y-4 mb-10 flex-grow">
                        <li class="flex items-start gap-3 text-sm text-gray-200">
                            <svg class="w-5 h-5 text-green-400 shrink-0 drop-shadow-[0_0_5px_rgba(74,222,128,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>~33 Deneme Sınavı Hakkı</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-200">
                            <svg class="w-5 h-5 text-green-400 shrink-0 drop-shadow-[0_0_5px_rgba(74,222,128,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Detaylı İstatistikler & Grafikler</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-200">
                            <svg class="w-5 h-5 text-green-400 shrink-0 drop-shadow-[0_0_5px_rgba(74,222,128,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Anında AI Konu Analizi</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-200">
                            <svg class="w-5 h-5 text-green-400 shrink-0 drop-shadow-[0_0_5px_rgba(74,222,128,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Öncelikli Destek Sistemi</span>
                        </li>
                    </ul>
                    <a href="register.php" class="block text-center w-full py-4 rounded-xl neon-button-purple font-bold text-base tracking-wide mt-auto">Hemen Yükselt</a>
                </div>

                <!-- Premium -->
                <div class="glass-panel pricing-card rounded-3xl p-8 border border-white/5 flex flex-col h-full shadow-none hover:shadow-[0_15px_35px_rgba(0,0,0,0.5)]">
                    <div class="text-center mb-8">
                        <div class="text-gray-400 font-medium mb-2 uppercase tracking-widest text-xs">Mega Paket</div>
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <i class="fa-solid fa-gem text-cyan-400 text-2xl drop-shadow-[0_0_8px_rgba(0,242,254,0.4)]"></i>
                            <span class="text-4xl font-bold text-white">1000</span>
                        </div>
                        <div class="text-3xl font-black text-white">800 <span class="text-sm text-gray-500 font-normal">TL</span></div>
                    </div>
                    <ul class="space-y-4 mb-10 flex-grow">
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-5 h-5 text-green-400 shrink-0 drop-shadow-[0_0_5px_rgba(74,222,128,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Yüksek Çözüm Kapasitesi</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-5 h-5 text-green-400 shrink-0 drop-shadow-[0_0_5px_rgba(74,222,128,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Gelişmiş İstatistik Paneli</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-5 h-5 text-green-400 shrink-0 drop-shadow-[0_0_5px_rgba(74,222,128,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Tüm Ekstra Özellikler</span>
                        </li>
                    </ul>
                    <a href="register.php" class="block text-center w-full py-3 rounded-xl neon-button font-bold text-sm tracking-wide mt-auto">Satın Al</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer with Distinct Visual Separation -->
    <footer class="relative bg-[#0e0f14] pt-20 pb-8 mt-10">
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-purple-500/40 to-transparent"></div>
        <div class="absolute top-0 inset-x-0 h-[10px] bg-gradient-to-b from-purple-500/5 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <a href="#" class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-cyan-400 to-purple-500 flex items-center justify-center">
                            <i class="fa-solid fa-layer-group text-white text-sm"></i>
                        </div>
                        <span class="font-outfit text-xl font-bold text-white">Bilgihavuzu</span>
                    </a>
                    <p class="text-sm text-gray-400 mb-6 leading-relaxed">Yeni nesil online sınav ve test analiz platformu. Yönetim Paneli entegrasyonu ile öğrenme sürecini dijitalleştir.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full glass-panel flex items-center justify-center text-gray-400 hover:text-cyan-400 hover:border-cyan-400 transition-all border-white/5 shadow-none"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full glass-panel flex items-center justify-center text-gray-400 hover:text-purple-400 hover:border-purple-400 transition-all border-white/5 shadow-none"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full glass-panel flex items-center justify-center text-gray-400 hover:text-cyan-400 hover:border-cyan-400 transition-all border-white/5 shadow-none"><i class="fa-brands fa-discord"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-bold text-white mb-6 font-outfit text-lg tracking-wide">Kategoriler</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-cyan-500/50"></span> YKS (TYT-AYT) Denemeleri</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-cyan-500/50"></span> LGS Hazırlık Testleri</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-cyan-500/50"></span> KPSS Genel Kültür / Yetenek</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-cyan-500/50"></span> YDT ve Alt Sınavlar</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-white mb-6 font-outfit text-lg tracking-wide">Kurumsal</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-purple-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-purple-500/50"></span> Hakkımızda</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-purple-500/50"></span> İletişim</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-purple-500/50"></span> Kullanım Koşulları</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-purple-500/50"></span> Gizlilik Politikası</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-white mb-6 font-outfit text-lg tracking-wide">İletişim</h4>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center"><i class="fa-solid fa-envelope text-cyan-400"></i></div> info@bilgihavuzu.com</li>
                        <li class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center"><i class="fa-solid fa-phone text-cyan-400"></i></div> +90 (850) 123 45 67</li>
                        <li class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center"><i class="fa-solid fa-location-dot text-cyan-400"></i></div> Teknoloji Geliştirme Bölgesi</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500">
                <p>&copy; <?php echo date('Y'); ?> Bilgihavuzu. Tüm hakları saklıdır.</p>
                <p class="mt-2 md:mt-0">Powered by <span class="text-cyan-400 font-semibold">Gürkan Software</span></p>
            </div>
        </div>
    </footer>

    <!-- Interactive Cookie Policy -->
    <div id="cookie-banner" class="fixed bottom-6 left-6 max-w-sm glass-panel p-5 rounded-2xl z-50 border border-cyan-500/20 shadow-2xl shadow-cyan-900/10 bg-[#07080d]/90 backdrop-blur-xl">
        <div class="flex gap-4">
            <div class="text-cyan-400 text-2xl mt-1"><i class="fa-solid fa-cookie-bite pulse-glow"></i></div>
            <div>
                <h4 class="font-bold text-white mb-1">Çerez Politikası</h4>
                <p class="text-xs text-gray-400 mb-4 leading-relaxed">Platformumuz size en iyi deneyimi sunmak, AI istatistiklerini hesaplamak ve site trafiğini analiz etmek için çerezleri kullanır.</p>
                <div class="flex gap-2">
                    <button onclick="acceptCookies()" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-black text-xs font-bold rounded-lg transition-colors">Kabul Et</button>
                    <button class="px-5 py-2 border border-white/10 hover:bg-white/5 text-white text-xs font-medium rounded-lg transition-colors">Tercihler</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-lg', 'shadow-cyan-900/10');
                nav.style.background = 'rgba(7, 8, 13, 0.9)';
            } else {
                nav.classList.remove('shadow-lg', 'shadow-cyan-900/10');
                nav.style.background = 'rgba(7, 8, 13, 0.7)';
            }
        });

        // Cookie Banner Logic
        document.addEventListener('DOMContentLoaded', () => {
            if (!localStorage.getItem('cookiesAccepted_v2')) {
                setTimeout(() => {
                    document.getElementById('cookie-banner').classList.add('show');
                }, 1500);
            }
        });

        function acceptCookies() {
            localStorage.setItem('cookiesAccepted_v2', 'true');
            document.getElementById('cookie-banner').classList.remove('show');
        }
    </script>
</body>
</html>
