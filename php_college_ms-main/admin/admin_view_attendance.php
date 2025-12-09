<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

// Get filter parameters
$filter_course = isset($_GET['course']) ? intval($_GET['course']) : 0;
$filter_subject = isset($_GET['subject']) ? intval($_GET['subject']) : 0;
$filter_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Build query
$attendance_query = "SELECT a.*, s.roll_no, s.fname, s.lname, sub.subject_name, sub.subject_code, c.course_code, c.cname
                     FROM attendance a
                     JOIN students s ON a.sid = s.sid
                     JOIN subjects sub ON a.subject_id = sub.id
                     LEFT JOIN courses c ON s.cid = c.cid
                     WHERE 1=1";

if ($filter_course > 0) {
    $attendance_query .= " AND s.cid = $filter_course";
}
if ($filter_subject > 0) {
    $attendance_query .= " AND a.subject_id = $filter_subject";
}
if ($filter_date) {
    $attendance_query .= " AND a.date = '$filter_date'";
}

$attendance_query .= " ORDER BY a.date DESC, s.roll_no";

$attendance_result = mysqli_query($conn, $attendance_query);

// Get courses and subjects for filters
$courses_query = "SELECT cid, course_code, cname FROM courses WHERE status = 'active' ORDER BY course_code";
$courses_result = mysqli_query($conn, $courses_query);

$subjects_query = "SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_code";
$subjects_result = mysqli_query($conn, $subjects_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-calendar-check"></i> View Attendance</h1>
        </div>

        <div class="form-container" style="margin-bottom: 24px;">
            <form method="GET" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
                <div class="form-group">
                    <label>Filter by Course</label>
                    <select name="course">
                        <option value="0">All Courses</option>
                        <?php 
                        mysqli_data_seek($courses_result, 0);
                        while ($course = mysqli_fetch_assoc($courses_result)): 
                        ?>
                            <option value="<?php echo $course['cid']; ?>" <?php echo $filter_course == $course['cid'] ? 'selected' : ''; ?>>
                                <?php echo $course['course_code'] . ' - ' . $course['cname']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter by Subject</label>
                    <select name="subject">
                        <option value="0">All Subjects</option>
                        <?php 
                        mysqli_data_seek($subjects_result, 0);
                        while ($subject = mysqli_fetch_assoc($subjects_result)): 
                        ?>
                            <option value="<?php echo $subject['id']; ?>" <?php echo $filter_subject == $subject['id'] ? 'selected' : ''; ?>>
                                <?php echo $subject['subject_code'] . ' - ' . $subject['subject_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter by Date</label>
                    <input type="date" name="date" value="<?php echo $filter_date; ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="admin_view_attendance.php" class="btn btn-secondary" style="margin-top: 8px; display: block; text-align: center;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($attendance_result) > 0): ?>
                        <?php while ($attendance = mysqli_fetch_assoc($attendance_result)): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($attendance['date'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($attendance['roll_no']); ?></strong></td>
                                <td><?php echo htmlspecialchars($attendance['fname'] . ' ' . $attendance['lname']); ?></td>
                                <td><?php echo htmlspecialchars($attendance['course_code'] . ' - ' . $attendance['cname']); ?></td>
                                <td><?php echo htmlspecialchars($attendance['subject_code'] . ' - ' . $attendance['subject_name']); ?></td>
                                <td>
                                    <?php
                                    $status_colors = [
                                        'present' => ['#d4edda', '#155724'],
                                        'absent' => ['#f8d7da', '#721c24'],
                                        'late' => ['#fff3cd', '#856404'],
                                        'excused' => ['#d1ecf1', '#0c5460']
                                    ];
                                    $colors = $status_colors[$attendance['status']] ?? ['#e0e0e0', '#666'];
                                    ?>
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                        background: <?php echo $colors[0]; ?>;
                                        color: <?php echo $colors[1]; ?>;">
                                        <?php echo ucfirst($attendance['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($attendance['remarks'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                No attendance records found for the selected filters.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


