<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$stmt = $db->query("
    SELECT t.*, l.name as lesson_name 
    FROM topics t 
    JOIN lessons l ON t.lesson_id = l.id 
    ORDER BY t.id DESC
");
$records = $stmt->fetchAll();
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-white">Konular</h3>
            <p class="text-muted mb-0">Derslere ait detaylı konu başlıkları</p>
        </div>
        <button class="btn btn-teal"><i class="fa-solid fa-plus me-2"></i> Yeni Ekle</button>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bağlı Olduğu Ders</th>
                        <th>Konu Adı</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><span class="badge" style="background-color: var(--teal);"><?= htmlspecialchars($r['lesson_name']) ?></span></td>
                        <td><strong class="text-navy"><?= htmlspecialchars($r['name']) ?></strong></td>
                        <td>
                            <?php if($r['status'] == 1): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Pasif</span>
                            <?php endif; ?>
                        </td>
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
