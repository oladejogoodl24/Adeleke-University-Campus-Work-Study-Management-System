# Work-Study Management System Documentation

## 📋 System Overview

**Work-Study Management System** is a comprehensive web application designed to manage work-study programs at Adeleke University. The system handles student job applications, work hour logging, supervisor approvals, and administrative oversight.

**Technology Stack:**
- **Backend:** PHP 8.x with MySQLi
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Database:** MySQL
- **Security:** Password hashing, prepared statements, session management

---

## 👥 User Roles & Access Levels

### 1. **Administrator (Admin)**
**Login:** Staff ID starting with "ADM" or role = 'admin' in database
**Responsibilities:**
- Approve/Reject student registrations
- Approve/Reject supervisor registrations
- Full system oversight
- Access to all management features

### 2. **Supervisor**
**Login:** Any approved supervisor account
**Responsibilities:**
- Review job applications for positions they supervise
- Approve/Reject work logs from their students
- Limited access to only their own data

### 3. **Student**
**Login:** Matriculation number and password
**Responsibilities:**
- Browse and apply for work-study jobs
- Log work hours for approved positions
- View application and work log status

---

## 📁 Complete Page Structure

### **Public Pages (No Login Required)**

#### `index.php` - Landing Page
- **Purpose:** Main entry point to the system
- **Features:**
  - Welcome message
  - Navigation to login pages
  - System overview
- **Links to:** Student Login, Supervisor Login

#### `student_login.php` - Student Authentication
- **Purpose:** Student login portal
- **Features:**
  - Matric number + password authentication
  - Password verification with hashing
  - Session management
- **Redirects to:** `student_dashboard.php` on success

#### `supervisor_login.php` - Management Authentication
- **Purpose:** Supervisor/Admin login portal
- **Features:**
  - Staff ID + password authentication
  - Role detection (admin vs supervisor)
  - Session-based access control
- **Redirects to:** `management/dashboard.php`

---

### **Student Dashboard Pages**

#### `student_dashboard.php` - Student Main Dashboard
- **Purpose:** Central hub for student activities
- **Features:**
  - **Navigation Sidebar:**
    - Dashboard (overview)
    - Browse Jobs (available positions)
    - My Applications (application status)
    - Log Hours (work logging)
    - Summary (work history)
  - **Dashboard Tab:**
    - Welcome message
    - Quick stats (approved jobs, pending applications)
    - Recent activity
  - **Browse Jobs Tab:**
    - List all available work-study positions
    - Job details (title, department, hours, supervisor)
    - Apply button for each job
  - **My Applications Tab:**
    - View all job applications
    - Status tracking (pending/approved/rejected)
    - Rejection reasons (if applicable)
  - **Log Hours Tab:**
    - Work hour submission form
    - Date, hours worked, description
    - Job selection dropdown
  - **Summary Tab:**
    - Work history overview
    - Total hours worked
    - Payment calculations

---

### **Management Dashboard Pages**

#### `management/dashboard.php` - Management Overview
- **Purpose:** Main dashboard for supervisors and admins
- **Features:**
  - **Statistics Cards:**
    - Pending Students (admin only)
    - Approved Students
    - Pending Supervisors (admin only)
    - Pending Job Applications
    - Pending Work Logs
  - **Quick Actions:**
    - Direct buttons to approval pages
  - **Recent Activity:**
    - Latest pending items
  - **Mobile Responsive:**
    - Hamburger menu for mobile devices
    - Collapsible sidebar

#### `management/approve_student.php` - Student Approval (Admin Only)
- **Purpose:** Review and approve student registrations
- **Features:**
  - **Access Control:** Admin-only access
  - **Pending Students Section:**
    - Student details (name, matric, email, department, level)
    - Approve/Reject buttons
    - Rejection reason textarea
  - **Approved Students Section:**
    - List of all approved students
  - **Database Operations:**
    - Updates `students.status` to 'approved'/'rejected'
    - Stores rejection reasons

#### `management/approve_supervisour.php` - Supervisor Approval (Admin Only)
- **Purpose:** Review and approve supervisor registrations
- **Features:**
  - **Access Control:** Admin-only access
  - **Pending Supervisors Section:**
    - Supervisor details (name, staff ID, email)
    - Approve/Reject buttons
    - Rejection reason textarea
  - **Approved Supervisors Section:**
    - List of all approved supervisors
  - **Database Operations:**
    - Updates `supervisors.status` to 'approved'/'rejected'

