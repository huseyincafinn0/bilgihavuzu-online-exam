<?php
require_once 'header.php';

if (!isset($_GET['id'])) {
    die("Geçersiz sınav.");
}
$exam_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Sınavın kullanıcıya ait olup olmadığını kontrol et ve skor var mı bak
$stmt = $db->prepare("SELECT * FROM exams WHERE id = ? AND user_id = ?");
$stmt->execute([$exam_id, $user_id]);
$exam = $stmt->fetch();

if (!$exam) {
    die("Sınav bulunamadı.");
}
if ($exam['score_percent'] > 0) {
    // Sınav zaten çözülmüş, sonuca git
    header("Location: result.php?id=" . $exam_id);
    exit();
}

// Soruları getir
$stmt = $db->prepare("
    SELECT eq.id as eq_id, q.* 
    FROM exam_questions eq
    JOIN questions q ON eq.question_id = q.id
    WHERE eq.exam_id = ?
    ORDER BY eq.id ASC
");
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll();

if (count($questions) == 0) {
    die("Sınavda soru bulunmuyor.");
}
?>

<style>
/* Cyberpunk Option Styling */
.option-input { display: none; }
.option-label:hover {
    border-color: #00f2fe !important;
    background: rgba(0, 242, 254, 0.05) !important;
}
.option-label:hover .opt-badge {
    color: #00f2fe !important;
    border-color: #00f2fe !important;
    box-shadow: 0 0 10px rgba(0, 242, 254, 0.5);
}
.option-input:checked + .option-label {
    border-color: #00f2fe !important;
    background: rgba(0, 242, 254, 0.1) !important;
    color: #ffffff !important;
    box-shadow: 0 0 15px rgba(0, 242, 254, 0.2);
}
.option-input:checked + .option-label .opt-badge {
    color: #07080d !important;
    background: #00f2fe !important;
    border-color: #00f2fe !important;
    box-shadow: 0 0 15px rgba(0, 242, 254, 0.8);
}
</style>

<!-- SKELETON LOADER -->
<div id="skeletonLoader" class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between mb-3">
            <div class="skeleton-box" style="width: 40%; height: 30px;"></div>
            <div class="skeleton-box" style="width: 20%; height: 30px; border-radius: 50px;"></div>
        </div>
        <div class="skeleton-box mb-4 w-100" style="height: 10px;"></div>
        <div class="skeleton-box w-100" style="height: 500px; border-radius: 12px;"></div>
    </div>
</div>

<div id="actualContent" style="display:none; opacity: 0; transition: opacity 0.4s ease;">
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 fw-bold text-white"><i class="fa-solid fa-pen-nib me-2" style="color: #00f2fe;"></i><?= htmlspecialchars($exam['title']) ?></h4>
            <div class="badge rounded-pill px-3 py-2 fs-6" id="timer" style="background: rgba(243, 85, 218, 0.2); border: 1px solid #f355da; color: #f355da;">00:00</div>
        </div>
        
        <div class="glass-card mb-4 overflow-hidden">
            <div class="exam-progress">
                <div class="exam-progress-bar" id="progressBar" style="width: 0%;"></div>
            </div>
        </div>

        <form action="finish_exam.php" method="POST" id="solveForm">
            <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
            
            <div id="questions_container" class="position-relative" style="min-height: 400px;">
                <?php foreach($questions as $index => $q): ?>
                    <div class="glass-card question-card position-absolute w-100" id="q_<?= $index ?>" style="top: 0; left: 0; <?= $index == 0 ? 'opacity: 1; z-index: 2;' : 'opacity: 0; z-index: 1; pointer-events: none;' ?> transition: opacity 0.3s ease;">
                        
                        <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.02);">
                            <h5 class="mb-0 fw-bold" style="color: #00f2fe;">Soru <?= $index + 1 ?> <span class="text-white-50 fw-normal fs-6">/ <?= count($questions) ?></span></h5>
                            <span class="badge rounded-pill px-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);"><?= htmlspecialchars($q['difficulty']) ?></span>
                        </div>
                        
                        <div class="p-4 p-md-5">
                            <div class="question-text mb-5"><?= nl2br(htmlspecialchars($q['question_text'])) ?></div>
                            
                            <div class="grid grid-cols-1 gap-4 my-6" id="options-container" style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin: 1.5rem 0;">
                                <?php foreach(['A', 'B', 'C', 'D', 'E'] as $opt): 
                                    $lower_opt = strtolower($opt);
                                    
                                    // Bulletproof PHP Database Mapping (With Fallbacks)
                                    $option_text = $q[$lower_opt . '_sikki'] ?? $q['option_' . $lower_opt] ?? $q[$opt] ?? '';
                                    
                                    if(empty(trim($option_text))) continue;
                                ?>
                                    <div class="position-relative">
                                        <input class="option-input" type="radio" name="answers[<?= $q['eq_id'] ?>]" id="opt_<?= $q['eq_id'] ?>_<?= $opt ?>" value="<?= $opt ?>">
                                        <label class="option-label d-flex align-items-center p-3 rounded" for="opt_<?= $q['eq_id'] ?>_<?= $opt ?>" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08); color: #e2e8f0; cursor: pointer; transition: all 0.2s ease;">
                                            <span class="badge opt-badge me-3 fs-6 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: rgba(255,255,255,0.05); color: #a0aec0; border: 1px solid rgba(255,255,255,0.1); transition: all 0.2s ease; border-radius: 8px;"><?= $opt ?></span>
                                            <span style="font-size: 1.05rem;"><?= htmlspecialchars($option_text) ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="p-4 d-flex justify-content-between" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.2);">
                            <button type="button" class="btn px-4 btn-prev rounded-pill text-white" style="border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05);" <?= $index == 0 ? 'disabled' : '' ?>><i class="fa-solid fa-arrow-left me-2"></i>Önceki</button>
                            
                            <?php if($index == count($questions) - 1): ?>
                                <button type="submit" class="btn px-4 rounded-pill fw-bold" style="background: linear-gradient(135deg, #00ff88, #00b359); color: #07080d; border: none; box-shadow: 0 0 15px rgba(0,255,136,0.4);" onclick="return confirm('Sınavı bitirip sonuçları görmek istediğine emin misin?');"><i class="fa-solid fa-flag-checkered me-2"></i>Sınavı Bitir</button>
                            <?php else: ?>
                                <button type="button" class="btn px-4 btn-next rounded-pill fw-bold" style="background: linear-gradient(135deg, #00f2fe, #4facfe); color: #fff; border: none; box-shadow: 0 0 15px rgba(0,242,254,0.4);">Sonraki <i class="fa-solid fa-arrow-right ms-2"></i></button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    let currentIndex = 0;
    const totalQuestions = <?= count($questions) ?>;
    const progressBar = document.getElementById('progressBar');
    
    function updateProgress() {
        const percent = ((currentIndex) / totalQuestions) * 100;
        progressBar.style.width = percent + '%';
        if(currentIndex === totalQuestions - 1) progressBar.style.width = '100%';
    }
    
    function showQuestion(index) {
        document.querySelectorAll('.question-card').forEach((card, i) => {
            if(i === index) {
                card.style.opacity = '1';
                card.style.zIndex = '2';
                card.style.pointerEvents = 'auto';
            } else {
                card.style.opacity = '0';
                card.style.zIndex = '1';
                card.style.pointerEvents = 'none';
            }
        });
        updateProgress();
    }

    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentIndex < totalQuestions - 1) {
                currentIndex++;
                showQuestion(currentIndex);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });

    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                showQuestion(currentIndex);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });

    // Auto-select next when option clicked (Optional smooth UX)
    document.querySelectorAll('.option-input').forEach(input => {
        input.addEventListener('change', () => {
            setTimeout(() => {
                if (currentIndex < totalQuestions - 1) {
                    currentIndex++;
                    showQuestion(currentIndex);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }, 400); // Wait 400ms to show the selected state
        });
    });

    updateProgress();
    
    // Skeleton Fade-in Logic
    setTimeout(() => {
        document.getElementById('skeletonLoader').style.display = 'none';
        const actualContent = document.getElementById('actualContent');
        actualContent.style.display = 'block';
        setTimeout(() => { actualContent.style.opacity = '1'; }, 50);
    }, 400);
});
</script>
</div>

<?php require_once 'footer.php'; ?>
