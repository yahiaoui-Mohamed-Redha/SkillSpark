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

// Get instructor's courses
$courses = [];
try {
    $query = "SELECT c.*, cat.name as category_name, 
              COUNT(e.id) as enrollment_count,
              AVG(cr.rating) as avg_rating
              FROM courses c 
              LEFT JOIN categories cat ON c.category_id = cat.id
              LEFT JOIN enrollments e ON c.id = e.course_id
              LEFT JOIN course_reviews cr ON c.id = cr.course_id
              WHERE c.instructor_id = :instructor_id 
              GROUP BY c.id
              ORDER BY c.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Error loading courses';
}

// Get categories for filtering
$categories = [];
try {
    $query = "SELECT * FROM categories ORDER BY name";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - SkillSpark</title>
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
                    <h1 class="ml-3 text-xl font-semibold text-gray-900">My Courses</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="create_course.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i>Create New Course
                    </a>
                    <a href="instructor-account.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex flex-wrap gap-4 items-center">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Categories</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="search" placeholder="Search courses..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if(empty($courses)): ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-book text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No courses yet</h3>
                    <p class="text-gray-600 mb-6">Start creating your first course to share your knowledge</p>
                    <a href="create_course.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Create Your First Course
                    </a>
                </div>
            <?php else: ?>
                <?php foreach($courses as $course): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <!-- Course Image -->
                        <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-book text-6xl text-white"></i>
                        </div>
                        
                        <!-- Course Info -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
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
                                <span class="text-sm text-gray-500"><?php echo $course['category_name'] ?? 'Uncategorized'; ?></span>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?><?php echo strlen($course['description']) > 100 ? '...' : ''; ?></p>
                            
                            <!-- Course Stats -->
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-1"></i>
                                    <?php echo $course['enrollment_count']; ?> students
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-star mr-1 text-yellow-500"></i>
                                    <?php echo number_format($course['avg_rating'] ?? 0, 1); ?>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-dollar-sign mr-1"></i>
                                    $<?php echo $course['price']; ?>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="edit_course.php?id=<?php echo $course['id']; ?>" 
                                   class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <a href="course_analytics.php?id=<?php echo $course['id']; ?>" 
                                   class="flex-1 bg-gray-600 text-white text-center py-2 px-4 rounded-md hover:bg-gray-700 text-sm">
                                    <i class="fas fa-chart-line mr-1"></i>Analytics
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Simple filtering functionality
        document.getElementById('search').addEventListener('input', filterCourses);
        document.getElementById('status').addEventListener('change', filterCourses);
        document.getElementById('category').addEventListener('change', filterCourses);

        function filterCourses() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const statusFilter = document.getElementById('status').value;
            const categoryFilter = document.getElementById('category').value;
            
            const courseCards = document.querySelectorAll('.bg-white.rounded-lg.shadow-md');
            
            courseCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const description = card.querySelector('p').textContent.toLowerCase();
                const status = card.querySelector('.px-2.py-1').textContent.toLowerCase();
                const category = card.querySelector('.text-sm.text-gray-500').textContent.toLowerCase();
                
                const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
                const matchesStatus = statusFilter === 'all' || status.includes(statusFilter);
                const matchesCategory = categoryFilter === 'all' || category.includes(categoryFilter);
                
                if (matchesSearch && matchesStatus && matchesCategory) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
