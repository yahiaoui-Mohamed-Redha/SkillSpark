<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$user = $auth->getCurrentUser();
$database = new Database();
$conn = $database->getConnection();

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];
    
    if (empty($subject) || empty($description)) {
        $error_message = 'Please fill in all required fields';
    } else {
        try {
            $query = "INSERT INTO support_tickets (user_id, subject, description, priority, created_at) 
                     VALUES (:user_id, :subject, :description, :priority, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':priority', $priority);
            $stmt->execute();
            
            $success_message = 'Support ticket submitted successfully! We will get back to you soon.';
            
        } catch (PDOException $e) {
            $error_message = 'Error submitting ticket: ' . $e->getMessage();
        }
    }
}

// Get user's tickets
$user_tickets = [];
try {
    $query = "SELECT * FROM support_tickets WHERE user_id = :user_id ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $user_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Error loading tickets';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - SkillSpark</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <img src="../public/assist/logo2.png" alt="SkillSpark" class="h-8 w-auto">
                    <h1 class="ml-3 text-xl font-semibold text-gray-900">Support Center</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if($_SESSION['role'] === 'student'): ?>
                        <a href="student-dashboard.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    <?php elseif($_SESSION['role'] === 'business'): ?>
                        <a href="instructor-account.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Submit New Ticket -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-plus-circle text-blue-600"></i> Submit New Ticket
                </h2>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <input type="text" id="subject" name="subject" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                    </div>
                    
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select id="priority" name="priority" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="low" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                            <option value="urgent" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'urgent') ? 'selected' : ''; ?>>Urgent</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea id="description" name="description" rows="4" required 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Please describe your issue in detail..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-paper-plane"></i> Submit Ticket
                    </button>
                </form>
            </div>

            <!-- My Tickets -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-ticket-alt text-green-600"></i> My Support Tickets
                </h2>
                
                <?php if(empty($user_tickets)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>No support tickets yet</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        <?php foreach($user_tickets as $ticket): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($ticket['subject']); ?></h3>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?php 
                                        switch($ticket['status']) {
                                            case 'open': echo 'bg-green-100 text-green-800'; break;
                                            case 'in_progress': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'resolved': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'closed': echo 'bg-gray-100 text-gray-800'; break;
                                        }
                                        ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2">
                                    <?php echo htmlspecialchars(substr($ticket['description'], 0, 100)); ?>
                                    <?php if(strlen($ticket['description']) > 100) echo '...'; ?>
                                </p>
                                
                                <div class="flex justify-between items-center text-xs text-gray-500">
                                    <span>
                                        <i class="fas fa-flag"></i> 
                                        <?php echo ucfirst($ticket['priority']); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo date('M j, Y', strtotime($ticket['created_at'])); ?>
                                    </span>
                                </div>
                                
                                <div class="mt-2">
                                    <a href="ticket_details.php?id=<?php echo $ticket['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Help -->
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-question-circle text-purple-600"></i> Quick Help
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <i class="fas fa-book text-3xl text-blue-600 mb-2"></i>
                    <h3 class="font-medium text-gray-900">Documentation</h3>
                    <p class="text-sm text-gray-600">Check our help center for common questions</p>
                </div>
                
                <div class="text-center">
                    <i class="fas fa-video text-3xl text-green-600 mb-2"></i>
                    <h3 class="font-medium text-gray-900">Video Tutorials</h3>
                    <p class="text-sm text-gray-600">Watch step-by-step guides</p>
                </div>
                
                <div class="text-center">
                    <i class="fas fa-comments text-3xl text-purple-600 mb-2"></i>
                    <h3 class="font-medium text-gray-900">Community Forum</h3>
                    <p class="text-sm text-gray-600">Get help from other users</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
