# Database Setup Guide for XAMPP

## Step-by-Step Instructions

### 1. Start XAMPP Services
- Open **XAMPP Control Panel**
- Start **Apache** and **MySQL** services
- Make sure both show green (running)

### 2. Open phpMyAdmin
- Click **Admin** button next to MySQL (or go to: `http://localhost/phpmyadmin`)

### 3. Create Database
- Click on **"New"** in the left sidebar
- Database name: `college_ms1`
- Collation: `utf8mb4_general_ci`
- Click **"Create"**

### 4. Import SQL File
**Option A: Using SQL Tab**
- Select the `college_ms1` database from left sidebar
- Click on **"SQL"** tab at the top
- Copy the entire content from `database_setup.sql` file
- Paste it in the SQL textarea
- Click **"Go"** button

**Option B: Using Import Tab**
- Select the `college_ms1` database
- Click on **"Import"** tab
- Click **"Choose File"** button
- Select `database_setup.sql` file
- Click **"Go"** button

### 5. Verify Tables
After import, you should see these tables in the left sidebar:
- ✅ users
- ✅ students
- ✅ teachers
- ✅ staff
- ✅ courses
- ✅ subjects
- ✅ sessions
- ✅ attendance
- ✅ grades
- ✅ assignments
- ✅ assignment_submissions
- ✅ notifications
- ✅ class_schedule

### 6. Test Default Admin Login
- Email: `admin@college.com`
- Password: `admin123`
- User Type: Select **Admin** on login page

---

## Database Configuration

The database connection is already configured in `database.php`:
```php
Host: localhost
Username: root
Password: (empty)
Database: college_ms1
```

If you need to change these settings, edit `database.php` file.

---

## Quick SQL Commands (if needed)

### Create Database Manually:
```sql
CREATE DATABASE college_ms1;
USE college_ms1;
```

### Check if tables exist:
```sql
SHOW TABLES;
```

### View users table structure:
```sql
DESCRIBE users;
```

---

## Troubleshooting

### Error: "Database doesn't exist"
- Make sure you created the database `college_ms1` first

### Error: "Access denied"
- Check MySQL is running in XAMPP
- Default username is `root` with no password

### Error: "Table already exists"
- Drop the database and recreate it, OR
- Use `CREATE TABLE IF NOT EXISTS` (already in SQL file)

### Can't connect to database
- Check `database.php` file settings
- Verify MySQL service is running in XAMPP
- Check if port 3306 is available

---

## Notes

- All passwords are hashed using PHP's `password_hash()` function
- The default admin password is: `admin123`
- Foreign keys are set up for data integrity
- All tables use `utf8mb4` charset for proper Unicode support






