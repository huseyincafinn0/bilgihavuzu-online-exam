<?php
require_once 'header.php';

if (!isset($_GET['id'])) {
    die("Geçersiz sınav.");
}
$exam_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM exams WHERE id = ? AND user_id = ?");
$stmt->execute([$exam_id, $user_id]);
$exam = $stmt->fetch();

if (!$exam) {
    die("Sınav bulunamadı.");
}

$stmt = $db->prepare("
    SELECT eq.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.option_e, q.correct_answer 
    FROM exam_questions eq
    JOIN questions q ON eq.question_id = q.id
    WHERE eq.exam_id = ?
    ORDER BY eq.id ASC
");
$stmt->execute([$exam_id]);
$details = $stmt->fetchAll();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-8 text-center text-md-start">
        <h2 class="fw-bold text-white" style="font-size: 2.5rem;">Sınav Analizi</h2>
        <p class="text-white-50" style="font-size: 1.1rem;"><?= htmlspecialchars($exam['title']) ?></p>
    </div>
    <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
        <a href="print_exam.php?id=<?= $exam_id ?>" target="_blank" class="btn btn-primary btn-lg shadow-sm rounded-pill px-4">
            <i class="fa-solid fa-print me-2"></i>PDF Olarak Kaydet
        </a>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-3 col-6 mb-4">
        <div class="glass-card result-box">
            <i class="fa-solid fa-list-ol text-primary"></i>
            <h4><?= $exam['total_questions'] ?></h4>
            <span>Toplam Soru</span>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-4">
        <div class="glass-card result-box correct">
            <i class="fa-regular fa-circle-check"></i>
            <h4><?= $exam['correct_count'] ?></h4>
            <span>Doğru</span>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-4">
        <div class="glass-card result-box wrong">
            <i class="fa-regular fa-circle-xmark"></i>
            <h4><?= $exam['wrong_count'] ?></h4>
            <span>Yanlış</span>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-4">
        <div class="glass-card result-box empty">
            <i class="fa-regular fa-circle-dot"></i>
            <h4><?= $exam['empty_count'] ?></h4>
            <span>Boş</span>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="glass-card overflow-hidden">
            <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(255,255,255,0.02);">
                <h5 class="mb-0 fw-bold text-white"><i class="fa-solid fa-key text-primary me-2" style="color: #00f2fe !important;"></i>Cevap Anahtarı Detayları</h5>
                <?php 
                $color = 'danger';
                if($exam['score_percent'] >= 70) $color = 'success';
                elseif($exam['score_percent'] >= 40) $color = 'warning';
                ?>
                <div class="badge bg-<?= $color ?> px-4 py-2 fs-6 rounded-pill">Başarı: %<?= number_format($exam['score_percent'], 2) ?></div>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="small text-uppercase" style="background: rgba(0, 242, 254, 0.05); color: #00f2fe; border-bottom: 1px solid rgba(0, 242, 254, 0.2);">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Doğru Cevap</th>
                                <th>Senin Cevabın</th>
                                <th class="pe-4">Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($details as $index => $d): ?>
                                <?php 
                                    $bg_class = '';
                                    $icon = '';
                                    $text_color = '';
                                    if ($d['user_answer'] === $d['correct_answer']) {
                                        $bg_class = 'bg-success';
                                        $icon = '<i class="fa-solid fa-check text-success"></i>';
                                        $text_color = 'text-success';
                                        $status_text = 'Doğru';
                                    } elseif (empty($d['user_answer'])) {
                                        $bg_class = 'bg-secondary';
                                        $icon = '<i class="fa-solid fa-minus text-secondary"></i>';
                                        $text_color = 'text-secondary';
                                        $status_text = 'Boş';
                                    } else {
                                        $bg_class = 'bg-danger';
                                        $icon = '<i class="fa-solid fa-xmark text-danger"></i>';
                                        $text_color = 'text-danger';
                                        $status_text = 'Yanlış';
                                    }
                                ?>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <td class="ps-4 fw-bold text-white-50">Soru <?= $index + 1 ?></td>
                                    <td><span class="badge" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); font-size: 1rem; padding: 8px 12px;"><?= htmlspecialchars($d['correct_answer']) ?></span></td>
                                    <td>
                                        <span class="badge <?= $bg_class ?> px-3 py-2 fs-6">
                                            <?= $d['user_answer'] ? htmlspecialchars($d['user_answer']) : '-' ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 fw-bold <?= $text_color ?>"><?= $icon ?> <?= $status_text ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-5 mb-5">
    <a href="dashboard.php" class="btn btn-outline-primary btn-lg rounded-pill px-4 me-3"><i class="fa-solid fa-house me-2"></i>Dashboard'a Dön</a>
    <a href="create_exam.php" class="btn btn-primary btn-lg rounded-pill px-4"><i class="fa-solid fa-plus me-2"></i>Yeni Deneme Oluştur</a>
</div>

<?php require_once 'footer.php'; ?>
