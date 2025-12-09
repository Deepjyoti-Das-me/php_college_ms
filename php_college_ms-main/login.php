<?php
session_start();
if (isset($_SESSION["user"], $_SESSION["user_type"])) {
    switch ($_SESSION["user_type"]) {
        case 'admin':
            header("Location: admin/admin.php");
            break;
        case 'student':
            header("Location: student/student.php");
            break;
        case 'teacher':
            header("Location: teacher/teacher.php");
            break;
        case 'staff':
            header("Location: staff/staff.php");
            break;
        default:
            header("Location: index1.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="deep_ai_chatbot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST["login"])) {
            $email = trim($_POST["email"]);
            $password = $_POST["password"];
            $selectedType = $_POST["user_type"] ?? '';
            
            require_once "database.php";

            // Use prepared statement to avoid SQL injection
            $sql = "SELECT id, full_name, email, password, user_type FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                mysqli_stmt_close($stmt);
            } else {
                $user = null;
            }

            if ($user) {
                if (password_verify($password, $user["password"])) {
                    // Enforce correct role: if selection doesn't match DB role, block login
                    if ($selectedType && $selectedType !== $user["user_type"]) {
                        echo "<div class='alert alert-danger'>This account is registered as " . htmlspecialchars(ucfirst($user['user_type'])) . ". Please select the correct role.</div>";
                    } else {
                        $_SESSION["user"] = "yes";
                        $_SESSION["user_type"] = $user["user_type"]; // trust DB role
                        $_SESSION["user_email"] = $email;
                        $_SESSION["user_name"] = $user["full_name"] ?? "User";
                        
                        // Redirect based on actual user role
                        switch($user["user_type"]) {
                            case 'admin':
                                header("Location: admin/admin.php");
                                break;
                            case 'student':
                                header("Location: student/student.php");
                                break;
                            case 'teacher':
                                header("Location: teacher/teacher.php");
                                break;
                            case 'staff':
                                header("Location: staff/staff.php");
                                break;
                            default:
                                header("Location: index1.php");
                        }
                        die();
                    }
                } else {
                    echo "<div class='alert alert-danger'>Password does not match</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Email does not match</div>";
            }
        }
        ?>
      
      <div class="login-wrapper">
        <h2 class="login-title">Select User Type</h2>
        <div class="user-type-buttons">
            <button type="button" class="user-type-btn student-btn" onclick="selectUserType('student')">
                <div class="btn-glow"></div>
                <span class="btn-icon">👨‍🎓</span>
                <span class="btn-text">Student</span>
                <div class="ripple"></div>
            </button>
            <button type="button" class="user-type-btn admin-btn" onclick="selectUserType('admin')">
                <div class="btn-glow"></div>
                <span class="btn-icon">👨‍💼</span>
                <span class="btn-text">Admin</span>
                <div class="ripple"></div>
            </button>
            <button type="button" class="user-type-btn teacher-btn" onclick="selectUserType('teacher')">
                <div class="btn-glow"></div>
                <span class="btn-icon">👨‍🏫</span>
                <span class="btn-text">Teacher</span>
                <div class="ripple"></div>
            </button>
            <button type="button" class="user-type-btn staff-btn" onclick="selectUserType('staff')">
                <div class="btn-glow"></div>
                <span class="btn-icon">👨‍💻</span>
                <span class="btn-text">Staff</span>
                <div class="ripple"></div>
            </button>
        </div>

        <form action="login.php" method="post" id="loginForm" style="display: none;">
            <input type="hidden" name="user_type" id="userTypeInput">
            <div class="selected-type-display" id="selectedTypeDisplay"></div>
            
            <div class="form-group">
                <input type="email" placeholder="Enter Email:" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password:" name="password" class="form-control" required>
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
            <div class="change-type">
                <button type="button" class="btn-change-type" onclick="resetUserType()">
                    <span class="change-icon">↺</span>
                    <span class="change-text">Change User Type</span>
                    <div class="change-ripple"></div>
                </button>
            </div>
        </form>
        
        <div class="register-link">
            <p>Not registered yet <a href="registration.php">Register Here</a></p>
        </div>
      </div>

    <script>
        // Add ripple effect on button click
        document.querySelectorAll('.user-type-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const ripple = this.querySelector('.ripple');
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('active');
                
                setTimeout(() => {
                    ripple.classList.remove('active');
                }, 600);
            });
        });

        // Add ripple effect for change type button
        document.querySelector('.btn-change-type')?.addEventListener('click', function(e) {
            const ripple = this.querySelector('.change-ripple');
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('active');
            
            setTimeout(() => {
                ripple.classList.remove('active');
            }, 600);
        });

        function selectUserType(type) {
            // Hide all buttons with animation
            document.querySelectorAll('.user-type-btn').forEach((btn, index) => {
                setTimeout(() => {
                    btn.style.opacity = '0';
                    btn.style.transform = 'scale(0.8) translateY(20px)';
                    setTimeout(() => {
                        btn.style.display = 'none';
                    }, 300);
                }, index * 50);
            });
            
            // Show login form with animation
            setTimeout(() => {
                const form = document.getElementById('loginForm');
                form.style.display = 'block';
                form.style.opacity = '0';
                form.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    form.style.opacity = '1';
                    form.style.transform = 'translateY(0)';
                }, 50);
            }, 400);
            
            document.getElementById('userTypeInput').value = type;
            
            // Display selected type
            const typeNames = {
                'student': 'Student',
                'admin': 'Admin',
                'teacher': 'Teacher',
                'staff': 'Staff'
            };
            const typeColors = {
                'student': '#4CAF50',
                'admin': '#f44336',
                'teacher': '#2196F3',
                'staff': '#FF9800'
            };
            
            document.getElementById('selectedTypeDisplay').innerHTML = 
                `<div class="selected-type-badge" style="background: ${typeColors[type]}">
                    Logging in as: <strong>${typeNames[type]}</strong>
                </div>`;
        }
        
        function resetUserType() {
            // Hide login form with animation
            const form = document.getElementById('loginForm');
            form.style.opacity = '0';
            form.style.transform = 'translateY(20px)';
            setTimeout(() => {
                form.style.display = 'none';
                document.getElementById('userTypeInput').value = '';
                document.getElementById('selectedTypeDisplay').innerHTML = '';
                
                // Show all buttons with animation
                document.querySelectorAll('.user-type-btn').forEach((btn, index) => {
                    btn.style.display = 'flex';
                    btn.style.opacity = '0';
                    btn.style.transform = 'scale(0.8) translateY(20px)';
                    setTimeout(() => {
                        btn.style.opacity = '1';
                        btn.style.transform = 'scale(1) translateY(0)';
                    }, index * 100);
                });
            }, 300);
        }
    </script>

    <!-- Deep AI Chatbot -->
    <?php include 'deep_ai_chatbot.php'; ?>

</body>
</html>