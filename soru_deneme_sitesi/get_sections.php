<?php
require_once 'database.php';
header('Content-Type: application/json; charset=utf-8');

try {
    if (isset($_GET['exam_type_id']) && !empty($_GET['exam_type_id'])) {
        $stmt = $db->prepare("SELECT id, name FROM exam_sections WHERE exam_type_id = ? AND status = 1 ORDER BY id ASC");
        $stmt->execute([$_GET['exam_type_id']]);
        echo json_encode($stmt->fetchAll());
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?>
