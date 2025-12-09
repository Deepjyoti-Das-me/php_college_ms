<?php
session_start();
require_once "database.php";

// Generate CAPTCHA (refresh on each GET to keep the refresh button meaningful)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['captcha_num1'], $_SESSION['captcha_num2'])) {
    $_SESSION['captcha_num1'] = rand(5, 15);
    $_SESSION['captcha_num2'] = rand(5, 15);
}
$captchaSum = $_SESSION['captcha_num1'] + $_SESSION['captcha_num2'];

// Handle form submission
if (isset($_POST["submit"])) {
    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];
    $userType = $_POST["user_type"] ?? '';
    $captchaAnswer = $_POST["captcha"] ?? '';
    
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $errors = array();
    
    // Validation
    if (empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat) OR empty($userType)) {
        array_push($errors, "All fields are required");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }
    
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    
    if ($password !== $passwordRepeat) {
        array_push($errors, "Password does not match");
    }
    
    if (!in_array($userType, ['student', 'teacher', 'staff'])) {
        array_push($errors, "Please select a valid user type");
    }
    
    // CAPTCHA validation
    $expectedAnswer = $_SESSION['captcha_num1'] + $_SESSION['captcha_num2'];
    if (empty($captchaAnswer)) {
        array_push($errors, "Please solve the CAPTCHA");
    } elseif ((int)$captchaAnswer !== (int)$expectedAnswer) {
        array_push($errors, "CAPTCHA answer is incorrect. Please try again.");
    }
    
    // Check if email already exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            array_push($errors, "Email already exists!");
        }
        mysqli_stmt_close($stmt);
    }
    
    // If no errors, proceed with registration
    if (count($errors) == 0) {
        // Insert into users table
        $sql = "INSERT INTO users (full_name, email, password, user_type) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $fullName, $email, $passwordHash, $userType);
            
            if (mysqli_stmt_execute($stmt)) {
                $userId = mysqli_insert_id($conn);
                
                // Parse full name into components
                $nameParts = explode(' ', trim($fullName));
                $fname = $nameParts[0] ?? '';
                $lname = $nameParts[count($nameParts) - 1] ?? '';
                $mname = '';
                if (count($nameParts) > 2) {
                    $mname = implode(' ', array_slice($nameParts, 1, -1));
                }
                
                // Generate unique ID based on user type
                $prefix = '';
                $idLength = 6;
                switch($userType) {
                    case 'student':
                        $prefix = 'STU';
                        // Get next roll number
                        $rollQuery = "SELECT MAX(CAST(SUBSTRING(roll_no, 4) AS UNSIGNED)) AS max_id FROM students WHERE roll_no LIKE 'STU%'";
                        $rollResult = mysqli_query($conn, $rollQuery);
                        $rollRow = mysqli_fetch_assoc($rollResult);
                        $nextRoll = ($rollRow['max_id'] ?? 0) + 1;
                        $uniqueId = $prefix . str_pad($nextRoll, $idLength, '0', STR_PAD_LEFT);
                        
                        // Get first available course
                        $courseQuery = "SELECT cid FROM courses WHERE status = 'active' LIMIT 1";
                        $courseResult = mysqli_query($conn, $courseQuery);
                        $courseRow = mysqli_fetch_assoc($courseResult);
                        
                        if (!$courseRow) {
                            // No course exists - add error
                            array_push($errors, "No active course available. Please contact administrator to add a course first.");
                            // Delete the user record we just created
                            mysqli_query($conn, "DELETE FROM users WHERE id = $userId");
                        } else {
                            $cid = $courseRow['cid'];
                            
                            // Insert into students table
                            $sql2 = "INSERT INTO students (user_id, cid, roll_no, fname, mname, lname, email, enrollment_date) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())";
                            $stmt2 = mysqli_prepare($conn, $sql2);
                            if ($stmt2) {
                                mysqli_stmt_bind_param($stmt2, "iisssss", $userId, $cid, $uniqueId, $fname, $mname, $lname, $email);
                                if (mysqli_stmt_execute($stmt2)) {
                                    // Success - regenerate CAPTCHA and show success message
                                    $_SESSION['captcha_num1'] = rand(5, 15);
                                    $_SESSION['captcha_num2'] = rand(5, 15);
                                    echo "<div class='alert alert-success'>Registration successful! Your Student Roll Number is: <strong>$uniqueId</strong></div>";
                                    // Clear form data
                                    $fullName = $email = $password = $passwordRepeat = $userType = '';
                                } else {
                                    array_push($errors, "Error creating student record: " . mysqli_error($conn));
                                    // Delete the user record
                                    mysqli_query($conn, "DELETE FROM users WHERE id = $userId");
                                }
                                mysqli_stmt_close($stmt2);
                            } else {
                                array_push($errors, "Error preparing student insert: " . mysqli_error($conn));
                                // Delete the user record
                                mysqli_query($conn, "DELETE FROM users WHERE id = $userId");
                            }
                        }
                        break;
                        
                    case 'teacher':
                        $prefix = 'TCH';
                        // Get next teacher ID
                        $teacherQuery = "SELECT MAX(CAST(SUBSTRING(teacher_id, 4) AS UNSIGNED)) AS max_id FROM teachers WHERE teacher_id LIKE 'TCH%'";
                        $teacherResult = mysqli_query($conn, $teacherQuery);
                        $teacherRow = mysqli_fetch_assoc($teacherResult);
                        $nextTeacher = ($teacherRow['max_id'] ?? 0) + 1;
                        $uniqueId = $prefix . str_pad($nextTeacher, $idLength, '0', STR_PAD_LEFT);
                        
                        // Insert into teachers table
                        $sql2 = "INSERT INTO teachers (user_id, teacher_id, fname, mname, lname, email, hire_date) VALUES (?, ?, ?, ?, ?, ?, CURDATE())";
                        $stmt2 = mysqli_prepare($conn, $sql2);
                        if ($stmt2) {
                            mysqli_stmt_bind_param($stmt2, "isssss", $userId, $uniqueId, $fname, $mname, $lname, $email);
                            if (mysqli_stmt_execute($stmt2)) {
                                // Success - regenerate CAPTCHA and show success message
                                $_SESSION['captcha_num1'] = rand(5, 15);
                                $_SESSION['captcha_num2'] = rand(5, 15);
                                echo "<div class='alert alert-success'>Registration successful! Your Teacher ID is: <strong>$uniqueId</strong></div>";
                                // Clear form data
                                $fullName = $email = $password = $passwordRepeat = $userType = '';
                            } else {
                                array_push($errors, "Error creating teacher record: " . mysqli_error($conn));
                                // Delete the user record
                                mysqli_query($conn, "DELETE FROM users WHERE id = $userId");
                            }
                            mysqli_stmt_close($stmt2);
                        } else {
                            array_push($errors, "Error preparing teacher insert: " . mysqli_error($conn));
                            // Delete the user record
                            mysqli_query($conn, "DELETE FROM users WHERE id = $userId");
                        }
                        break;
                        
                    case 'staff':
                        $prefix = 'STF';
                        // Get next staff ID
                        $staffQuery = "SELECT MAX(CAST(SUBSTRING(staff_id, 4) AS UNSIGNED)) AS max_id FROM staff WHERE staff_id LIKE 'STF%'";
                        $staffResult = mysqli_query($conn, $staffQuery);
                        $staffRow = mysqli_fetch_assoc($staffResult);
                        $nextStaff = ($staffRow['max_id'] ?? 0) + 1;
                        $uniqueId = $prefix . str_pad($nextStaff, $idLength, '0', STR_PAD_LEFT);
                        
                        // Insert into staff table
                        $sql2 = "INSERT INTO staff (user_id, staff_id, fname, mname, lname, email, hire_date) VALUES (?, ?, ?, ?, ?, ?, CURDATE())";
                        $stmt2 = mysqli_prepare($conn, $sql2);
                        if ($stmt2) {
                            mysqli_stmt_bind_param($stmt2, "isssss", $userId, $uniqueId, $fname, $mname, $lname, $email);
                            if (mysqli_stmt_execute($stmt2)) {
                                // Success - regenerate CAPTCHA and show success message
                                $_SESSION['captcha_num1'] = rand(5, 15);
                                $_SESSION['captcha_num2'] = rand(5, 15);
                                echo "<div class='alert alert-success'>Registration successful! Your Staff ID is: <strong>$uniqueId</strong></div>";
                                // Clear form data
                                $fullName = $email = $password = $passwordRepeat = $userType = '';
                            } else {
                                array_push($errors, "Error creating staff record: " . mysqli_error($conn));
                                // Delete the user record
                                mysqli_query($conn, "DELETE FROM users WHERE id = $userId");
                            }
                            mysqli_stmt_close($stmt2);
                        } else {
                            array_push($errors, "Error preparing staff insert: " . mysqli_error($conn));
                            // Delete the user record
                            mysqli_query($conn, "DELETE FROM users WHERE id = $userId");
                        }
                        break;
                }
            } else {
                array_push($errors, "Registration failed. Please try again.");
            }
            mysqli_stmt_close($stmt);
        } else {
            array_push($errors, "Database error. Please try again.");
        }
    }
    
    // Display errors
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        // Regenerate CAPTCHA on error
        $_SESSION['captcha_num1'] = rand(5, 15);
        $_SESSION['captcha_num2'] = rand(5, 15);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Registration - College Management System</title>
  <link rel="stylesheet" href="registration.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="deep_ai_chatbot.css">