#### `management/approve_applications.php` - Job Application Review
- **Purpose:** Supervisors review student job applications
- **Features:**
  - **Supervisor Filtering:** Only shows applications for jobs they supervise
  - **Three Sections:**
    - **Pending Applications:** Applications awaiting review
    - **Approved Applications:** Successfully approved applications
    - **Rejected Applications:** Rejected applications with reasons
  - **Application Details:**
    - Student information
    - Job details
    - Application date
    - Approval/Rejection actions
  - **Database Operations:**
    - Updates `job_applications.status`
    - Stores `updated_at` timestamp
    - Stores rejection reasons

#### `management/approve_worklog.php` - Work Log Review
- **Purpose:** Supervisors review student work hour submissions
- **Features:**
  - **Supervisor Filtering:** Only shows work logs from their students
  - **Two Sections:**
    - **Pending Work Logs:** Hours awaiting approval
    - **All Work Logs:** Complete history with status
  - **Work Log Details:**
    - Student name and matric number
    - Work date, hours, description
    - Supervisor name
    - Status (pending/approved/rejected)
    - Rejection reasons (if applicable)
  - **Database Operations:**
    - Updates `work_logs.status`
    - Stores `updated_at` timestamp
    - Stores rejection reasons

---

## 🗄️ Database Structure

### **Core Tables:**

#### `students`
- **Purpose:** Student account information
- **Key Fields:**
  - `id` (Primary Key)
  - `full_name`, `matric_number`, `email`
  - `department`, `level`, `scholarship_status`
  - `status` (pending/approved/rejected)
  - `password` (hashed)
  - `created_at`

#### `supervisors`
- **Purpose:** Supervisor/Admin account information
- **Key Fields:**
  - `id` (Primary Key)
  - `full_name`, `staff_id`, `email`
  - `status` (pending/approved/rejected)
  - `role` (supervisor/admin)
  - `password` (hashed)
  - `created_at`

#### `jobs`
- **Purpose:** Available work-study positions
- **Key Fields:**
  - `id` (Primary Key)
  - `title`, `department`
  - `hours_required`, `positions_available`
  - `supervisor` (name) - **Legacy field**
  - `supervisor_id` (Foreign Key to supervisors.id) - **New field**

#### `job_applications`
- **Purpose:** Student job application records
- **Key Fields:**
  - `id` (Primary Key)
  - `student_id` (FK to students.id)
  - `job_id` (FK to jobs.id)
  - `application_date`, `status`
  - `updated_at` (timestamp)
  - `rejection_reason` (text)

#### `work_logs`
- **Purpose:** Student work hour submissions
- **Key Fields:**
  - `id` (Primary Key)
  - `student_id` (FK to students.id)
  - `supervisor_id` (FK to supervisors.id)
  - `job_id` (FK to jobs.id) - **New field**
  - `work_date`, `hours_worked`, `description`
  - `status` (pending/approved/rejected)
  - `updated_at` (timestamp)
  - `rejection_reason` (text)
  - `created_at`

#### `admins` (Legacy)
- **Purpose:** Separate admin table (not currently used)
- **Note:** System now uses supervisors table with role='admin'

---

## 🔄 Complete System Workflows

### **Student Registration & Approval Workflow**

```
1. Student visits index.php
2. Student clicks "Student Login"
3. Student registers (if new) → status = 'pending'
4. Admin logs in with staff_id = '24/0856'
5. Admin goes to "Approve Students"
6. Admin approves/rejects student
7. Student status changes to 'approved'/'rejected'
8. Approved student can now login and use system
```

### **Job Application Workflow**

```
1. Student logs in → student_dashboard.php
2. Student clicks "Browse Jobs" tab
3. Student sees available positions
4. Student clicks "Apply" on desired job
5. Application created with status = 'pending'
6. Supervisor logs in → management/dashboard.php
7. Supervisor sees pending applications count
8. Supervisor clicks "Job Applications"
9. Supervisor reviews application details
10. Supervisor approves/rejects with optional reason
11. Student sees status update in "My Applications"
```

### **Work Logging Workflow**

```
1. Student gets job approved
2. Student clicks "Log Hours" tab
3. Student fills work log form (date, hours, description, job)
4. Work log created with status = 'pending'
5. Supervisor sees pending work logs count
6. Supervisor clicks "View Work Logs"
7. Supervisor reviews work details
8. Supervisor approves/rejects with optional reason
9. Student sees approval in dashboard
10. Approved hours count toward payment
```

---

## 🔐 Security Features

