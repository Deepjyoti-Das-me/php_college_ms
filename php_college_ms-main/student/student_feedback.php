<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "student") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$success = "";
$error = "";

$student_email = $_SESSION["user_email"];
$student_query = "SELECT sid FROM students s JOIN users u ON s.user_id = u.id WHERE u.email = '$student_email' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_row = mysqli_fetch_assoc($student_result);
$sid = $student_row['sid'];

if (isset($_POST["submit_feedback"])) {
    $feedback_type = mysqli_real_escape_string($conn, $_POST["feedback_type"]);
    $subject = mysqli_real_escape_string($conn, $_POST["subject"]);
    $message = mysqli_real_escape_string($conn, $_POST["message"]);
    $rating = intval($_POST["rating"]);
    
    if (empty($feedback_type) || empty($subject) || empty($message) || $rating < 1) {
        $error = "All fields are required!";
    } else {
        $sql = "INSERT INTO student_feedback (sid, feedback_type, subject, message, rating, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssi", $sid, $feedback_type, $subject, $message, $rating);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Feedback submitted successfully! Thank you for your input.";
            $_POST = array();
        } else {
            $error = "Error submitting feedback: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Get feedback history
$feedback_query = "SELECT * FROM student_feedback WHERE sid = $sid ORDER BY created_at DESC";
$feedback_result = mysqli_query($conn, $feedback_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback - Student Panel</title>
    <link rel="stylesheet" href="student.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'student_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-comment-dots"></i> Give Feedback</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <!-- Feedback Form -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-edit"></i> Submit Feedback</h2>
                <form method="POST" action="" class="admin-form">
                    <div class="form-group">
                        <label>Feedback Type <span class="required">*</span></label>
                        <select name="feedback_type" required>
                            <option value="">Select Type</option>
                            <option value="course">Course</option>
                            <option value="teacher">Teacher</option>
                            <option value="facility">Facility</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject <span class="required">*</span></label>
                        <input type="text" name="subject" required placeholder="Brief subject of your feedback">
                    </div>
                    <div class="form-group">
                        <label>Message <span class="required">*</span></label>
                        <textarea name="message" rows="6" required placeholder="Please share your feedback..."><?php echo $_POST['message'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Rating <span class="required">*</span></label>
                        <div style="display: flex; gap: 10px; margin-top: 8px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <label style="cursor: pointer; font-size: 24px;">
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" required style="display: none;">
                                    <i class="far fa-star rating-star" data-rating="<?php echo $i; ?>"></i>
                                </label>
                            <?php endfor; ?>
                        </div>
                        <small>Click on stars to rate (1 = Poor, 5 = Excellent)</small>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="submit_feedback" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Feedback
                        </button>
                    </div>
                </form>
            </div>

            <!-- Feedback History -->
            <div class="data-table">
                <h2 style="margin-bottom: 20px; padding: 0 16px;"><i class="fas fa-history"></i> My Feedback History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Rating</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($feedback_result) > 0): ?>
                            <?php while ($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($feedback['created_at'])); ?></td>
                                    <td><?php echo ucfirst($feedback['feedback_type']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['subject']); ?></td>
                                    <td>
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $feedback['rating'] ? '<i class="fas fa-star" style="color: #FFD700;"></i>' : '<i class="far fa-star" style="color: #ccc;"></i>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                            background: <?php 
                                            echo $feedback['status'] == 'resolved' ? '#d4edda' : 
                                                   ($feedback['status'] == 'reviewed' ? '#fff3cd' : '#f8d7da'); 
                                            ?>;
                                            color: <?php 
                                            echo $feedback['status'] == 'resolved' ? '#155724' : 
                                                   ($feedback['status'] == 'reviewed' ? '#856404' : '#721c24'); 
                                            ?>;">
                                            <?php echo ucfirst($feedback['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                                    No feedback submitted yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Star rating interaction
        document.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.querySelector(`input[name="rating"][value="${rating}"]`).checked = true;
                
                // Update star display
                document.querySelectorAll('.rating-star').forEach((s, index) => {
                    if (index < rating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                        s.style.color = '#FFD700';
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                        s.style.color = '#ccc';
                    }
                });
            });
        });
    </script>
</body>
</html>


