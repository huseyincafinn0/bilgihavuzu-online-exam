<?php
require_once 'database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['action'])) {
    echo json_encode(['error' => 'No action provided']);
    exit();
}

$action = $_GET['action'];
$data = [];

try {
    if ($action === 'get_sections' && isset($_GET['exam_type_id'])) {
        $stmt = $db->prepare("SELECT id, name FROM exam_sections WHERE exam_type_id = ? AND status = 1 ORDER BY id ASC");
        $stmt->execute([(int)$_GET['exam_type_id']]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    elseif ($action === 'get_lessons' && isset($_GET['section_id'])) {
        $stmt = $db->prepare("SELECT id, name FROM lessons WHERE exam_section_id = ? AND status = 1 ORDER BY id ASC");
        $stmt->execute([(int)$_GET['section_id']]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    elseif ($action === 'get_topics' && isset($_GET['lesson_id'])) {
        $stmt = $db->prepare("SELECT id, name FROM topics WHERE lesson_id = ? AND status = 1 ORDER BY id ASC");
        $stmt->execute([(int)$_GET['lesson_id']]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    else {
        echo json_encode(['error' => 'Invalid parameters']);
        exit();
    }

    echo json_encode($data);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
exit();
?>
