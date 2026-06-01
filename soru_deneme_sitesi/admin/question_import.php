<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$message = '';
$msg_type = '';
$error_logs = [];
$success_count = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];

    if ($file['error'] == 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) == 'csv') {

            // Read CSV
            if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) {
                // Remove BOM if present
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle); // No BOM, rewind to start
                }

                $header = fgetcsv($handle, 1000, ","); // Skip header

                // Caches to prevent repeated DB queries
                $cache_types = [];
                $cache_sections = [];
                $cache_lessons = [];
                $cache_topics = [];

                $row_number = 1; // 1 was header

                $db->beginTransaction();

                try {
                    $insert_stmt = $db->prepare("
                        INSERT INTO questions 
                        (exam_type_id, exam_section_id, lesson_id, topic_id, question_text, option_a, option_b, option_c, option_d, option_e, correct_answer, difficulty) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");

                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        $row_number++;

                        if ($row_number == 1)
                            continue;

                        // Satır boşsa atla
                        if (count(array_filter($data)) == 0)
                            continue;

                        if (count($data) < 12) {
                            $error_logs[] = "Satır $row_number: Eksik sütun verisi (Tüm sütunlar dolu olmalıdır).";
                            continue;

                        }

                        $type_name = trim($data[0]);
                        $section_name = trim($data[1]);
                        $lesson_name = trim($data[2]);
                        $topic_name = trim($data[3]);
                        $q_text = trim($data[4]);
                        $opt_a = trim($data[5]);
                        $opt_b = trim($data[6]);
                        $opt_c = trim($data[7]);
                        $opt_d = trim($data[8]);
                        $opt_e = trim($data[9]);
                        $correct = strtoupper(trim($data[10]));
                        $difficulty = ucfirst(strtolower(trim($data[11])));

                        // 1. Sınav Türü ID Bul
                        if (!isset($cache_types[$type_name])) {
                            $stmt = $db->prepare("SELECT id FROM exam_types WHERE name = ?");
                            $stmt->execute([$type_name]);
                            $tid = $stmt->fetchColumn();
                            if ($tid)
                                $cache_types[$type_name] = $tid;
                            else {
                                $error_logs[] = "Satır $row_number: Sınav türü bulunamadı ('$type_name').";
                                continue;
                            }
                        }
                        $type_id = $cache_types[$type_name];

                        // 2. Alt Sınav ID Bul
                        $sec_cache_key = $type_id . '_' . $section_name;
                        if (!isset($cache_sections[$sec_cache_key])) {
                            $stmt = $db->prepare("SELECT id FROM exam_sections WHERE exam_type_id = ? AND name = ?");
                            $stmt->execute([$type_id, $section_name]);
                            $sid = $stmt->fetchColumn();
                            if ($sid)
                                $cache_sections[$sec_cache_key] = $sid;
                            else {
                                $error_logs[] = "Satır $row_number: Alt sınav bulunamadı ('$section_name').";
                                continue;
                            }
                        }
                        $section_id = $cache_sections[$sec_cache_key];

                        // 3. Ders ID Bul
                        $les_cache_key = $section_id . '_' . $lesson_name;
                        if (!isset($cache_lessons[$les_cache_key])) {
                            $stmt = $db->prepare("SELECT id FROM lessons WHERE exam_section_id = ? AND name = ?");
                            $stmt->execute([$section_id, $lesson_name]);
                            $lid = $stmt->fetchColumn();
                            if ($lid) {
                                $cache_lessons[$les_cache_key] = $lid;
                            } else {
                                $insert_lesson = $db->prepare("INSERT INTO lessons (exam_section_id, name) VALUES (?, ?)");
                                $insert_lesson->execute([$section_id, $lesson_name]);
                                $cache_lessons[$les_cache_key] = $db->lastInsertId();
                            }
                        }
                        $lesson_id = $cache_lessons[$les_cache_key];

                        // 4. Konu ID Bul
                        $top_cache_key = $lesson_id . '_' . $topic_name;
                        if (!isset($cache_topics[$top_cache_key])) {
                            $stmt = $db->prepare("SELECT id FROM topics WHERE lesson_id = ? AND name = ?");
                            $stmt->execute([$lesson_id, $topic_name]);
                            $topid = $stmt->fetchColumn();
                            if ($topid) {
                                $cache_topics[$top_cache_key] = $topid;
                            } else {
                                $insert_topic = $db->prepare("INSERT INTO topics (lesson_id, name) VALUES (?, ?)");
                                $insert_topic->execute([$lesson_id, $topic_name]);
                                $cache_topics[$top_cache_key] = $db->lastInsertId();
                            }
                        }
                        $topic_id = $cache_topics[$top_cache_key];

                        // Insert
                        $insert_stmt->execute([
                            $type_id,
                            $section_id,
                            $lesson_id,
                            $topic_id,
                            $q_text,
                            $opt_a,
                            $opt_b,
                            $opt_c,
                            $opt_d,
                            $opt_e,
                            $correct,
                            $difficulty
                        ]);
                        $success_count++;
                    }

                    $db->commit();

                    if ($success_count > 0) {
                        $msg_type = count($error_logs) > 0 ? 'warning' : 'success';
                        $message = "$success_count soru başarıyla yüklendi.";
                        if (count($error_logs) > 0) {
                            $message .= " Ancak " . count($error_logs) . " satırda hata çıktı.";
                        }
                    } else {
                        $msg_type = 'danger';
                        $message = "Hiç soru yüklenemedi. Lütfen hataları kontrol edin.";
                    }

                } catch (Exception $e) {
                    $db->rollBack();
                    $msg_type = 'danger';
                    $message = "Veritabanı hatası: " . $e->getMessage();
                }

                fclose($handle);
            } else {
                $msg_type = 'danger';
                $message = "Dosya okunamadı.";
            }

        } else {
            $msg_type = 'danger';
            $message = "Lütfen sadece .csv uzantılı dosya yükleyin.";
        }
    } else {
        $msg_type = 'danger';
        $message = "Dosya yüklenirken bir hata oluştu.";
    }
}
?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-white">Toplu Soru Yükleme</h3>
            <p class="text-muted mb-0">CSV dosyası ile binlerce soruyu tek seferde sisteme aktarın.</p>
        </div>
        <a href="downloads/sablon.csv" class="btn btn-outline-teal" download><i class="fa-solid fa-download me-2"></i>
            Örnek Şablonu İndir</a>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="admin-card">
                <h5 class="card-title">CSV Dosyası Yükle</h5>

                <?php if ($message): ?>
                    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show shadow-sm" role="alert">
                        <i
                            class="fa-solid <?= $msg_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="upload-area mb-4">
                        <div class="upload-area-inner text-center p-5 border rounded"
                            style="border: 2px dashed var(--teal) !important; background: rgba(20, 184, 166, 0.03);">
                            <i class="fa-solid fa-cloud-arrow-up fa-3x text-teal mb-3"></i>
                            <h5 class="text-navy">Dosyanızı Sürükleyin veya Seçin</h5>
                            <p class="text-muted mb-4">Sadece .csv uzantılı virgülle ayrılmış dosyalar desteklenir.</p>
                            <input class="form-control" type="file" name="csv_file" accept=".csv" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-teal px-4"><i class="fa-solid fa-upload me-2"></i> Verileri
                            Aktar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-5">
            <?php if (!empty($error_logs)): ?>
                <div class="admin-card border-danger">
                    <h5 class="card-title text-danger"><i class="fa-solid fa-circle-xmark me-2"></i> Hata Raporu</h5>
                    <div class="error-log-box p-3 rounded"
                        style="background: rgba(255,0,85,0.05); border: 1px solid rgba(255,0,85,0.2); max-height: 300px; overflow-y: auto; font-size: 0.9rem;">
                        <ul class="text-danger mb-0 ps-3">
                                <?php foreach ($error_logs as $err): ?>
                                <li class="mb-1"><?= htmlspecialchars($err) ?></li>
                                <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <div class="admin-card border-0 shadow-sm" style="background: rgba(255,255,255,0.02);">
                    <h5 class="card-title text-white"><i class="fa-solid fa-circle-info text-teal me-2"></i> Yükleme Talimatı
                    </h5>
                    <ul class="text-muted ps-3 mb-0" style="font-size: 0.95rem; line-height: 1.8;">
                        <li>İndirdiğiniz şablon dosyasının başlık satırını <strong>silmeyin.</strong></li>
                        <li>Kategori isimleri (Sınav, Ders, Konu) sistemdeki isimlerle <strong>birebir aynı</strong>
                            yazılmalıdır (büyük/küçük harf duyarlıdır).</li>
                        <li>Zorluk seviyesi alanına sadece <code>Kolay</code>, <code>Orta</code> veya <code>Zor</code>
                            yazılmalıdır.</li>
                        <li>Sistem bulamadığı konuları atlar ve işlem sonunda sağ tarafta bir hata listesi olarak gösterir.
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .upload-area-inner:hover {
        background: rgba(20, 184, 166, 0.08) !important;
        cursor: pointer;
        transition: 0.3s;
    }

    .text-teal {
        color: var(--teal);
    }

    .text-navy {
        color: var(--navy-dark);
    }

    .btn-outline-teal {
        border-color: var(--teal);
        color: var(--teal);
        transition: 0.3s;
    }

    .btn-outline-teal:hover {
        background: var(--teal);
        color: #fff;
    }
</style>

<?php require_once 'includes/footer.php'; ?>