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
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium hidden lg:block transition-colors">SkillSpark Business</a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium hidden lg:block transition-colors">Teach on SkillSpark</a>
                    <a href="#" class="text-black hover:text-[#0E447A] font-medium hidden lg:block transition-colors">My Learning</a>
                    
                    <!-- Icons -->
                    <button class="p-2 text-gray-600 hover:text-[#0E447A] transition-colors relative">
                        <i class="fas fa-heart text-xl"></i>
                    </button>
                    <button class="p-2 text-gray-600 hover:text-[#0E447A] transition-colors relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </button>
                    <button class="p-2 text-gray-600 hover:text-[#0E447A] transition-colors relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-[#0E447A] rounded-full"></span>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="relative group">
                        <button class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm hover:bg-[#0E447A] transition-colors">
                            YM
                        </button>
                        <!-- User Dropdown -->
                        <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payment Methods</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Help & Support</a>
                                <hr class="my-2">
                                <a href="index.html" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign Out</a>
                            </div>
                        </div>
                    </div>
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
                    YM
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Welcome back, yahiaoui</h1>
                    <a href="#" class="text-[#0E447A] hover:underline font-medium">Add occupation and interests</a>
                </div>
            </div>

            <!-- What to learn next section -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">What to learn next</h2>
                <h3 class="text-lg text-gray-600 mb-6">Trending courses</h3>
                
                <!-- Course Carousel -->
                <div class="relative">
                    <div class="flex space-x-6 overflow-x-auto pb-4 px-4" id="course-carousel">
                        <!-- Course Card 1 -->
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
                                <h4 class="font-bold text-gray-800 mb-2 text-sm">100 Days of Code: The Complete Python Pro...</h4>
                                <p class="text-sm text-gray-600 mb-2">Dr. Angela Yu, Developer and Lead...</p>
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">4.7 (392,406)</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg font-bold text-gray-800">$10.99</span>
                                        <span class="text-sm text-gray-500 line-through">$64.99</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">Premium</span>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Card 2 -->
                        <div class="flex-shrink-0 w-80 bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="h-48 bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center relative">
                                <div class="text-center text-white">
                                    <i class="fas fa-robot text-6xl mb-2 opacity-20"></i>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-robot text-2xl text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <h4 class="font-bold text-gray-800 mb-2 text-sm">The Complete Agentic AI Engineering Course (2025)</h4>
                                <p class="text-sm text-gray-600 mb-2">Ed Donner, Ligency Team</p>
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">4.7 (14,746)</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg font-bold text-gray-800">$9.99</span>
                                        <span class="text-sm text-gray-500 line-through">$44.99</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">Premium</span>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Card 3 -->
                        <div class="flex-shrink-0 w-80 bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="h-48 bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center relative">
                                <div class="text-center text-white">
                                    <i class="fas fa-laptop-code text-6xl mb-2 opacity-20"></i>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-laptop-code text-2xl text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <h4 class="font-bold text-gray-800 mb-2 text-sm">The Complete Full-Stack Web Development Bootcamp</h4>
                                <p class="text-sm text-gray-600 mb-2">Dr. Angela Yu, Developer and Lead...</p>
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">4.7 (452,870)</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg font-bold text-gray-800">$10.99</span>
                                        <span class="text-sm text-gray-500 line-through">$64.99</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">Premium</span>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Card 4 -->
                        <div class="flex-shrink-0 w-80 bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="h-48 bg-gradient-to-br from-orange-500 to-yellow-600 flex items-center justify-center relative">
                                <div class="text-center text-white">
                                    <i class="fas fa-cloud text-6xl mb-2 opacity-20"></i>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-cloud text-2xl text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <h4 class="font-bold text-gray-800 mb-2 text-sm">[NEW] Ultimate AWS Certified Cloud Practitioner CLF-C02...</h4>
                                <p class="text-sm text-gray-600 mb-2">Stephane Maarek | AWS Certified Cloud...</p>
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">4.7 (264,864)</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg font-bold text-gray-800">$15.99</span>
                                        <span class="text-sm text-gray-500 line-through">$89.99</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">Premium</span>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Card 5 -->
                        <div class="flex-shrink-0 w-80 bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="h-48 bg-gradient-to-br from-orange-500 to-yellow-600 flex items-center justify-center relative">
                                <div class="text-center text-white">
                                    <i class="fas fa-cloud text-6xl mb-2 opacity-20"></i>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-cloud text-2xl text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <h4 class="font-bold text-gray-800 mb-2 text-sm">Ultimate AWS Certified Solutions Architect Associate...</h4>
                                <p class="text-sm text-gray-600 mb-2">Stephane Maarek | AWS Certified Cloud...</p>
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">4.7 (269,422)</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg font-bold text-gray-800">$15.99</span>
                                        <span class="text-sm text-gray-500 line-through">$89.99</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">Premium</span>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Bestseller</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Arrow -->
                    <button class="absolute right-0 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-right text-gray-600"></i>
                    </button>
                </div>
            </div>

            <!-- Short and sweet courses section -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Short and sweet courses for you</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Course Card 1 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center relative">
                            <div class="text-center text-white">
                                <i class="fas fa-chart-line text-6xl mb-2 opacity-20"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-line text-2xl text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-800 mb-2">Data Science Fundamentals</h4>
                            <p class="text-sm text-gray-600 mb-2">Learn the basics of data science</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-800">$29.99</span>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">New</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Card 2 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center relative">
                            <div class="text-center text-white">
                                <i class="fas fa-palette text-6xl mb-2 opacity-20"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-palette text-2xl text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-800 mb-2">UI/UX Design Masterclass</h4>
                            <p class="text-sm text-gray-600 mb-2">Master the art of user interface design</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-800">$39.99</span>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Popular</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Card 3 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center relative">
                            <div class="text-center text-white">
                                <i class="fas fa-mobile-alt text-6xl mb-2 opacity-20"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-mobile-alt text-2xl text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-800 mb-2">Mobile App Development</h4>
                            <p class="text-sm text-gray-600 mb-2">Build amazing mobile applications</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-800">$49.99</span>
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">Premium</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Learning Section -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Continue your learning</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Learning Card 1 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center relative">
                            <div class="text-center text-white">
                                <i class="fas fa-play-circle text-6xl mb-2 opacity-20"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-play-circle text-2xl text-white"></i>
                                </div>
                            </div>
                            <div class="absolute bottom-4 right-4">
                                <div class="bg-white bg-opacity-20 rounded-full px-3 py-1">
                                    <span class="text-white text-sm font-medium">65% Complete</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-800 mb-2">JavaScript Fundamentals</h4>
                            <p class="text-sm text-gray-600 mb-4">Learn the basics of JavaScript programming</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="bg-[#0E447A] h-2 rounded-full" style="width: 65%"></div>
                            </div>
                            <p class="text-sm text-gray-600">13 of 20 lessons completed</p>
                        </div>
                    </div>

                    <!-- Learning Card 2 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center relative">
                            <div class="text-center text-white">
                                <i class="fas fa-database text-6xl mb-2 opacity-20"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-database text-2xl text-white"></i>
                                </div>
                            </div>
                            <div class="absolute bottom-4 right-4">
                                <div class="bg-white bg-opacity-20 rounded-full px-3 py-1">
                                    <span class="text-white text-sm font-medium">30% Complete</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-800 mb-2">Database Design</h4>
                            <p class="text-sm text-gray-600 mb-4">Master database design principles</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="bg-[#0E447A] h-2 rounded-full" style="width: 30%"></div>
                            </div>
                            <p class="text-sm text-gray-600">6 of 20 lessons completed</p>
                        </div>
                    </div>

                    <!-- Learning Card 3 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-green-500 to-blue-600 flex items-center justify-center relative">
                            <div class="text-center text-white">
                                <i class="fas fa-check-circle text-6xl mb-2 opacity-20"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check-circle text-2xl text-white"></i>
                                </div>
                            </div>
                            <div class="absolute bottom-4 right-4">
                                <div class="bg-white bg-opacity-20 rounded-full px-3 py-1">
                                    <span class="text-white text-sm font-medium">Completed</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-800 mb-2">HTML & CSS Basics</h4>
                            <p class="text-sm text-gray-600 mb-4">Learn web development fundamentals</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                            <p class="text-sm text-gray-600">Certificate earned!</p>
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

        // Course carousel functionality
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('course-carousel');
            const scrollAmount = 320; // Width of one course card + gap

            // Add scroll functionality to the arrow button
            const arrowButton = document.querySelector('.absolute.right-0 button');
            if (arrowButton) {
                arrowButton.addEventListener('click', function() {
                    carousel.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                });
            }

            // Add hover effects to course cards
            const courseCards = document.querySelectorAll('.bg-white.rounded-lg.shadow-md');
            courseCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>

</html>
