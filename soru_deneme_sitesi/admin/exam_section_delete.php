<?php
session_start();
require_once '../database.php';

// Güvenlik: Kullanıcı giriş yapmış mı ve admin mi?
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    // ID parametresini güvenli bir şekilde al
    $id = intval($_GET['id']);

    if ($id > 0) {
        try {
            // 1. İlişkisel Veri Kontrolü (Dersler tablosunda bu alt sınava bağlı ders var mı?)
            $stmt_check_lessons = $db->prepare("SELECT COUNT(*) FROM lessons WHERE exam_section_id = ?");
            $stmt_check_lessons->execute([$id]);
            $lessons_count = $stmt_check_lessons->fetchColumn();

            // 2. İlişkisel Veri Kontrolü (Sorular tablosunda bu alt sınava bağlı soru var mı?)
            $stmt_check_questions = $db->prepare("SELECT COUNT(*) FROM questions WHERE exam_section_id = ?");
            $stmt_check_questions->execute([$id]);
            $questions_count = $stmt_check_questions->fetchColumn();

            if ($lessons_count > 0 || $questions_count > 0) {
                // Eğer ilişkili veri varsa güvenli bir şekilde silme işlemini durdur
                header("Location: exam_sections.php?status=has_relation");
                exit();
            }

            // Hiçbir bağımlılık yoksa, güvenle DELETE işlemini yap
            $stmt_delete = $db->prepare("DELETE FROM exam_sections WHERE id = ?");
            if ($stmt_delete->execute([$id])) {
                header("Location: exam_sections.php?status=success");
                exit();
            } else {
                header("Location: exam_sections.php?status=error");
                exit();
            }

        } catch (PDOException $e) {
            // Beklenmeyen bir veritabanı hatasında error döndür
            header("Location: exam_sections.php?status=error");
            exit();
        }
    } else {
        header("Location: exam_sections.php?status=error");
        exit();
    }
} else {
    header("Location: exam_sections.php");
    exit();
}
?>
