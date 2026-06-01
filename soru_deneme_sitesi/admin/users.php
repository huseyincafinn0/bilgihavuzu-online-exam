<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
$records = $stmt->fetchAll();
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-white">Kullanıcılar</h3>
            <p class="text-muted mb-0">Sisteme kayıtlı tüm öğrenci ve yöneticiler</p>
        </div>
        <button class="btn btn-teal"><i class="fa-solid fa-plus me-2"></i> Yeni Kullanıcı</button>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ad Soyad</th>
                        <th>Kullanıcı Adı</th>
                        <th>E-Posta</th>
                        <th>Rol</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($r['full_name']) ?>&background=random" class="rounded-circle me-2" width="30">
                                <strong><?= htmlspecialchars($r['full_name']) ?></strong>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($r['username']) ?></td>
                        <td><?= htmlspecialchars($r['email']) ?></td>
                        <td>
                            <?php if($r['role'] == 'admin'): ?>
                                <span class="badge bg-danger">Yönetici</span>
                            <?php else: ?>
                                <span class="badge bg-info">Öğrenci</span>
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
