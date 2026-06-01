<?php
ob_start(); // PHP'de yönlendirme (header) hatalarını önlemek için tamponlamayı başlatıyoruz
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// 1. Gelen ID bilgisini kontrol et ve güvenli bir şekilde al
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: exam_sections.php?status=error");
    exit;
}

$id = intval($_GET['id']);

// 2. Bu ID'ye ait mevcut alt sınav verisini çek (Sütun adını 'name' yaptık)
$query = $db->prepare("SELECT * FROM exam_sections WHERE id = :id");
$query->execute(['id' => $id]);
$section = $query->fetch();

if (!$section) {
    header("Location: exam_sections.php?status=not_found");
    exit;
}

// 3. Form gönderildiğinde (Kaydet butonuna basıldığında) çalışacak güncelleme kodu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['section_name']);

    if (!empty($new_name)) {
        try {
            // Tablodaki sütun adı 'name' olduğu için güncellemeyi buna göre yapıyoruz
            $update = $db->prepare("UPDATE exam_sections SET name = :name WHERE id = :id");
            $result = $update->execute([
                'name' => $new_name,
                'id' => $id
            ]);

            if ($result) {
                header("Location: exam_sections.php?status=updated");
                exit;
            }
        } catch (PDOException $e) {
            die("Güncelleme sırasında bir hata oluştu: " . $e->getMessage());
        }
    }
}
?>

<div class="main-content">
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex align-items-center">
                        <h5 class="mb-0"><i class="fa-solid fa-pen-to-square me-2"></i> Alt Sınav Adı Düzenle</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="section_name" class="form-label fw-bold">Alt Sınav Adı</label>
                                <input type="text" class="form-control" id="section_name" name="section_name"
                                    value="<?= htmlspecialchars($section['name']); ?>" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="exam_sections.php" class="btn btn-secondary">İptal Et</a>
                                <button type="submit" class="btn btn-success">Değişiklikleri Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
ob_end_flush(); // Sayfa çıktısını tarayıcıya gönderiyoruz
?>