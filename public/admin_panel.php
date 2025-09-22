<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if user is admin
if($_SESSION['role'] !== 'admin') {
    header('Location: student-dashboard.php');
    exit();
}

$user = $auth->getCurrentUser();
$database = new Database();
$conn = $database->getConnection();

// Get admin statistics
$stats = [
    'total_users' => 0,
    'total_courses' => 0,
    'total_enrollments' => 0,
    'total_earnings' => 0,
    'recent_users' => [],
    'recent_courses' => [],
    'pending_support' => 0
];

try {
    // Total users
    $query = "SELECT COUNT(*) as count FROM users";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_users'] = $result['count'];

    // Total courses
    $query = "SELECT COUNT(*) as count FROM courses";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_courses'] = $result['count'];

    // Total enrollments
    $query = "SELECT COUNT(*) as count FROM enrollments";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_enrollments'] = $result['count'];

    // Total earnings
    $query = "SELECT SUM(amount) as total FROM instructor_earnings";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_earnings'] = $result['total'] ?? 0;

    // Recent users
    $query = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['recent_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent courses
    $query = "SELECT c.*, u.first_name, u.last_name 
              FROM courses c 
              JOIN users u ON c.instructor_id = u.id 
              ORDER BY c.created_at DESC LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['recent_courses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pending support tickets
    $query = "SELECT COUNT(*) as count FROM support_tickets WHERE status = 'open'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['pending_support'] = $result['count'];

} catch (PDOException $e) {
    $error_message = 'Error loading admin data: ' . $e->getMessage();
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
    <title>Admin Panel - SkillSpark</title>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="admin-dashboard.php" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Admin Panel</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="admin-dashboard.php" class="text-gray-600 hover:text-gray-900">
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
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_users']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-book text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Courses</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_courses']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-graduation-cap text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Enrollments</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_enrollments']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-dollar-sign text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Earnings</p>
                        <p class="text-2xl font-bold text-gray-900">$<?php echo number_format($stats['total_earnings'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <a href="admin_users.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Manage Users</h3>
                        <p class="text-sm text-gray-600">View and manage all users</p>
                    </div>
                </div>
            </a>

            <a href="admin_courses.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-book text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Manage Courses</h3>
                        <p class="text-sm text-gray-600">Review and approve courses</p>
                    </div>
                </div>
            </a>

            <a href="admin_support.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-life-ring text-2xl text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Support Tickets</h3>
                        <p class="text-sm text-gray-600">Handle support requests</p>
                        <?php if ($stats['pending_support'] > 0): ?>
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                                <?php echo $stats['pending_support']; ?> pending
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>

            <a href="admin_analytics.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-chart-bar text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Analytics</h3>
                        <p class="text-sm text-gray-600">Platform statistics</p>
                    </div>
                </div>
            </a>

            <a href="admin_categories.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-tags text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Categories</h3>
                        <p class="text-sm text-gray-600">Manage course categories</p>
                    </div>
                </div>
            </a>

            <a href="admin_settings.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <i class="fas fa-cog text-2xl text-gray-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Settings</h3>
                        <p class="text-sm text-gray-600">Platform configuration</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Users</h3>
                <?php if (empty($stats['recent_users'])): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-users text-4xl mb-4"></i>
                        <p>No users found</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($stats['recent_users'] as $user): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-1 rounded-full <?php echo $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : ($user['role'] === 'business' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                                <p class="text-sm text-gray-500 mt-1"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Courses -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Courses</h3>
                <?php if (empty($stats['recent_courses'])): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-book text-4xl mb-4"></i>
                        <p>No courses found</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($stats['recent_courses'] as $course): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($course['title']); ?></p>
                                <p class="text-sm text-gray-600">by <?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?></p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-1 rounded-full <?php echo $course['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo ucfirst($course['status']); ?>
                                </span>
                                <p class="text-sm text-gray-500 mt-1">$<?php echo $course['price']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
