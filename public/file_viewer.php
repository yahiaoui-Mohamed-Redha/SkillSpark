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

// Get file parameters from URL
$file_id = $_GET['id'] ?? 0;
$file_type = $_GET['type'] ?? '';
$file_path = $_GET['file'] ?? '';

// Handle direct file path
if ($file_path) {
    $file_path = urldecode($file_path);
    if (file_exists($file_path)) {
        $file_info = pathinfo($file_path);
        $mime_type = mime_content_type($file_path);
        
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . filesize($file_path));
        header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
        
        readfile($file_path);
        exit();
    } else {
        http_response_code(404);
        exit('File not found');
    }
}

if (!$file_id || !$file_type) {
    http_response_code(404);
    exit('File not found');
}

try {
    if ($file_type === 'user_document') {
        // Get user document
        $query = "SELECT * FROM user_documents WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $file_id);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->execute();
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($file_type === 'course_media') {
        // Get course media (check if user has access to the course)
        $query = "SELECT cm.* FROM course_media cm 
                  JOIN courses c ON cm.course_id = c.id 
                  WHERE cm.id = :id AND (c.instructor_id = :user_id OR c.id IN (
                      SELECT course_id FROM enrollments WHERE user_id = :user_id
                  ))";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $file_id);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->execute();
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        http_response_code(404);
        exit('Invalid file type');
    }
    
    if (!$file) {
        http_response_code(404);
        exit('File not found or access denied');
    }
    
    $file_path = $file['file_path'];
    
    if (!file_exists($file_path)) {
        http_response_code(404);
        exit('File not found on server');
    }
    
    // Set appropriate headers
    header('Content-Type: ' . $file['file_type']);
    header('Content-Length: ' . $file['file_size']);
    header('Content-Disposition: inline; filename="' . $file['file_name'] . '"');
    
    // Output file
    readfile($file_path);
    
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database error');
}
?>
