<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$stmt = $db->query("
    SELECT es.*, et.name as exam_type_name 
    FROM exam_sections es 
    JOIN exam_types et ON es.exam_type_id = et.id 
    ORDER BY es.id DESC
");
$records = $stmt->fetchAll();
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-white">Alt Sınavlar</h3>
            <p class="text-muted mb-0">Sistemde kayıtlı alt sınavlar (bölümler)</p>
        </div>
        <button class="btn btn-teal"><i class="fa-solid fa-plus me-2"></i> Yeni Ekle</button>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> Alt sınav başarıyla silindi.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'has_relation'): ?>
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-exclamation-triangle me-2"></i> <strong>Silinemedi!</strong> Bu alt sınava bağlı dersler
                veya sorular bulunmaktadır. Önce bağlı verileri silmelisiniz.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'error'): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-xmark me-2"></i> Silme işlemi sırasında bir hata oluştu.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bağlı Olduğu Sınav Türü</th>
                        <th>Alt Sınav Adı</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><span class="badge bg-primary"><?= htmlspecialchars($r['exam_type_name']) ?></span></td>
                            <td><strong class="text-navy"><?= htmlspecialchars($r['name']) ?></strong></td>
                            <td>
                                <?php if ($r['status'] == 1): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Pasif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="exam_section_edit.php?id=<?= $r['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="exam_section_delete.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Bu alt sınavı silmek istediğinize emin misiniz? Bağlı tüm veriler etkilenebilir.');"><i
                                        class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>