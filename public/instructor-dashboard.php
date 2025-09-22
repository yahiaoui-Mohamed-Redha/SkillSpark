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

// Redirect to instructor account page
header('Location: instructor-account.php');
exit();

$user = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../public/assist/icon.png">
    <title>Instructor Dashboard - SkillSpark Platform</title>
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
                    <div class="relative group">
                        <button class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center bg-transparent border-none cursor-pointer">
                            Explore
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
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
                        <input type="text" placeholder="Search for anything"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]">
                    </div>
                </div>

                <!-- Right side navigation -->
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium hidden lg:block transition-colors">My Courses</a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium hidden lg:block transition-colors">Analytics</a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium hidden lg:block transition-colors">Earnings</a>
                    
                    <!-- Icons -->
                    <button class="p-2 text-gray-600 hover:text-[#0E447A] transition-colors relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-[#0E447A] rounded-full"></span>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="relative group">
                        <button class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm hover:bg-[#0E447A] transition-colors">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                        </button>
                        <!-- User Dropdown -->
                        <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payment Methods</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Help & Support</a>
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
                <div class="w-16 h-16 bg-black text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?></h1>
                    <p class="text-lg text-gray-600">Ready to share your knowledge with the world?</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-book text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Courses</p>
                            <p class="text-2xl font-bold text-gray-900">12</p>
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
                            <p class="text-2xl font-bold text-gray-900">2,847</p>
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
                            <p class="text-2xl font-bold text-gray-900">4.8</p>
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
                            <p class="text-2xl font-bold text-gray-900">$24,567</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#0E447A] rounded-full">
                                <i class="fas fa-plus text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Create New Course</h3>
                                <p class="text-sm text-gray-600">Start building your next course</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 rounded-full">
                                <i class="fas fa-chart-line text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">View Analytics</h3>
                                <p class="text-sm text-gray-600">Track your course performance</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-500 rounded-full">
                                <i class="fas fa-cog text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Course Settings</h3>
                                <p class="text-sm text-gray-600">Manage your courses</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Courses -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Recent Courses</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Course Card 1 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center relative">
                            <div class="text-center text-white">
                                <i class="fas fa-code text-6xl mb-2 opacity-20"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-code text-2xl text-white"></i>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Published</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-800 mb-2">Complete Python Bootcamp</h4>
                            <p class="text-sm text-gray-600 mb-4">Learn Python programming from scratch</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span><i class="fas fa-users mr-1"></i> 1,234 students</span>
                                    <span><i class="fas fa-star mr-1"></i> 4.8</span>
                                </div>
                                <span class="text-lg font-bold text-gray-800">$49.99</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Card 2 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center relative">
                            <div class="text-center text-white">
                                <i class="fas fa-chart-bar text-6xl mb-2 opacity-20"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-bar text-2xl text-white"></i>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded">Draft</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-800 mb-2">Data Science Fundamentals</h4>
                            <p class="text-sm text-gray-600 mb-4">Master data science concepts and tools</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span><i class="fas fa-users mr-1"></i> 0 students</span>
                                    <span><i class="fas fa-star mr-1"></i> -</span>
                                </div>
                                <span class="text-lg font-bold text-gray-800">$59.99</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Card 3 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center relative">
                            <div class="text-center text-white">
                                <i class="fas fa-laptop-code text-6xl mb-2 opacity-20"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-laptop-code text-2xl text-white"></i>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Review</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-800 mb-2">Web Development Masterclass</h4>
                            <p class="text-sm text-gray-600 mb-4">Full-stack web development course</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span><i class="fas fa-users mr-1"></i> 2,156 students</span>
                                    <span><i class="fas fa-star mr-1"></i> 4.9</span>
                                </div>
                                <span class="text-lg font-bold text-gray-800">$79.99</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Recent Activity</h2>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-plus text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">New student enrolled</p>
                                <p class="text-xs text-gray-600">John Doe enrolled in "Complete Python Bootcamp"</p>
                                <p class="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-star text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">New review received</p>
                                <p class="text-xs text-gray-600">5-star review for "Web Development Masterclass"</p>
                                <p class="text-xs text-gray-500">4 hours ago</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Payment received</p>
                                <p class="text-xs text-gray-600">$49.99 from course sale</p>
                                <p class="text-xs text-gray-500">6 hours ago</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Student completed course</p>
                                <p class="text-xs text-gray-600">Sarah Johnson completed "Complete Python Bootcamp"</p>
                                <p class="text-xs text-gray-500">1 day ago</p>
                            </div>
                        </div>
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
