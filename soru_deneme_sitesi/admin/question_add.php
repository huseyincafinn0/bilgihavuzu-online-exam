<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$message = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_question'])) {
    $exam_type_id = $_POST['exam_type_id'] ?? 0;
    $exam_section_id = $_POST['exam_section_id'] ?? 0;
    $lesson_id = $_POST['lesson_id'] ?? 0;
    $topic_id = $_POST['topic_id'] ?? 0;
    $question_text = $_POST['question_text'] ?? '';
    $option_a = $_POST['option_a'] ?? '';
    $option_b = $_POST['option_b'] ?? '';
    $option_c = $_POST['option_c'] ?? '';
    $option_d = $_POST['option_d'] ?? '';
    $option_e = $_POST['option_e'] ?? '';
    $correct_answer = $_POST['correct_answer'] ?? '';
    $difficulty = $_POST['difficulty'] ?? 'Orta';

    if ($exam_type_id && $exam_section_id && $lesson_id && $topic_id && !empty($question_text) && !empty($correct_answer)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO questions 
                (exam_type_id, exam_section_id, lesson_id, topic_id, question_text, option_a, option_b, option_c, option_d, option_e, correct_answer, difficulty) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $exam_type_id, $exam_section_id, $lesson_id, $topic_id, 
                $question_text, $option_a, $option_b, $option_c, $option_d, $option_e, 
                $correct_answer, $difficulty
            ]);
            $message = 'Soru başarıyla eklendi!';
            $msg_type = 'success';
        } catch(PDOException $e) {
            $message = 'Soru eklenirken bir veritabanı hatası oluştu: ' . $e->getMessage();
            $msg_type = 'danger';
        }
    } else {
        $message = 'Lütfen tüm zorunlu alanları (Sınav Türü, Alt Sınav, Ders, Konu, Soru Metni ve Doğru Cevap) doldurunuz.';
        $msg_type = 'warning';
    }
}

// Tüm Sınav Türlerini çek
$types = $db->query("SELECT id, name FROM exam_types WHERE status = 1")->fetchAll();
?>

