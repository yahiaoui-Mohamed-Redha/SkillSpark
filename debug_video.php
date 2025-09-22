<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "=== Course Media Table ===\n";
$query = 'SELECT * FROM course_media';
$stmt = $conn->prepare($query);
$stmt->execute();
$media = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($media as $m) {
    echo "ID: {$m['id']}, Course ID: {$m['course_id']}, Type: {$m['media_type']}, Path: {$m['file_path']}, Name: {$m['file_name']}\n";
}

echo "\n=== Lessons Table ===\n";
$query = 'SELECT * FROM lessons';
$stmt = $conn->prepare($query);
$stmt->execute();
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($lessons as $l) {
    echo "ID: {$l['id']}, Course ID: {$l['course_id']}, Title: {$l['title']}, Video URL: {$l['video_url']}\n";
}

echo "\n=== Files in uploads directory ===\n";
$files = glob('uploads/**/*');
foreach($files as $file) {
    if(is_file($file)) {
        echo "File: $file, Size: " . filesize($file) . " bytes\n";
    }
}

echo "\n=== Courses Table ===\n";
$query = 'SELECT id, title, instructor_id FROM courses';
$stmt = $conn->prepare($query);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($courses as $c) {
    echo "Course ID: {$c['id']}, Title: {$c['title']}, Instructor: {$c['instructor_id']}\n";
}
?>
