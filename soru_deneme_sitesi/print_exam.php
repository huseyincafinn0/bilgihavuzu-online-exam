<?php
require_once 'database.php';
if (!isset($_SESSION['user_id'])) {
    die("Lütfen giriş yapın.");
}

if (!isset($_GET['id'])) {
    die("Geçersiz sınav.");
}
$exam_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT e.*, l.name as lesson_name FROM exams e JOIN lessons l ON e.lesson_id = l.id WHERE e.id = ? AND e.user_id = ?");
$stmt->execute([$exam_id, $user_id]);
$exam = $stmt->fetch();

if (!$exam) {
    die("Sınav bulunamadı.");
}

$stmt = $db->prepare("
    SELECT eq.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.option_e 
    FROM exam_questions eq
    JOIN questions q ON eq.question_id = q.id
    WHERE eq.exam_id = ?
    ORDER BY eq.id ASC
");
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($exam['title']) ?> - Yazdır</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            font-size: 11pt; 
            line-height: 1.5; 
            color: #1a1a1a; 
            margin: 0; 
            padding: 40px; 
            background: #f8f9fa;
        }
        
        .report-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .header { 
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #00f2fe; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
        }
        
        .header-brand {
            font-size: 24pt;
            font-weight: 700;
            color: #07080d;
        }
        
        .header-brand span {
            color: #00f2fe;
        }

        .header-info {
            text-align: right;
            font-size: 10pt;
            color: #555;
        }
        
        .header-info p {
            margin: 2px 0;
        }

        .exam-title {
            text-align: center;
            font-size: 18pt;
            font-weight: 700;
            margin-bottom: 30px;
            color: #333;
        }

        .question-box { 
            margin-bottom: 25px; 
            page-break-inside: avoid; 
            border: 1px solid #eaeaea;
            padding: 15px;
            border-radius: 6px;
            background: #fafafa;
        }
        
        .question-text {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .options { 
            margin-top: 10px; 
            margin-left: 10px; 
        }
        
        .options div { 
            margin-bottom: 6px; 
            padding: 4px 8px;
            border-radius: 4px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn-print {
            background: linear-gradient(135deg, #00f2fe, #4facfe);
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 242, 254, 0.4);
        }

        @media print {
            body { background: #ffffff; padding: 0; }
            .report-card { border: none; box-shadow: none; padding: 0; }
            .no-print { display: none !important; }
            .question-box { background: transparent; border-color: #ddd; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">Bu Sayfayı Yazdır / PDF Olarak Kaydet</button>
</div>

<div class="report-card">
    <div class="header">
        <div class="header-brand">Bilgi<span>havuzu</span></div>
        <div class="header-info">
            <p><strong>Öğrenci:</strong> <?= htmlspecialchars($_SESSION['full_name']) ?></p>
            <p><strong>Ders:</strong> <?= htmlspecialchars($exam['lesson_name']) ?></p>
            <p><strong>Soru Sayısı:</strong> <?= $exam['total_questions'] ?></p>
        </div>
    </div>
    
    <div class="exam-title"><?= htmlspecialchars($exam['title']) ?></div>

<div class="content">
    <?php foreach($questions as $index => $q): ?>
        <div class="question-box">
            <div class="question-text"><strong>Soru <?= $index + 1 ?>:</strong> <?= nl2br(htmlspecialchars($q['question_text'])) ?></div>
            
            <div class="options">
                <div>A) <?= htmlspecialchars($q['option_a']) ?></div>
                <div>B) <?= htmlspecialchars($q['option_b']) ?></div>
                <div>C) <?= htmlspecialchars($q['option_c']) ?></div>
                <div>D) <?= htmlspecialchars($q['option_d']) ?></div>
                <div>E) <?= htmlspecialchars($q['option_e']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>

</body>
</html>
