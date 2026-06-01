<?php
require_once 'database.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['topics'])) {
    $user_id = $_SESSION['user_id'];
    $exam_type_id = $_POST['exam_type_id'];
    $exam_section_id = $_POST['exam_section_id'];
    $lesson_id = $_POST['lesson_id'];
    $topics = $_POST['topics']; // array format: topic_id => count

    // Başlık oluştur
    $stmt = $db->prepare("SELECT name FROM lessons WHERE id = ?");
    $stmt->execute([$lesson_id]);
    $lesson_name = $stmt->fetchColumn();
    $title = $lesson_name . " Denemesi - " . date('d.m.Y');

    $selected_questions = [];
    $total_questions = 0;

    // Kullanıcının daha önce çözdüğü tüm soruların ID'lerini bir kere çekiyoruz
    // Bu sayede döngü içinde sürekli veritabanına sorgu atmayacağız (Optimizasyon)
    $stmt_solved = $db->prepare("
        SELECT eq.question_id 
        FROM exam_questions eq
        JOIN exams e ON eq.exam_id = e.id
        WHERE e.user_id = ?
    ");
    $stmt_solved->execute([$user_id]);
    $solved_global_ids = $stmt_solved->fetchAll(PDO::FETCH_COLUMN);

    foreach ($topics as $topic_id => $count) {
        $count = (int)$count;
        if ($count > 0) {
            if ($count > 15) $count = 15;
            
            // Konuya ait TÜM aktif soruların ID'lerini çek (ORDER BY RAND'dan 100 kat hızlıdır)
            $stmt = $db->prepare("SELECT id FROM questions WHERE topic_id = ? AND status = 1");
            $stmt->execute([$topic_id]);
            $all_topic_q_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if(empty($all_topic_q_ids)) continue;

            // 1. Çözülmemişleri bul (Tüm konudaki sorulardan, daha önce çözdüklerini çıkar)
            $unsolved_ids = array_diff($all_topic_q_ids, $solved_global_ids);
            
            $selected_for_topic = [];

            // 2. Önce Çözülmemişleri karıştır ve eksiği kadar al
            if(!empty($unsolved_ids)) {
                shuffle($unsolved_ids); // PHP tarafında randomize
                $take_count = min(count($unsolved_ids), $count);
                $selected_for_topic = array_slice($unsolved_ids, 0, $take_count);
            }

            // 3. Eğer çözülmemişler yetmediyse (hepsini çözdüyse) eksiği çözülenlerden tamamla
            $remaining = $count - count($selected_for_topic);
            if ($remaining > 0) {
                // Sadece bu konuya ait çözülmüşleri bul
                $solved_this_topic = array_intersect($all_topic_q_ids, $solved_global_ids);
                if(!empty($solved_this_topic)) {
                    shuffle($solved_this_topic);
                    $selected_for_topic = array_merge($selected_for_topic, array_slice($solved_this_topic, 0, $remaining));
                }
            }
            
            // Seçilenleri genel havuza ekle
            foreach ($selected_for_topic as $qid) {
                $selected_questions[] = $qid;
                $total_questions++;
            }
        }
    }

    if ($total_questions == 0) {
        die("Seçilen konularda soru havuzda bulunamadı veya sistemde aktif soru yok.");
    }

    // Exam kaydını oluştur
    $stmt = $db->prepare("INSERT INTO exams (user_id, exam_type_id, exam_section_id, lesson_id, title, total_questions) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $exam_type_id, $exam_section_id, $lesson_id, $title, $total_questions]);
    $exam_id = $db->lastInsertId();

    // Soruları kaydet
    $stmt = $db->prepare("INSERT INTO exam_questions (exam_id, question_id) VALUES (?, ?)");
    foreach ($selected_questions as $qid) {
        $stmt->execute([$exam_id, $qid]);
    }

    header("Location: solve_exam.php?id=" . $exam_id);
    exit();
} else {
    header("Location: create_exam.php");
    exit();
}
?>
