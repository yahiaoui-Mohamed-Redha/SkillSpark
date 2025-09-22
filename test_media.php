<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "=== Testing Course Media ===\n";

// Test course 2 which should have media
$course_id = 2;

try {
    $query = "SELECT * FROM course_media WHERE course_id = :course_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Course ID: $course_id\n";
    echo "Media count: " . count($media) . "\n";
    
    foreach($media as $m) {
        echo "Media ID: {$m['id']}\n";
        echo "Type: {$m['media_type']}\n";
        echo "File Path: {$m['file_path']}\n";
        echo "File Name: {$m['file_name']}\n";
        echo "File Size: {$m['file_size']}\n";
        echo "File Type: {$m['file_type']}\n";
        echo "---\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Test if files exist
echo "\n=== File Existence Test ===\n";
$files_to_check = [
    'uploads/course_video/68d160c2514f3_1758552258.mp4',
    'uploads/course_cover/68d160c24feb9_1758552258.png'
];

foreach($files_to_check as $file) {
    echo "File: $file\n";
    echo "Exists: " . (file_exists($file) ? 'YES' : 'NO') . "\n";
    if(file_exists($file)) {
        echo "Size: " . filesize($file) . " bytes\n";
        echo "MIME: " . mime_content_type($file) . "\n";
    }
    echo "---\n";
}
?>
