<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$success = "";
$error = "";

// Get admin data
$admin_email = $_SESSION["user_email"];
$admin_query = "SELECT a.*, u.email, u.phone FROM admin a JOIN users u ON a.user_id = u.id WHERE u.email = '$admin_email' LIMIT 1";
$admin_result = mysqli_query($conn, $admin_query);
$admin_data = mysqli_fetch_assoc($admin_result);

if (isset($_POST["update_profile"])) {
    $fname = mysqli_real_escape_string($conn, $_POST["fname"]);
    $mname = mysqli_real_escape_string($conn, $_POST["mname"] ?? "");
    $lname = mysqli_real_escape_string($conn, $_POST["lname"]);
    $ph_no = mysqli_real_escape_string($conn, $_POST["ph_no"]);
    $s_no = mysqli_real_escape_string($conn, $_POST["s_no"] ?? "");
    $s_name = mysqli_real_escape_string($conn, $_POST["s_name"] ?? "");
    $pincode = mysqli_real_escape_string($conn, $_POST["pincode"] ?? "");
    $district = mysqli_real_escape_string($conn, $_POST["district"] ?? "");
    $state = mysqli_real_escape_string($conn, $_POST["state"] ?? "");
    $country = mysqli_real_escape_string($conn, $_POST["country"] ?? "India");
    
    if (empty($fname) || empty($lname)) {
        $error = "First name and last name are required!";
    } else {
        $aid = $admin_data['aid'];
        $user_id = $admin_data['user_id'];
        
        // Update admin table
        $sql1 = "UPDATE admin SET fname = ?, mname = ?, lname = ?, ph_no = ?, s_no = ?, s_name = ?, pincode = ?, district = ?, state = ?, country = ? WHERE aid = ?";
        $stmt1 = mysqli_prepare($conn, $sql1);
        mysqli_stmt_bind_param($stmt1, "ssssssssssi", $fname, $mname, $lname, $ph_no, $s_no, $s_name, $pincode, $district, $state, $country, $aid);
        
        // Update users table
        $full_name = trim($fname . " " . $mname . " " . $lname);
        $sql2 = "UPDATE users SET full_name = ?, phone = ? WHERE id = ?";
        $stmt2 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "ssi", $full_name, $ph_no, $user_id);
        
        if (mysqli_stmt_execute($stmt1) && mysqli_stmt_execute($stmt2)) {
            $success = "Profile updated successfully!";
            // Refresh admin data
            $admin_result = mysqli_query($conn, $admin_query);
            $admin_data = mysqli_fetch_assoc($admin_result);
            $_SESSION["user_name"] = $full_name;
        } else {
            $error = "Error updating profile: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt1);
        mysqli_stmt_close($stmt2);
    }
}

// Handle password change
if (isset($_POST["change_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All password fields are required!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long!";
    } else {
        $user_id = $admin_data['user_id'];
        $user_query = "SELECT password FROM users WHERE id = $user_id LIMIT 1";
        $user_result = mysqli_query($conn, $user_query);
        $user_row = mysqli_fetch_assoc($user_result);
        
        if (password_verify($current_password, $user_row['password'])) {
            $passwordHash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_pass = "UPDATE users SET password = '$passwordHash' WHERE id = $user_id";
            if (mysqli_query($conn, $update_pass)) {
                $success = "Password changed successfully!";
            } else {
                $error = "Error changing password: " . mysqli_error($conn);
            }
        } else {
            $error = "Current password is incorrect!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-edit"></i> Update Profile</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <!-- Profile Information -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-user"></i> Personal Information</h2>
                <form method="POST" action="" class="admin-form">
                    <div class="form-section">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>First Name <span class="required">*</span></label>
                                <input type="text" name="fname" value="<?php echo htmlspecialchars($admin_data['fname'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text" name="mname" value="<?php echo htmlspecialchars($admin_data['mname'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Last Name <span class="required">*</span></label>
                                <input type="text" name="lname" value="<?php echo htmlspecialchars($admin_data['lname'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($admin_data['email']); ?>" disabled style="background: #f5f5f5;">
                                <small>Email cannot be changed</small>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="ph_no" value="<?php echo htmlspecialchars($admin_data['ph_no'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-map-marker-alt"></i> Address</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Street Number</label>
                                <input type="text" name="s_no" value="<?php echo htmlspecialchars($admin_data['s_no'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Street Name</label>
                                <input type="text" name="s_name" value="<?php echo htmlspecialchars($admin_data['s_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Pincode</label>
                                <input type="text" name="pincode" value="<?php echo htmlspecialchars($admin_data['pincode'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>District</label>
                                <input type="text" name="district" value="<?php echo htmlspecialchars($admin_data['district'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="state" value="<?php echo htmlspecialchars($admin_data['state'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" name="country" value="<?php echo htmlspecialchars($admin_data['country'] ?? 'India'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-lock"></i> Change Password</h2>
                <form method="POST" action="" class="admin-form">
                    <div class="form-section">
                        <div class="form-grid">
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Current Password <span class="required">*</span></label>
                                <input type="password" name="current_password" required>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>New Password <span class="required">*</span></label>
                                <input type="password" name="new_password" required minlength="8">
                                <small>Minimum 8 characters</small>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Confirm New Password <span class="required">*</span></label>
                                <input type="password" name="confirm_password" required minlength="8">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>


