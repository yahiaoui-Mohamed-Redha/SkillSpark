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

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if (!$course_id) {
    header('Location: student-dashboard.php');
    exit();
}

// Get course and enrollment details
$course = null;
$enrollment = null;
$certificate = null;

try {
    // Get course info
    $query = "SELECT c.*, u.first_name, u.last_name 
              FROM courses c 
              JOIN users u ON c.instructor_id = u.id 
              WHERE c.id = :course_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        header('Location: student-dashboard.php');
        exit();
    }
    
    // Check enrollment
    $query = "SELECT * FROM enrollments WHERE course_id = :course_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$enrollment) {
        header('Location: student-dashboard.php');
        exit();
    }
    
    // Check if certificate already exists
    $query = "SELECT * FROM certificates WHERE course_id = :course_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $certificate = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    header('Location: student-dashboard.php');
    exit();
}

// Generate certificate if it doesn't exist
if (!$certificate) {
    try {
        $certificate_id = 'CERT_' . time() . '_' . rand(1000, 9999);
        $query = "INSERT INTO certificates (user_id, course_id, certificate_id, issued_at) 
                 VALUES (:user_id, :course_id, :certificate_id, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':certificate_id', $certificate_id);
        $stmt->execute();
        
        // Get the newly created certificate
        $query = "SELECT * FROM certificates WHERE course_id = :course_id AND user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->execute();
        $certificate = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $error_message = 'Error generating certificate: ' . $e->getMessage();
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
    <title>Certificate - <?php echo htmlspecialchars($course['title']); ?></title>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="student-dashboard.php" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Certificate</h1>
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

        <?php if ($certificate): ?>
            <!-- Certificate -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Certificate Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-8 text-center">
                    <div class="mb-4">
                        <i class="fas fa-certificate text-6xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">Certificate of Completion</h1>
                    <p class="text-lg opacity-90">This certifies that</p>
                </div>

                <!-- Certificate Body -->
                <div class="p-8 text-center">
                    <div class="mb-8">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">
                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                        </h2>
                        <p class="text-xl text-gray-600 mb-6">has successfully completed the course</p>
                        <h3 class="text-2xl font-semibold text-blue-600 mb-4">
                            <?php echo htmlspecialchars($course['title']); ?>
                        </h3>
                        <p class="text-gray-600 mb-6">taught by <?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?></p>
                    </div>

                    <!-- Certificate Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="text-center">
                            <div class="bg-gray-100 rounded-lg p-4">
                                <i class="fas fa-calendar text-2xl text-blue-600 mb-2"></i>
                                <h4 class="font-semibold text-gray-900">Completion Date</h4>
                                <p class="text-gray-600"><?php echo date('F j, Y', strtotime($certificate['issued_at'])); ?></p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-gray-100 rounded-lg p-4">
                                <i class="fas fa-clock text-2xl text-green-600 mb-2"></i>
                                <h4 class="font-semibold text-gray-900">Duration</h4>
                                <p class="text-gray-600"><?php echo $course['duration']; ?> hours</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="bg-gray-100 rounded-lg p-4">
                                <i class="fas fa-tag text-2xl text-purple-600 mb-2"></i>
                                <h4 class="font-semibold text-gray-900">Category</h4>
                                <p class="text-gray-600"><?php echo htmlspecialchars($course['category']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate ID -->
                    <div class="border-t pt-6">
                        <p class="text-sm text-gray-500 mb-2">Certificate ID</p>
                        <p class="font-mono text-lg text-gray-700"><?php echo htmlspecialchars($certificate['certificate_id']); ?></p>
                    </div>
                </div>

                <!-- Certificate Footer -->
                <div class="bg-gray-50 p-6">
                    <div class="flex items-center justify-between">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-gray-200 rounded-lg mx-auto mb-2 flex items-center justify-center">
                                <i class="fas fa-signature text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-600">Instructor Signature</p>
                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?></p>
                        </div>
                        <div class="text-center">
                            <div class="w-24 h-24 bg-gray-200 rounded-lg mx-auto mb-2 flex items-center justify-center">
                                <i class="fas fa-seal text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-600">Platform Seal</p>
                            <p class="font-semibold text-gray-900">SkillSpark</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-center space-x-4">
                <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Print Certificate
                </button>
                <button onclick="downloadCertificate()" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </button>
                <button onclick="shareCertificate()" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700">
                    <i class="fas fa-share mr-2"></i>Share
                </button>
            </div>

            <!-- Verification Info -->
            <div class="mt-8 bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Certificate Verification</h3>
                <p class="text-blue-800 mb-4">This certificate can be verified using the Certificate ID above.</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-blue-700">Verification URL:</span>
                    <code class="text-sm bg-white px-2 py-1 rounded">skillspark.com/verify/<?php echo htmlspecialchars($certificate['certificate_id']); ?></code>
                </div>
            </div>
        <?php else: ?>
            <!-- No Certificate -->
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-exclamation-triangle text-6xl text-yellow-500 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Certificate Not Available</h2>
                <p class="text-gray-600 mb-6">There was an error generating your certificate. Please try again later.</p>
                <a href="student-dashboard.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function downloadCertificate() {
            // In a real implementation, this would generate a PDF
            alert('PDF download functionality would be implemented here');
        }

        function shareCertificate() {
            if (navigator.share) {
                navigator.share({
                    title: 'Certificate of Completion',
                    text: 'I completed the course: <?php echo htmlspecialchars($course['title']); ?>',
                    url: window.location.href
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    alert('Certificate link copied to clipboard!');
                });
            }
        }

        // Print styles
        const printStyles = `
            @media print {
                body * { visibility: hidden; }
                .certificate, .certificate * { visibility: visible; }
                .certificate { position: absolute; left: 0; top: 0; width: 100%; }
                .no-print { display: none !important; }
            }
        `;
        
        const styleSheet = document.createElement("style");
        styleSheet.innerText = printStyles;
        document.head.appendChild(styleSheet);
    </script>
</body>
</html>
