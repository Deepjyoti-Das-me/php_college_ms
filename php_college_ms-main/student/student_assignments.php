<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "student") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$student_email = $_SESSION["user_email"];
$student_query = "SELECT s.sid, s.cid FROM students s JOIN users u ON s.user_id = u.id WHERE u.email = '$student_email' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);
$sid = $student_data['sid'];
$cid = $student_data['cid'];

// Get assignments for student's course
$assignments_query = "SELECT a.*, sub.subject_code, sub.subject_name, t.fname as t_fname, t.lname as t_lname,
                      (SELECT COUNT(*) FROM assignment_submissions asub WHERE asub.assignment_id = a.id AND asub.sid = $sid) as submitted
                      FROM assignments a
                      JOIN subjects sub ON a.subject_id = sub.id
                      LEFT JOIN teachers t ON a.tid = t.tid
                      WHERE sub.cid = $cid
                      ORDER BY a.due_date ASC";
$assignments_result = mysqli_query($conn, $assignments_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - Student Panel</title>
    <link rel="stylesheet" href="student.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-clipboard-list"></i> My Assignments</h1>
        </div>

        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Due Date</th>
                        <th>Total Marks</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($assignments_result) > 0): ?>
                        <?php while ($assignment = mysqli_fetch_assoc($assignments_result)): ?>
                            <?php
                            $due_date = new DateTime($assignment['due_date']);
                            $now = new DateTime();
                            $is_overdue = $due_date < $now;
                            $is_submitted = $assignment['submitted'] > 0;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($assignment['subject_code']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($assignment['subject_name']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                <td style="max-width: 300px;">
                                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo htmlspecialchars($assignment['description'] ?? 'No description'); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php echo date('d M Y, h:i A', strtotime($assignment['due_date'])); ?>
                                    <?php if ($is_overdue && !$is_submitted): ?>
                                        <br><small style="color: #E64A4A;">Overdue</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $assignment['total_marks'] ?? 'N/A'; ?></td>
                                <td>
                                    <?php if ($is_submitted): ?>
                                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: #d4edda; color: #155724;">
                                            <i class="fas fa-check"></i> Submitted
                                        </span>
                                    <?php elseif ($is_overdue): ?>
                                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: #f8d7da; color: #721c24;">
                                            <i class="fas fa-exclamation-triangle"></i> Overdue
                                        </span>
                                    <?php else: ?>
                                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: #fff3cd; color: #856404;">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="student_view_assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php if (!$is_submitted && !$is_overdue): ?>
                                        <a href="student_submit_assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary" style="margin-top: 4px; display: block;">
                                            <i class="fas fa-upload"></i> Submit
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                No assignments found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>


