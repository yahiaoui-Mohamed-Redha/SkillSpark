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

// Get notifications
$notifications = [];
$unread_count = 0;

try {
    $query = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Count unread notifications
    $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $unread_count = $result['count'];
    
} catch (PDOException $e) {
    $notifications = [];
}

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'mark_read' && isset($_POST['notification_id'])) {
        $notification_id = intval($_POST['notification_id']);
        try {
            $query = "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $notification_id);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
            
            header('Location: notifications.php');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error updating notification: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'mark_all_read') {
        try {
            $query = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
            
            header('Location: notifications.php');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error updating notifications: ' . $e->getMessage();
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
    <title>Notifications - SkillSpark</title>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo $_SESSION['role'] === 'student' ? 'student-dashboard.php' : 'instructor-dashboard.php'; ?>" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Notifications</h1>
                    <?php if ($unread_count > 0): ?>
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full ml-2">
                            <?php echo $unread_count; ?> unread
                        </span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?php echo $_SESSION['role'] === 'student' ? 'student-dashboard.php' : 'instructor-dashboard.php'; ?>" class="text-gray-600 hover:text-gray-900">
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

        <!-- Actions -->
        <?php if (!empty($notifications)): ?>
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Your Notifications</h2>
            <form method="POST" class="inline">
                <input type="hidden" name="action" value="mark_all_read">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                    <i class="fas fa-check-double mr-2"></i>Mark All as Read
                </button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Notifications List -->
        <?php if (empty($notifications)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-bell text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Notifications</h3>
                <p class="text-gray-600">You don't have any notifications yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($notifications as $notification): ?>
                <div class="bg-white rounded-lg shadow-md p-6 <?php echo $notification['is_read'] ? 'opacity-75' : 'border-l-4 border-blue-500'; ?>">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <?php
                                $icon_class = 'fas fa-info-circle text-blue-600';
                                $bg_class = 'bg-blue-100';
                                
                                switch ($notification['type']) {
                                    case 'enrollment':
                                        $icon_class = 'fas fa-user-plus text-green-600';
                                        $bg_class = 'bg-green-100';
                                        break;
                                    case 'course':
                                        $icon_class = 'fas fa-book text-purple-600';
                                        $bg_class = 'bg-purple-100';
                                        break;
                                    case 'payment':
                                        $icon_class = 'fas fa-dollar-sign text-yellow-600';
                                        $bg_class = 'bg-yellow-100';
                                        break;
                                    case 'support':
                                        $icon_class = 'fas fa-life-ring text-red-600';
                                        $bg_class = 'bg-red-100';
                                        break;
                                }
                                ?>
                                <div class="p-2 <?php echo $bg_class; ?> rounded-full mr-3">
                                    <i class="<?php echo $icon_class; ?>"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($notification['title']); ?></h3>
                                <?php if (!$notification['is_read']): ?>
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full ml-2">New</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-600 mb-3"><?php echo htmlspecialchars($notification['message']); ?></p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                                </span>
                                <?php if (!$notification['is_read']): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-check mr-1"></i>Mark as Read
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
