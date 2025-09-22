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

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Get categories
$categories = [];
try {
    $query = "SELECT * FROM categories ORDER BY name";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// Build search query
$where_conditions = ["c.status = 'active'"];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(c.title LIKE :search OR c.description LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($category)) {
    $where_conditions[] = "c.category = :category";
    $params[':category'] = $category;
}

if ($price_filter === 'free') {
    $where_conditions[] = "c.price = 0";
} elseif ($price_filter === 'paid') {
    $where_conditions[] = "c.price > 0";
}

$order_by = "c.created_at DESC";
if ($sort === 'price_low') {
    $order_by = "c.price ASC";
} elseif ($sort === 'price_high') {
    $order_by = "c.price DESC";
} elseif ($sort === 'rating') {
    $order_by = "avg_rating DESC";
}

// Get courses
$courses = [];
try {
    $query = "SELECT c.*, 
              u.first_name, u.last_name,
              COUNT(e.id) as enrollment_count,
              AVG(cr.rating) as avg_rating,
              cm.file_path as cover_image
              FROM courses c 
              LEFT JOIN users u ON c.instructor_id = u.id
              LEFT JOIN enrollments e ON c.id = e.course_id
              LEFT JOIN course_reviews cr ON c.id = cr.course_id
              LEFT JOIN course_media cm ON c.id = cm.course_id AND cm.media_type = 'course_cover'
              WHERE " . implode(' AND ', $where_conditions) . "
              GROUP BY c.id
              ORDER BY " . $order_by;
    
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $courses = [];
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
    <title>Browse Courses - SkillSpark</title>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo $_SESSION['role'] === 'student' ? 'student-dashboard.php' : 'instructor-dashboard.php'; ?>" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Browse Courses</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?php echo $_SESSION['role'] === 'student' ? 'student-dashboard.php' : 'instructor-dashboard.php'; ?>" class="text-gray-600 hover:text-gray-900">
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
        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Search courses...">
                    </div>
                    
                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="category" name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $category === $cat['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Price Filter -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                        <select id="price" name="price" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Prices</option>
                            <option value="free" <?php echo $price_filter === 'free' ? 'selected' : ''; ?>>Free</option>
                            <option value="paid" <?php echo $price_filter === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        </select>
                    </div>
                    
                    <!-- Sort -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select id="sort" name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                            <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-between">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <a href="browse_courses.php" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
                        <i class="fas fa-refresh mr-2"></i>Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Results Count -->
        <div class="mb-6">
            <p class="text-gray-600">
                Found <?php echo count($courses); ?> course<?php echo count($courses) !== 1 ? 's' : ''; ?>
                <?php if (!empty($search)): ?>
                    for "<?php echo htmlspecialchars($search); ?>"
                <?php endif; ?>
            </p>
        </div>

        <!-- Courses Grid -->
        <?php if (empty($courses)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Courses Found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your search criteria or browse all courses.</p>
                <a href="browse_courses.php" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                    Browse All Courses
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($courses as $course): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Course Image -->
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
                            <div class="h-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <i class="fas fa-book text-6xl text-white"></i>
                            </div>
                        <?php endif; ?>
                        <?php else: ?>
                            <div class="h-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <i class="fas fa-book text-6xl text-white"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Price Badge -->
                        <div class="absolute top-2 right-2">
                            <?php if ($course['price'] == 0): ?>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Free</span>
                            <?php else: ?>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">$<?php echo $course['price']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Course Info -->
                    <div class="p-6">
                        <div class="mb-2">
                            <span class="text-sm text-gray-500"><?php echo htmlspecialchars($course['category']); ?></span>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            <?php echo htmlspecialchars($course['title']); ?>
                        </h3>
                        
                        <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                            <?php echo htmlspecialchars($course['description']); ?>
                        </p>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-user mr-1"></i>
                                <?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-users mr-1"></i>
                                <?php echo $course['enrollment_count']; ?> students
                            </div>
                        </div>
                        
                        <?php if ($course['avg_rating'] > 0): ?>
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <?php 
                                $rating = round($course['avg_rating'], 1);
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
                            <span class="ml-2 text-sm text-gray-600"><?php echo $rating; ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-between items-center">
                            <div class="text-lg font-bold text-gray-900">
                                <?php if ($course['price'] == 0): ?>
                                    <span class="text-green-600">Free</span>
                                <?php else: ?>
                                    $<?php echo $course['price']; ?>
                                <?php endif; ?>
                            </div>
                            <a href="student_course_view.php?course_id=<?php echo $course['id']; ?>" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                View Course
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
