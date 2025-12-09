<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$tid = intval($_GET['tid'] ?? 0);
$success = "";
$error = "";

// Get teacher data
$teacher_query = "SELECT t.*, u.email, u.status as user_status 
                  FROM teachers t 
                  LEFT JOIN users u ON t.user_id = u.id 
                  WHERE t.tid = $tid LIMIT 1";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher = mysqli_fetch_assoc($teacher_result);

if (!$teacher) {
    header("Location: admin_manage_teacher.php");
    exit();
}

if (isset($_POST["update_teacher"])) {
    $fname = mysqli_real_escape_string($conn, $_POST["fname"]);
    $mname = mysqli_real_escape_string($conn, $_POST["mname"] ?? "");
    $lname = mysqli_real_escape_string($conn, $_POST["lname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $ph_no = mysqli_real_escape_string($conn, $_POST["ph_no"]);
    $s_no = mysqli_real_escape_string($conn, $_POST["s_no"] ?? "");
    $s_name = mysqli_real_escape_string($conn, $_POST["s_name"] ?? "");
    $pincode = mysqli_real_escape_string($conn, $_POST["pincode"] ?? "");
    $district = mysqli_real_escape_string($conn, $_POST["district"] ?? "");
    $state = mysqli_real_escape_string($conn, $_POST["state"] ?? "");
    $country = mysqli_real_escape_string($conn, $_POST["country"] ?? "India");
    $salary = floatval($_POST["salary"] ?? 0);
    $status = mysqli_real_escape_string($conn, $_POST["status"] ?? "active");
    
    if (empty($fname) || empty($lname) || empty($email)) {
        $error = "Please fill all required fields";
    } else {
        // Check if email is changed and already exists
        if ($email != $teacher['email']) {
            $check_email = "SELECT * FROM users WHERE email = '$email' AND id != " . $teacher['user_id'];
            $result = mysqli_query($conn, $check_email);
            if (mysqli_num_rows($result) > 0) {
                $error = "Email already exists!";
            }
        }
        
        if (empty($error)) {
            $full_name = trim($fname . " " . $mname . " " . $lname);
            
            // Update users table
            $sql1 = "UPDATE users SET full_name = ?, email = ?, phone = ?, status = ? WHERE id = ?";
            $stmt1 = mysqli_prepare($conn, $sql1);
            mysqli_stmt_bind_param($stmt1, "ssssi", $full_name, $email, $ph_no, $status, $teacher['user_id']);
            
            if (mysqli_stmt_execute($stmt1)) {
                // Update teachers table
                $sql2 = "UPDATE teachers SET fname = ?, mname = ?, lname = ?, email = ?, ph_no = ?, s_no = ?, s_name = ?, pincode = ?, district = ?, state = ?, country = ?, salary = ? WHERE tid = ?";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "ssssssssssssdi", $fname, $mname, $lname, $email, $ph_no, $s_no, $s_name, $pincode, $district, $state, $country, $salary, $tid);
                
                if (mysqli_stmt_execute($stmt2)) {
                    $success = "Teacher updated successfully!";
                    // Refresh teacher data
                    $teacher_result = mysqli_query($conn, $teacher_query);
                    $teacher = mysqli_fetch_assoc($teacher_result);
                } else {
                    $error = "Error updating teacher: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt2);
            } else {
                $error = "Error updating user: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt1);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Edit Teacher</h1>
            <div>
                <a href="admin_view_teacher.php?tid=<?php echo $tid; ?>" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="admin_manage_teacher.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
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
                    <h3><i class="fas fa-user"></i> Personal Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Teacher ID</label>
                            <input type="text" value="<?php echo htmlspecialchars($teacher['teacher_id']); ?>" disabled style="background: #f5f5f5;">
                        </div>
                        <div class="form-group">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="fname" value="<?php echo htmlspecialchars($teacher['fname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="mname" value="<?php echo htmlspecialchars($teacher['mname'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="lname" value="<?php echo htmlspecialchars($teacher['lname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="ph_no" value="<?php echo htmlspecialchars($teacher['ph_no'] ?? ''); ?>" pattern="[0-9]{10}">
                        </div>
                        <div class="form-group">
                            <label>Salary</label>
                            <input type="number" name="salary" value="<?php echo $teacher['salary'] ?? 0; ?>" step="0.01" min="0">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="active" <?php echo $teacher['user_status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $teacher['user_status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Street Number</label>
                            <input type="text" name="s_no" value="<?php echo htmlspecialchars($teacher['s_no'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Street Name</label>
                            <input type="text" name="s_name" value="<?php echo htmlspecialchars($teacher['s_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Pincode</label>
                            <input type="text" name="pincode" value="<?php echo htmlspecialchars($teacher['pincode'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text" name="district" value="<?php echo htmlspecialchars($teacher['district'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" value="<?php echo htmlspecialchars($teacher['state'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="<?php echo htmlspecialchars($teacher['country'] ?? 'India'); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="update_teacher" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Teacher
                    </button>
                    <a href="admin_manage_teacher.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

