<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../public/assist/icon.png">
    <title>Register - SkillSpark Platform</title>
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

                <!-- Right side navigation -->
                <div class="flex items-center space-x-4">
                    <a href="index.html" class="text-black hover:text-[#0E447A] font-medium transition-colors">Back to Home</a>
                    <a href="index.html" class="text-[#0E447A] hover:underline font-medium transition-colors">Sign In</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Form Section -->
    <section class="bg-gradient-to-b from-[#0E447A] via-[#0E447A] via-[#0E447A] via-[#398bd2] to-gray-50 min-h-screen py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-4">Join SkillSpark Today</h1>
                <p class="text-xl text-white opacity-90">Start your learning journey with us</p>
            </div>

            <!-- Progress Steps -->
            <div class="flex justify-center mb-8">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-white text-[#0E447A] rounded-full flex items-center justify-center font-bold step-indicator active" data-step="1">1</div>
                        <span class="ml-2 text-white font-medium">Role Selection</span>
                    </div>
                    <div class="w-16 h-0.5 bg-white opacity-30"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-white bg-opacity-30 text-white rounded-full flex items-center justify-center font-bold step-indicator" data-step="2">2</div>
                        <span class="ml-2 text-white opacity-70 font-medium">Personal Info</span>
                    </div>
                    <div class="w-16 h-0.5 bg-white opacity-30"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-white bg-opacity-30 text-white rounded-full flex items-center justify-center font-bold step-indicator" data-step="3">3</div>
                        <span class="ml-2 text-white opacity-70 font-medium">Create Account</span>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-lg shadow-2xl p-8">
                <!-- Step 1: Role Selection -->
                <div id="step-1" class="step-content">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Choose Your Role</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Role -->
                        <div class="role-card border-2 border-gray-200 rounded-lg p-6 cursor-pointer hover:border-[#0E447A] transition-all duration-300" data-role="student">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-graduation-cap text-2xl text-[#0E447A]"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Student</h3>
                                <p class="text-gray-600 mb-4">Learn new skills and advance your career with our comprehensive courses</p>
                                <ul class="text-sm text-gray-500 text-left">
                                    <li class="mb-2">• Access to thousands of courses</li>
                                    <li class="mb-2">• Learn at your own pace</li>
                                    <li class="mb-2">• Get certificates upon completion</li>
                                    <li class="mb-2">• Join our learning community</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Business Account Role -->
                        <div class="role-card border-2 border-gray-200 rounded-lg p-6 cursor-pointer hover:border-[#0E447A] transition-all duration-300" data-role="business">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-briefcase text-2xl text-[#0E447A]"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Business Account</h3>
                                <p class="text-gray-600 mb-4">Teach and share your expertise with learners worldwide</p>
                                <ul class="text-sm text-gray-500 text-left">
                                    <li class="mb-2">• Create and sell your courses</li>
                                    <li class="mb-2">• Reach global audience</li>
                                    <li class="mb-2">• Earn from your expertise</li>
                                    <li class="mb-2">• Access teaching tools</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-8">
                        <button id="next-step-1" class="bg-[#0E447A] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#1e5a96] transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Next Step
                        </button>
                    </div>
                </div>

                <!-- Step 2: Personal Information -->
                <div id="step-2" class="step-content hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Personal Information</h2>
                    
                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                <input type="text" id="firstName" name="firstName" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                    placeholder="Enter your first name">
                            </div>
                            <div>
                                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                <input type="text" id="lastName" name="lastName" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                    placeholder="Enter your last name">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                placeholder="Enter your email address">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                placeholder="Enter your phone number">
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                            <select id="country" name="country" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]">
                                <option value="">Select your country</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="UK">United Kingdom</option>
                                <option value="AU">Australia</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                                <option value="ES">Spain</option>
                                <option value="IT">Italy</option>
                                <option value="JP">Japan</option>
                                <option value="CN">China</option>
                                <option value="IN">India</option>
                                <option value="BR">Brazil</option>
                                <option value="MX">Mexico</option>
                                <option value="AR">Argentina</option>
                                <option value="ZA">South Africa</option>
                                <option value="EG">Egypt</option>
                                <option value="NG">Nigeria</option>
                                <option value="KE">Kenya</option>
                                <option value="MA">Morocco</option>
                                <option value="DZ">Algeria</option>
                                <option value="TN">Tunisia</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Business Account Specific Fields -->
                        <div id="business-fields" class="hidden">
                            <div class="border-t pt-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Business Account Setup</h3>
                                
                                <!-- Profile Images -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <!-- Profile Image -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image *</label>
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0E447A] transition-colors cursor-pointer" id="profile-image-upload">
                                            <input type="file" id="profileImage" name="profileImage" accept="image/*" class="hidden" required>
                                            <div id="profile-image-preview" class="hidden">
                                                <img id="profile-image-preview-img" class="w-20 h-20 rounded-full mx-auto mb-2 object-cover">
                                                <p class="text-sm text-gray-600">Click to change</p>
                                            </div>
                                            <div id="profile-image-placeholder">
                                                <i class="fas fa-user-circle text-4xl text-gray-400 mb-2"></i>
                                                <p class="text-sm text-gray-600">Click to upload profile image</p>
                                                <p class="text-xs text-gray-500">JPG, PNG up to 5MB</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cover Image -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0E447A] transition-colors cursor-pointer" id="cover-image-upload">
                                            <input type="file" id="coverImage" name="coverImage" accept="image/*" class="hidden">
                                            <div id="cover-image-preview" class="hidden">
                                                <img id="cover-image-preview-img" class="w-full h-24 rounded object-cover mb-2">
                                                <p class="text-sm text-gray-600">Click to change</p>
                                            </div>
                                            <div id="cover-image-placeholder">
                                                <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                                                <p class="text-sm text-gray-600">Click to upload cover image</p>
                                                <p class="text-xs text-gray-500">JPG, PNG up to 10MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Uploads -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- ID Card Upload -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">ID Card/Passport *</label>
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0E447A] transition-colors cursor-pointer" id="id-card-upload">
                                            <input type="file" id="idCard" name="idCard" accept="image/*,.pdf" class="hidden" required>
                                            <div id="id-card-preview" class="hidden">
                                                <i class="fas fa-file-pdf text-3xl text-red-500 mb-2"></i>
                                                <p id="id-card-filename" class="text-sm text-gray-600 mb-1"></p>
                                                <p class="text-sm text-gray-600">Click to change</p>
                                            </div>
                                            <div id="id-card-placeholder">
                                                <i class="fas fa-id-card text-4xl text-gray-400 mb-2"></i>
                                                <p class="text-sm text-gray-600">Click to upload ID card</p>
                                                <p class="text-xs text-gray-500">JPG, PNG, PDF up to 10MB</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Diploma/Certificate Upload -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Diploma/Certificate *</label>
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0E447A] transition-colors cursor-pointer" id="diploma-upload">
                                            <input type="file" id="diploma" name="diploma" accept="image/*,.pdf" class="hidden" required>
                                            <div id="diploma-preview" class="hidden">
                                                <i class="fas fa-file-pdf text-3xl text-red-500 mb-2"></i>
                                                <p id="diploma-filename" class="text-sm text-gray-600 mb-1"></p>
                                                <p class="text-sm text-gray-600">Click to change</p>
                                            </div>
                                            <div id="diploma-placeholder">
                                                <i class="fas fa-graduation-cap text-4xl text-gray-400 mb-2"></i>
                                                <p class="text-sm text-gray-600">Click to upload diploma</p>
                                                <p class="text-xs text-gray-500">JPG, PNG, PDF up to 10MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Business Information -->
                                <div class="mt-6">
                                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio/Description *</label>
                                    <textarea id="bio" name="bio" rows="4" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                        placeholder="Tell us about your expertise and teaching experience..."></textarea>
                                </div>

                                <div class="mt-4">
                                    <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">Area of Specialization *</label>
                                    <input type="text" id="specialization" name="specialization" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                        placeholder="e.g., Web Development, Data Science, Digital Marketing">
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="flex justify-between mt-8">
                        <button id="prev-step-2" class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-colors">
                            Previous
                        </button>
                        <button id="next-step-2" class="bg-[#0E447A] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#1e5a96] transition-colors">
                            Next Step
                        </button>
                    </div>
                </div>

                <!-- Step 3: Create Account -->
                <div id="step-3" class="step-content hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Create Your Account</h2>
                    
                    <form class="space-y-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                            <input type="text" id="username" name="username" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                placeholder="Choose a unique username">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                placeholder="Create a strong password">
                        </div>

                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                placeholder="Confirm your password">
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="terms" name="terms" required
                                class="rounded border-gray-300 text-[#0E447A] focus:ring-[#0E447A]">
                            <label for="terms" class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-[#0E447A] hover:underline">Terms of Service</a> and <a href="#" class="text-[#0E447A] hover:underline">Privacy Policy</a>
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="newsletter" name="newsletter"
                                class="rounded border-gray-300 text-[#0E447A] focus:ring-[#0E447A]">
                            <label for="newsletter" class="ml-2 text-sm text-gray-600">
                                Subscribe to our newsletter for updates and course recommendations
                            </label>
                        </div>
                    </form>

                    <div class="flex justify-between mt-8">
                        <button id="prev-step-3" class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-colors">
                            Previous
                        </button>
                        <button id="create-account" class="bg-[#FCA41A] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#e6930f] transition-colors">
                            Create Account
                        </button>
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

        // Registration form functionality
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            let selectedRole = null;

            // Role selection
            const roleCards = document.querySelectorAll('.role-card');
            const nextStep1Btn = document.getElementById('next-step-1');
            const businessFields = document.getElementById('business-fields');

            roleCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove active class from all cards
                    roleCards.forEach(c => c.classList.remove('border-[#0E447A]', 'bg-blue-50'));
                    
                    // Add active class to selected card
                    this.classList.add('border-[#0E447A]', 'bg-blue-50');
                    
                    // Enable next button
                    selectedRole = this.getAttribute('data-role');
                    nextStep1Btn.disabled = false;
                });
            });

            // Step navigation
            const steps = document.querySelectorAll('.step-content');
            const stepIndicators = document.querySelectorAll('.step-indicator');

            function showStep(stepNumber) {
                steps.forEach((step, index) => {
                    if (index + 1 === stepNumber) {
                        step.classList.remove('hidden');
                    } else {
                        step.classList.add('hidden');
                    }
                });

                stepIndicators.forEach((indicator, index) => {
                    if (index + 1 <= stepNumber) {
                        indicator.classList.remove('bg-white', 'bg-opacity-30', 'text-white');
                        indicator.classList.add('bg-white', 'text-[#0E447A]');
                    } else {
                        indicator.classList.remove('bg-white', 'text-[#0E447A]');
                        indicator.classList.add('bg-white', 'bg-opacity-30', 'text-white');
                    }
                });
            }

            // File upload functionality
            function setupFileUpload(inputId, previewId, placeholderId, filenameId = null) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                const uploadArea = document.getElementById(inputId.replace('Image', '-image-upload').replace('Card', '-card-upload').replace('diploma', 'diploma-upload'));

                uploadArea.addEventListener('click', () => input.click());
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (inputId.includes('Image')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const img = document.getElementById(inputId.replace('Image', '-image-preview-img'));
                                img.src = e.target.result;
                                preview.classList.remove('hidden');
                                placeholder.classList.add('hidden');
                            };
                            reader.readAsDataURL(file);
                        } else {
                            if (filenameId) {
                                document.getElementById(filenameId).textContent = file.name;
                            }
                            preview.classList.remove('hidden');
                            placeholder.classList.add('hidden');
                        }
                    }
                });
            }

            // Setup file uploads
            setupFileUpload('profileImage', 'profile-image-preview', 'profile-image-placeholder');
            setupFileUpload('coverImage', 'cover-image-preview', 'cover-image-placeholder');
            setupFileUpload('idCard', 'id-card-preview', 'id-card-placeholder', 'id-card-filename');
            setupFileUpload('diploma', 'diploma-preview', 'diploma-placeholder', 'diploma-filename');

            // Next step buttons
            document.getElementById('next-step-1').addEventListener('click', function() {
                if (selectedRole) {
                    currentStep = 2;
                    showStep(currentStep);
                    
                    // Show/hide business fields based on role
                    if (selectedRole === 'business') {
                        businessFields.classList.remove('hidden');
                    } else {
                        businessFields.classList.add('hidden');
                    }
                }
            });

            document.getElementById('next-step-2').addEventListener('click', function() {
                // Validate step 2 form
                const firstName = document.getElementById('firstName').value;
                const lastName = document.getElementById('lastName').value;
                const email = document.getElementById('email').value;
                const country = document.getElementById('country').value;

                let isValid = firstName && lastName && email && country;

                // Additional validation for business accounts
                if (selectedRole === 'business') {
                    const profileImage = document.getElementById('profileImage').files[0];
                    const idCard = document.getElementById('idCard').files[0];
                    const diploma = document.getElementById('diploma').files[0];
                    const bio = document.getElementById('bio').value;
                    const specialization = document.getElementById('specialization').value;

                    if (!profileImage || !idCard || !diploma || !bio || !specialization) {
                        isValid = false;
                    }
                }

                if (isValid) {
                    currentStep = 3;
                    showStep(currentStep);
                } else {
                    alert('Please fill in all required fields');
                }
            });

            // Previous step buttons
            document.getElementById('prev-step-2').addEventListener('click', function() {
                currentStep = 1;
                showStep(currentStep);
            });

            document.getElementById('prev-step-3').addEventListener('click', function() {
                currentStep = 2;
                showStep(currentStep);
            });

            // Create account button
            document.getElementById('create-account').addEventListener('click', function() {
                // Validate step 3 form
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                const terms = document.getElementById('terms').checked;

                if (!username || !password || !confirmPassword) {
                    alert('Please fill in all required fields');
                    return;
                }

                if (password !== confirmPassword) {
                    alert('Passwords do not match');
                    return;
                }

                if (!terms) {
                    alert('Please agree to the Terms of Service and Privacy Policy');
                    return;
                }

                // Show success message
                alert('Account created successfully! Redirecting to login...');
                // In a real application, you would submit the form data to a server
                // For now, redirect to the main page
                window.location.href = 'index.html';
            });

            // Initialize with step 1
            showStep(1);
        });
    </script>
</body>

</html>
