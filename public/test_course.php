<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    echo "Not logged in";
    exit();
}

$user = $auth->getCurrentUser();
echo "User ID: " . $user['id'] . "<br>";
echo "User Role: " . $user['role'] . "<br>";

$database = new Database();
$conn = $database->getConnection();

// Test database connection
try {
    $query = "SELECT COUNT(*) as count FROM courses";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Total courses: " . $result['count'] . "<br>";
    
    // Get instructor's courses
    $query = "SELECT id, title, instructor_id FROM courses WHERE instructor_id = :instructor_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Instructor courses:<br>";
    foreach($courses as $course) {
        echo "- Course ID: " . $course['id'] . ", Title: " . $course['title'] . "<br>";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
