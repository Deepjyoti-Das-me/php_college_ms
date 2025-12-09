# College Management System - Project Structure

## 📁 Folder Organization

The project has been organized into separate folders for better code management and easier navigation.

### Root Directory
Contains shared files used across all panels:
- `database.php` - Database connection file
- `login.php` - Login page
- `registration.php` - Registration page
- `logout.php` - Logout handler
- `index.php` - Homepage
- `login.css`, `registration.css`, `style.css` - Shared CSS files
- `database_complete.sql` - Database schema
- Documentation files (README.md, SETUP_INSTRUCTIONS.md, etc.)

### 📂 admin/
Contains all admin panel files:
- `admin.php` - Admin dashboard
- `admin_header.php` - Admin header/sidebar component
- `admin.css` - Admin panel styles
- `admin_add_student.php` - Add student form
- `admin_manage_student.php` - Manage students page
- `admin_add_staff.php` - Add staff form
- `admin_manage_staff.php` - Manage staff page
- `admin_add_course.php` - Add course form
- `admin_manage_course.php` - Manage courses page
- `admin_add_subject.php` - Add subject form
- `admin_manage_subject.php` - Manage subjects page
- `admin_session.php` - Session management
- `admin_view_attendance.php` - View attendance
- `admin_notify_staff.php` - Notify staff
- `admin_notify_student.php` - Notify students
- `admin_student_feedback.php` - Student feedback management
- `admin_staff_feedback.php` - Staff feedback management
- `admin_staff_leave.php` - Staff leave management
- `admin_student_leave.php` - Student leave management
- `admin_update_profile.php` - Update admin profile

### 📂 student/
Contains all student panel files:
- `student.php` - Student dashboard
- `student_header.php` - Student header/sidebar component
- `student.css` - Student panel styles
- `student_my_profile.php` - View profile
- `student_attendance.php` - View attendance
- `student_courses.php` - View courses
- `student_assignments.php` - View/submit assignments
- `student_grades.php` - View grades
- `student_schedule.php` - View class schedule
- `student_leave.php` - Request leave
- `student_feedback.php` - Submit feedback
- `student_notifications.php` - View notifications

### 📂 teacher/
Contains all teacher panel files:
- `teacher.php` - Teacher dashboard
- `teacher_header.php` - Teacher header/sidebar component
- `teacher.css` - Teacher panel styles
- `teacher_my_profile.php` - View profile
- `teacher_classes.php` - View assigned classes
- `teacher_take_attendance.php` - Mark attendance
- `teacher_assignments.php` - Create/manage assignments
- `teacher_grades.php` - Enter/manage grades
- `teacher_my_students.php` - View students
- `teacher_schedule.php` - View class schedule
- `teacher_notifications.php` - View notifications
- `get_students.php` - AJAX helper for fetching students

### 📂 staff/
Contains all staff panel files:
- `staff.php` - Staff dashboard
- `staff_header.php` - Staff header/sidebar component
- `staff.css` - Staff panel styles
- `staff_my_profile.php` - View profile
- `staff_leave.php` - Request leave
- `staff_feedback.php` - Submit feedback
- `staff_notifications.php` - View notifications

## 🔗 Path References

### Database Connection
All panel files use: `require_once "../database.php";`

### CSS Files
All panel files reference their CSS in the same folder:
- Admin: `href="admin.css"`
- Student: `href="student.css"`
- Teacher: `href="teacher.css"`
- Staff: `href="staff.css"`

### Header Includes
All panel pages include their header from the same folder:
- `<?php include 'admin_header.php'; ?>`
- `<?php include 'student_header.php'; ?>`
- `<?php include 'teacher_header.php'; ?>`
- `<?php include 'staff_header.php'; ?>`

### Login Redirects
After login, users are redirected to:
- Admin: `admin/admin.php`
- Student: `student/student.php`
- Teacher: `teacher/teacher.php`
- Staff: `staff/staff.php`

### Logout
All panels link to: `../logout.php`

### Internal Links
Links within the same panel folder work as relative paths (e.g., `admin_add_student.php` from `admin.php`)

## 📝 Notes

- All files have been updated with correct relative paths
- Session checks redirect to `../login.php` if not authenticated
- Database connection is shared from root directory
- Each panel is self-contained in its own folder for easy maintenance

## 🎯 Benefits of This Structure

1. **Easy Navigation** - Find files quickly by role
2. **Better Organization** - Related files grouped together
3. **Easier Maintenance** - Update one panel without affecting others
4. **Clear Separation** - Each user type has its own space
5. **Scalability** - Easy to add new features to specific panels

