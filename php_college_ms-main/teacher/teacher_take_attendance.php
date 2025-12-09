<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "teacher") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$teacher_email = $_SESSION["user_email"];
$teacher_query = "SELECT tid FROM teachers t JOIN users u ON t.user_id = u.id WHERE u.email = '$teacher_email' LIMIT 1";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher_data = mysqli_fetch_assoc($teacher_result);
$tid = $teacher_data['tid'];

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_attendance'])) {
    $subject_id = intval($_POST['subject_id']);
    $date = $_POST['date'];
    $attendance_data = $_POST['attendance'] ?? [];
    
    foreach ($attendance_data as $sid => $status) {
        $sid = intval($sid);
        $status = mysqli_real_escape_string($conn, $status);
        $remarks = isset($_POST['remarks'][$sid]) ? mysqli_real_escape_string($conn, $_POST['remarks'][$sid]) : '';
        
        // Check if attendance already exists
        $check_query = "SELECT id FROM attendance WHERE sid = $sid AND subject_id = $subject_id AND date = '$date'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Update existing
            $update_query = "UPDATE attendance SET status = '$status', tid = $tid, remarks = '$remarks' 
                            WHERE sid = $sid AND subject_id = $subject_id AND date = '$date'";
            mysqli_query($conn, $update_query);
        } else {
            // Insert new
            $insert_query = "INSERT INTO attendance (sid, subject_id, tid, date, status, remarks) 
                           VALUES ($sid, $subject_id, $tid, '$date', '$status', '$remarks')";
            mysqli_query($conn, $insert_query);
        }
    }
    
    echo "<div class='alert alert-success'>Attendance marked successfully!</div>";
}

// Get selected subject
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
$selected_subject = null;
if ($subject_id > 0) {
    $subject_query = "SELECT * FROM subjects WHERE id = $subject_id AND tid = $tid LIMIT 1";
    $subject_result = mysqli_query($conn, $subject_query);
    $selected_subject = mysqli_fetch_assoc($subject_result);
}

// Get all subjects taught by this teacher
$subjects_query = "SELECT * FROM subjects WHERE tid = $tid ORDER BY subject_name";
$subjects_result = mysqli_query($conn, $subjects_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance - Teacher Panel</title>
    <link rel="stylesheet" href="teacher.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'teacher_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-calendar-check"></i> Take Attendance</h1>
        </div>

        <div class="form-container">
            <form method="GET" style="margin-bottom: 20px;">
                <div class="form-group">
                    <label>Select Subject</label>
                    <select name="subject_id" required onchange="this.form.submit()">
                        <option value="">-- Select Subject --</option>
                        <?php while ($subject = mysqli_fetch_assoc($subjects_result)): ?>
                            <option value="<?php echo $subject['id']; ?>" <?php echo $subject_id == $subject['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>

            <?php if ($selected_subject): ?>
                <?php
                // Get students enrolled in this subject's course
                $students_query = "SELECT s.*, u.email 
                                  FROM students s 
                                  JOIN users u ON s.user_id = u.id 
                                  WHERE s.cid = (SELECT cid FROM subjects WHERE id = $subject_id) 
                                  AND s.status = 'active'
                                  ORDER BY s.roll_no";
                $students_result = mysqli_query($conn, $students_query);
                ?>
                <form method="POST">
                    <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <h3 style="margin: 20px 0;">Mark Attendance for: <?php echo htmlspecialchars($selected_subject['subject_name']); ?></h3>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['roll_no']); ?></td>
                                    <td><?php echo htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?></td>
                                    <td>
                                        <select name="attendance[<?php echo $student['sid']; ?>]" required>
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="late">Late</option>
                                            <option value="excused">Excused</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="remarks[<?php echo $student['sid']; ?>]" placeholder="Optional remarks">
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" name="submit_attendance" class="btn-primary">
                            <i class="fas fa-save"></i> Submit Attendance
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-calendar-check" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p>Please select a subject to mark attendance.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

