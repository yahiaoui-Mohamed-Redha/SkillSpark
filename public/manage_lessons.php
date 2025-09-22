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

// Get course ID and playlist ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$playlist_id = isset($_GET['playlist_id']) ? intval($_GET['playlist_id']) : 0;

if (!$course_id || !$playlist_id) {
    header('Location: my_courses.php');
    exit();
}

// Verify course ownership and get playlist info
try {
    $query = "SELECT c.*, cp.name as playlist_name, cp.description as playlist_description 
              FROM courses c 
              JOIN course_playlists cp ON c.id = cp.course_id 
              WHERE c.id = :course_id AND c.instructor_id = :instructor_id AND cp.id = :playlist_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':instructor_id', $user['id']);
    $stmt->bindParam(':playlist_id', $playlist_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        header('Location: my_courses.php');
        exit();
    }
    
    $course = $result;
    $playlist = [
        'id' => $playlist_id,
        'name' => $result['playlist_name'],
        'description' => $result['playlist_description']
    ];
} catch (PDOException $e) {
    header('Location: my_courses.php');
    exit();
}

// Get lessons for this playlist
$lessons = [];
try {
    $query = "SELECT * FROM lessons WHERE course_id = :course_id AND playlist_id = :playlist_id ORDER BY lesson_order, lesson_number";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':playlist_id', $playlist_id);
    $stmt->execute();
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $lessons = [];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_lesson':
                $title = trim($_POST['title']);
                $description = trim($_POST['description']);
                $video_url = trim($_POST['video_url']);
                $duration = intval($_POST['duration']);
                $is_preview = isset($_POST['is_preview']) ? 1 : 0;
                
                if (!empty($title)) {
                    try {
                        // Get next lesson number and order
                        $query = "SELECT MAX(lesson_number) as max_number, MAX(lesson_order) as max_order 
                                  FROM lessons WHERE course_id = :course_id AND playlist_id = :playlist_id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':course_id', $course_id);
                        $stmt->bindParam(':playlist_id', $playlist_id);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $next_number = ($result['max_number'] ?? 0) + 1;
                        $next_order = ($result['max_order'] ?? 0) + 1;
                        
                        $query = "INSERT INTO lessons (course_id, playlist_id, title, description, lesson_number, video_url, duration, is_preview, lesson_order) 
                                 VALUES (:course_id, :playlist_id, :title, :description, :lesson_number, :video_url, :duration, :is_preview, :lesson_order)";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':course_id', $course_id);
                        $stmt->bindParam(':playlist_id', $playlist_id);
                        $stmt->bindParam(':title', $title);
                        $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':lesson_number', $next_number);
                        $stmt->bindParam(':video_url', $video_url);
                        $stmt->bindParam(':duration', $duration);
                        $stmt->bindParam(':is_preview', $is_preview);
                        $stmt->bindParam(':lesson_order', $next_order);
                        $stmt->execute();
                        
                        header('Location: manage_lessons.php?course_id=' . $course_id . '&playlist_id=' . $playlist_id . '&success=lesson_created');
                        exit();
                    } catch (PDOException $e) {
                        $error_message = 'Error creating lesson: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'delete_lesson':
                $lesson_id = intval($_POST['lesson_id']);
                try {
                    $query = "DELETE FROM lessons WHERE id = :lesson_id AND course_id = :course_id AND playlist_id = :playlist_id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':lesson_id', $lesson_id);
                    $stmt->bindParam(':course_id', $course_id);
                    $stmt->bindParam(':playlist_id', $playlist_id);
                    $stmt->execute();
                    
                    header('Location: manage_lessons.php?course_id=' . $course_id . '&playlist_id=' . $playlist_id . '&success=lesson_deleted');
                    exit();
                } catch (PDOException $e) {
                    $error_message = 'Error deleting lesson: ' . $e->getMessage();
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
    <title>Manage Lessons - <?php echo htmlspecialchars($playlist['name']); ?></title>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="manage_playlists.php?course_id=<?php echo $course_id; ?>" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Manage Lessons</h1>
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
        <!-- Course and Playlist Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($course['title']); ?></h2>
                    <h3 class="text-lg text-blue-600"><?php echo htmlspecialchars($playlist['name']); ?></h3>
                    <p class="text-gray-600"><?php echo htmlspecialchars($playlist['description']); ?></p>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-500">Playlist ID: <?php echo $playlist_id; ?></span>
                </div>
            </div>
        </div>

        <!-- Success Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php
                switch ($_GET['success']) {
                    case 'lesson_created':
                        echo 'Lesson created successfully!';
                        break;
                    case 'lesson_deleted':
                        echo 'Lesson deleted successfully!';
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

        <!-- Create New Lesson -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Create New Lesson</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create_lesson">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Lesson Title</label>
                        <input type="text" id="title" name="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter lesson title">
                    </div>
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter duration in minutes">
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Enter lesson description"></textarea>
                </div>
                <div>
                    <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">Video URL</label>
                    <input type="url" id="video_url" name="video_url"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter video URL (YouTube, Vimeo, etc.)">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="is_preview" name="is_preview" class="mr-2">
                    <label for="is_preview" class="text-sm text-gray-700">Make this lesson a preview (free for all students)</label>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Create Lesson
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Lessons -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Playlist Lessons</h3>
            
            <?php if (empty($lessons)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-book text-4xl mb-4"></i>
                    <p>No lessons created yet. Create your first lesson above!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($lessons as $lesson): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2 py-1 rounded mr-3">
                                            Lesson <?php echo $lesson['lesson_number']; ?>
                                        </span>
                                        <?php if ($lesson['is_preview']): ?>
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded mr-2">
                                                <i class="fas fa-eye mr-1"></i>Preview
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($lesson['is_active']): ?>
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">
                                                Inactive
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($lesson['title']); ?></h4>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($lesson['description']); ?></p>
                                    <div class="flex items-center mt-2 text-xs text-gray-500">
                                        <?php if ($lesson['duration'] > 0): ?>
                                            <span class="mr-4"><i class="fas fa-clock mr-1"></i><?php echo $lesson['duration']; ?> minutes</span>
                                        <?php endif; ?>
                                        <?php if (!empty($lesson['video_url'])): ?>
                                            <span class="mr-4"><i class="fas fa-video mr-1"></i>Video Available</span>
                                        <?php endif; ?>
                                        <span>Order: <?php echo $lesson['lesson_order']; ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="edit_lesson.php?course_id=<?php echo $course_id; ?>&playlist_id=<?php echo $playlist_id; ?>&lesson_id=<?php echo $lesson['id']; ?>" 
                                       class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 text-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this lesson?')">
                                        <input type="hidden" name="action" value="delete_lesson">
                                        <input type="hidden" name="lesson_id" value="<?php echo $lesson['id']; ?>">
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
