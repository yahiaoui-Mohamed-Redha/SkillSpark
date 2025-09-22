<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = $auth->getCurrentUser();
$database = new Database();
$conn = $database->getConnection();

// Get course media for course 2 (which has a video)
$course_id = 2;
$course_media = [];

try {
    $query = "SELECT * FROM course_media WHERE course_id = :course_id ORDER BY media_type, created_at";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $course_media = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Course Media Debug</h2>";
    echo "<pre>";
    print_r($course_media);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Test video display
$course_video = array_filter($course_media, function($media) {
    return $media['media_type'] === 'video';
});

echo "<h2>Video Filter Result</h2>";
echo "<pre>";
print_r($course_video);
echo "</pre>";

if (!empty($course_video)) {
    $video = array_values($course_video)[0];
    echo "<h2>Video Details</h2>";
    echo "<pre>";
    print_r($video);
    echo "</pre>";
    
    // Fix file path
    $video_path = $video['file_path'];
    if (strpos($video_path, '../') === 0) {
        $video_path = substr($video_path, 3);
    }
    
    echo "<h2>Fixed Video Path</h2>";
    echo "Original: " . $video['file_path'] . "<br>";
    echo "Fixed: " . $video_path . "<br>";
    echo "File exists: " . (file_exists($video_path) ? 'YES' : 'NO') . "<br>";
    
    echo "<h2>Video Display Test</h2>";
    echo "<video controls width='400'>";
    echo "<source src='file_viewer.php?file=" . urlencode($video_path) . "' type='" . htmlspecialchars($video['file_type']) . "'>";
    echo "Your browser does not support the video tag.";
    echo "</video>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Debug Test</title>
</head>
<body>
    <h1>Video Debug Test</h1>
</body>
</html>
