<?php
// Veritabanı bağlantısı ve oturum başlatma (config.php'yi de kapsar)
require_once 'database.php';

// 1. GET Parametresine Göre Sınav Türünü Al
$exam_title = isset($_GET['type']) ? strtoupper(trim($_GET['type'])) : 'YKS';

// Dinamik Sınav ID Eşleştirmesi (Fallback mekanizması)
$type_map = [
    'YKS' => 1,
    'LGS' => 2,
    'KPSS' => 3,
    'YDT' => 4
];
$exam_type_id = isset($type_map[$exam_title]) ? $type_map[$exam_title] : 1;

/* 2. OTURUM (SESSION) KONTROLÜ VE VERİTABANINDAN SORU ÇEKME
  Eğer oturumda henüz sorular oluşturulmadıysa veya sınav türü değiştiyse veritabanından çek.
*/
if (!isset($_SESSION['quiz_questions']) || $_SESSION['quiz_type'] !== $exam_title) {
    
    $_SESSION['quiz_type'] = $exam_title;
    $_SESSION['current_index'] = 0; 
    $_SESSION['user_answers'] = []; 

    try {
        // Güvenli veritabanı sorgusu (Hem ID hem de olası metin eşleşmesi için)
        $stmt = $db->prepare("SELECT * FROM questions WHERE exam_type_id = :type_id OR exam_type_id = :type_str ORDER BY RAND() LIMIT 15");
        $stmt->execute([
            ':type_id' => $exam_type_id,
            ':type_str' => $exam_title
        ]);
        $fetched_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Eğer veritabanında bu kategori için soru yoksa, boş sınav patlamasın diye fallback ekleyelim
        if (count($fetched_questions) === 0) {
            $fetched_questions = [
                [
                    'id' => 0,
                    'lesson_id' => 'Örnek Ders',
                    'topic_id' => 'Örnek Konu',
                    'question_text' => "Bu kategori için henüz veritabanına soru eklenmemiştir. Lütfen admin panelinden " . $exam_title . " soruları ekleyiniz.",
                    'option_a' => "Seçenek A",
                    'option_b' => "Seçenek B",
                    'option_c' => "Seçenek C",
                    'option_d' => "Seçenek D",
                    'option_e' => "Seçenek E"
                ]
            ];
        }

        $_SESSION['quiz_questions'] = $fetched_questions;

    } catch (PDOException $e) {
        die("Veritabanı hatası: " . $e->getMessage());
    }
}

$questions = $_SESSION['quiz_questions'];
$current_index = $_SESSION['current_index'];
$total_questions = count($questions);
$current_question = $questions[$current_index];

