<?php
require_once 'database.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exam_id'])) {
    $exam_id = (int)$_POST['exam_id'];
    $user_id = $_SESSION['user_id'];
    $answers = $_POST['answers'] ?? []; // eq_id => 'A'

    // Sınavın bu kullanıcıya ait olduğundan emin ol
    $stmt = $db->prepare("SELECT * FROM exams WHERE id = ? AND user_id = ?");
    $stmt->execute([$exam_id, $user_id]);
    $exam = $stmt->fetch();

    if (!$exam) {
        die("Yetkisiz erişim.");
    }
    if ($exam['score_percent'] > 0) {
        header("Location: result.php?id=" . $exam_id);
        exit();
    }

    $correct_count = 0;
    $wrong_count = 0;
    $empty_count = 0;

    // Soruları ve doğru cevapları getir
    $stmt = $db->prepare("
        SELECT eq.id as eq_id, q.correct_answer 
        FROM exam_questions eq
        JOIN questions q ON eq.question_id = q.id
        WHERE eq.exam_id = ?
    ");
    $stmt->execute([$exam_id]);
    $questions = $stmt->fetchAll();

    foreach ($questions as $q) {
        $eq_id = $q['eq_id'];
        $correct_answer = $q['correct_answer'];
        
        if (isset($answers[$eq_id])) {
            $user_answer = $answers[$eq_id];
            $is_correct = ($user_answer === $correct_answer) ? 1 : 0;
            
            if ($is_correct) {
                $correct_count++;
            } else {
                $wrong_count++;
            }

            // exam_questions tablosunu güncelle
            $upd = $db->prepare("UPDATE exam_questions SET user_answer = ?, is_correct = ? WHERE id = ?");
            $upd->execute([$user_answer, $is_correct, $eq_id]);
        } else {
            $empty_count++;
            // Boş bırakıldıysa kaydet
            $upd = $db->prepare("UPDATE exam_questions SET user_answer = NULL, is_correct = 0 WHERE id = ?");
            $upd->execute([$eq_id]);
        }
    }

    // Başarı yüzdesini hesapla
    $total = $exam['total_questions'];
    $score_percent = ($total > 0) ? ($correct_count / $total) * 100 : 0;

    // exams tablosunu güncelle
    $upd = $db->prepare("UPDATE exams SET correct_count = ?, wrong_count = ?, empty_count = ?, score_percent = ? WHERE id = ?");
    $upd->execute([$correct_count, $wrong_count, $empty_count, $score_percent, $exam_id]);

    header("Location: result.php?id=" . $exam_id);
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>
