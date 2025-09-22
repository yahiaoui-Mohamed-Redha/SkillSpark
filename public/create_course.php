<?php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();

// Check if user is logged in and is an instructor
if(!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

if($_SESSION['role'] !== 'business') {
    header('Location: student-dashboard.php');
    exit();
}

$user = $auth->getCurrentUser();
$database = new Database();
$conn = $database->getConnection();

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $level = $_POST['level'];
    $duration_hours = intval($_POST['duration_hours']);
    
    if (empty($title) || empty($description) || empty($category_id)) {
        $error_message = 'Please fill in all required fields';
    } else {
        try {
            $conn->beginTransaction();
            
            // Get category name
            $category_name = '';
            $cat_query = "SELECT name FROM categories WHERE id = :category_id";
            $cat_stmt = $conn->prepare($cat_query);
            $cat_stmt->bindParam(':category_id', $category_id);
            $cat_stmt->execute();
            $category_result = $cat_stmt->fetch();
            if ($category_result) {
                $category_name = $category_result['name'];
            }
            
            // Insert course
            $query = "INSERT INTO courses (title, description, instructor_id, price, category, level, duration_hours, created_at) 
                     VALUES (:title, :description, :instructor_id, :price, :category, :level, :duration_hours, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':instructor_id', $user['id']);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category', $category_name);
            $stmt->bindParam(':level', $level);
            $stmt->bindParam(':duration_hours', $duration_hours);
            $stmt->execute();
            
            $course_id = $conn->lastInsertId();
            
            // Handle course cover upload
            if (isset($_FILES['course_cover']) && $_FILES['course_cover']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/course_cover/';
                $file_extension = strtolower(pathinfo($_FILES['course_cover']['name'], PATHINFO_EXTENSION));
                $filename = uniqid() . '_' . time() . '.' . $file_extension;
                $file_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['course_cover']['tmp_name'], $file_path)) {
                    // Save to course_media table
                    $query = "INSERT INTO course_media (course_id, media_type, file_name, file_path, file_size, file_type) 
                             VALUES (:course_id, 'course_cover', :file_name, :file_path, :file_size, :file_type)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':course_id', $course_id);
                    $stmt->bindParam(':file_name', $_FILES['course_cover']['name']);
                    $stmt->bindParam(':file_path', $file_path);
                    $stmt->bindParam(':file_size', $_FILES['course_cover']['size']);
                    $stmt->bindParam(':file_type', $_FILES['course_cover']['type']);
                    $stmt->execute();
                }
            }
            
            $conn->commit();
            $success_message = 'Course created successfully!';
            
        } catch (PDOException $e) {
            $conn->rollBack();
            $error_message = 'Error creating course: ' . $e->getMessage();
        }
    }
}

// Get categories
$categories = [];
try {
    $query = "SELECT * FROM categories ORDER BY name";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Error loading categories';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course - SkillSpark</title>
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
                    <h1 class="ml-3 text-xl font-semibold text-gray-900">Create Course</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="instructor-account.php" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
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

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Course Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title *</label>
                        <input type="text" id="title" name="title" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select id="category_id" name="category_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price ($)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '0'; ?>">
                    </div>
                    
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                        <select id="level" name="level" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="beginner" <?php echo (isset($_POST['level']) && $_POST['level'] == 'beginner') ? 'selected' : ''; ?>>Beginner</option>
                            <option value="intermediate" <?php echo (isset($_POST['level']) && $_POST['level'] == 'intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="advanced" <?php echo (isset($_POST['level']) && $_POST['level'] == 'advanced') ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>
                    
                    
                    <div>
                        <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">Duration (Hours)</label>
                        <input type="number" id="duration_hours" name="duration_hours" min="1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo isset($_POST['duration_hours']) ? htmlspecialchars($_POST['duration_hours']) : '1'; ?>">
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea id="description" name="description" rows="4" required 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
            </div>
            
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Course Cover Image</h2>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer" id="cover-upload">
                    <input type="file" id="course_cover" name="course_cover" accept="image/*" class="hidden">
                    <div id="cover-preview" class="hidden">
                        <img id="cover-preview-img" class="w-32 h-20 object-cover mx-auto mb-2 rounded">
                        <p class="text-sm text-gray-600">Click to change</p>
                    </div>
                    <div id="cover-placeholder">
                        <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600">Click to upload course cover</p>
                        <p class="text-xs text-gray-500">JPG, PNG up to 10MB</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="instructor-account.php" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Create Course
                </button>
            </div>
        </form>
    </div>

    <script>
        // File upload handling
        const coverUpload = document.getElementById('cover-upload');
        const coverInput = document.getElementById('course_cover');
        const coverPreview = document.getElementById('cover-preview');
        const coverPlaceholder = document.getElementById('cover-placeholder');
        const coverPreviewImg = document.getElementById('cover-preview-img');

        coverUpload.addEventListener('click', () => coverInput.click());

        coverInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverPreviewImg.src = e.target.result;
                    coverPreview.classList.remove('hidden');
                    coverPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