// 3. BUTONLARA BASILDIĞINDA ÇALIŞACAK BACKEND (POST) MANTIĞI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Kullanıcı bir şık işaretlediyse hafızaya kaydet
    if (isset($_POST['selected_answer']) && $_POST['selected_answer'] !== '') {
        $_SESSION['user_answers'][$current_index] = $_POST['selected_answer'];
    }

    // Hangi butona basıldı?
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'next' && $current_index < $total_questions - 1) {
            $_SESSION['current_index']++;
        } elseif ($_POST['action'] === 'prev' && $current_index > 0) {
            $_SESSION['current_index']--;
        } elseif ($_POST['action'] === 'finish') {
            // Sınav bittiğinde session verilerini temizle ve index.php'ye dön
            unset($_SESSION['quiz_questions']);
            unset($_SESSION['quiz_type']);
            unset($_SESSION['current_index']);
            unset($_SESSION['user_answers']);
            
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const toastHTML = \`
                    <div id='cyberToast' class='position-fixed top-0 end-0 p-4' style='z-index: 9999; position: fixed; top: 20px; right: 20px; opacity: 1; transition: opacity 0.4s ease;'>
                        <div style='background: rgba(13, 15, 24, 0.95); backdrop-filter: blur(12px); border-left: 4px solid #00ff88; box-shadow: 0 0 25px rgba(0, 255, 136, 0.3); border-radius: 8px; padding: 1rem; color: white; display: flex; align-items: center;'>
                            <strong style='font-size: 1.1rem;'><span style='color:#00ff88; margin-right: 8px;'>✓</span> Sınav başarıyla tamamlandı!</strong>
                        </div>
                    </div>\`;
                    document.body.insertAdjacentHTML('beforeend', toastHTML);
                    setTimeout(() => { window.location.href='index.php'; }, 2500);
                });
            </script>";
            exit;
        }
    }

    // Sayfayı POST-Redirect-GET patternine uygun olarak yenile (Form tekrar gönderimini engeller)
    header("Location: quiz.php?type=" . urlencode($exam_title));
    exit;
}

// Kullanıcı bu soruyu daha önce işaretlediyse o şıkkı hafızadan geri getir
$saved_answer = isset($_SESSION['user_answers'][$current_index]) ? $_SESSION['user_answers'][$current_index] : '';

// Dinamik Progress Bar Yüzdesi Hesaplama
$progress_percentage = (($current_index + 1) / $total_questions) * 100;
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($exam_title); ?> Sınav Oturumu | Bilgihavuzu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            background-color: #07080d;
            font-family: 'Inter', sans-serif;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(0, 242, 254, 0.05), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(243, 85, 218, 0.05), transparent 25%);
        }

        .glass-panel {
            background: rgba(13, 15, 24, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(157, 78, 221, 0.15);
        }

        .neon-glow-cyan {
            box-shadow: 0 0 15px rgba(0, 242, 254, 0.15);
        }

        .option-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s ease-in-out;
        }

        .option-card:hover:not(.selected) {
            background: rgba(0, 242, 254, 0.04);
            border-color: rgba(0, 242, 254, 0.4);
            box-shadow: 0 0 10px rgba(0, 242, 254, 0.1);
        }

        /* Seçilen Şıkkın Alacağı Muazzam Neon Tarz */
        .option-card.selected {
            background: rgba(0, 242, 254, 0.1) !important;
            border-color: #00f2fe !important;
            box-shadow: 0 0 18px rgba(0, 242, 254, 0.2);
        }

        .option-card.selected .badge-circle {
            background-color: #00f2fe !important;
            color: #07080d !important;
            border-color: #00f2fe !important;
            box-shadow: 0 0 8px rgba(0, 242, 254, 0.4);
        }
    </style>
</head>

<body class="text-gray-200 min-h-screen flex flex-col justify-between">

    <form method="POST" action="" class="min-h-screen flex flex-col justify-between w-full">

        <header class="glass-panel sticky top-0 z-50 border-b border-purple-500/10 px-6 py-4">
            <div class="max-w-5xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <a href="index.php" class="text-gray-400 hover:text-white transition-colors text-sm font-medium mr-2">
                        ← Çıkış
                    </a>
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-purple-600/20 text-purple-400 rounded-full border border-purple-500/20 shadow-[0_0_10px_rgba(157,78,221,0.2)]">
                        <?php echo htmlspecialchars($exam_title); ?> MOCK EXAM
                    </span>
                    <h1 class="text-lg font-semibold text-white hidden md:block ml-2">
                        Soru <?php echo ($current_index + 1) . " / " . $total_questions; ?>
                    </h1>
                </div>

                <div class="flex items-center gap-2 bg-cyan-950/30 border border-cyan-500/20 px-4 py-2 rounded-xl neon-glow-cyan">
                    <span class="text-cyan-400 text-sm font-medium">Kalan Süre:</span>
                    <span id="timer" class="text-white font-mono font-bold tracking-wider">45:00</span>
                </div>
            </div>

            <!-- Dinamik Progress Bar -->
            <div class="absolute bottom-0 left-0 h-[2px] bg-gradient-to-r from-cyan-400 to-purple-500 transition-all duration-500 shadow-[0_0_8px_rgba(0,242,254,0.5)]"
                style="width: <?php echo $progress_percentage; ?>%;"></div>
        </header>

        <main class="max-w-3xl mx-auto w-full px-4 py-8 flex-grow flex flex-col justify-center">
            <div class="glass-panel rounded-3xl p-6 md:p-10 border border-purple-500/20 shadow-[0_15px_40px_rgba(0,0,0,0.5)] space-y-8 relative overflow-hidden">
                
                <!-- Hafif Arkaplan Glow'u -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/5 rounded-full blur-[80px] pointer-events-none"></div>

                <div class="space-y-4 relative z-10">
                    <p class="text-xs text-purple-400 uppercase tracking-widest font-bold drop-shadow-[0_0_5px_rgba(157,78,221,0.3)]">
                        DERS: <?php echo htmlspecialchars($current_question['lesson_id'] ?? 'N/A'); ?> &nbsp;•&nbsp; KONU: <?php echo htmlspecialchars($current_question['topic_id'] ?? 'N/A'); ?>
                    </p>
                    <h2 class="text-xl md:text-2xl text-white font-medium leading-relaxed tracking-wide">
                        <?php echo htmlspecialchars($current_question['question_text']); ?>
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-4 relative z-10" id="options-container">
                    <?php foreach (['a', 'b', 'c', 'd', 'e'] as $idx):
                        // Veritabanında e şıkkı olmayabilir veya boş olabilir, boşsa gösterme
                        if (empty($current_question['option_' . $idx]) && $idx === 'e') continue;
                        if (!isset($current_question['option_' . $idx])) continue;

                        // Seçili şıkkı işaretle
                        $isSelected = ($saved_answer === $idx) ? 'selected' : '';
                    ?>
                        <button type="button" data-option="<?php echo $idx; ?>"
                            class="option-card option-btn w-full text-left px-5 py-4 rounded-xl flex items-center gap-4 group cursor-pointer <?php echo $isSelected; ?>">
                            <div class="badge-circle w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-sm font-bold text-gray-400 group-hover:text-cyan-400 group-hover:border-cyan-400/40 transition-all uppercase shrink-0">
                                <?php echo $idx; ?>
                            </div>
                            <span class="text-gray-300 group-hover:text-white transition-colors duration-300 text-base leading-relaxed">
                                <?php echo htmlspecialchars($current_question['option_' . $idx]); ?>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="selected_answer" id="selected-answer-input"
                    value="<?php echo htmlspecialchars($saved_answer); ?>">
            </div>
        </main>

        <footer class="glass-panel border-t border-purple-500/10 px-6 py-5">
            <div class="max-w-3xl mx-auto flex justify-between items-center">

                <?php if ($current_index > 0): ?>
                    <button type="submit" name="action" value="prev"
                        class="px-6 py-3 rounded-xl border border-white/10 text-gray-300 hover:text-white hover:bg-white/5 transition-all text-sm font-bold cursor-pointer hover:border-cyan-500/30">
                        Önceki Soru
                    </button>
                <?php else: ?>
                    <div class="w-10"></div>
                <?php endif; ?>

                <?php if ($current_index < $total_questions - 1): ?>
                    <button type="submit" name="action" value="next"
                        class="px-8 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-400 hover:to-purple-500 text-white font-bold text-sm shadow-lg shadow-purple-500/20 hover:shadow-[0_0_20px_rgba(0,242,254,0.4)] transition-all cursor-pointer transform hover:scale-[1.02]">
                        Sonraki Soru
                    </button>
                <?php else: ?>
                    <button type="submit" name="action" value="finish"
                        class="px-8 py-3 rounded-xl bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-400 hover:to-pink-500 text-white font-bold text-sm shadow-lg shadow-red-500/20 hover:shadow-[0_0_20px_rgba(239,68,68,0.4)] transition-all cursor-pointer transform hover:scale-[1.02]">
                        Sınavı Bitir
                    </button>
                <?php endif; ?>

            </div>
        </footer>

    </form>

    <script>
        // 1. Şık Seçim Efekti ve Akıcı Geçişler
        const optionButtons = document.querySelectorAll('.option-btn');
        const hiddenInput = document.getElementById('selected-answer-input');

        optionButtons.forEach(button => {
            button.addEventListener('click', function () {
                optionButtons.forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');

                const choice = this.getAttribute('data-option');
                hiddenInput.value = choice;
            });
        });

        // 2. Geri Sayım Motoru (Session veya LocalStorage ile geliştirilebilir)
        let duration = 45 * 60;
        const timerDisplay = document.getElementById('timer');
        setInterval(() => {
            let minutes = Math.floor(duration / 60);
            let seconds = duration % 60;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            timerDisplay.textContent = minutes + ':' + seconds;
            if (--duration < 0) duration = 0;
        }, 1000);
    </script>
</body>

</html>