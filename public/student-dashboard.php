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

// Get featured courses
$database = new Database();
$conn = $database->getConnection();

$featured_courses = [];
try {
    $query = "SELECT c.*, 
              COUNT(e.id) as enrollment_count,
              AVG(cr.rating) as avg_rating,
              cm.file_path as cover_image,
              u.first_name, u.last_name
              FROM courses c 
              LEFT JOIN enrollments e ON c.id = e.course_id
              LEFT JOIN course_reviews cr ON c.id = cr.course_id
              LEFT JOIN course_media cm ON c.id = cm.course_id AND cm.media_type = 'course_cover'
              LEFT JOIN users u ON c.instructor_id = u.id
              WHERE c.status = 'active'
              GROUP BY c.id
              ORDER BY c.created_at DESC
              LIMIT 6";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $featured_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featured_courses = [];
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
    <title>Student Dashboard - SkillSpark Platform</title>
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
                    <a href="logout.php" class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Logout
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Navigation -->
    <section class="bg-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-8 overflow-x-auto">
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">Development</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">Business</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">Finance & Accounting</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">IT & Software</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">Office Productivity</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">Personal Development</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">Design</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">Marketing</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">Health & Fitness</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium whitespace-nowrap transition-colors">Music</a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="flex items-center mb-8">
                <div class="w-16 h-16 bg-black text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?></h1>
                    <a href="#" class="text-[#0E447A] hover:underline font-medium">Add occupation and interests</a>
                </div>
            </div>

            <!-- What to learn next section -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">What to learn next</h2>
                <h3 class="text-lg text-gray-600 mb-6">Trending courses</h3>
                
                <!-- Course Carousel -->
                <div class="relative">
                    <div class="flex space-x-6 overflow-x-auto pb-4" id="course-carousel">
                        <?php if(empty($featured_courses)): ?>
                            <!-- Fallback Course Card -->
                            <div class="flex-shrink-0 w-80 bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="h-48 bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center relative">
                                    <div class="text-center text-white">
                                        <i class="fas fa-code text-6xl mb-2 opacity-20"></i>
                                    </div>
                                    <div class="absolute top-4 left-4">
                                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                            <i class="fas fa-code text-2xl text-white"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h4 class="font-bold text-gray-800 mb-2 text-sm">Sample Course</h4>
                                    <p class="text-sm text-gray-600 mb-2">Instructor Name</p>
                                    <div class="flex items-center mb-2">
                                        <div class="flex text-yellow-400">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">4.6 (0)</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-lg font-bold text-gray-800">$0.00</span>
                                        </div>
                                        <div class="flex space-x-1">
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Free</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach($featured_courses as $course): ?>
                            <div class="flex-shrink-0 w-80 bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='course_detail.php?id=<?php echo $course['id']; ?>'">
                                <div class="h-48 relative overflow-hidden">
                                    <?php if (!empty($course['cover_image'])): 
                                        // Fix file path - remove ../ prefix if it exists
                                        $cover_path = $course['cover_image'];
                                        if (strpos($cover_path, '../') === 0) {
                                            $cover_path = substr($cover_path, 3);
                                        }
                                        if (file_exists($cover_path)): ?>
                                        <img src="file_viewer.php?file=<?php echo urlencode($cover_path); ?>" 
                                             alt="<?php echo htmlspecialchars($course['title']); ?>" 
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="h-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center relative">
                                            <div class="text-center text-white">
                                                <i class="fas fa-book text-6xl mb-2 opacity-20"></i>
                                            </div>
                                            <div class="absolute top-4 left-4">
                                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-book text-2xl text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-6">
                                    <h4 class="font-bold text-gray-800 mb-2 text-sm"><?php echo htmlspecialchars($course['title']); ?></h4>
                                    <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?></p>
                                    <div class="flex items-center mb-2">
                                        <div class="flex text-yellow-400">
                                            <?php 
                                            $rating = round($course['avg_rating'] ?? 0, 1);
                                            for($i = 1; $i <= 5; $i++) {
                                                if($i <= $rating) {
                                                    echo '<i class="fas fa-star"></i>';
                                                } elseif($i - 0.5 <= $rating) {
                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600"><?php echo $rating; ?> (<?php echo $course['enrollment_count']; ?>)</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-lg font-bold text-gray-800">$<?php echo $course['price']; ?></span>
                                            <?php if($course['price'] > 0): ?>
                                                <span class="text-sm text-gray-500 line-through">$<?php echo $course['price'] * 2; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex space-x-1">
                                            <?php if($course['price'] == 0): ?>
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Free</span>
                                            <?php else: ?>
                                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Premium</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Continue learning section -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Continue learning</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Learning Card 1 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-code text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Web Development</h3>
                                <p class="text-sm text-gray-600">In Progress</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Progress</span>
                                <span>75%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                        <button class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Continue Learning
                        </button>
                    </div>

                    <!-- Learning Card 2 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-chart-line text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Data Science</h3>
                                <p class="text-sm text-gray-600">Completed</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Progress</span>
                                <span>100%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                        <button class="w-full bg-gray-200 text-gray-700 py-2 rounded-md hover:bg-gray-300 transition-colors">
                            View Certificate
                        </button>
                    </div>

                    <!-- Learning Card 3 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-palette text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">UI/UX Design</h3>
                                <p class="text-sm text-gray-600">Not Started</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Progress</span>
                                <span>0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <button class="w-full bg-purple-600 text-white py-2 rounded-md hover:bg-purple-700 transition-colors">
                            Start Learning
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recommended for you section -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Recommended for you</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Recommendation Card 1 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-32 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-4xl text-white"></i>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Mobile App Development</h3>
                            <p class="text-sm text-gray-600 mb-3">Learn to build mobile applications</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-800">$29.99</span>
                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Course</button>
                            </div>
                        </div>
                    </div>

                    <!-- Recommendation Card 2 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-32 bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                            <i class="fas fa-database text-4xl text-white"></i>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Database Management</h3>
                            <p class="text-sm text-gray-600 mb-3">Master database design and optimization</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-800">$24.99</span>
                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Course</button>
                            </div>
                        </div>
                    </div>

                    <!-- Recommendation Card 3 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-32 bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center">
                            <i class="fas fa-shield-alt text-4xl text-white"></i>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Cybersecurity</h3>
                            <p class="text-sm text-gray-600 mb-3">Protect systems from cyber threats</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-800">$39.99</span>
                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Course</button>
                            </div>
                        </div>
                    </div>

                    <!-- Recommendation Card 4 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-32 bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center">
                            <i class="fas fa-heart text-4xl text-white"></i>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Digital Marketing</h3>
                            <p class="text-sm text-gray-600 mb-3">Grow your business online</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-800">$19.99</span>
                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Course</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Add shadow to header on scroll
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 0) {
                header.classList.add('shadow-lg');
            } else {
                header.classList.remove('shadow-lg');
            }
        });
    </script>
</body>
</html>
