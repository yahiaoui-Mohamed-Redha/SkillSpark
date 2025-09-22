<?php
require_once '../config/auth.php';

$auth = new Auth();

// Redirect if already logged in
if($auth->isLoggedIn()) {
    $auth->redirectByRole();
}

$error_message = '';
$success_message = '';

// Handle registration form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $data = [
        'firstName' => trim($_POST['firstName']),
        'lastName' => trim($_POST['lastName']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'country' => $_POST['country'],
        'username' => trim($_POST['username']),
        'password' => $_POST['password'],
        'confirmPassword' => $_POST['confirmPassword'],
        'role' => $_POST['role'],
        'bio' => isset($_POST['bio']) ? trim($_POST['bio']) : '',
        'specialization' => isset($_POST['specialization']) ? trim($_POST['specialization']) : '',
        'newsletter' => isset($_POST['newsletter']) ? 1 : 0
    ];

    // Validation
    if(empty($data['firstName']) || empty($data['lastName']) || empty($data['email']) || 
       empty($data['country']) || empty($data['username']) || empty($data['password'])) {
        $error_message = 'Please fill in all required fields';
    } elseif($data['password'] !== $data['confirmPassword']) {
        $error_message = 'Passwords do not match';
    } elseif(strlen($data['password']) < 6) {
        $error_message = 'Password must be at least 6 characters long';
    } else {
        $result = $auth->register($data);
        
        if($result['success']) {
            $success_message = 'Registration successful! You can now login.';
            // Clear form data
            $data = [];
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
                    <a href="index.php" class="text-black hover:text-[#0E447A] font-medium transition-colors">Back to Home</a>
                    <a href="login.php" class="text-[#0E447A] hover:underline font-medium transition-colors">Sign In</a>
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

            <!-- Registration Form -->
            <div class="bg-white rounded-lg shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Create Your Account</h2>
                
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
                
                <form method="POST" class="space-y-6">
                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">Choose Your Role *</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#0E447A] transition-colors">
                                <input type="radio" name="role" value="student" class="mr-3" required>
                                <div class="flex items-center">
                                    <i class="fas fa-graduation-cap text-2xl text-[#0E447A] mr-3"></i>
                                    <div>
                                        <div class="font-medium">Student</div>
                                        <div class="text-sm text-gray-500">Learn new skills and advance your career</div>
                                    </div>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#0E447A] transition-colors">
                                <input type="radio" name="role" value="business" class="mr-3" required>
                                <div class="flex items-center">
                                    <i class="fas fa-briefcase text-2xl text-[#0E447A] mr-3"></i>
                                    <div>
                                        <div class="font-medium">Business Account</div>
                                        <div class="text-sm text-gray-500">Teach and share your expertise</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" id="firstName" name="firstName" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                placeholder="Enter your first name" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>">
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" id="lastName" name="lastName" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                placeholder="Enter your last name" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                            placeholder="Enter your email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                            placeholder="Enter your phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                        <select id="country" name="country" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]">
                            <option value="">Select your country</option>
                            <option value="US" <?php echo (isset($_POST['country']) && $_POST['country'] == 'US') ? 'selected' : ''; ?>>United States</option>
                            <option value="CA" <?php echo (isset($_POST['country']) && $_POST['country'] == 'CA') ? 'selected' : ''; ?>>Canada</option>
                            <option value="UK" <?php echo (isset($_POST['country']) && $_POST['country'] == 'UK') ? 'selected' : ''; ?>>United Kingdom</option>
                            <option value="AU" <?php echo (isset($_POST['country']) && $_POST['country'] == 'AU') ? 'selected' : ''; ?>>Australia</option>
                            <option value="DE" <?php echo (isset($_POST['country']) && $_POST['country'] == 'DE') ? 'selected' : ''; ?>>Germany</option>
                            <option value="FR" <?php echo (isset($_POST['country']) && $_POST['country'] == 'FR') ? 'selected' : ''; ?>>France</option>
                            <option value="ES" <?php echo (isset($_POST['country']) && $_POST['country'] == 'ES') ? 'selected' : ''; ?>>Spain</option>
                            <option value="IT" <?php echo (isset($_POST['country']) && $_POST['country'] == 'IT') ? 'selected' : ''; ?>>Italy</option>
                            <option value="JP" <?php echo (isset($_POST['country']) && $_POST['country'] == 'JP') ? 'selected' : ''; ?>>Japan</option>
                            <option value="CN" <?php echo (isset($_POST['country']) && $_POST['country'] == 'CN') ? 'selected' : ''; ?>>China</option>
                            <option value="IN" <?php echo (isset($_POST['country']) && $_POST['country'] == 'IN') ? 'selected' : ''; ?>>India</option>
                            <option value="BR" <?php echo (isset($_POST['country']) && $_POST['country'] == 'BR') ? 'selected' : ''; ?>>Brazil</option>
                            <option value="MX" <?php echo (isset($_POST['country']) && $_POST['country'] == 'MX') ? 'selected' : ''; ?>>Mexico</option>
                            <option value="AR" <?php echo (isset($_POST['country']) && $_POST['country'] == 'AR') ? 'selected' : ''; ?>>Argentina</option>
                            <option value="ZA" <?php echo (isset($_POST['country']) && $_POST['country'] == 'ZA') ? 'selected' : ''; ?>>South Africa</option>
                            <option value="EG" <?php echo (isset($_POST['country']) && $_POST['country'] == 'EG') ? 'selected' : ''; ?>>Egypt</option>
                            <option value="NG" <?php echo (isset($_POST['country']) && $_POST['country'] == 'NG') ? 'selected' : ''; ?>>Nigeria</option>
                            <option value="KE" <?php echo (isset($_POST['country']) && $_POST['country'] == 'KE') ? 'selected' : ''; ?>>Kenya</option>
                            <option value="MA" <?php echo (isset($_POST['country']) && $_POST['country'] == 'MA') ? 'selected' : ''; ?>>Morocco</option>
                            <option value="DZ" <?php echo (isset($_POST['country']) && $_POST['country'] == 'DZ') ? 'selected' : ''; ?>>Algeria</option>
                            <option value="TN" <?php echo (isset($_POST['country']) && $_POST['country'] == 'TN') ? 'selected' : ''; ?>>Tunisia</option>
                            <option value="other" <?php echo (isset($_POST['country']) && $_POST['country'] == 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <!-- Account Information -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                        <input type="text" id="username" name="username" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                            placeholder="Choose a unique username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    </div>

                    <!-- Business Account Fields (hidden by default) -->
                    <div id="business-fields" style="display: none;">
                        <div class="border-t pt-6 mt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Business Account Information</h3>
                            
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio/Description *</label>
                                <textarea id="bio" name="bio" rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                    placeholder="Tell us about your expertise and teaching experience..."><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
                            </div>

                            <div class="mt-4">
                                <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">Area of Specialization *</label>
                                <input type="text" id="specialization" name="specialization"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0E447A] focus:border-[#0E447A]"
                                    placeholder="e.g., Web Development, Data Science, Digital Marketing" value="<?php echo isset($_POST['specialization']) ? htmlspecialchars($_POST['specialization']) : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Newsletter -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="terms" name="terms" required
                                class="rounded border-gray-300 text-[#0E447A] focus:ring-[#0E447A]">
                            <label for="terms" class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-[#0E447A] hover:underline">Terms of Service</a> and <a href="#" class="text-[#0E447A] hover:underline">Privacy Policy</a>
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="newsletter" name="newsletter"
                                class="rounded border-gray-300 text-[#0E447A] focus:ring-[#0E447A]" <?php echo (isset($_POST['newsletter']) && $_POST['newsletter']) ? 'checked' : ''; ?>>
                            <label for="newsletter" class="ml-2 text-sm text-gray-600">
                                Subscribe to our newsletter for updates and course recommendations
                            </label>
                        </div>
                    </div>

                    <button type="submit" name="register" class="w-full bg-[#FCA41A] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#e6930f] transition-colors">
                        Create Account
                    </button>
                </form>
                
                <p class="text-center text-sm text-gray-600 mt-6">
                    Already have an account? 
                    <a href="login.php" class="text-[#0E447A] hover:underline font-medium">Sign in</a>
                </p>
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

        // Show/hide business fields based on role selection
        document.addEventListener('DOMContentLoaded', function() {
            const roleInputs = document.querySelectorAll('input[name="role"]');
            const businessFields = document.getElementById('business-fields');
            const bioField = document.getElementById('bio');
            const specializationField = document.getElementById('specialization');

            roleInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value === 'business') {
                        businessFields.style.display = 'block';
                        bioField.required = true;
                        specializationField.required = true;
                    } else {
                        businessFields.style.display = 'none';
                        bioField.required = false;
                        specializationField.required = false;
                    }
                });
            });
        });
    </script>
</body>

</html>
