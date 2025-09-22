<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in
if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if user is an instructor
if($_SESSION['role'] !== 'business') {
    header('Location: student-dashboard.php');
    exit();
}

$user = $auth->getCurrentUser();
$database = new Database();
$conn = $database->getConnection();

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if (!$course_id) {
    header('Location: my_courses.php');
    exit();
}

// Verify course ownership
try {
    $query = "SELECT * FROM courses WHERE id = :course_id AND instructor_id = :instructor_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        header('Location: my_courses.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: my_courses.php');
    exit();
}

// Get playlists for this course
$playlists = [];
try {
    $query = "SELECT * FROM course_playlists WHERE course_id = :course_id ORDER BY playlist_order, created_at";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $playlists = [];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_playlist':
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                
                if (!empty($name)) {
                    try {
                        // Get next playlist order
                        $query = "SELECT MAX(playlist_order) as max_order FROM course_playlists WHERE course_id = :course_id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':course_id', $course_id);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $next_order = ($result['max_order'] ?? 0) + 1;
                        
                        $query = "INSERT INTO course_playlists (course_id, name, description, playlist_order) VALUES (:course_id, :name, :description, :playlist_order)";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':course_id', $course_id);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':playlist_order', $next_order);
                        $stmt->execute();
                        
                        header('Location: manage_playlists.php?course_id=' . $course_id . '&success=playlist_created');
                        exit();
                    } catch (PDOException $e) {
                        $error_message = 'Error creating playlist: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'delete_playlist':
                $playlist_id = intval($_POST['playlist_id']);
                try {
                    $query = "DELETE FROM course_playlists WHERE id = :playlist_id AND course_id = :course_id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':playlist_id', $playlist_id);
                    $stmt->bindParam(':course_id', $course_id);
                    $stmt->execute();
                    
                    header('Location: manage_playlists.php?course_id=' . $course_id . '&success=playlist_deleted');
                    exit();
                } catch (PDOException $e) {
                    $error_message = 'Error deleting playlist: ' . $e->getMessage();
                }
                break;
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
    <title>Manage Playlists - <?php echo htmlspecialchars($course['title']); ?></title>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="my_courses.php" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Manage Playlists</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="instructor-account.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="logout.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Course Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($course['title']); ?></h2>
                    <p class="text-gray-600"><?php echo htmlspecialchars($course['description']); ?></p>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-500">Course ID: <?php echo $course['id']; ?></span>
                </div>
            </div>
        </div>

        <!-- Success Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php
                switch ($_GET['success']) {
                    case 'playlist_created':
                        echo 'Playlist created successfully!';
                        break;
                    case 'playlist_deleted':
                        echo 'Playlist deleted successfully!';
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Create New Playlist -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Create New Playlist</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create_playlist">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Playlist Name</label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter playlist name">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Enter playlist description"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Create Playlist
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Playlists -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Playlists</h3>
            
            <?php if (empty($playlists)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-list text-4xl mb-4"></i>
                    <p>No playlists created yet. Create your first playlist above!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($playlists as $playlist): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($playlist['name']); ?></h4>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($playlist['description']); ?></p>
                                    <div class="flex items-center mt-2 text-xs text-gray-500">
                                        <span class="mr-4">Order: <?php echo $playlist['playlist_order']; ?></span>
                                        <span class="mr-4">Created: <?php echo date('M j, Y', strtotime($playlist['created_at'])); ?></span>
                                        <span class="px-2 py-1 rounded-full <?php echo $playlist['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $playlist['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="manage_lessons.php?course_id=<?php echo $course_id; ?>&playlist_id=<?php echo $playlist['id']; ?>" 
                                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                        <i class="fas fa-book mr-1"></i>Manage Lessons
                                    </a>
                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this playlist?')">
                                        <input type="hidden" name="action" value="delete_playlist">
                                        <input type="hidden" name="playlist_id" value="<?php echo $playlist['id']; ?>">
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
