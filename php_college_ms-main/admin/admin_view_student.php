<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$sid = intval($_GET['sid'] ?? 0);
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-eye"></i> View Student Details</h1>
            <div>
                <a href="admin_edit_student.php?sid=<?php echo $sid; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="admin_manage_student.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
            <!-- Profile Card -->
            <div class="form-container">
                <div style="text-align: center; padding: 20px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #4CAF50, #45a049); display: flex; align-items: center; justify-content: center; font-size: 48px; color: white;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($student['fname'] . ' ' . ($student['mname'] ? $student['mname'] . ' ' : '') . $student['lname']); ?></h2>
                    <p style="color: #666; margin-bottom: 20px;"><?php echo htmlspecialchars($student['roll_no']); ?></p>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px; margin-bottom: 10px;">
                        <strong>Course:</strong><br>
                        <?php echo htmlspecialchars(($student['course_code'] ?? 'N/A') . ' - ' . ($student['cname'] ?? 'No Course')); ?>
                    </div>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px;">
                        <strong>Status:</strong><br>
                        <span style="color: <?php echo $student['user_status'] == 'active' ? '#4CAF50' : '#F44336'; ?>;">
                            <?php echo strtoupper($student['user_status']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Student Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['roll_no']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['fname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['mname'] ?? ''); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['lname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($student['email']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['ph_no'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Course Code</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['course_code'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['cname'] ?? 'No Course'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Semester</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['semester'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Year</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['year'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Enrollment Date</label>
                        <input type="text" value="<?php echo $student['enrollment_date'] ? date('d M Y', strtotime($student['enrollment_date'])) : 'N/A'; ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Address</label>
                        <textarea rows="3" disabled style="background: #f5f5f5;">
<?php 
$address = [];
if ($student['s_no']) $address[] = $student['s_no'];
if ($student['s_name']) $address[] = $student['s_name'];
if ($student['district']) $address[] = $student['district'];
if ($student['state']) $address[] = $student['state'];
if ($student['pincode']) $address[] = $student['pincode'];
if ($student['country']) $address[] = $student['country'];
echo !empty($address) ? htmlspecialchars(implode(', ', $address)) : 'N/A';
?>
                        </textarea>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