<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-white">Soru Ekle</h3>
            <p class="text-muted mb-0">Soru Havuzuna Yeni Soru Ekle</p>
        </div>
        <a href="questions.php" class="btn btn-navy"><i class="fa-solid fa-arrow-left me-2"></i> Sorulara Dön</a>
    </div>

    <div class="admin-card">
        <h5 class="card-title">Soru Detayları Formu</h5>
        
        <?php if($message): ?>
            <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sınav Türü <span class="text-danger">*</span></label>
                    <select name="exam_type_id" id="exam_type" class="form-select" required>
                        <option value="">Sınav Türü Seçiniz...</option>
                        <?php foreach($types as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Alt Sınav <span class="text-danger">*</span></label>
                    <select name="exam_section_id" id="exam_section" class="form-select" required disabled>
                        <option value="">Önce Sınav Türü Seçiniz...</option>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ders <span class="text-danger">*</span></label>
                    <select name="lesson_id" id="lesson" class="form-select" required disabled>
                        <option value="">Önce Alt Sınav Seçiniz...</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-teal fw-bold">Konu <span class="text-danger">*</span></label>
                    <select name="topic_id" id="topic" class="form-select border-teal" required disabled>
                        <option value="">Önce Ders Seçiniz...</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Soru Metni <span class="text-danger">*</span></label>
                <textarea name="question_text" class="form-control" rows="5" placeholder="Soru metnini buraya giriniz..." required></textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">A Şıkkı</label>
                    <input type="text" name="option_a" class="form-control" placeholder="A Şıkkı İçeriği" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">B Şıkkı</label>
                    <input type="text" name="option_b" class="form-control" placeholder="B Şıkkı İçeriği" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">C Şıkkı</label>
                    <input type="text" name="option_c" class="form-control" placeholder="C Şıkkı İçeriği" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">D Şıkkı</label>
                    <input type="text" name="option_d" class="form-control" placeholder="D Şıkkı İçeriği" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">E Şıkkı</label>
                    <input type="text" name="option_e" class="form-control" placeholder="E Şıkkı İçeriği">
                </div>
            </div>

            <div class="row mb-4 p-3 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label text-success"><i class="fa-solid fa-check"></i> Doğru Cevap <span class="text-danger">*</span></label>
                    <select name="correct_answer" class="form-select" required>
                        <option value="">Seçiniz...</option>
                        <option value="A">A Şıkkı</option>
                        <option value="B">B Şıkkı</option>
                        <option value="C">C Şıkkı</option>
                        <option value="D">D Şıkkı</option>
                        <option value="E">E Şıkkı</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Zorluk Seviyesi</label>
                    <select name="difficulty" class="form-select">
                        <option value="Kolay">Kolay</option>
                        <option value="Orta" selected>Orta</option>
                        <option value="Zor">Zor</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="reset" class="btn btn-light border"><i class="fa-solid fa-eraser me-1"></i> Formu Temizle</button>
                <button type="submit" name="add_question" class="btn btn-teal"><i class="fa-solid fa-floppy-disk me-1"></i> Soruyu Kaydet</button>
            </div>
        </form>
    </div>
</div>

<style>
.border-teal { border-color: var(--teal) !important; box-shadow: 0 0 0 0.2rem rgba(20, 184, 166, 0.1); }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const examTypeSelect = document.getElementById('exam_type');
    const examSectionSelect = document.getElementById('exam_section');
    const lessonSelect = document.getElementById('lesson');
    const topicSelect = document.getElementById('topic');

    async function fetchData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network error');
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            return null;
        }
    }

    // Sınav Türü Değişimi
    examTypeSelect.addEventListener('change', async (e) => {
        const typeId = e.target.value;
        
        examSectionSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        examSectionSelect.disabled = true;
        lessonSelect.innerHTML = '<option value="">Önce Alt Sınav Seçiniz...</option>';
        lessonSelect.disabled = true;
        topicSelect.innerHTML = '<option value="">Önce Ders Seçiniz...</option>';
        topicSelect.disabled = true;
        topicSelect.classList.remove('border-teal');

        if (typeId) {
            const data = await fetchData(`../get_sections.php?exam_type_id=${typeId}`);
            examSectionSelect.innerHTML = '<option value="">Seçiniz...</option>';
            if (data && data.length > 0) {
                data.forEach(sec => {
                    const opt = document.createElement('option');
                    opt.value = sec.id;
                    opt.textContent = sec.name;
                    examSectionSelect.appendChild(opt);
                });
                examSectionSelect.disabled = false;
            } else {
                examSectionSelect.innerHTML = '<option value="">Alt Sınav Bulunamadı</option>';
            }
        } else {
            examSectionSelect.innerHTML = '<option value="">Önce Sınav Türü Seçiniz...</option>';
        }
    });

    // Alt Sınav Değişimi
    examSectionSelect.addEventListener('change', async (e) => {
        const sectionId = e.target.value;
        
        lessonSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        lessonSelect.disabled = true;
        topicSelect.innerHTML = '<option value="">Önce Ders Seçiniz...</option>';
        topicSelect.disabled = true;
        topicSelect.classList.remove('border-teal');

        if (sectionId) {
            const data = await fetchData(`../get_lessons.php?section_id=${sectionId}`);
            lessonSelect.innerHTML = '<option value="">Seçiniz...</option>';
            if (data && data.length > 0) {
                data.forEach(les => {
                    const opt = document.createElement('option');
                    opt.value = les.id;
                    opt.textContent = les.name;
                    lessonSelect.appendChild(opt);
                });
                lessonSelect.disabled = false;
            } else {
                lessonSelect.innerHTML = '<option value="">Ders Bulunamadı</option>';
            }
        } else {
            lessonSelect.innerHTML = '<option value="">Önce Alt Sınav Seçiniz...</option>';
        }
    });

    // Ders Değişimi
    lessonSelect.addEventListener('change', async (e) => {
        const lessonId = e.target.value;
        
        topicSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        topicSelect.disabled = true;
        topicSelect.classList.remove('border-teal');

        if (lessonId) {
            const data = await fetchData(`../get_topics.php?lesson_id=${lessonId}`);
            topicSelect.innerHTML = '<option value="">Seçiniz...</option>';
            if (data && data.length > 0) {
                data.forEach(top => {
                    const opt = document.createElement('option');
                    opt.value = top.id;
                    opt.textContent = top.name;
                    topicSelect.appendChild(opt);
                });
                topicSelect.disabled = false;
                topicSelect.classList.add('border-teal'); // Highlight success state
            } else {
                topicSelect.innerHTML = '<option value="">Ders için konu girilmemiş.</option>';
            }
        } else {
            topicSelect.innerHTML = '<option value="">Önce Ders Seçiniz...</option>';
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
