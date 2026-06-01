<?php
require_once 'database.php';
header('Content-Type: application/json; charset=utf-8');

try {
    if (isset($_GET['lesson_id']) && !empty($_GET['lesson_id'])) {
        $stmt = $db->prepare("SELECT id, name FROM topics WHERE lesson_id = ? AND status = 1 ORDER BY id ASC");
        $stmt->execute([$_GET['lesson_id']]);
        echo json_encode($stmt->fetchAll());
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?>
