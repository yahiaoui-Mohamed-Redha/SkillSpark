<?php
// Quick database setup for missing tables
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Create missing tables one by one
    $tables = [
        // Categories table
        "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            icon VARCHAR(100),
            color VARCHAR(7),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Course sections table
        "CREATE TABLE IF NOT EXISTS course_sections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            section_order INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        )",
        
        // Course reviews table
        "CREATE TABLE IF NOT EXISTS course_reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            review_text TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_review (course_id, user_id)
        )",
        
        // Wishlist table
        "CREATE TABLE IF NOT EXISTS wishlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            course_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            UNIQUE KEY unique_wishlist (user_id, course_id)
        )",
        
        // Payments table
        "CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            course_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(50),
            transaction_id VARCHAR(255),
            status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        )",
        
        // Certificates table
        "CREATE TABLE IF NOT EXISTS certificates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            course_id INT NOT NULL,
            certificate_url VARCHAR(255),
            issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        )",
        
        // Announcements table
        "CREATE TABLE IF NOT EXISTS announcements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            author_id INT NOT NULL,
            target_audience ENUM('all', 'students', 'instructors', 'admins') DEFAULT 'all',
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        // Notifications table
        "CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        // File uploads table for user documents
        "CREATE TABLE IF NOT EXISTS user_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            document_type ENUM('id_card', 'diploma', 'certificate', 'profile_image', 'cover_image') NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_size INT,
            file_type VARCHAR(100),
            upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_verified BOOLEAN DEFAULT FALSE,
            verified_by INT,
            verified_at TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
        )",
        
        // Course media table for video uploads and course materials
        "CREATE TABLE IF NOT EXISTS course_media (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            lesson_id INT,
            media_type ENUM('video', 'audio', 'document', 'image', 'course_cover') NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_size INT,
            file_type VARCHAR(100),
            duration_seconds INT,
            thumbnail_path VARCHAR(500),
            is_preview BOOLEAN DEFAULT FALSE,
            upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
        )",
        
        // Instructor earnings table
        "CREATE TABLE IF NOT EXISTS instructor_earnings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            instructor_id INT NOT NULL,
            course_id INT NOT NULL,
            student_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            platform_fee DECIMAL(10,2) NOT NULL,
            instructor_share DECIMAL(10,2) NOT NULL,
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        // Course progress table
        "CREATE TABLE IF NOT EXISTS course_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            course_id INT NOT NULL,
            lesson_id INT NOT NULL,
            completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
            UNIQUE KEY unique_progress (user_id, lesson_id)
        )"
    ];
    
    foreach ($tables as $table) {
        try {
            $conn->exec($table);
            echo "✓ Table created successfully\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "⚠ Error: " . $e->getMessage() . "\n";
            } else {
                echo "✓ Table already exists\n";
            }
        }
    }
    
    // Insert sample categories
    $categories = [
        ['Web Development', 'Learn to build modern web applications', 'fas fa-code', '#3B82F6'],
        ['Data Science', 'Master data analysis and machine learning', 'fas fa-chart-line', '#10B981'],
        ['Mobile Development', 'Create mobile apps for iOS and Android', 'fas fa-mobile-alt', '#8B5CF6'],
        ['Design', 'UI/UX design and graphic design courses', 'fas fa-palette', '#F59E0B'],
        ['Business', 'Business skills and entrepreneurship', 'fas fa-briefcase', '#EF4444'],
        ['Marketing', 'Digital marketing and social media', 'fas fa-bullhorn', '#06B6D4'],
        ['Photography', 'Learn photography techniques', 'fas fa-camera', '#84CC16'],
        ['Music', 'Music production and theory', 'fas fa-music', '#F97316']
    ];
    
    $stmt = $conn->prepare("INSERT IGNORE INTO categories (name, description, icon, color) VALUES (?, ?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }
    
    echo "\n🎉 Database setup completed successfully!\n";
    echo "All tables have been created and sample data inserted.\n";
    
} catch (PDOException $e) {
    echo "❌ Database setup failed: " . $e->getMessage() . "\n";
}
?>
