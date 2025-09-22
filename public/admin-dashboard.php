<?php
require_once '../config/auth.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if user is an admin
if($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$user = $auth->getCurrentUser();

// Get database connection for admin queries
require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Get statistics
$stats = [];

// Total users
$query = "SELECT COUNT(*) as total FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_users'] = $stmt->fetch()['total'];

// Total students
$query = "SELECT COUNT(*) as total FROM users WHERE role = 'student'";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_students'] = $stmt->fetch()['total'];

// Total instructors
$query = "SELECT COUNT(*) as total FROM users WHERE role = 'business'";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_instructors'] = $stmt->fetch()['total'];

// Total courses
$query = "SELECT COUNT(*) as total FROM courses";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_courses'] = $stmt->fetch()['total'];

// Total enrollments
$query = "SELECT COUNT(*) as total FROM enrollments";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_enrollments'] = $stmt->fetch()['total'];

// Recent users
$query = "SELECT first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->execute();
$recent_users = $stmt->fetchAll();

// Recent courses
$query = "SELECT c.title, c.price, c.created_at, u.first_name, u.last_name 
          FROM courses c 
          LEFT JOIN users u ON c.instructor_id = u.id 
          ORDER BY c.created_at DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->execute();
$recent_courses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../public/assist/icon.png">
    <title>Admin Dashboard - SkillSpark Platform</title>
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
                        <a href="admin_panel.php" class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center">
                            <i class="fas fa-cog mr-1"></i>
                            Admin Panel
                        </a>
                        <a href="admin_support.php" class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center">
                            <i class="fas fa-life-ring mr-1"></i>
                            Support
                        </a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium transition-colors">Users</a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium transition-colors">Courses</a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium transition-colors">Analytics</a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium transition-colors">Settings</a>
                </div>

                <!-- Right side navigation -->
                <div class="flex items-center space-x-4">
                    <!-- Icons -->
                    <button class="p-2 text-gray-600 hover:text-[#0E447A] transition-colors relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="relative group">
                        <button class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center font-bold text-sm hover:bg-red-700 transition-colors">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                        </button>
                        <!-- User Dropdown -->
                        <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">System Settings</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Security</a>
                                <hr class="my-2">
                                <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign Out</a>
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
            <div class="flex items-center mb-8">
                <div class="w-16 h-16 bg-red-600 text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
                    <p class="text-lg text-gray-600">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?> - Platform Administration</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_users']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-graduation-cap text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Students</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_students']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-chalkboard-teacher text-2xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Instructors</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_instructors']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-full">
                            <i class="fas fa-book text-2xl text-orange-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Courses</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_courses']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-500 rounded-full">
                                <i class="fas fa-user-plus text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Manage Users</h3>
                                <p class="text-sm text-gray-600">View and manage all users</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-500 rounded-full">
                                <i class="fas fa-book text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Manage Courses</h3>
                                <p class="text-sm text-gray-600">Review and approve courses</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 rounded-full">
                                <i class="fas fa-chart-line text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Analytics</h3>
                                <p class="text-sm text-gray-600">View platform analytics</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-500 rounded-full">
                                <i class="fas fa-cog text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Settings</h3>
                                <p class="text-sm text-gray-600">Platform configuration</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Users</h3>
                    <div class="space-y-4">
                        <?php foreach($recent_users as $recent_user): ?>
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($recent_user['first_name'] . ' ' . $recent_user['last_name']); ?></p>
                                <p class="text-xs text-gray-600"><?php echo htmlspecialchars($recent_user['email']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo ucfirst($recent_user['role']); ?> • <?php echo date('M j, Y', strtotime($recent_user['created_at'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Courses -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Courses</h3>
                    <div class="space-y-4">
                        <?php foreach($recent_courses as $recent_course): ?>
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-book text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($recent_course['title']); ?></p>
                                <p class="text-xs text-gray-600">By <?php echo htmlspecialchars($recent_course['first_name'] . ' ' . $recent_course['last_name']); ?></p>
                                <p class="text-xs text-gray-500">$<?php echo $recent_course['price']; ?> • <?php echo date('M j, Y', strtotime($recent_course['created_at'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">System Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Database Connection</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Email Service</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">File Storage</span>
                    </div>
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
