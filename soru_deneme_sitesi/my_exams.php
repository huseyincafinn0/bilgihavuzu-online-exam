<?php
require_once 'header.php';

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT e.*, et.name as exam_type, es.name as section_name, l.name as lesson_name 
                      FROM exams e
                      LEFT JOIN exam_types et ON e.exam_type_id = et.id
                      LEFT JOIN exam_sections es ON e.exam_section_id = es.id
                      LEFT JOIN lessons l ON e.lesson_id = l.id
                      WHERE e.user_id = ? ORDER BY e.created_at DESC");
$stmt->execute([$user_id]);
$exams = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Geçmiş Denemelerim</h2>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tarih</th>
                                <th>Başlık</th>
                                <th>Sınav/Ders</th>
                                <th>Soru Sayısı</th>
                                <th>Doğru/Yanlış/Boş</th>
                                <th>Başarı Yüzdesi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($exams) > 0): ?>
                                <?php foreach($exams as $ex): ?>
                                    <tr>
                                        <td><?= date('d.m.Y H:i', strtotime($ex['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($ex['title']) ?></td>
                                        <td><?= htmlspecialchars($ex['exam_type'] . ' > ' . $ex['section_name'] . ' > ' . $ex['lesson_name']) ?></td>
                                        <td><?= $ex['total_questions'] ?></td>
                                        <td>
                                            <span class="text-success fw-bold"><?= $ex['correct_count'] ?></span> / 
                                            <span class="text-danger fw-bold"><?= $ex['wrong_count'] ?></span> /
                                            <span class="text-secondary fw-bold"><?= $ex['empty_count'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $ex['score_percent'] >= 50 ? 'success' : 'warning text-dark' ?>">
                                                %<?= $ex['score_percent'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="result.php?id=<?= $ex['id'] ?>" class="btn btn-sm btn-outline-primary">Detay</a>
                                            <a href="print_exam.php?id=<?= $ex['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Yazdır</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center text-muted p-4">Henüz hiç deneme çözmediniz.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
