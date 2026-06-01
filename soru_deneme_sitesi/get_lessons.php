<?php
require_once 'database.php';
header('Content-Type: application/json; charset=utf-8');

try {
    if (isset($_GET['section_id']) && !empty($_GET['section_id'])) {
        $stmt = $db->prepare("SELECT id, name FROM lessons WHERE exam_section_id = ? AND status = 1 ORDER BY id ASC");
        $stmt->execute([$_GET['section_id']]);
        echo json_encode($stmt->fetchAll());
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?>
