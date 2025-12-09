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

// Handle grade entry
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_grade'])) {
    $sid = intval($_POST['sid']);
    $subject_id = intval($_POST['subject_id']);
    $exam_type = mysqli_real_escape_string($conn, $_POST['exam_type']);
    $marks_obtained = floatval($_POST['marks_obtained']);
    $total_marks = floatval($_POST['total_marks']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $exam_date = $_POST['exam_date'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');
    
    // Calculate grade
    $percentage = ($marks_obtained / $total_marks) * 100;
    $grade = 'F';
    if ($percentage >= 90) $grade = 'A+';
    elseif ($percentage >= 80) $grade = 'A';
    elseif ($percentage >= 70) $grade = 'B+';
    elseif ($percentage >= 60) $grade = 'B';
    elseif ($percentage >= 50) $grade = 'C';
    elseif ($percentage >= 40) $grade = 'D';
    
    $insert_query = "INSERT INTO grades (sid, subject_id, tid, exam_type, marks_obtained, total_marks, grade, semester, exam_date, remarks) 
                    VALUES ($sid, $subject_id, $tid, '$exam_type', $marks_obtained, $total_marks, '$grade', '$semester', '$exam_date', '$remarks')";
    if (mysqli_query($conn, $insert_query)) {
        echo "<div class='alert alert-success'>Grade entered successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Get subjects taught by this teacher
$subjects_query = "SELECT * FROM subjects WHERE tid = $tid ORDER BY subject_name";
$subjects_result = mysqli_query($conn, $subjects_query);

// Get all grades entered by this teacher
$grades_query = "SELECT g.*, s.roll_no, s.fname, s.lname, sub.subject_code, sub.subject_name 
                FROM grades g 
                JOIN students s ON g.sid = s.sid 
                JOIN subjects sub ON g.subject_id = sub.id 
                WHERE g.tid = $tid 
                ORDER BY g.exam_date DESC";
$grades_result = mysqli_query($conn, $grades_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades & Results - Teacher Panel</title>
    <link rel="stylesheet" href="teacher.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'teacher_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> Grades & Results</h1>
            <button class="btn-primary" onclick="document.getElementById('gradeForm').style.display='block'">
                <i class="fas fa-plus"></i> Enter Grade
            </button>
        </div>

        <!-- Enter Grade Form -->
        <div id="gradeForm" class="form-container" style="display: none; margin-bottom: 24px;">
            <h2>Enter New Grade</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Subject *</label>
                        <select name="subject_id" id="subjectSelect" required onchange="loadStudents()">
                            <option value="">-- Select Subject --</option>
                            <?php 
                            mysqli_data_seek($subjects_result, 0);
                            while ($subject = mysqli_fetch_assoc($subjects_result)): 
                            ?>
                                <option value="<?php echo $subject['id']; ?>">
                                    <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Student *</label>
                        <select name="sid" id="studentSelect" required>
                            <option value="">-- Select Subject First --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Exam Type *</label>
                        <input type="text" name="exam_type" placeholder="e.g., Mid-term, Final, Quiz" required>
                    </div>
                    <div class="form-group">
                        <label>Semester *</label>
                        <input type="text" name="semester" placeholder="e.g., 1, 2, 3" required>
                    </div>
                    <div class="form-group">
                        <label>Marks Obtained *</label>
                        <input type="number" name="marks_obtained" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Total Marks *</label>
                        <input type="number" name="total_marks" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Exam Date *</label>
                        <input type="date" name="exam_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Remarks</label>
                        <textarea name="remarks" rows="2"></textarea>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" name="submit_grade" class="btn-primary">
                        <i class="fas fa-save"></i> Submit Grade
                    </button>
                    <button type="button" class="btn-secondary" onclick="document.getElementById('gradeForm').style.display='none'">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Grades List -->
        <div class="form-container">
            <?php if (mysqli_num_rows($grades_result) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Roll No</th>
                            <th>Subject</th>
                            <th>Exam Type</th>
                            <th>Marks</th>
                            <th>Grade</th>
                            <th>Semester</th>
                            <th>Exam Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($grade = mysqli_fetch_assoc($grades_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($grade['fname'] . ' ' . $grade['lname']); ?></td>
                                <td><?php echo htmlspecialchars($grade['roll_no']); ?></td>
                                <td><?php echo htmlspecialchars($grade['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($grade['exam_type']); ?></td>
                                <td>
                                    <strong><?php echo $grade['marks_obtained']; ?></strong> / <?php echo $grade['total_marks']; ?>
                                    (<?php echo round(($grade['marks_obtained'] / $grade['total_marks']) * 100, 2); ?>%)
                                </td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $grade['grade'] == 'A+' ? 'success' : 
                                            ($grade['grade'] == 'A' ? 'info' : 
                                            ($grade['grade'] == 'B' || $grade['grade'] == 'B+' ? 'warning' : 'danger')); 
                                    ?>">
                                        <?php echo $grade['grade']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($grade['semester']); ?></td>
                                <td><?php echo date('d M Y', strtotime($grade['exam_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-chart-line" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p>No grades entered yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function loadStudents() {
            const subjectId = document.getElementById('subjectSelect').value;
            const studentSelect = document.getElementById('studentSelect');
            
            if (!subjectId) {
                studentSelect.innerHTML = '<option value="">-- Select Subject First --</option>';
                return;
            }
            
            // Fetch students for this subject's course
            fetch('get_students.php?subject_id=' + subjectId)
                .then(response => response.json())
                .then(data => {
                    studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
                    data.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.sid;
                        option.textContent = student.roll_no + ' - ' + student.fname + ' ' + student.lname;
                        studentSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    studentSelect.innerHTML = '<option value="">Error loading students</option>';
                });
        }
    </script>
</body>
</html>

