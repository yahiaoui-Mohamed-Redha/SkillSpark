<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if user is an instructor
if($_SESSION['role'] !== 'business') {
    header('Location: student-dashboard.php');
    exit();
}

$user = $auth->getCurrentUser();
$database = new Database();
$conn = $database->getConnection();

// Get analytics data
$analytics = [
    'total_courses' => 0,
    'total_students' => 0,
    'total_earnings' => 0,
    'monthly_earnings' => 0,
    'course_stats' => [],
    'recent_enrollments' => [],
    'top_courses' => []
];

try {
    // Total courses
    $query = "SELECT COUNT(*) as count FROM courses WHERE instructor_id = :instructor_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $analytics['total_courses'] = $result['count'];

    // Total students
    $query = "SELECT COUNT(DISTINCT e.user_id) as count 
              FROM enrollments e 
              JOIN courses c ON e.course_id = c.id 
              WHERE c.instructor_id = :instructor_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $analytics['total_students'] = $result['count'];

    // Total earnings
    $query = "SELECT SUM(amount) as total FROM instructor_earnings WHERE instructor_id = :instructor_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $analytics['total_earnings'] = $result['total'] ?? 0;

    // Monthly earnings (current month)
    $query = "SELECT SUM(amount) as total FROM instructor_earnings 
              WHERE instructor_id = :instructor_id 
              AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $analytics['monthly_earnings'] = $result['total'] ?? 0;

    // Course statistics
    $query = "SELECT c.id, c.title, c.price, 
              COUNT(e.id) as enrollment_count,
              AVG(cr.rating) as avg_rating,
              SUM(ie.amount) as earnings
              FROM courses c 
              LEFT JOIN enrollments e ON c.id = e.course_id
              LEFT JOIN course_reviews cr ON c.id = cr.course_id
              LEFT JOIN instructor_earnings ie ON c.id = ie.course_id
              WHERE c.instructor_id = :instructor_id
              GROUP BY c.id
              ORDER BY enrollment_count DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $analytics['course_stats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent enrollments
    $query = "SELECT e.*, c.title as course_title, u.first_name, u.last_name
              FROM enrollments e 
              JOIN courses c ON e.course_id = c.id
              JOIN users u ON e.user_id = u.id
              WHERE c.instructor_id = :instructor_id
              ORDER BY e.enrolled_at DESC
              LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $analytics['recent_enrollments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top courses by enrollment
    $query = "SELECT c.title, COUNT(e.id) as enrollment_count
              FROM courses c 
              LEFT JOIN enrollments e ON c.id = e.course_id
              WHERE c.instructor_id = :instructor_id
              GROUP BY c.id, c.title
              ORDER BY enrollment_count DESC
              LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $analytics['top_courses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = 'Error loading analytics: ' . $e->getMessage();
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
    <title>Analytics - SkillSpark</title>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="instructor-account.php" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Analytics Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="instructor-account.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="logout.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Error Messages -->
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-book text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Courses</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $analytics['total_courses']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-users text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $analytics['total_students']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-dollar-sign text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Earnings</p>
                        <p class="text-2xl font-bold text-gray-900">$<?php echo number_format($analytics['total_earnings'], 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">This Month</p>
                        <p class="text-2xl font-bold text-gray-900">$<?php echo number_format($analytics['monthly_earnings'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Course Performance -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Performance</h3>
                <?php if (empty($analytics['course_stats'])): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chart-bar text-4xl mb-4"></i>
                        <p>No course data available</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($analytics['course_stats'] as $course): ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($course['title']); ?></h4>
                                <span class="text-sm text-gray-500">$<?php echo $course['price']; ?></span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Enrollments:</span>
                                    <span class="font-semibold"><?php echo $course['enrollment_count']; ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Rating:</span>
                                    <span class="font-semibold">
                                        <?php echo $course['avg_rating'] ? number_format($course['avg_rating'], 1) : 'N/A'; ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Earnings:</span>
                                    <span class="font-semibold">$<?php echo number_format($course['earnings'] ?? 0, 2); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Enrollments -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Enrollments</h3>
                <?php if (empty($analytics['recent_enrollments'])): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-user-plus text-4xl mb-4"></i>
                        <p>No recent enrollments</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($analytics['recent_enrollments'] as $enrollment): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></p>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($enrollment['course_title']); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500"><?php echo date('M j, Y', strtotime($enrollment['enrolled_at'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Courses -->
        <?php if (!empty($analytics['top_courses'])): ?>
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Courses</h3>
            <div class="space-y-3">
                <?php foreach ($analytics['top_courses'] as $index => $course): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-sm font-medium text-blue-600"><?php echo $index + 1; ?></span>
                        </div>
                        <span class="font-medium text-gray-900"><?php echo htmlspecialchars($course['title']); ?></span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-500"><?php echo $course['enrollment_count']; ?> enrollments</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="create_course.php" class="bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 text-center">
                    <i class="fas fa-plus text-2xl mb-2"></i>
                    <p class="font-semibold">Create New Course</p>
                </a>
                <a href="my_courses.php" class="bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 text-center">
                    <i class="fas fa-book text-2xl mb-2"></i>
                    <p class="font-semibold">Manage Courses</p>
                </a>
                <a href="instructor-account.php" class="bg-purple-600 text-white p-4 rounded-lg hover:bg-purple-700 text-center">
                    <i class="fas fa-user text-2xl mb-2"></i>
                    <p class="font-semibold">Account Settings</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