### **Authentication & Authorization**
- **Password Hashing:** Uses PHP `password_hash()` with PASSWORD_DEFAULT
- **Session Management:** Proper session handling with timeouts
- **Role-Based Access:** Different permissions for admin/supervisor/student
- **SQL Injection Protection:** All queries use prepared statements

### **Access Control Examples**
- Admin-only pages check: `$_SESSION['management_role'] == 'admin'`
- Supervisor filtering: `WHERE supervisor_id = ?`
- Student data isolation: `WHERE student_id = ?`

---

## 📱 Mobile Responsiveness

### **Responsive Features:**
- **Viewport Meta Tag:** `<meta name="viewport" content="width=device-width, initial-scale=1.0">`
- **Mobile Breakpoint:** `@media (max-width: 768px)`
- **Hamburger Menu:** JavaScript toggle for mobile navigation
- **Flexible Layouts:** Grid and flexbox for different screen sizes
- **Touch-Friendly:** Minimum 44px button sizes

### **Mobile-Optimized Pages:**
- All management pages have mobile layouts
- Student dashboard adapts to mobile screens
- Forms stack vertically on small screens
- Sidebar becomes overlay menu on mobile

---

## 🎨 Design System

### **Color Scheme:**
- **Primary:** `#261661` (Dark Purple) - headers, buttons, badges
- **Accent:** `#FFBF00` (Gold) - highlights, hover effects
- **Background:** `#D5E4EF` (Light Blue) - main background
- **White:** `#ffffff` - cards, content areas

### **Typography:**
- **Font Family:** Segoe UI, Arial, sans-serif
- **Responsive Sizing:** Font sizes adjust for mobile

### **UI Components:**
- **Cards:** White containers with subtle shadows
- **Buttons:** Purple with gold hover effects
- **Badges:** Status indicators (pending/approved/rejected)
- **Forms:** Clean inputs with proper spacing

---

## 🚀 Deployment & Setup

### **Requirements:**
- PHP 8.x or higher
- MySQL 5.7+ or MariaDB 10.0+
- Apache/Nginx web server
- XAMPP/WAMP for local development

### **Installation Steps:**
1. **Database Setup:**
   - Import `adeleke_work_study.sql`
   - Run additional update commands from `database_updates.sql`

2. **File Permissions:**
   - Ensure web server can read PHP files
   - Session directory writable

3. **Configuration:**
   - Update database credentials in `db.php`
   - Configure virtual host if needed

4. **Admin Setup:**
   - Login with Staff ID: `24/0856`
   - Password: [set during initial setup]

---

## 📊 Key Features Summary

### **Student Features:**
- ✅ Job browsing and application
- ✅ Work hour logging
- ✅ Application status tracking
- ✅ Rejection reason viewing
- ✅ Mobile-responsive interface

### **Supervisor Features:**
- ✅ Job application approval/rejection
- ✅ Work log review and approval
- ✅ Limited access to own data only
- ✅ Rejection reason specification

### **Admin Features:**
- ✅ Student registration approval
- ✅ Supervisor registration approval
- ✅ Full system oversight
- ✅ Complete access to all features

### **System Features:**
- ✅ Role-based access control
- ✅ Secure authentication
- ✅ Mobile responsiveness
- ✅ Comprehensive audit trails
- ✅ Real-time status updates

---

## 🔧 Recent Updates & Fixes

### **Database Improvements:**
- Added `supervisor_id` to jobs table
- Added `updated_at` timestamps
- Added `rejection_reason` fields
- Added `job_id` to work_logs

### **Code Fixes:**
- Fixed admin role detection
- Fixed page redirects after form submission
- Added rejection reason textareas
- Improved supervisor filtering logic

### **UI/UX Improvements:**
- Mobile-responsive design
- Consistent color scheme
- Hamburger menu for mobile
- Enhanced error handling

---

## 🎯 Presentation Points

### **System Strengths:**
1. **Complete Workflow:** End-to-end student-to-payment process
2. **Security:** Proper authentication and authorization
3. **Scalability:** Role-based access for future expansion
4. **User Experience:** Intuitive interfaces for all user types
5. **Mobile Ready:** Works on phones and tablets
6. **Data Integrity:** Foreign key relationships and constraints

### **Technical Highlights:**
- Modern PHP practices (prepared statements, sessions)
- Responsive CSS with mobile-first approach
- Clean database design with proper relationships
- Comprehensive error handling and validation
- Audit trails for all approvals/rejections

This documentation covers every aspect of the Work-Study Management System. The system is production-ready and handles the complete lifecycle of work-study program management at Adeleke University.</content>
<filePath>PROJECT_DOCUMENTATION.md