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
$database = new Database();
$conn = $database->getConnection();

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if (!$course_id) {
    header('Location: browse_courses.php');
    exit();
}

// Get course details
$course = null;
$is_enrolled = false;

try {
    $query = "SELECT c.*, u.first_name, u.last_name 
              FROM courses c 
              JOIN users u ON c.instructor_id = u.id 
              WHERE c.id = :course_id AND c.status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        header('Location: browse_courses.php');
        exit();
    }
    
    // Check if already enrolled
    $query = "SELECT * FROM enrollments WHERE course_id = :course_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $is_enrolled = !empty($stmt->fetch(PDO::FETCH_ASSOC));
    
} catch (PDOException $e) {
    header('Location: browse_courses.php');
    exit();
}

// Handle payment processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_payment') {
    if (!$is_enrolled && $course['price'] > 0) {
        try {
            $conn->beginTransaction();
            
            // Create payment record
            $payment_id = 'PAY_' . time() . '_' . rand(1000, 9999);
            $query = "INSERT INTO payments (user_id, course_id, amount, payment_method, payment_status, payment_id, created_at) 
                     VALUES (:user_id, :course_id, :amount, :payment_method, 'completed', :payment_id, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->bindParam(':amount', $course['price']);
            $stmt->bindParam(':payment_method', $_POST['payment_method']);
            $stmt->bindParam(':payment_id', $payment_id);
            $stmt->execute();
            
            // Create enrollment
            $query = "INSERT INTO enrollments (course_id, user_id, enrolled_at) VALUES (:course_id, :user_id, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
            
            // Update instructor earnings
            $instructor_earning = $course['price'] * 0.7; // 70% to instructor, 30% platform fee
            $query = "INSERT INTO instructor_earnings (instructor_id, course_id, amount, payment_id, created_at) 
                     VALUES (:instructor_id, :course_id, :amount, :payment_id, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':instructor_id', $course['instructor_id']);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->bindParam(':amount', $instructor_earning);
            $stmt->bindParam(':payment_id', $payment_id);
            $stmt->execute();
            
            $conn->commit();
            
            header('Location: student_course_view.php?course_id=' . $course_id . '&payment_success=1');
            exit();
            
        } catch (PDOException $e) {
            $conn->rollBack();
            $error_message = 'Payment processing failed: ' . $e->getMessage();
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
    <title>Payment - <?php echo htmlspecialchars($course['title']); ?></title>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="student_course_view.php?course_id=<?php echo $course_id; ?>" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Payment</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="student-dashboard.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-home"></i>
                    </a>
                    <a href="logout.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Error Messages -->
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($is_enrolled): ?>
            <!-- Already Enrolled -->
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Already Enrolled</h2>
                <p class="text-gray-600 mb-6">You are already enrolled in this course.</p>
                <a href="student_course_view.php?course_id=<?php echo $course_id; ?>" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                    <i class="fas fa-play mr-2"></i>Access Course
                </a>
            </div>
        <?php else: ?>
            <!-- Payment Form -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Course Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Course Summary</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($course['description']); ?></p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Instructor:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($course['category']); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Duration:</span>
                            <span class="font-medium"><?php echo $course['duration']; ?> hours</span>
                        </div>
                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between text-lg font-semibold">
                                <span>Total:</span>
                                <span class="text-blue-600">$<?php echo $course['price']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Details</h2>
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="process_payment">
                        
                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="payment_method" value="credit_card" class="mr-3" checked>
                                    <i class="fas fa-credit-card text-blue-600 mr-3"></i>
                                    <span>Credit Card</span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="payment_method" value="paypal" class="mr-3">
                                    <i class="fab fa-paypal text-blue-600 mr-3"></i>
                                    <span>PayPal</span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="payment_method" value="stripe" class="mr-3">
                                    <i class="fab fa-stripe text-blue-600 mr-3"></i>
                                    <span>Stripe</span>
                                </label>
                            </div>
                        </div>

                        <!-- Card Details (for credit card) -->
                        <div id="card-details" class="space-y-4">
                            <div>
                                <label for="card_number" class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                <input type="text" id="card_number" name="card_number" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="1234 5678 9012 3456">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="expiry" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                                    <input type="text" id="expiry" name="expiry" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="MM/YY">
                                </div>
                                <div>
                                    <label for="cvv" class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                                    <input type="text" id="cvv" name="cvv" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="123">
                                </div>
                            </div>
                            <div>
                                <label for="cardholder_name" class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                                <input type="text" id="cardholder_name" name="cardholder_name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="John Doe">
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="flex items-start">
                            <input type="checkbox" id="terms" name="terms" required class="mt-1 mr-3">
                            <label for="terms" class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> 
                                and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </label>
                        </div>

                        <!-- Payment Button -->
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 font-semibold">
                            <i class="fas fa-lock mr-2"></i>Pay $<?php echo $course['price']; ?>
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Show/hide card details based on payment method
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const cardDetails = document.getElementById('card-details');
                if (this.value === 'credit_card') {
                    cardDetails.style.display = 'block';
                } else {
                    cardDetails.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
