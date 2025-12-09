<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$success = "";
$error = "";

if (isset($_POST["add_subject"])) {
    $subject_code = mysqli_real_escape_string($conn, $_POST["subject_code"]);
    $subject_name = mysqli_real_escape_string($conn, $_POST["subject_name"]);
    $cid = intval($_POST["cid"] ?? 0);
    $semester = intval($_POST["semester"] ?? 0);
    $credits = intval($_POST["credits"] ?? 0);
    $tid = intval($_POST["tid"] ?? 0);
    $subject_type = mysqli_real_escape_string($conn, $_POST["subject_type"] ?? "Core");
    $description = mysqli_real_escape_string($conn, $_POST["description"] ?? "");
    
    if (empty($subject_code) || empty($subject_name)) {
        $error = "Subject code and name are required!";
    } else {
        $check_code = "SELECT * FROM subjects WHERE subject_code = '$subject_code'";
        $result = mysqli_query($conn, $check_code);
        if (mysqli_num_rows($result) > 0) {
            $error = "Subject code already exists!";
        } else {
            $sql = "INSERT INTO subjects (subject_code, subject_name, cid, semester, credits, tid, subject_type, description) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            $tid_val = $tid > 0 ? $tid : null;
            $cid_val = $cid > 0 ? $cid : null;
            mysqli_stmt_bind_param($stmt, "ssiiisss", $subject_code, $subject_name, $cid_val, $semester, $credits, $tid_val, $subject_type, $description);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Subject added successfully! Subject Code: $subject_code";
                $_POST = array();
            } else {
                $error = "Error adding subject: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$courses_query = "SELECT cid, course_code, cname FROM courses WHERE status = 'active' ORDER BY course_code";
$courses_result = mysqli_query($conn, $courses_query);

$teachers_query = "SELECT tid, teacher_id, fname, lname FROM teachers WHERE status = 'active' ORDER BY fname";
$teachers_result = mysqli_query($conn, $teachers_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book-open"></i> Add New Subject</h1>
            <a href="admin_manage_subject.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Manage Subjects</a>
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
                    <h3><i class="fas fa-info-circle"></i> Subject Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Subject Code <span class="required">*</span></label>
                            <input type="text" name="subject_code" value="<?php echo $_POST['subject_code'] ?? ''; ?>" required placeholder="e.g., CS101">
                        </div>
                        <div class="form-group">
                            <label>Subject Name <span class="required">*</span></label>
                            <input type="text" name="subject_name" value="<?php echo $_POST['subject_name'] ?? ''; ?>" required placeholder="e.g., Data Structures">
                        </div>
                        <div class="form-group">
                            <label>Subject Type</label>
                            <select name="subject_type">
                                <option value="Core" <?php echo (isset($_POST['subject_type']) && $_POST['subject_type'] == 'Core') ? 'selected' : ''; ?>>Core</option>
                                <option value="VAC" <?php echo (isset($_POST['subject_type']) && $_POST['subject_type'] == 'VAC') ? 'selected' : ''; ?>>VAC</option>
                                <option value="SEC" <?php echo (isset($_POST['subject_type']) && $_POST['subject_type'] == 'SEC') ? 'selected' : ''; ?>>SEC</option>
                                <option value="AEC" <?php echo (isset($_POST['subject_type']) && $_POST['subject_type'] == 'AEC') ? 'selected' : ''; ?>>AEC</option>
                                <option value="IDC" <?php echo (isset($_POST['subject_type']) && $_POST['subject_type'] == 'IDC') ? 'selected' : ''; ?>>IDC</option>
                                <option value="Minor" <?php echo (isset($_POST['subject_type']) && $_POST['subject_type'] == 'Minor') ? 'selected' : ''; ?>>Minor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <select name="cid">
                                <option value="">Select Course (Optional)</option>
                                <?php while ($course = mysqli_fetch_assoc($courses_result)): ?>
                                    <option value="<?php echo $course['cid']; ?>" <?php echo (isset($_POST['cid']) && $_POST['cid'] == $course['cid']) ? 'selected' : ''; ?>>
                                        <?php echo $course['course_code'] . ' - ' . $course['cname']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Semester</label>
                            <input type="number" name="semester" value="<?php echo $_POST['semester'] ?? ''; ?>" min="1" max="12">
                        </div>
                        <div class="form-group">
                            <label>Credits</label>
                            <input type="number" name="credits" value="<?php echo $_POST['credits'] ?? ''; ?>" min="1" max="10">
                        </div>
                        <div class="form-group">
                            <label>Assigned Teacher</label>
                            <select name="tid">
                                <option value="">Select Teacher (Optional)</option>
                                <?php while ($teacher = mysqli_fetch_assoc($teachers_result)): ?>
                                    <option value="<?php echo $teacher['tid']; ?>" <?php echo (isset($_POST['tid']) && $_POST['tid'] == $teacher['tid']) ? 'selected' : ''; ?>>
                                        <?php echo $teacher['teacher_id'] . ' - ' . $teacher['fname'] . ' ' . $teacher['lname']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Description</label>
                            <textarea name="description" rows="4" placeholder="Subject description..."><?php echo $_POST['description'] ?? ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="add_subject" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Subject
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