</head>
<body>
  <div class="container">
    <div class="form">
      <h2><i class="fas fa-user-plus"></i> Create Account</h2>
      
      <form action="registration.php" method="post" id="registrationForm">
        <div class="form-group">
          <input type="text" class="form-control" name="fullname" placeholder="Full Name" value="<?php echo isset($fullName) ? htmlspecialchars($fullName) : ''; ?>" required>
        </div>
        
        <div class="form-group">
          <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
        </div>
        
        <div class="form-group">
          <select class="form-control" name="user_type" required>
            <option value="">Select User Type</option>
            <option value="student" <?php echo (isset($userType) && $userType == 'student') ? 'selected' : ''; ?>>Student</option>
            <option value="teacher" <?php echo (isset($userType) && $userType == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
            <option value="staff" <?php echo (isset($userType) && $userType == 'staff') ? 'selected' : ''; ?>>Staff</option>
          </select>
          <small style="color: rgba(230, 245, 255, 0.6); font-size: 12px; margin-top: 4px; display: block;">
            <i class="fas fa-info-circle"></i> Admin accounts can only be created by existing administrators
          </small>
        </div>
        
        <div class="form-group">
          <input type="password" class="form-control" name="password" placeholder="Password (min. 8 characters)" required>
        </div>
        
        <div class="form-group">
          <input type="password" class="form-control" name="repeat_password" placeholder="Confirm Password" required>
        </div>
        
        <div class="form-group captcha-group">
          <div class="captcha-container">
            <div class="captcha-display">
              <span class="captcha-question">
                <i class="fas fa-shield-alt"></i> 
                What is <?php echo $_SESSION['captcha_num1']; ?> + <?php echo $_SESSION['captcha_num2']; ?> = ?
              </span>
              <button type="button" class="captcha-refresh" onclick="refreshCaptcha()" title="Refresh CAPTCHA">
                <i class="fas fa-sync-alt"></i>
              </button>
            </div>
            <input type="number" class="form-control captcha-input" name="captcha" placeholder="Enter the sum" required min="0">
          </div>
        </div>
        
        <div class="form-btn">
          <input type="submit" class="btn btn-primary" value="Register" name="submit">
        </div>
      </form>
      
      <div class="helper">
        <p>Already have an account? <a href="login.php">Login Here</a></p>
      </div>
    </div>
  </div>

  <script>
    function refreshCaptcha() {
      location.reload();
    }
    
    // Simple form validation
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
      const password = document.querySelector('input[name="password"]').value;
      const repeatPassword = document.querySelector('input[name="repeat_password"]').value;
      const userType = document.querySelector('select[name="user_type"]').value;
      
      if (password !== repeatPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }
      
      if (password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long!');
        return false;
      }
      
      if (!userType) {
        e.preventDefault();
        alert('Please select a user type!');
        return false;
      }
    });
  </script>

  <!-- Deep AI Chatbot -->
  <?php include 'deep_ai_chatbot.php'; ?>

</body>
</html>