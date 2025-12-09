<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$sid = intval($_GET['sid'] ?? 0);
$success = "";
$error = "";

// Get student data
$student_query = "SELECT s.*, c.course_code, c.cname, u.email, u.status as user_status 
                  FROM students s 
                  LEFT JOIN courses c ON s.cid = c.cid 
                  LEFT JOIN users u ON s.user_id = u.id 
                  WHERE s.sid = $sid LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student = mysqli_fetch_assoc($student_result);

if (!$student) {
    header("Location: admin_manage_student.php");
    exit();
}

if (isset($_POST["update_student"])) {
    $fname = mysqli_real_escape_string($conn, $_POST["fname"]);
    $mname = mysqli_real_escape_string($conn, $_POST["mname"] ?? "");
    $lname = mysqli_real_escape_string($conn, $_POST["lname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $ph_no = mysqli_real_escape_string($conn, $_POST["ph_no"]);
    $cid = intval($_POST["cid"]);
    $s_no = mysqli_real_escape_string($conn, $_POST["s_no"] ?? "");
    $s_name = mysqli_real_escape_string($conn, $_POST["s_name"] ?? "");
    $pincode = mysqli_real_escape_string($conn, $_POST["pincode"] ?? "");
    $district = mysqli_real_escape_string($conn, $_POST["district"] ?? "");
    $state = mysqli_real_escape_string($conn, $_POST["state"] ?? "");
    $country = mysqli_real_escape_string($conn, $_POST["country"] ?? "India");
    $semester = mysqli_real_escape_string($conn, $_POST["semester"] ?? "");
    $year = intval($_POST["year"] ?? date('Y'));
    $status = mysqli_real_escape_string($conn, $_POST["status"] ?? "active");
    
    if (empty($fname) || empty($lname) || empty($email) || empty($cid)) {
        $error = "Please fill all required fields";
    } else {
        // Check if email is changed and already exists
        if ($email != $student['email']) {
            $check_email = "SELECT * FROM users WHERE email = '$email' AND id != " . $student['user_id'];
            $result = mysqli_query($conn, $check_email);
            if (mysqli_num_rows($result) > 0) {
                $error = "Email already exists!";
            }
        }
        
        if (empty($error)) {
            $full_name = trim($fname . " " . $mname . " " . $lname);
            
            // Update users table
            $sql1 = "UPDATE users SET full_name = ?, email = ?, phone = ?, status = ? WHERE id = ?";
            $stmt1 = mysqli_prepare($conn, $sql1);
            mysqli_stmt_bind_param($stmt1, "ssssi", $full_name, $email, $ph_no, $status, $student['user_id']);
            
            if (mysqli_stmt_execute($stmt1)) {
                // Update students table
                $sql2 = "UPDATE students SET cid = ?, fname = ?, mname = ?, lname = ?, email = ?, ph_no = ?, s_no = ?, s_name = ?, pincode = ?, district = ?, state = ?, country = ?, semester = ?, year = ? WHERE sid = ?";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "isssssssssssssi", $cid, $fname, $mname, $lname, $email, $ph_no, $s_no, $s_name, $pincode, $district, $state, $country, $semester, $year, $sid);
                
                if (mysqli_stmt_execute($stmt2)) {
                    $success = "Student updated successfully!";
                    // Refresh student data
                    $student_result = mysqli_query($conn, $student_query);
                    $student = mysqli_fetch_assoc($student_result);
                } else {
                    $error = "Error updating student: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt2);
            } else {
                $error = "Error updating user: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt1);
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
    <title>Edit Student - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Edit Student</h1>
            <div>
                <a href="admin_view_student.php?sid=<?php echo $sid; ?>" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="admin_manage_student.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
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
                            <label>Roll Number</label>
                            <input type="text" value="<?php echo htmlspecialchars($student['roll_no']); ?>" disabled style="background: #f5f5f5;">
                        </div>
                        <div class="form-group">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="fname" value="<?php echo htmlspecialchars($student['fname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="mname" value="<?php echo htmlspecialchars($student['mname'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="lname" value="<?php echo htmlspecialchars($student['lname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="ph_no" value="<?php echo htmlspecialchars($student['ph_no'] ?? ''); ?>" pattern="[0-9]{10}">
                        </div>
                        <div class="form-group">
                            <label>Course <span class="required">*</span></label>
                            <select name="cid" required>
                                <option value="">-- Select Course --</option>
                                <?php while ($course = mysqli_fetch_assoc($courses_result)): ?>
                                    <option value="<?php echo $course['cid']; ?>" <?php echo $student['cid'] == $course['cid'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['cname']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Semester</label>
                            <input type="text" name="semester" value="<?php echo htmlspecialchars($student['semester'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" name="year" value="<?php echo $student['year'] ?? date('Y'); ?>" min="2000" max="2099">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="active" <?php echo $student['user_status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $student['user_status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="graduated" <?php echo $student['status'] == 'graduated' ? 'selected' : ''; ?>>Graduated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Street Number</label>
                            <input type="text" name="s_no" value="<?php echo htmlspecialchars($student['s_no'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Street Name</label>
                            <input type="text" name="s_name" value="<?php echo htmlspecialchars($student['s_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Pincode</label>
                            <input type="text" name="pincode" value="<?php echo htmlspecialchars($student['pincode'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text" name="district" value="<?php echo htmlspecialchars($student['district'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" value="<?php echo htmlspecialchars($student['state'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="<?php echo htmlspecialchars($student['country'] ?? 'India'); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="update_student" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Student
                    </button>
                    <a href="admin_manage_student.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

