<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$stmt = $db->query("SELECT * FROM exam_types ORDER BY id DESC");
$records = $stmt->fetchAll();
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-white">Sınav Türleri</h3>
            <p class="text-muted mb-0">Sistemde kayıtlı ana sınav türleri</p>
        </div>
        <button class="btn btn-teal"><i class="fa-solid fa-plus me-2"></i> Yeni Ekle</button>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sınav Türü Adı</th>
                        <th>Durum</th>
                        <th>Oluşturulma Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><span class="badge bg-primary"><?= htmlspecialchars($r['name']) ?></span></td>
                        <td>
                            <?php if($r['status'] == 1): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
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
