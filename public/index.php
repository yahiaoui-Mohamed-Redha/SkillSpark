<?php
require_once '../config/auth.php';

$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    $auth->redirectByRole();
}

$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = 'Please fill in all fields';
    } else {
        $result = $auth->login($email, $password);

        if ($result['success']) {
            $auth->redirectByRole();
        } else {
            $error_message = $result['message'];
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
    <title>SkilSpark Platform - LMS</title>
</head>

<body class="bg-gray-50">
    <!-- Header Section -->
    <section id="header" class="bg-white sticky top-0 z-50 transition-shadow duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-1 lg:px-2">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <img src="../public/assist/logo2.png" alt="Assist Logo" class="h-12 w-auto">
                    </div>
                </div>

                <!-- Navigation Links with Dropdown -->
                <div class="hidden md:flex items-center space-x-6">
                    <div class="relative group">
                        <button class="text-black hover:text-[#0E447A] font-medium transition-colors flex items-center bg-transparent border-none cursor-pointer">
                            Explore
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="absolute top-full left-0 mt-1 w-64 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Categories</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Development</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Business</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Design</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Marketing</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Data Science</a>
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
                        <input type="text" placeholder="Search for anything"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]">
                    </div>
                </div>

                <!-- Right side navigation -->
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium hidden lg:block transition-colors">Plans & Pricing</a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium hidden lg:block transition-colors">Business</a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium hidden lg:block transition-colors">Teach on Assist</a>

                    <!-- Buttons -->
                    <button class="px-4 py-2 bg-[#FCA41A] text-white rounded-md hover:bg-[#e6930f] font-medium transition-colors">
                        Sign up
                    </button>

                    <!-- Language Icon -->
                    <button class="p-2 border border-gray-300 rounded-md hover:border-[#0E447A] hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6.371c0 4.418 -2.239 6.629 -5 6.629" />
                            <path d="M4 6.371h7" />
                            <path d="M5 9c0 2.144 2.252 3.908 6 4" />
                            <path d="M12 20l4 -9l4 9" />
                            <path d="M19.1 18h-6.2" />
                            <path d="M6.694 3l.793 .582" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Hero Section with Login Form -->
    <section class="bg-gradient-to-b from-[#0E447A] via-[#0E447A] via-[#0E447A] via-[#398bd2] to-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left side - Content -->
                <div class="text-white">
                    <h1 class="text-5xl font-bold mb-6">Ready to reimagine your career?</h1>
                    <p class="text-xl mb-8">Get the skills and real-world experience employers want with Career Accelerators.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="bg-[#FCA41A] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#e6930f] transition-colors">
                            Start Learning Today
                        </button>
                        <button class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-[#0E447A] transition-colors">
                            Explore Courses
                        </button>
                    </div>
                </div>

                <!-- Right side - Login Form -->
                <div class="bg-white rounded-lg shadow-2xl p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Welcome Back</h2>

                    <?php if($error_message): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <?php if($success_message): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form action="" method="post" class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                placeholder="Enter your password">
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-[#0E447A] focus:ring-[#0E447A]">
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                            <a href="#" class="text-sm text-[#0E447A] hover:underline">Forgot password?</a>
                        </div>

                        <button type="submit" name="login" class="w-full bg-[#0E447A] text-white py-3 rounded-lg font-semibold hover:bg-[#1e5a96] transition-colors">
                            Sign In
                        </button>

                        <!-- Google Login Button -->
                        <button type="button" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                            </svg>
                            Continue with Google
                        </button>
                    </form>

                    <p class="text-center text-sm text-gray-600 mt-6">
                        Don't have an account?
                        <a href="register.php" class="text-[#0E447A] hover:underline font-medium">Sign up</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sponsors Bar -->
    <section class="bg-gray-50 py-8 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <p class="text-gray-600 text-sm">Trusted by over 17,000 companies and millions of learners around the world</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 items-center justify-items-center">
                <div class="flex items-center justify-center">
                    <img src="../public/assist/sponsors/logo-miclat.png" alt="" class="h-16 w-auto grayscale hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="flex items-center justify-center">
                    <img src="../public/assist/sponsors/Samsung.png" alt="Samsung" class="h-20 w-auto grayscale hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="flex items-center justify-center">
                    <img src="../public/assist/sponsors/download.png" alt="Cisco" class="h-14 w-auto grayscale hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="flex items-center justify-center">
                    <img src="../public/assist/sponsors/HpZ4m7OD_400x400.jpg" alt="" class="h-16 w-auto grayscale hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="flex items-center justify-center">
                    <img src="../public/assist/sponsors/skills_olympic-logo.png" alt="" class="h-16 w-auto grayscale hover:grayscale-0 transition-all duration-300">
                </div>
            </div>
        </div>
    </section>

    <!-- Course Categories Navigation -->
    <section class="bg-white py-6 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-8 overflow-x-auto">
                <a href="#" class="text-[#0E447A] font-semibold border-b-2 border-[#0E447A] pb-2 whitespace-nowrap">Data Science</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium pb-2 whitespace-nowrap">IT Certifications</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium pb-2 whitespace-nowrap">Leadership</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium pb-2 whitespace-nowrap">Web Development</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium pb-2 whitespace-nowrap">Communication</a>
                <a href="#" class="text-gray-600 hover:text-[#0E447A] font-medium pb-2 whitespace-nowrap">Business Analytics & Intelligence</a>
            </div>
        </div>
    </section>

    <!-- Course Topics Filters -->
    <section class="bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-4 flex-wrap gap-2">
                <button class="filter-btn active bg-[#0E447A] text-white px-6 py-2 rounded-full font-medium whitespace-nowrap transition-all duration-200" data-filter="all">
                    All Courses <span class="text-sm opacity-90">17M+ learners</span>
                </button>
                <button class="filter-btn active bg-[#0E447A] text-white px-6 py-2 rounded-full font-medium hover:bg-gray-200 whitespace-nowrap transition-all duration-200" data-filter="chatgpt">
                    ChatGPT <span class="text-sm opacity-90">5M+ learners</span>
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-200 whitespace-nowrap transition-all duration-200" data-filter="data-science">
                    Data Science <span class="text-sm text-gray-500">8M+ learners</span>
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-200 whitespace-nowrap transition-all duration-200" data-filter="python">
                    Python <span class="text-sm text-gray-500">49.9M+ learners</span>
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-200 whitespace-nowrap transition-all duration-200" data-filter="machine-learning">
                    Machine Learning <span class="text-sm text-gray-500">9M+ learners</span>
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-200 whitespace-nowrap transition-all duration-200" data-filter="deep-learning">
                    Deep Learning <span class="text-sm text-gray-500">2M+ learners</span>
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-200 whitespace-nowrap transition-all duration-200" data-filter="ai">
                    Artificial Intelligence (AI) <span class="text-sm text-gray-500">4M+ learners</span>
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-200 whitespace-nowrap transition-all duration-200" data-filter="statistics">
                    Statistics <span class="text-sm text-gray-500">1M+ learners</span>
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 px-6 py-2 rounded-full font-medium hover:bg-gray-200 whitespace-nowrap transition-all duration-200" data-filter="nlp">
                    Natural Language Processing <span class="text-sm text-gray-500">868,500+ learners</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Featured Courses Section -->
    <section class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Featured Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="courses-grid">
                <!-- Course Card 1 -->
                <div class="course-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300" data-category="chatgpt ai">
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-robot text-4xl mb-2"></i>
                            <p class="text-sm font-medium">AI & ChatGPT</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 mb-2">The Complete AI Guide: Learn ChatGPT, Generative AI & More</h3>
                        <p class="text-sm text-gray-600 mb-2">Julian Melanson, Benza Maman</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="ml-2 text-sm text-gray-600">4.5 (53,056)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-800">$54.99</span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                        </div>
                    </div>
                </div>

                <!-- Course Card 2 -->
                <div class="course-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300" data-category="chatgpt">
                    <div class="h-48 bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-brain text-4xl mb-2"></i>
                            <p class="text-sm font-medium">ChatGPT Course</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 mb-2">ChatGPT: Complete ChatGPT Course For Work 2025 (Ethically)!</h3>
                        <p class="text-sm text-gray-600 mb-2">Steve Ballinger, MBA</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="ml-2 text-sm text-gray-600">4.5 (119,742)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-800">$59.99</span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                        </div>
                    </div>
                </div>

                <!-- Course Card 3 -->
                <div class="course-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300" data-category="ai machine-learning">
                    <div class="h-48 bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-bullhorn text-4xl mb-2"></i>
                            <p class="text-sm font-medium">AI Marketing</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 mb-2">ChatGPT, DeepSeek, Grok and 30+ More AI Marketing Assistants</h3>
                        <p class="text-sm text-gray-600 mb-2">Anton Voroniuk</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="ml-2 text-sm text-gray-600">4.3 (901)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-800">$34.99</span>
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">Premium</span>
                        </div>
                    </div>
                </div>

                <!-- Course Card 4 -->
                <div class="course-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300" data-category="ai chatgpt">
                    <div class="h-48 bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-graduation-cap text-4xl mb-2"></i>
                            <p class="text-sm font-medium">Complete Guide</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 mb-2">ChatGPT & Generative AI - The Complete Guide</h3>
                        <p class="text-sm text-gray-600 mb-2">Academind by Maximilian Schwarzmüller</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="ml-2 text-sm text-gray-600">4.6 (26,603)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-800">$59.99</span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Course Cards for other categories -->
                <div class="course-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300" data-category="data-science python">
                    <div class="h-48 bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-chart-line text-4xl mb-2"></i>
                            <p class="text-sm font-medium">Data Science</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 mb-2">Python for Data Science and Machine Learning Bootcamp</h3>
                        <p class="text-sm text-gray-600 mb-2">Jose Portilla</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="ml-2 text-sm text-gray-600">4.6 (89,234)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-800">$89.99</span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                        </div>
                    </div>
                </div>

                <div class="course-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300" data-category="machine-learning deep-learning">
                    <div class="h-48 bg-gradient-to-br from-teal-500 to-green-600 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-brain text-4xl mb-2"></i>
                            <p class="text-sm font-medium">Deep Learning</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 mb-2">Deep Learning A-Z: Hands-On Artificial Neural Networks</h3>
                        <p class="text-sm text-gray-600 mb-2">Kirill Eremenko, Hadelin de Ponteves</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="ml-2 text-sm text-gray-600">4.4 (45,123)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-800">$94.99</span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                        </div>
                    </div>
                </div>

                <div class="course-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300" data-category="statistics">
                    <div class="h-48 bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-chart-bar text-4xl mb-2"></i>
                            <p class="text-sm font-medium">Statistics</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 mb-2">Statistics for Data Science and Business Analysis</h3>
                        <p class="text-sm text-gray-600 mb-2">365 Careers</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="ml-2 text-sm text-gray-600">4.5 (12,456)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-800">$49.99</span>
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Popular</span>
                        </div>
                    </div>
                </div>

                <div class="course-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300" data-category="nlp">
                    <div class="h-48 bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-language text-4xl mb-2"></i>
                            <p class="text-sm font-medium">NLP</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 mb-2">Natural Language Processing with Python</h3>
                        <p class="text-sm text-gray-600 mb-2">Lazy Programmer Inc.</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="ml-2 text-sm text-gray-600">4.3 (8,234)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-800">$69.99</span>
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">Premium</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Career Accelerators Section -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Ready to reimagine your career?</h2>
                <p class="text-xl text-gray-600">Get the skills and real-world experience employers want with Career Accelerators.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- Career Card 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-orange-400 to-yellow-500 flex items-center justify-center relative">
                        <i class="fas fa-code text-6xl text-white opacity-20"></i>
                        <div class="absolute top-4 left-4">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-code text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <img src="../public/assist/logo2.png" alt="Instructor" class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h3 class="font-bold text-gray-800">Full Stack Web Developer</h3>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span>4.7</span>
                            </div>
                            <span class="bg-gray-100 px-2 py-1 rounded">458K ratings</span>
                            <span class="bg-gray-100 px-2 py-1 rounded">87.8 total hours</span>
                        </div>
                    </div>
                </div>

                <!-- Career Card 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center relative">
                        <i class="fas fa-mobile-alt text-6xl text-white opacity-20"></i>
                        <div class="absolute top-4 left-4">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-mobile-alt text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <img src="../public/assist/logo2.png" alt="Instructor" class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h3 class="font-bold text-gray-800">Digital Marketer</h3>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span>4.5</span>
                            </div>
                            <span class="bg-gray-100 px-2 py-1 rounded">3.6K ratings</span>
                            <span class="bg-gray-100 px-2 py-1 rounded">28.4 total hours</span>
                        </div>
                    </div>
                </div>

                <!-- Career Card 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center relative">
                        <i class="fas fa-chart-bar text-6xl text-white opacity-20"></i>
                        <div class="absolute top-4 left-4">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-chart-bar text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <img src="../public/assist/logo2.png" alt="Instructor" class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h3 class="font-bold text-gray-800">Data Scientist</h3>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span>4.6</span>
                            </div>
                            <span class="bg-gray-100 px-2 py-1 rounded">221K ratings</span>
                            <span class="bg-gray-100 px-2 py-1 rounded">47.1 total hours</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button class="bg-white border-2 border-[#0E447A] text-[#0E447A] px-8 py-3 rounded-lg font-semibold hover:bg-[#0E447A] hover:text-white transition-colors">
                    All Career Accelerators
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="w-full bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!--Grid-->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 md:gap-8 py-10 max-sm:max-w-sm max-sm:mx-auto gap-y-8">
                <div class="col-span-full mb-10 lg:col-span-2 lg:mb-0">
                    <a href="#" class="flex justify-center lg:justify-start">
                        <img src="../public/assist/logo2.png" alt="SkillSpark Logo" class="h-16 w-auto">
                    </a>
                    <p class="py-8 text-sm text-gray-500 lg:max-w-xs text-center lg:text-left">Trusted in more than 100 countries & 5 million customers. Have any query ?</p>
                    <a href="javascript:;" class="py-2.5 px-5 h-9 block w-fit bg-[#0E447A] rounded-full shadow-sm text-xs text-white mx-auto transition-all duration-500 hover:bg-[#1e5a96] lg:mx-0">
                        Contact us
                    </a>
                </div>
                <!--End Col-->
                <div class="lg:mx-auto text-left ">
                    <h4 class="text-lg text-black font-medium mb-7">SkillSpark</h4>
                    <ul class="text-sm transition-all duration-500">
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Home</a></li>
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">About</a></li>
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Pricing</a></li>
                        <li><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Features</a></li>
                    </ul>
                </div>
                <!--End Col-->
                <div class="lg:mx-auto text-left ">
                    <h4 class="text-lg text-black font-medium mb-7">Courses</h4>
                    <ul class="text-sm transition-all duration-500">
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Web Development</a></li>
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Data Science</a></li>
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">AI & Machine Learning</a></li>
                        <li><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Business Analytics</a></li>
                    </ul>
                </div>
                <!--End Col-->
                <div class="lg:mx-auto text-left">
                    <h4 class="text-lg text-black font-medium mb-7">Resources</h4>
                    <ul class="text-sm transition-all duration-500">
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">FAQs</a></li>
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Quick Start</a></li>
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Documentation</a></li>
                        <li><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">User Guide</a></li>
                    </ul>
                </div>
                <!--End Col-->
                <div class="lg:mx-auto text-left">
                    <h4 class="text-lg text-black font-medium mb-7">Blogs</h4>
                    <ul class="text-sm transition-all duration-500">
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">News</a></li>
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Tips & Tricks</a></li>
                        <li class="mb-6"><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">New Updates</a></li>
                        <li><a href="javascript:;" class="text-gray-600 hover:text-[#0E447A]">Events</a></li>
                    </ul>
                </div>
            </div>
            <!--Grid-->
            <div class="py-7 border-t border-gray-200">
                <div class="flex items-center justify-center flex-col lg:justify-between lg:flex-row">
                    <span class="text-sm text-gray-500">©<a href="#" class="text-[#0E447A]">SkillSpark</a> 2025, All rights reserved - By Yahiaoui Mohamed Redha.</span>
                    <div class="flex mt-4 space-x-4 sm:justify-center lg:mt-0 ">
                        <a href="javascript:;" class="w-9 h-9 rounded-full bg-black flex justify-center items-center hover:bg-[#0E447A]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <g id="Social Media">
                                    <path id="Vector" d="M11.3214 8.93666L16.4919 3.05566H15.2667L10.7772 8.16205L7.1914 3.05566H3.05566L8.47803 10.7774L3.05566 16.9446H4.28097L9.022 11.552L12.8088 16.9446H16.9446L11.3211 8.93666H11.3214ZM9.64322 10.8455L9.09382 10.0765L4.72246 3.95821H6.60445L10.1322 8.8959L10.6816 9.66481L15.2672 16.083H13.3852L9.64322 10.8458V10.8455Z" fill="white" />
                                </g>
                            </svg>
                        </a>
                        <a href="javascript:;" class="w-9 h-9 rounded-full bg-black flex justify-center items-center hover:bg-[#0E447A]">
                            <svg class="w-[1.25rem] h-[1.125rem] text-white" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.70975 7.93663C4.70975 6.65824 5.76102 5.62163 7.0582 5.62163C8.35537 5.62163 9.40721 6.65824 9.40721 7.93663C9.40721 9.21502 8.35537 10.2516 7.0582 10.2516C5.76102 10.2516 4.70975 9.21502 4.70975 7.93663ZM3.43991 7.93663C3.43991 9.90608 5.05982 11.5025 7.0582 11.5025C9.05658 11.5025 10.6765 9.90608 10.6765 7.93663C10.6765 5.96719 9.05658 4.37074 7.0582 4.37074C5.05982 4.37074 3.43991 5.96719 3.43991 7.93663ZM9.97414 4.22935C9.97408 4.39417 10.0236 4.55531 10.1165 4.69239C10.2093 4.82946 10.3413 4.93633 10.4958 4.99946C10.6503 5.06259 10.8203 5.07916 10.9844 5.04707C11.1484 5.01498 11.2991 4.93568 11.4174 4.81918C11.5357 4.70268 11.6163 4.55423 11.649 4.39259C11.6817 4.23095 11.665 4.06339 11.6011 3.91109C11.5371 3.7588 11.4288 3.6286 11.2898 3.53698C11.1508 3.44536 10.9873 3.39642 10.8201 3.39635H10.8197C10.5955 3.39646 10.3806 3.48424 10.222 3.64043C10.0635 3.79661 9.97434 4.00843 9.97414 4.22935ZM4.21142 13.5892C3.52442 13.5584 3.15101 13.4456 2.90286 13.3504C2.57387 13.2241 2.33914 13.0738 2.09235 12.8309C1.84555 12.588 1.69278 12.3569 1.56527 12.0327C1.46854 11.7882 1.3541 11.4201 1.32287 10.7431C1.28871 10.0111 1.28189 9.79119 1.28189 7.93669C1.28189 6.08219 1.28927 5.86291 1.32287 5.1303C1.35416 4.45324 1.46944 4.08585 1.56527 3.84069C1.69335 3.51647 1.84589 3.28513 2.09235 3.04191C2.3388 2.79869 2.57331 2.64813 2.90286 2.52247C3.1509 2.42713 3.52442 2.31435 4.21142 2.28358C4.95417 2.24991 5.17729 2.24319 7.0582 2.24319C8.9391 2.24319 9.16244 2.25047 9.90582 2.28358C10.5928 2.31441 10.9656 2.42802 11.2144 2.52247C11.5434 2.64813 11.7781 2.79902 12.0249 3.04191C12.2717 3.2848 12.4239 3.51647 12.552 3.84069C12.6487 4.08513 12.7631 4.45324 12.7944 5.1303C12.8285 5.86291 12.8354 6.08219 12.8354 7.93669C12.8354 9.79119 12.8285 10.0105 12.7944 10.7431C12.7631 11.4201 12.6481 11.7881 12.552 12.0327C12.4239 12.3569 12.2714 12.5882 12.0249 12.8309C11.7784 13.0736 11.5434 13.2241 11.2144 13.3504C10.9663 13.4457 10.5928 13.5585 9.90582 13.5892C9.16306 13.6229 8.93994 13.6296 7.0582 13.6296C5.17645 13.6296 4.95395 13.6229 4.21142 13.5892ZM4.15307 1.03424C3.40294 1.06791 2.89035 1.18513 2.4427 1.3568C1.9791 1.53408 1.58663 1.77191 1.19446 2.1578C0.802277 2.54369 0.56157 2.93108 0.381687 3.38797C0.207498 3.82941 0.0885535 4.3343 0.0543922 5.07358C0.0196672 5.81402 0.0117188 6.05074 0.0117188 7.93663C0.0117188 9.82252 0.0196672 10.0592 0.0543922 10.7997C0.0885535 11.539 0.207498 12.0439 0.381687 12.4853C0.56157 12.9419 0.802334 13.3297 1.19446 13.7155C1.58658 14.1012 1.9791 14.3387 2.4427 14.5165C2.89119 14.6881 3.40294 14.8054 4.15307 14.839C4.90479 14.8727 5.1446 14.8811 7.0582 14.8811C8.9718 14.8811 9.212 14.8732 9.96332 14.839C10.7135 14.8054 11.2258 14.6881 11.6737 14.5165C12.137 14.3387 12.5298 14.1014 12.9219 13.7155C13.3141 13.3296 13.5543 12.9419 13.7347 12.4853C13.9089 12.0439 14.0284 11.539 14.062 10.7997C14.0962 10.0587 14.1041 9.82252 14.1041 7.93663C14.1041 6.05074 14.0962 5.81402 14.062 5.07358C14.0278 4.33424 13.9089 3.82913 13.7347 3.38797C13.5543 2.93135 13.3135 2.5443 12.9219 2.1578C12.5304 1.7713 12.137 1.53408 11.6743 1.3568C11.2258 1.18513 10.7135 1.06735 9.96388 1.03424C9.21256 1.00058 8.97236 0.992188 7.05876 0.992188C5.14516 0.992188 4.90479 1.00002 4.15307 1.03424Z" fill="currentColor" />
                            </svg>

                        </a>
                        <a href="javascript:;" class="w-9 h-9 rounded-full bg-black flex justify-center items-center hover:bg-[#0E447A]">
                            <svg class="w-[1rem] h-[1rem] text-white" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.8794 11.5527V3.86835H0.318893V11.5527H2.87967H2.8794ZM1.59968 2.81936C2.4924 2.81936 3.04817 2.2293 3.04817 1.49188C3.03146 0.737661 2.4924 0.164062 1.61666 0.164062C0.74032 0.164062 0.167969 0.737661 0.167969 1.49181C0.167969 2.22923 0.723543 2.8193 1.5829 2.8193H1.59948L1.59968 2.81936ZM4.29668 11.5527H6.85698V7.26187C6.85698 7.03251 6.87369 6.80255 6.94134 6.63873C7.12635 6.17968 7.54764 5.70449 8.25514 5.70449C9.18141 5.70449 9.55217 6.4091 9.55217 7.44222V11.5527H12.1124V7.14672C12.1124 4.78652 10.8494 3.68819 9.16483 3.68819C7.78372 3.68819 7.17715 4.45822 6.84014 4.98267H6.85718V3.86862H4.29681C4.33023 4.5895 4.29661 11.553 4.29661 11.553L4.29668 11.5527Z" fill="currentColor" />
                            </svg>

                        </a>
                        <a href="javascript:;" class="w-9 h-9 rounded-full bg-black flex justify-center items-center hover:bg-[#0E447A]">
                            <svg class="w-[1.25rem] h-[0.875rem] text-white" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.9346 1.13529C14.5684 1.30645 15.0665 1.80588 15.2349 2.43896C15.5413 3.58788 15.5413 5.98654 15.5413 5.98654C15.5413 5.98654 15.5413 8.3852 15.2349 9.53412C15.0642 10.1695 14.5661 10.669 13.9346 10.8378C12.7886 11.1449 8.19058 11.1449 8.19058 11.1449C8.19058 11.1449 3.59491 11.1449 2.44657 10.8378C1.81277 10.6666 1.31461 10.1672 1.14622 9.53412C0.839844 8.3852 0.839844 5.98654 0.839844 5.98654C0.839844 5.98654 0.839844 3.58788 1.14622 2.43896C1.31695 1.80353 1.81511 1.30411 2.44657 1.13529C3.59491 0.828125 8.19058 0.828125 8.19058 0.828125C8.19058 0.828125 12.7886 0.828125 13.9346 1.13529ZM10.541 5.98654L6.72178 8.19762V3.77545L10.541 5.98654Z" fill="currentColor" />
                            </svg>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

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

        // Course Filter Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const courseCards = document.querySelectorAll('.course-card');

            // Add hover effects to course cards
            courseCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Filter functionality
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');

                    // Remove active class from all buttons
                    filterButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-[#0E447A]', 'text-white');
                        btn.classList.add('bg-gray-100', 'text-gray-700');
                    });

                    // Add active class to clicked button
                    this.classList.add('active', 'bg-[#0E447A]', 'text-white');
                    this.classList.remove('bg-gray-100', 'text-gray-700');

                    // Filter course cards
                    courseCards.forEach(card => {
                        const categories = card.getAttribute('data-category').split(' ');

                        if (filter === 'all' || categories.includes(filter)) {
                            card.style.display = 'block';
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';

                            // Animate in
                            setTimeout(() => {
                                card.style.transition = 'all 0.3s ease';
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, 50);
                        } else {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(-20px)';

                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            });

            // Initialize with All Courses filter active
            const allCoursesButton = document.querySelector('[data-filter="all"]');
            if (allCoursesButton) {
                allCoursesButton.click();
            }
        });
    </script>
</body>

</html>