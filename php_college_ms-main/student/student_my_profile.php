<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "student") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$student_email = $_SESSION["user_email"];
$student_query = "SELECT s.*, c.course_code, c.cname, u.email, u.phone 
                   FROM students s 
                   JOIN users u ON s.user_id = u.id 
                   LEFT JOIN courses c ON s.cid = c.cid 
                   WHERE u.email = '$student_email' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Student Panel</title>
    <link rel="stylesheet" href="student.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user"></i> My Profile</h1>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
            <!-- Profile Card -->
            <div class="form-container">
                <div style="text-align: center; padding: 20px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #4CAF50, #45a049); display: flex; align-items: center; justify-content: center; font-size: 48px; color: white;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($student_data['fname'] . ' ' . ($student_data['mname'] ? $student_data['mname'] . ' ' : '') . $student_data['lname']); ?></h2>
                    <p style="color: #666; margin-bottom: 20px;"><?php echo htmlspecialchars($student_data['roll_no']); ?></p>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px; margin-bottom: 10px;">
                        <strong>Course:</strong><br>
                        <?php echo htmlspecialchars(($student_data['course_code'] ?? 'N/A') . ' - ' . ($student_data['cname'] ?? 'No Course')); ?>
                    </div>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px;">
                        <strong>Semester:</strong> <?php echo htmlspecialchars($student_data['semester'] ?? 'N/A'); ?><br>
                        <strong>Year:</strong> <?php echo htmlspecialchars($student_data['year'] ?? 'N/A'); ?>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Personal Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($student_data['roll_no']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($student_data['fname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($student_data['mname'] ?? ''); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($student_data['lname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($student_data['email']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($student_data['ph_no'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Address</label>
                        <textarea rows="3" disabled style="background: #f5f5f5;">
<?php 
$address = [];
if ($student_data['s_no']) $address[] = $student_data['s_no'];
if ($student_data['s_name']) $address[] = $student_data['s_name'];
if ($student_data['district']) $address[] = $student_data['district'];
if ($student_data['state']) $address[] = $student_data['state'];
if ($student_data['pincode']) $address[] = $student_data['pincode'];
if ($student_data['country']) $address[] = $student_data['country'];
echo !empty($address) ? htmlspecialchars(implode(', ', $address)) : 'N/A';
?>
                        </textarea>
                    </div>
                    <div class="form-group">
                        <label>Enrollment Date</label>
                        <input type="text" value="<?php echo $student_data['enrollment_date'] ? date('d M Y', strtotime($student_data['enrollment_date'])) : 'N/A'; ?>" disabled style="background: #f5f5f5;">
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>


