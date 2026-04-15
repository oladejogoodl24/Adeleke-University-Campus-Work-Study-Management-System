# Adeleke University Work-Study Portal

A comprehensive PHP-based web application for managing campus work-study programs at Adeleke University.

## Features

- **Student Module**: Register, apply for jobs, log work hours, track work-study status
- **Supervisor Module**: Manage job postings, approve student work logs, monitor work hours
- **Admin Module**: System-wide approvals, student/supervisor management, comprehensive reporting
- **Responsive Design**: Mobile-friendly interface with modern UI/UX
- **Session Management**: Secure login and authentication for all user roles

## Project Structure

```
work_study/
├── admin/               # Admin-related files
├── assets/              # Images and static resources
├── management/          # Supervisor/Admin dashboard
├── student/             # Student-specific pages
├── supervisor/          # Supervisor login/registration
├── index.php            # Role selection page
├── student_login.php    # Student login
├── supervisor_login.php # Supervisor login
├── admin_login.php      # Admin login
├── student_dashboard.php # Main student interface
├── db.php               # Database connection (not tracked in git)
└── logout.php           # Logout handler
```

## Installation & Setup

### Prerequisites
- XAMPP or similar PHP development environment
- PHP 7.4+
- MySQL/MariaDB database
- Git

### Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/work-study-portal.git
   cd work-study-portal
   ```

2. **Database Setup**
   - Create a MySQL database named `adeleke_work_study`
   - Import the database schema from `database_updates.sql`

3. **Configure Database Connection**
   - Create `db.php` in the root directory:
   ```php
   <?php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $database = "adeleke_work_study";

   $conn = new mysqli($servername, $username, $password, $database);

   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ?>
   ```

4. **Start Development Server**
   - Place project in XAMPP's htdocs folder
   - Access via `http://localhost/work_study/`

## User Roles & Access

### Student
- Apply for available jobs
- Log daily work hours
- Track work-study status
- View approved hours

### Supervisor
- Create and manage job postings
- Approve/reject student work logs
- Monitor student hours
- Generate reports

### Admin
- Approve student registrations
- Approve supervisor accounts
- View all work logs
- System-wide management and reporting

## Usage

1. Navigate to `/index.php` to select your role
2. Log in or register based on your role
3. Credentials will be verified against the database
4. Upon approval (for students/supervisors), access features

## Testing Credentials

- For admin: Use credentials from the `admins` table
- Students and supervisors must register and be approved by admin
- Test supervisor can have staff_id starting with "ADM" to auto-set as admin role

## Security Notes

- Database credentials should be stored in environment variables (not included in git)
- Passwords are hashed using PHP's `password_hash()` function
- SQL prepared statements prevent injection attacks
- Session-based authentication

## Recent Updates (April 2026)

- ✅ Fixed popup modal for "+Log Hours" feature
- ✅ Unified login/registration UI across all user types
- ✅ Fixed logout redirect behavior
- ✅ Fixed admin redirect to correct dashboard
- ✅ Improved mobile responsiveness
- ✅ Centered card layouts for better UX

## Known Issues & To-Do

- [ ] Email notification system
- [ ] Payment integration for work-study stipends
- [ ] Advanced reporting dashboard
- [ ] Two-factor authentication
- [ ] API endpoint documentation

## Contributors

- Development Team - Adeleke University

## License

© 2026 Adeleke University. All Rights Reserved.
