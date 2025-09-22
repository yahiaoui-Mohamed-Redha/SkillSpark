<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in and is an instructor
if(!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

if($_SESSION['role'] !== 'business') {
    header('Location: student-dashboard.php');
    exit();
}

$user = $auth->getCurrentUser();
$database = new Database();
$conn = $database->getConnection();

$course_id = $_GET['id'] ?? 0;
$success_message = '';
$error_message = '';

// Debug information
if (empty($course_id)) {
    $error_message = 'No course ID provided';
}

// Get course details
$course = null;
$course_media = [];
$enrollments = [];
$reviews = [];

try {
    // Get course
    $query = "SELECT c.*, u.first_name, u.last_name 
              FROM courses c 
              JOIN users u ON c.instructor_id = u.id 
              WHERE c.id = :course_id AND c.instructor_id = :instructor_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        $error_message = 'Course not found or you do not have permission to view this course';
    }
    
    // Get course media (with error handling)
    $course_media = [];
    try {
        $query = "SELECT * FROM course_media WHERE course_id = :course_id ORDER BY media_type, created_at";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        $course_media = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Table might not exist yet, continue without media
        $course_media = [];
    }
    
    // Get enrollments
    $enrollments = [];
    try {
        $query = "SELECT e.*, u.first_name, u.last_name, u.email 
                  FROM enrollments e 
                  JOIN users u ON e.user_id = u.id 
                  WHERE e.course_id = :course_id 
                  ORDER BY e.enrolled_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Table might not exist yet, continue without enrollments
        $enrollments = [];
    }
    
    // Get reviews
    $reviews = [];
    try {
        $query = "SELECT cr.*, u.first_name, u.last_name 
                  FROM course_reviews cr 
                  JOIN users u ON cr.user_id = u.id 
                  WHERE cr.course_id = :course_id 
                  ORDER BY cr.created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Table might not exist yet, continue without reviews
        $reviews = [];
    }
    
} catch (PDOException $e) {
    $error_message = 'Error loading course details: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - SkillSpark</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <img src="../public/assist/logo2.png" alt="SkillSpark" class="h-8 w-auto">
                    <h1 class="ml-3 text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($course['title']); ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="edit_course.php?id=<?php echo $course_id; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-edit mr-1"></i>Edit Course
                    </a>
                    <a href="my_courses.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left"></i> Back to Courses
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if($course): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Course Video -->
                <?php 
                $course_video = array_filter($course_media, function($media) {
                    return $media['media_type'] === 'video';
                });
                if (!empty($course_video)): 
                    $video = array_values($course_video)[0];
                ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Video</h2>
                        <video class="w-full rounded-lg" controls>
                            <source src="<?php echo htmlspecialchars($video['file_path']); ?>" type="<?php echo htmlspecialchars($video['file_type']); ?>">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                <?php endif; ?>

                <!-- Course Description -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Description</h2>
                    <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                </div>

                <!-- Course Media -->
                <?php if (!empty($course_media)): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Materials</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach($course_media as $media): ?>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-<?php echo $media['media_type'] === 'video' ? 'video' : 'file'; ?> text-blue-600 mr-2"></i>
                                        <span class="font-medium"><?php echo ucfirst($media['media_type']); ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($media['file_name']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo round($media['file_size'] / 1024 / 1024, 2); ?> MB</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Course Stats -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Statistics</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Price:</span>
                            <span class="font-semibold">$<?php echo $course['price']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Level:</span>
                            <span class="font-semibold"><?php echo ucfirst($course['level']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duration:</span>
                            <span class="font-semibold"><?php echo $course['duration_hours']; ?> hours</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Students:</span>
                            <span class="font-semibold"><?php echo count($enrollments); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rating:</span>
                            <span class="font-semibold">
                                <?php 
                                $avg_rating = 0;
                                if (!empty($reviews)) {
                                    $total_rating = array_sum(array_column($reviews, 'rating'));
                                    $avg_rating = round($total_rating / count($reviews), 1);
                                }
                                echo $avg_rating;
                                ?>
                                <i class="fas fa-star text-yellow-500"></i>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php 
                                switch($course['status']) {
                                    case 'active': echo 'bg-green-100 text-green-800'; break;
                                    case 'inactive': echo 'bg-red-100 text-red-800'; break;
                                    case 'draft': echo 'bg-yellow-100 text-yellow-800'; break;
                                }
                                ?>">
                                <?php echo ucfirst($course['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Recent Enrollments -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Enrollments</h3>
                    <?php if(empty($enrollments)): ?>
                        <p class="text-gray-500 text-sm">No enrollments yet</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach(array_slice($enrollments, 0, 5) as $enrollment): ?>
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($enrollment['enrolled_at'])); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if(count($enrollments) > 5): ?>
                            <a href="#" class="text-blue-600 text-sm hover:underline mt-3 block">View all <?php echo count($enrollments); ?> students</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Recent Reviews -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Reviews</h3>
                    <?php if(empty($reviews)): ?>
                        <p class="text-gray-500 text-sm">No reviews yet</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach(array_slice($reviews, 0, 3) as $review): ?>
                                <div class="border-b border-gray-200 pb-3 last:border-b-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></span>
                                        <div class="flex">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star text-<?php echo $i <= $review['rating'] ? 'yellow' : 'gray'; ?>-400 text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if(count($reviews) > 3): ?>
                            <a href="#" class="text-blue-600 text-sm hover:underline mt-3 block">View all <?php echo count($reviews); ?> reviews</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <i class="fas fa-exclamation-triangle text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Course Not Found</h3>
            <p class="text-gray-600 mb-6">The course you're looking for doesn't exist or you don't have permission to view it.</p>
            <a href="my_courses.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to My Courses
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
