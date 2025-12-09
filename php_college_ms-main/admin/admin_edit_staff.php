<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$sfid = intval($_GET['sfid'] ?? 0);
$success = "";
$error = "";

// Get staff data
$staff_query = "SELECT s.*, u.email, u.status as user_status 
                FROM staff s 
                LEFT JOIN users u ON s.user_id = u.id 
                WHERE s.sfid = $sfid LIMIT 1";
$staff_result = mysqli_query($conn, $staff_query);
$staff = mysqli_fetch_assoc($staff_result);

if (!$staff) {
    header("Location: admin_manage_staff.php");
    exit();
}

if (isset($_POST["update_staff"])) {
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
    $designation = mysqli_real_escape_string($conn, $_POST["designation"] ?? "");
    $shift_morning = isset($_POST["shift_morning"]) ? 1 : 0;
    $shift_day = isset($_POST["shift_day"]) ? 1 : 0;
    $status = mysqli_real_escape_string($conn, $_POST["status"] ?? "active");
    
    if (empty($fname) || empty($lname) || empty($email)) {
        $error = "Please fill all required fields";
    } else {
        // Check if email is changed and already exists
        if ($email != $staff['email']) {
            $check_email = "SELECT * FROM users WHERE email = '$email' AND id != " . $staff['user_id'];
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
            mysqli_stmt_bind_param($stmt1, "ssssi", $full_name, $email, $ph_no, $status, $staff['user_id']);
            
            if (mysqli_stmt_execute($stmt1)) {
                // Update staff table
                $sql2 = "UPDATE staff SET fname = ?, mname = ?, lname = ?, email = ?, ph_no = ?, s_no = ?, s_name = ?, pincode = ?, district = ?, state = ?, country = ?, salary = ?, designation = ?, shift_morning = ?, shift_day = ? WHERE sfid = ?";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "ssssssssssssdssi", $fname, $mname, $lname, $email, $ph_no, $s_no, $s_name, $pincode, $district, $state, $country, $salary, $designation, $shift_morning, $shift_day, $sfid);
                
                if (mysqli_stmt_execute($stmt2)) {
                    $success = "Staff updated successfully!";
                    // Refresh staff data
                    $staff_result = mysqli_query($conn, $staff_query);
                    $staff = mysqli_fetch_assoc($staff_result);
                } else {
                    $error = "Error updating staff: " . mysqli_error($conn);
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
    <title>Edit Staff - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Edit Staff</h1>
            <div>
                <a href="admin_view_staff.php?sfid=<?php echo $sfid; ?>" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="admin_manage_staff.php" class="btn btn-secondary">
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
                            <label>Staff ID</label>
                            <input type="text" value="<?php echo htmlspecialchars($staff['staff_id']); ?>" disabled style="background: #f5f5f5;">
                        </div>
                        <div class="form-group">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="fname" value="<?php echo htmlspecialchars($staff['fname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="mname" value="<?php echo htmlspecialchars($staff['mname'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="lname" value="<?php echo htmlspecialchars($staff['lname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="ph_no" value="<?php echo htmlspecialchars($staff['ph_no'] ?? ''); ?>" pattern="[0-9]{10}">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="active" <?php echo $staff['user_status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $staff['user_status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Street Number</label>
                            <input type="text" name="s_no" value="<?php echo htmlspecialchars($staff['s_no'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Street Name</label>
                            <input type="text" name="s_name" value="<?php echo htmlspecialchars($staff['s_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Pincode</label>
                            <input type="text" name="pincode" value="<?php echo htmlspecialchars($staff['pincode'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text" name="district" value="<?php echo htmlspecialchars($staff['district'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" value="<?php echo htmlspecialchars($staff['state'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="<?php echo htmlspecialchars($staff['country'] ?? 'India'); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-briefcase"></i> Employment Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Designation</label>
                            <input type="text" name="designation" value="<?php echo htmlspecialchars($staff['designation'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Salary</label>
                            <input type="number" name="salary" value="<?php echo $staff['salary'] ?? 0; ?>" step="0.01" min="0">
                        </div>
                        <div class="form-group">
                            <label>Shift</label>
                            <div style="display: flex; gap: 20px; margin-top: 8px;">
                                <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                                    <input type="checkbox" name="shift_morning" value="1" <?php echo $staff['shift_morning'] ? 'checked' : ''; ?>>
                                    Morning
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                                    <input type="checkbox" name="shift_day" value="1" <?php echo $staff['shift_day'] ? 'checked' : ''; ?>>
                                    Day
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="update_staff" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Staff
                    </button>
                    <a href="admin_manage_staff.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

