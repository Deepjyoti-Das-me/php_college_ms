<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$success = "";
$error = "";

if (isset($_POST["add_student"])) {
    // Get form data
    $fname = mysqli_real_escape_string($conn, $_POST["fname"]);
    $mname = mysqli_real_escape_string($conn, $_POST["mname"] ?? "");
    $lname = mysqli_real_escape_string($conn, $_POST["lname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $ph_no = mysqli_real_escape_string($conn, $_POST["ph_no"]);
    $roll_no = mysqli_real_escape_string($conn, $_POST["roll_no"]);
    $cid = intval($_POST["cid"]);
    $s_no = mysqli_real_escape_string($conn, $_POST["s_no"] ?? "");
    $s_name = mysqli_real_escape_string($conn, $_POST["s_name"] ?? "");
    $pincode = mysqli_real_escape_string($conn, $_POST["pincode"] ?? "");
    $district = mysqli_real_escape_string($conn, $_POST["district"] ?? "");
    $state = mysqli_real_escape_string($conn, $_POST["state"] ?? "");
    $country = mysqli_real_escape_string($conn, $_POST["country"] ?? "India");
    $semester = mysqli_real_escape_string($conn, $_POST["semester"] ?? "");
    $year = intval($_POST["year"] ?? date('Y'));
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    
    // Validation
    if (empty($fname) || empty($lname) || empty($email) || empty($roll_no) || empty($cid) || empty($password)) {
        $error = "Please fill all required fields";
    } else {
        // Check if email exists
        $check_email = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);
        if (mysqli_num_rows($result) > 0) {
            $error = "Email already exists!";
        } else {
            // Check if roll number exists
            $check_roll = "SELECT * FROM students WHERE roll_no = '$roll_no'";
            $result = mysqli_query($conn, $check_roll);
            if (mysqli_num_rows($result) > 0) {
                $error = "Roll number already exists!";
            } else {
                // Hash password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $full_name = trim($fname . " " . $mname . " " . $lname);
                
                // Insert into users table
                $sql1 = "INSERT INTO users (full_name, email, password, user_type, phone, status) VALUES (?, ?, ?, 'student', ?, 'active')";
                $stmt1 = mysqli_prepare($conn, $sql1);
                mysqli_stmt_bind_param($stmt1, "ssss", $full_name, $email, $passwordHash, $ph_no);
                
                if (mysqli_stmt_execute($stmt1)) {
                    $user_id = mysqli_insert_id($conn);
                    
                    // Insert into students table
                    $sql2 = "INSERT INTO students (user_id, cid, roll_no, fname, mname, lname, email, ph_no, s_no, s_name, pincode, district, state, country, enrollment_date, semester, year) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?)";
                    $stmt2 = mysqli_prepare($conn, $sql2);
                    mysqli_stmt_bind_param($stmt2, "iissssssssssssssi", $user_id, $cid, $roll_no, $fname, $mname, $lname, $email, $ph_no, $s_no, $s_name, $pincode, $district, $state, $country, $semester, $year);
                    
                    if (mysqli_stmt_execute($stmt2)) {
                        $success = "Student added successfully! Roll No: $roll_no";
                        // Clear form
                        $_POST = array();
                    } else {
                        $error = "Error adding student: " . mysqli_error($conn);
                        // Delete user if student insert fails
                        mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
                    }
                    mysqli_stmt_close($stmt2);
                } else {
                    $error = "Error creating user: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt1);
            }
        }
    }
}

// Get courses for dropdown
$courses_query = "SELECT cid, course_code, cname FROM courses WHERE status = 'active' ORDER BY course_code";
$courses_result = mysqli_query($conn, $courses_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-graduate"></i> Add New Student</h1>
            <a href="admin_manage_student.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Manage Students</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="" class="admin-form">
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Personal Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="fname" value="<?php echo $_POST['fname'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="mname" value="<?php echo $_POST['mname'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="lname" value="<?php echo $_POST['lname'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="ph_no" value="<?php echo $_POST['ph_no'] ?? ''; ?>" pattern="[0-9]{10}">
                        </div>
                        <div class="form-group">
                            <label>Roll Number <span class="required">*</span></label>
                            <input type="text" name="roll_no" value="<?php echo $_POST['roll_no'] ?? ''; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Street Number</label>
                            <input type="text" name="s_no" value="<?php echo $_POST['s_no'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Street Name</label>
                            <input type="text" name="s_name" value="<?php echo $_POST['s_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Pincode</label>
                            <input type="text" name="pincode" value="<?php echo $_POST['pincode'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text" name="district" value="<?php echo $_POST['district'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" value="<?php echo $_POST['state'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="<?php echo $_POST['country'] ?? 'India'; ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-graduation-cap"></i> Academic Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Course <span class="required">*</span></label>
                            <select name="cid" required>
                                <option value="">Select Course</option>
                                <?php while ($course = mysqli_fetch_assoc($courses_result)): ?>
                                    <option value="<?php echo $course['cid']; ?>" <?php echo (isset($_POST['cid']) && $_POST['cid'] == $course['cid']) ? 'selected' : ''; ?>>
                                        <?php echo $course['course_code'] . ' - ' . $course['cname']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Semester</label>
                            <input type="text" name="semester" value="<?php echo $_POST['semester'] ?? ''; ?>" placeholder="e.g., 1st, 2nd">
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" name="year" value="<?php echo $_POST['year'] ?? date('Y'); ?>" min="2020" max="2030">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-lock"></i> Account Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Password <span class="required">*</span></label>
                            <input type="password" name="password" required minlength="8">
                            <small>Minimum 8 characters</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="add_student" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Student
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset Form
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>


