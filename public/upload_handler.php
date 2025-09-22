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

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $upload_type = $_POST['upload_type'] ?? '';
    $file = $_FILES['file'];
    
    // Validate upload type
    $allowed_types = ['id_card', 'diploma', 'certificate', 'profile_image', 'cover_image', 'course_video', 'course_cover'];
    if (!in_array($upload_type, $allowed_types)) {
        $response['message'] = 'Invalid upload type';
        echo json_encode($response);
        exit();
    }
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'File upload error';
        echo json_encode($response);
        exit();
    }
    
    // Check file size (max 50MB for videos, 10MB for others)
    $max_size = ($upload_type === 'course_video') ? 50 * 1024 * 1024 : 10 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        $response['message'] = 'File too large';
        echo json_encode($response);
        exit();
    }
    
    // Validate file type
    $allowed_extensions = [];
    switch ($upload_type) {
        case 'profile_image':
        case 'cover_image':
        case 'course_cover':
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            break;
        case 'id_card':
        case 'diploma':
        case 'certificate':
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
            break;
        case 'course_video':
            $allowed_extensions = ['mp4', 'avi', 'mov', 'wmv', 'flv'];
            break;
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        $response['message'] = 'Invalid file type';
        echo json_encode($response);
        exit();
    }
    
    // Create upload directory
    $upload_dir = '../uploads/' . $upload_type . '/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        try {
            // Save to database
            if (in_array($upload_type, ['id_card', 'diploma', 'certificate', 'profile_image', 'cover_image'])) {
                // User documents
                $query = "INSERT INTO user_documents (user_id, document_type, file_name, file_path, file_size, file_type) 
                         VALUES (:user_id, :document_type, :file_name, :file_path, :file_size, :file_type)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':user_id', $user['id']);
                $stmt->bindParam(':document_type', $upload_type);
                $stmt->bindParam(':file_name', $file['name']);
                $stmt->bindParam(':file_path', $file_path);
                $stmt->bindParam(':file_size', $file['size']);
                $stmt->bindParam(':file_type', $file['type']);
                $stmt->execute();
            } else {
                // Course media
                $course_id = $_POST['course_id'] ?? null;
                $lesson_id = $_POST['lesson_id'] ?? null;
                $media_type = ($upload_type === 'course_video') ? 'video' : 'image';
                
                $query = "INSERT INTO course_media (course_id, lesson_id, media_type, file_name, file_path, file_size, file_type) 
                         VALUES (:course_id, :lesson_id, :media_type, :file_name, :file_path, :file_size, :file_type)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':course_id', $course_id);
                $stmt->bindParam(':lesson_id', $lesson_id);
                $stmt->bindParam(':media_type', $media_type);
                $stmt->bindParam(':file_name', $file['name']);
                $stmt->bindParam(':file_path', $file_path);
                $stmt->bindParam(':file_size', $file['size']);
                $stmt->bindParam(':file_type', $file['type']);
                $stmt->execute();
            }
            
            $response['success'] = true;
            $response['message'] = 'File uploaded successfully';
            $response['file_path'] = $file_path;
            $response['file_name'] = $filename;
            
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Failed to move uploaded file';
    }
} else {
    $response['message'] = 'No file uploaded';
}

echo json_encode($response);
?>
