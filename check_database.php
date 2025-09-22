<?php
// Database check script
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "🔍 Checking database tables...\n\n";
    
    $required_tables = [
        'users', 'courses', 'lessons', 'enrollments', 'categories',
        'course_sections', 'course_reviews', 'wishlist', 'payments',
        'certificates', 'announcements', 'notifications', 'user_documents',
        'course_media', 'instructor_earnings', 'course_progress'
    ];
    
    $missing_tables = [];
    
    foreach ($required_tables as $table) {
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        
        if ($stmt->rowCount() > 0) {
            echo "✓ $table - EXISTS\n";
        } else {
            echo "❌ $table - MISSING\n";
            $missing_tables[] = $table;
        }
    }
    
    if (empty($missing_tables)) {
        echo "\n🎉 All tables exist! Database is ready.\n";
    } else {
        echo "\n⚠ Missing tables: " . implode(', ', $missing_tables) . "\n";
        echo "Please run quick_setup.php to create missing tables.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your database configuration.\n";
}
?>
