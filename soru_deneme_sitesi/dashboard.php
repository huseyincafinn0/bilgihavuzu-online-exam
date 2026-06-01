<?php
require_once 'header.php';

$user_id = $_SESSION['user_id'];

// Toplam çözdüğü deneme
$stmt = $db->prepare("SELECT COUNT(*) FROM exams WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_exams = $stmt->fetchColumn();

// Toplam çözdüğü soru sayısı
$stmt = $db->prepare("SELECT SUM(total_questions) FROM exams WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_questions = $stmt->fetchColumn() ?: 0;

// Son 5 deneme
$stmt = $db->prepare("SELECT e.*, et.name as exam_type, es.name as section_name, l.name as lesson_name 
                      FROM exams e
                      LEFT JOIN exam_types et ON e.exam_type_id = et.id
                      LEFT JOIN exam_sections es ON e.exam_section_id = es.id
                      LEFT JOIN lessons l ON e.lesson_id = l.id
                      WHERE e.user_id = ? ORDER BY e.created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_exams = $stmt->fetchAll();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold text-white" style="font-size: 2.5rem;">Hoş Geldin, <span class="text-primary" style="color: #00f2fe !important; text-shadow: 0 0 10px rgba(0,242,254,0.5);"><?= htmlspecialchars($_SESSION['full_name']) ?></span>!</h2>
        <p class="text-white-50" style="font-size: 1.1rem;">Buradan güncel eğitim istatistiklerini görebilir ve kendini test etmeye başlayabilirsin.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="create_exam.php" class="btn btn-primary btn-lg shadow-sm rounded-pill px-4"><i class="fa-solid fa-plus me-2"></i>Yeni Deneme</a>
    </div>
</div>

<!-- SKELETON LOADER -->
<div id="skeletonLoader">
    <div class="row mb-5">
        <div class="col-md-6 mb-4"><div class="skeleton-box w-100" style="height: 180px;"></div></div>
        <div class="col-md-6 mb-4"><div class="skeleton-box w-100" style="height: 180px;"></div></div>
    </div>
    <div class="row">
        <div class="col-12"><div class="skeleton-box w-100" style="height: 300px;"></div></div>
    </div>
</div>

<div id="actualContent" style="display:none; opacity: 0; transition: opacity 0.4s ease;">
<div class="row mb-5">
    <div class="col-md-6 mb-4">
        <div class="glass-card p-4 text-center">
            <div class="stat-circle shadow-lg" style="background: linear-gradient(135deg, #00f2fe, #4facfe); border: 2px solid rgba(0,242,254,0.5); box-shadow: 0 0 20px rgba(0,242,254,0.3) !important;">
                <i class="fa-solid fa-file-signature mb-1" style="font-size: 20px;"></i>
                <h3><?= $total_exams ?></h3>
            </div>
            <h5 class="fw-bold text-white mt-3">Çözülen Deneme</h5>
            <p class="text-white-50 small mb-0">Bugüne kadar tamamladığın sınav sayısı</p>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="glass-card p-4 text-center">
            <div class="stat-circle shadow-lg" style="background: linear-gradient(135deg, #f355da, #9d4edd); border: 2px solid rgba(243,85,218,0.5); box-shadow: 0 0 20px rgba(243,85,218,0.3) !important;">
                <i class="fa-solid fa-layer-group mb-1" style="font-size: 20px;"></i>
                <h3><?= $total_questions ?></h3>
            </div>
            <h5 class="fw-bold text-white mt-3">Çözülen Soru</h5>
            <p class="text-white-50 small mb-0">Bugüne kadar karşılaştığın toplam soru</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="glass-card overflow-hidden">
            <div class="p-4" style="border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(255,255,255,0.02);">
                <h5 class="mb-0 fw-bold text-white"><i class="fa-solid fa-clock-rotate-left me-2" style="color:#00f2fe;"></i>Son Denemelerin</h5>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead class="small text-uppercase" style="background: rgba(0, 242, 254, 0.05); color: #00f2fe; border-bottom: 1px solid rgba(0, 242, 254, 0.2);">
                            <tr>
                                <th class="ps-4">Tarih</th>
                                <th>Sınav/Ders</th>
                                <th>Soru</th>
                                <th>Analiz</th>
                                <th>Başarı</th>
                                <th class="pe-4 text-end">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($recent_exams) > 0): ?>
                                <?php foreach($recent_exams as $ex): ?>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                        <td class="ps-4 text-white-50"><i class="fa-regular fa-calendar me-2"></i><?= date('d.m.Y H:i', strtotime($ex['created_at'])) ?></td>
                                        <td class="fw-semibold text-white"><?= htmlspecialchars($ex['exam_type'] . ' - ' . $ex['lesson_name']) ?></td>
                                        <td><span class="badge rounded-pill px-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);"><?= $ex['total_questions'] ?> Soru</span></td>
                                        <td>
                                            <span class="fw-bold" style="color: #00ff88;"><i class="fa-solid fa-check me-1"></i><?= $ex['correct_count'] ?></span> &nbsp;
                                            <span class="fw-bold" style="color: #ff0055;"><i class="fa-solid fa-xmark me-1"></i><?= $ex['wrong_count'] ?></span> &nbsp;
                                            <span class="text-white-50 fw-bold"><i class="fa-solid fa-minus me-1"></i><?= $ex['empty_count'] ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                            $color = 'danger';
                                            if($ex['score_percent'] >= 70) $color = 'success';
                                            elseif($ex['score_percent'] >= 40) $color = 'warning';
                                            ?>
                                            <span class="badge bg-<?= $color ?> rounded-pill px-3 py-2">%<?= $ex['score_percent'] ?></span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="result.php?id=<?= $ex['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Görüntüle <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-5">
                                        <i class="fa-regular fa-folder-open mb-3" style="font-size: 40px; opacity: 0.5;"></i><br>
                                        Henüz hiç deneme çözmediniz.<br>
                                        <a href="create_exam.php" class="btn btn-sm btn-primary mt-3">Hemen Başla</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        document.getElementById('skeletonLoader').style.display = 'none';
        const actualContent = document.getElementById('actualContent');
        actualContent.style.display = 'block';
        setTimeout(() => { actualContent.style.opacity = '1'; }, 50);
    }, 400);
});
</script>

<?php require_once 'footer.php'; ?>
