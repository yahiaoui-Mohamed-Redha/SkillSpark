<?php
require_once '../config/auth.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if user is a business account
if($_SESSION['role'] !== 'business') {
    header('Location: student-dashboard.php');
    exit();
}

$user = $auth->getCurrentUser();

// Get database connection
require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Get instructor statistics
$stats = [];

// Total courses
$query = "SELECT COUNT(*) as total FROM courses WHERE instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $user['id']);
$stmt->execute();
$stats['total_courses'] = $stmt->fetch()['total'];

// Total students
$query = "SELECT COUNT(DISTINCT e.user_id) as total FROM enrollments e 
          JOIN courses c ON e.course_id = c.id 
          WHERE c.instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $user['id']);
$stmt->execute();
$stats['total_students'] = $stmt->fetch()['total'];

// Total earnings
$query = "SELECT SUM(instructor_share) as total FROM instructor_earnings WHERE instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $user['id']);
$stmt->execute();
$stats['total_earnings'] = $stmt->fetch()['total'] ?? 0;

// Average rating
$query = "SELECT AVG(cr.rating) as avg_rating FROM course_reviews cr 
          JOIN courses c ON cr.course_id = c.id 
          WHERE c.instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $user['id']);
$stmt->execute();
$stats['avg_rating'] = round($stmt->fetch()['avg_rating'] ?? 0, 1);

// Recent courses
$query = "SELECT * FROM courses WHERE instructor_id = :instructor_id ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $user['id']);
$stmt->execute();
$recent_courses = $stmt->fetchAll();

// Recent enrollments
$query = "SELECT e.*, c.title as course_title, u.first_name, u.last_name 
          FROM enrollments e 
          JOIN courses c ON e.course_id = c.id 
          JOIN users u ON e.user_id = u.id 
          WHERE c.instructor_id = :instructor_id 
          ORDER BY e.enrolled_at DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $user['id']);
$stmt->execute();
$recent_enrollments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../public/assist/icon.png">
    <title>Instructor Account - SkillSpark Platform</title>
</head>

<body class="bg-gray-50">
    <!-- Header Section -->
    <section id="header" class="bg-white sticky top-0 z-50 transition-shadow duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <img src="../public/assist/logo2.png" alt="SkillSpark Logo" class="h-12 w-auto">
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="support_ticket.php" class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center">
                        <i class="fas fa-life-ring mr-1"></i>
                        Support
                    </a>
                    <div class="relative group">
                        <button class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center bg-transparent border-none cursor-pointer">
                            Teaching
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <!-- Teaching Dropdown -->
                        <div class="absolute top-full left-0 mt-1 w-64 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="create_course.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-plus mr-2"></i>Create New Course
                                </a>
                                <a href="my_courses.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-book mr-2"></i>My Courses
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-video mr-2"></i>Course Builder
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-chart-line mr-2"></i>Course Analytics
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-graduation-cap mr-2"></i>Certificates
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center bg-transparent border-none cursor-pointer">
                            Students
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <!-- Students Dropdown -->
                        <div class="absolute top-full left-0 mt-1 w-64 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-users mr-2"></i>All Students
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-plus mr-2"></i>New Enrollments
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-comments mr-2"></i>Student Messages
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-star mr-2"></i>Reviews & Ratings
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-trophy mr-2"></i>Top Students
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center bg-transparent border-none cursor-pointer">
                            Analytics
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <!-- Analytics Dropdown -->
                        <div class="absolute top-full left-0 mt-1 w-64 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-chart-bar mr-2"></i>Performance Overview
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-dollar-sign mr-2"></i>Earnings Report
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-eye mr-2"></i>Course Views
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-clock mr-2"></i>Engagement Metrics
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-download mr-2"></i>Export Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center bg-transparent border-none cursor-pointer">
                            Tools
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <!-- Tools Dropdown -->
                        <div class="absolute top-full left-0 mt-1 w-64 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-video mr-2"></i>Video Recording
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-microphone mr-2"></i>Audio Recording
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-pdf mr-2"></i>PDF Creator
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-quiz mr-2"></i>Quiz Builder
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-calendar mr-2"></i>Schedule Live Sessions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-lg mx-8">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" placeholder="Search courses, students..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]">
                    </div>
                </div>

                <!-- Right side navigation -->
                <div class="flex items-center space-x-4">
                    <!-- Icons -->
                    <button class="p-2 text-gray-600 hover:text-[#0E447A] transition-colors relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-[#0E447A] rounded-full"></span>
                    </button>
                    
                    <button class="p-2 text-gray-600 hover:text-[#0E447A] transition-colors relative">
                        <i class="fas fa-question-circle text-xl"></i>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="relative group">
                        <button class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm hover:bg-purple-700 transition-colors">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                        </button>
                        <!-- User Dropdown -->
                        <div class="absolute top-full right-0 mt-2 w-64 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                    <p class="text-xs text-gray-500">Instructor</p>
                                </div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>My Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Account Settings
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-credit-card mr-2"></i>Payment Methods
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-chart-line mr-2"></i>Earnings
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-question-circle mr-2"></i>Help & Support
                                </a>
                                <hr class="my-2">
                                <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Sign Out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?></h1>
                        <p class="text-lg text-gray-600">Manage your courses and track your success</p>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <a href="create_course.php" class="bg-[#0E447A] text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#1e5a96] transition-colors inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>Create Course
                    </a>
                    <button class="bg-white border-2 border-[#0E447A] text-[#0E447A] px-6 py-3 rounded-lg font-semibold hover:bg-[#0E447A] hover:text-white transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>View Analytics
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-book text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Courses</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_courses']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-users text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_students']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-star text-2xl text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Average Rating</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['avg_rating']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-dollar-sign text-2xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Earnings</p>
                            <p class="text-2xl font-bold text-gray-900">$<?php echo number_format($stats['total_earnings'], 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Recent Courses -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Courses</h3>
                        <a href="#" class="text-sm text-[#0E447A] hover:underline">View All</a>
                    </div>
                    <div class="space-y-4">
                        <?php foreach($recent_courses as $course): ?>
                        <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($course['title']); ?></p>
                                <p class="text-xs text-gray-500">$<?php echo $course['price']; ?> • <?php echo $course['students_count']; ?> students</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500"><?php echo date('M j', strtotime($course['created_at'])); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Enrollments -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Enrollments</h3>
                        <a href="#" class="text-sm text-[#0E447A] hover:underline">View All</a>
                    </div>
                    <div class="space-y-4">
                        <?php foreach($recent_enrollments as $enrollment): ?>
                        <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></p>
                                <p class="text-xs text-gray-500">Enrolled in <?php echo htmlspecialchars($enrollment['course_title']); ?></p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500"><?php echo date('M j', strtotime($enrollment['enrolled_at'])); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-500 rounded-full">
                                <i class="fas fa-plus text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Create Course</h3>
                                <p class="text-sm text-gray-600">Start building your next course</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 rounded-full">
                                <i class="fas fa-video text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Record Video</h3>
                                <p class="text-sm text-gray-600">Record new course content</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-500 rounded-full">
                                <i class="fas fa-chart-line text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">View Analytics</h3>
                                <p class="text-sm text-gray-600">Track your performance</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-500 rounded-full">
                                <i class="fas fa-comments text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Student Messages</h3>
                                <p class="text-sm text-gray-600">Respond to students</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Header scroll shadow effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 10) {
                header.classList.add('shadow-lg');
            } else {
                header.classList.remove('shadow-lg');
            }
        });
    </script>
</body>

</html>
