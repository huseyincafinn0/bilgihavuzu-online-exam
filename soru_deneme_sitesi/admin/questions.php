<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$stmt = $db->query("
    SELECT q.*, et.name as exam_type, es.name as section_name, l.name as lesson_name, t.name as topic_name 
    FROM questions q
    JOIN exam_types et ON q.exam_type_id = et.id
    JOIN exam_sections es ON q.exam_section_id = es.id
    JOIN lessons l ON q.lesson_id = l.id
    JOIN topics t ON q.topic_id = t.id
    ORDER BY q.id DESC LIMIT 100
");
$questions = $stmt->fetchAll();
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-white">Sorular</h3>
            <p class="text-muted mb-0">Sistemde kayıtlı sorular listesi</p>
        </div>
        <a href="question_add.php" class="btn btn-teal"><i class="fa-solid fa-plus me-2"></i> Yeni Soru Ekle</a>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sınav Türü</th>
                        <th>Alt Sınav</th>
                        <th>Ders</th>
                        <th>Konu</th>
                        <th>Zorluk</th>
                        <th>Doğru Cevap</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($questions as $q): ?>
                    <tr>
                        <td><?= $q['id'] ?></td>
                        <td><span class="badge bg-primary"><?= htmlspecialchars($q['exam_type']) ?></span></td>
                        <td><?= htmlspecialchars($q['section_name']) ?></td>
                        <td><?= htmlspecialchars($q['lesson_name']) ?></td>
                        <td><?= htmlspecialchars($q['topic_name']) ?></td>
                        <td>
                            <?php 
                            $badge = $q['difficulty'] == 'Zor' ? 'danger' : ($q['difficulty'] == 'Kolay' ? 'success' : 'warning');
                            ?>
                            <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($q['difficulty']) ?></span>
                        </td>
                        <td><strong class="text-teal"><?= htmlspecialchars($q['correct_answer']) ?></strong></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
