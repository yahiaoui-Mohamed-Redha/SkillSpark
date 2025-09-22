<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user = $auth->getCurrentUser();
$database = new Database();
$conn = $database->getConnection();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $course_id = intval($_POST['course_id']);
    $comment_text = trim($_POST['comment_text']);
    
    if (empty($comment_text)) {
        $response['message'] = 'Comment text is required';
    } else {
        try {
            // Check if course exists and user has access
            $query = "SELECT id FROM courses WHERE id = :course_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                $response['message'] = 'Course not found';
            } else {
                // Insert comment
                $query = "INSERT INTO course_comments (course_id, user_id, comment_text, created_at) 
                         VALUES (:course_id, :user_id, :comment_text, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':course_id', $course_id);
                $stmt->bindParam(':user_id', $user['id']);
                $stmt->bindParam(':comment_text', $comment_text);
                $stmt->execute();
                
                $response['success'] = true;
                $response['message'] = 'Comment added successfully';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>
