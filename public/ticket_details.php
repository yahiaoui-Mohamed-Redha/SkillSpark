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

$ticket_id = $_GET['id'] ?? 0;
$success_message = '';
$error_message = '';

// Get ticket details
$ticket = null;
$messages = [];

try {
    // Get ticket
    $query = "SELECT st.*, u.first_name, u.last_name, u.email 
              FROM support_tickets st 
              JOIN users u ON st.user_id = u.id 
              WHERE st.id = :ticket_id AND st.user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':ticket_id', $ticket_id);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ticket) {
        header('Location: support_ticket.php');
        exit();
    }
    
    // Get messages
    $query = "SELECT tm.*, u.first_name, u.last_name, u.role 
              FROM ticket_messages tm 
              JOIN users u ON tm.user_id = u.id 
              WHERE tm.ticket_id = :ticket_id 
              ORDER BY tm.created_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':ticket_id', $ticket_id);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = 'Error loading ticket details';
}

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        try {
            $query = "INSERT INTO ticket_messages (ticket_id, user_id, message, is_admin, created_at) 
                     VALUES (:ticket_id, :user_id, :message, :is_admin, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ticket_id', $ticket_id);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->bindParam(':message', $message);
            $is_admin = ($user['role'] === 'admin') ? 1 : 0;
            $stmt->bindParam(':is_admin', $is_admin);
            $stmt->execute();
            
            $success_message = 'Message sent successfully!';
            
            // Refresh messages
            $query = "SELECT tm.*, u.first_name, u.last_name, u.role 
                      FROM ticket_messages tm 
                      JOIN users u ON tm.user_id = u.id 
                      WHERE tm.ticket_id = :ticket_id 
                      ORDER BY tm.created_at ASC";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ticket_id', $ticket_id);
            $stmt->execute();
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error_message = 'Error sending message: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - SkillSpark</title>
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
                    <h1 class="ml-3 text-xl font-semibold text-gray-900">Ticket #<?php echo $ticket_id; ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="support_ticket.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left"></i> Back to Support
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

        <!-- Ticket Info -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($ticket['subject']); ?></h2>
                    <p class="text-sm text-gray-600">Created by <?php echo htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']); ?></p>
                </div>
                <div class="flex space-x-2">
                    <span class="px-3 py-1 text-sm rounded-full 
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
                    <span class="px-3 py-1 text-sm rounded-full 
                        <?php 
                        switch($ticket['priority']) {
                            case 'low': echo 'bg-gray-100 text-gray-800'; break;
                            case 'medium': echo 'bg-yellow-100 text-yellow-800'; break;
                            case 'high': echo 'bg-orange-100 text-orange-800'; break;
                            case 'urgent': echo 'bg-red-100 text-red-800'; break;
                        }
                        ?>">
                        <?php echo ucfirst($ticket['priority']); ?>
                    </span>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-medium text-gray-900 mb-2">Description:</h3>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
            </div>
            
            <div class="mt-4 text-sm text-gray-500">
                <span><i class="fas fa-calendar"></i> Created: <?php echo date('M j, Y g:i A', strtotime($ticket['created_at'])); ?></span>
                <?php if($ticket['updated_at'] !== $ticket['created_at']): ?>
                    <span class="ml-4"><i class="fas fa-edit"></i> Updated: <?php echo date('M j, Y g:i A', strtotime($ticket['updated_at'])); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Messages -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Conversation</h3>
            
            <?php if(empty($messages)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-comments text-4xl mb-4"></i>
                    <p>No messages yet</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach($messages as $message): ?>
                        <div class="flex <?php echo $message['is_admin'] ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg 
                                <?php echo $message['is_admin'] ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900'; ?>">
                                <div class="flex items-center mb-1">
                                    <span class="text-sm font-medium">
                                        <?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?>
                                    </span>
                                    <?php if($message['is_admin']): ?>
                                        <span class="ml-2 text-xs bg-blue-500 px-2 py-1 rounded">Admin</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-sm"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                <p class="text-xs mt-1 opacity-75">
                                    <?php echo date('M j, g:i A', strtotime($message['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Add Message -->
        <?php if($ticket['status'] !== 'closed'): ?>
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add Message</h3>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <textarea name="message" rows="4" required 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Type your message here..."></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-gray-100 rounded-lg p-4 text-center text-gray-600">
                <i class="fas fa-lock text-2xl mb-2"></i>
                <p>This ticket is closed and no longer accepts new messages.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
