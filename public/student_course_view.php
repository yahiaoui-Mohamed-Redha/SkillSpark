<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if user is a student
if($_SESSION['role'] !== 'student') {
    header('Location: instructor-dashboard.php');
    exit();
}

$user = $auth->getCurrentUser();
$database = new Database();
$conn = $database->getConnection();

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if (!$course_id) {
    header('Location: student-dashboard.php');
    exit();
}

// Get course details
$course = null;
$is_enrolled = false;
$enrollment = null;

try {
    // Get course info
    $query = "SELECT c.*, u.first_name, u.last_name, u.profile_image 
              FROM courses c 
              JOIN users u ON c.instructor_id = u.id 
              WHERE c.id = :course_id AND c.status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        header('Location: student-dashboard.php');
        exit();
    }
    
    // Check if student is enrolled
    $query = "SELECT * FROM enrollments WHERE course_id = :course_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
    $is_enrolled = !empty($enrollment);
    
} catch (PDOException $e) {
    header('Location: student-dashboard.php');
    exit();
}

// Get course playlists and lessons
$playlists = [];
if ($is_enrolled) {
    try {
        $query = "SELECT cp.*, 
                  (SELECT COUNT(*) FROM lessons l WHERE l.playlist_id = cp.id AND l.is_active = 1) as lesson_count
                  FROM course_playlists cp 
                  WHERE cp.course_id = :course_id AND cp.is_active = 1
                  ORDER BY cp.playlist_order, cp.created_at";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get lessons for each playlist
        foreach ($playlists as &$playlist) {
            $query = "SELECT * FROM lessons 
                      WHERE course_id = :course_id AND playlist_id = :playlist_id AND is_active = 1
                      ORDER BY lesson_order, lesson_number";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->bindParam(':playlist_id', $playlist['id']);
            $stmt->execute();
            $playlist['lessons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $playlists = [];
    }
}

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enroll') {
    if (!$is_enrolled) {
        try {
            $query = "INSERT INTO enrollments (course_id, user_id, enrolled_at) VALUES (:course_id, :user_id, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
            
            header('Location: student_course_view.php?course_id=' . $course_id . '&enrolled=1');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error enrolling in course: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../public/assist/icon.png">
    <title><?php echo htmlspecialchars($course['title']); ?> - SkillSpark</title>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="student-dashboard.php" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($course['title']); ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="student-dashboard.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-home"></i>
                    </a>
                    <a href="logout.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Messages -->
        <?php if (isset($_GET['enrolled']) && $_GET['enrolled'] == '1'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                Successfully enrolled in this course!
            </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Course Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($course['title']); ?></h2>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($course['description']); ?></p>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span><i class="fas fa-user mr-1"></i>Instructor: <?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?></span>
                        <span><i class="fas fa-tag mr-1"></i><?php echo htmlspecialchars($course['category']); ?></span>
                        <span><i class="fas fa-clock mr-1"></i><?php echo $course['duration']; ?> hours</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-gray-900 mb-2">
                        <?php if ($course['price'] == 0): ?>
                            <span class="text-green-600">Free</span>
                        <?php else: ?>
                            $<?php echo $course['price']; ?>
                        <?php endif; ?>
                    </div>
                    <?php if (!$is_enrolled): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="enroll">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 font-semibold">
                                <i class="fas fa-play mr-2"></i>Enroll Now
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="bg-green-100 text-green-800 px-4 py-2 rounded-md font-semibold">
                            <i class="fas fa-check mr-2"></i>Enrolled
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($is_enrolled): ?>
            <!-- Course Content -->
            <?php if (!empty($playlists)): ?>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Course Content</h3>
                <div class="space-y-6">
                    <?php foreach ($playlists as $playlist): ?>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($playlist['name']); ?></h4>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($playlist['description']); ?></p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-500"><?php echo count($playlist['lessons']); ?> lessons</span>
                            </div>
                        </div>
                        
                        <?php if (!empty($playlist['lessons'])): ?>
                        <div class="space-y-2">
                            <?php foreach ($playlist['lessons'] as $lesson): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-blue-600"><?php echo $lesson['lesson_number']; ?></span>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900"><?php echo htmlspecialchars($lesson['title']); ?></h5>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($lesson['description']); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <?php if ($lesson['is_preview']): ?>
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">
                                            <i class="fas fa-eye mr-1"></i>Preview
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($lesson['duration'] > 0): ?>
                                        <span class="text-sm text-gray-500">
                                            <i class="fas fa-clock mr-1"></i><?php echo $lesson['duration']; ?> min
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($lesson['video_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($lesson['video_url']); ?>" target="_blank" 
                                           class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                            <i class="fas fa-play mr-1"></i>Watch
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-book text-2xl mb-2"></i>
                            <p>No lessons in this playlist yet</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-book text-4xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Content Available</h3>
                    <p>The instructor hasn't added any content to this course yet.</p>
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Enrollment CTA -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="text-center py-8">
                    <i class="fas fa-lock text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Enroll to Access Content</h3>
                    <p class="text-gray-600 mb-6">Enroll in this course to access all lessons and materials.</p>
                    <form method="POST" class="inline">
                        <input type="hidden" name="action" value="enroll">
                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 font-semibold text-lg">
                            <i class="fas fa-play mr-2"></i>Enroll Now
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
