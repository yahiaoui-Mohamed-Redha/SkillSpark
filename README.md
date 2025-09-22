# SkillSpark - Learning Management System

A comprehensive Learning Management System (LMS) built with PHP, MySQL, and modern web technologies.

## 🚀 Features

### User Management
- **Multi-role system**: Students, Instructors, and Admins
- **Secure authentication** with password hashing
- **User profiles** with bio, specialization, and document uploads
- **Role-based access control**

### Course Management
- **Course creation** with rich metadata
- **Video uploads** and course materials
- **Course categories** and filtering
- **Pricing and enrollment** system
- **Course reviews** and ratings

### Learning Features
- **Student dashboard** with course progress
- **Instructor dashboard** with analytics
- **Course enrollment** and tracking
- **Certificate generation**
- **Wishlist functionality**

### Support System
- **Support ticket system** for user assistance
- **Admin ticket management**
- **Real-time messaging** between users and admins
- **Priority-based ticket handling**

### Admin Features
- **Comprehensive admin dashboard**
- **User management** and analytics
- **Support ticket management**
- **Platform statistics**

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Tailwind CSS
- **Icons**: Font Awesome
- **Server**: Apache (XAMPP)

## 📋 Prerequisites

- XAMPP or similar LAMP/WAMP stack
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache web server
- Git

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yahiaoui-Mohamed-Redha/SkillSpark.git
cd SkillSpark
```

### 2. Database Setup
1. Start XAMPP and ensure MySQL is running
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Import the database schema:
   ```sql
   -- Run the SQL file in database/create_database.sql
   ```

### 3. Quick Database Setup
```bash
# Run the database setup script
php quick_setup.php
```

### 4. Configure Database
Update `config/database.php` if needed:
```php
private $host = 'localhost';
private $db_name = 'skillspark_db';
private $username = 'root';
private $password = '';
```

### 5. Set Up File Permissions
```bash
# Create upload directories
mkdir uploads
mkdir uploads/id_card uploads/diploma uploads/certificate
mkdir uploads/profile_image uploads/cover_image
mkdir uploads/course_video uploads/course_cover
```

## 👥 Default Users

The system comes with pre-configured test users:

### Student Account
- **Email**: `mohamed@skillspark.com`
- **Password**: `password123`
- **Role**: Student

### Instructor Account
- **Email**: `redha@skillspark.com`
- **Password**: `password123`
- **Role**: Instructor

### Admin Account
- **Email**: `admin@skillspark.com`
- **Password**: `admin123`
- **Role**: Administrator

## 🌐 Access Points

### Main Application
- **Homepage**: `http://localhost/SkillSpark/public/index.php`
- **Login**: `http://localhost/SkillSpark/public/index.php`

### User Dashboards
- **Student Dashboard**: After login as student
- **Instructor Account**: After login as instructor
- **Admin Dashboard**: After login as admin

### Support System
- **User Support**: `http://localhost/SkillSpark/public/support_ticket.php`
- **Admin Support**: `http://localhost/SkillSpark/public/admin_support.php`

## 📁 Project Structure

```
SkillSpark/
├── config/                 # Configuration files
│   ├── auth.php           # Authentication system
│   └── database.php       # Database connection
├── database/              # Database schema
│   └── create_database.sql
├── public/                # Public web files
│   ├── index.php         # Main landing page
│   ├── register.php      # User registration
│   ├── student-dashboard.php
│   ├── instructor-account.php
│   ├── admin-dashboard.php
│   ├── support_ticket.php
│   └── admin_support.php
├── uploads/               # User uploaded files
├── src/                   # Source files
└── README.md
```

## 🔧 Configuration

### Database Configuration
Edit `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'skillspark_db';
private $username = 'root';
private $password = '';
```

### File Upload Settings
- Maximum file size: 50MB for videos, 10MB for others
- Supported formats: JPG, PNG, PDF, MP4, AVI, MOV
- Upload directories are automatically created

## 🚀 Features Overview

### For Students
- Browse and enroll in courses
- Track learning progress
- Submit support tickets
- Manage profile and documents

### For Instructors
- Create and manage courses
- Upload course materials
- Track student progress
- View earnings and analytics
- Manage support tickets

### For Administrators
- Manage all users and courses
- Handle support tickets
- View platform analytics
- Manage system settings

## 🔒 Security Features

- **Password hashing** with PHP's `password_hash()`
- **SQL injection protection** with PDO prepared statements
- **File upload validation** with type and size checks
- **Session management** for user authentication
- **Role-based access control**

## 📊 Database Schema

The system includes comprehensive database tables:
- `users` - User accounts and profiles
- `courses` - Course information
- `enrollments` - Student course enrollments
- `lessons` - Course lessons
- `categories` - Course categories
- `support_tickets` - Support system
- `instructor_earnings` - Earnings tracking
- And many more...

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -am 'Add feature'`
4. Push to branch: `git push origin feature-name`
5. Submit a pull request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 👨‍💻 Authors

- **Mohamed Yahiaoui** - Student Role
- **Redha Yahiaoui** - Instructor Role
- **Admin Yahiaoui** - Administrator Role

## 📞 Support

For support and questions:
- Create an issue in the GitHub repository
- Use the built-in support ticket system
- Contact: [GitHub Repository](https://github.com/yahiaoui-Mohamed-Redha/SkillSpark.git)

## 🎯 Roadmap

- [ ] Mobile responsive design improvements
- [ ] Advanced analytics dashboard
- [ ] Video streaming optimization
- [ ] Payment gateway integration
- [ ] Multi-language support
- [ ] API development

---

**SkillSpark** - Empowering education through technology! 🎓✨
