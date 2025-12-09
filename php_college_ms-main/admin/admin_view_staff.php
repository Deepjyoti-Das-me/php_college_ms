<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$sfid = intval($_GET['sfid'] ?? 0);
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Staff - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-eye"></i> View Staff Details</h1>
            <div>
                <a href="admin_edit_staff.php?sfid=<?php echo $sfid; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="admin_manage_staff.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
            <!-- Profile Card -->
            <div class="form-container">
                <div style="text-align: center; padding: 20px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #FF9800, #F57C00); display: flex; align-items: center; justify-content: center; font-size: 48px; color: white;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($staff['fname'] . ' ' . ($staff['mname'] ? $staff['mname'] . ' ' : '') . $staff['lname']); ?></h2>
                    <p style="color: #666; margin-bottom: 20px;"><?php echo htmlspecialchars($staff['staff_id']); ?></p>
                    <div style="padding: 12px; background: #f5f5f5; border-radius: 8px;">
                        <strong>Status:</strong><br>
                        <span style="color: <?php echo $staff['user_status'] == 'active' ? '#4CAF50' : '#F44336'; ?>;">
                            <?php echo strtoupper($staff['user_status']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Staff Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Staff ID</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff['staff_id']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff['fname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff['mname'] ?? ''); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff['lname']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($staff['email']); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff['ph_no'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Designation</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff['designation'] ?? 'N/A'); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="text" value="₹<?php echo number_format($staff['salary'] ?? 0, 2); ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Shift</label>
                        <input type="text" value="<?php 
                            $shifts = [];
                            if ($staff['shift_morning']) $shifts[] = 'Morning';
                            if ($staff['shift_day']) $shifts[] = 'Day';
                            echo !empty($shifts) ? implode(', ', $shifts) : 'N/A';
                        ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Hire Date</label>
                        <input type="text" value="<?php echo $staff['hire_date'] ? date('d M Y', strtotime($staff['hire_date'])) : 'N/A'; ?>" disabled style="background: #f5f5f5;">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Address</label>
                        <textarea rows="3" disabled style="background: #f5f5f5;">
<?php 
$address = [];
if ($staff['s_no']) $address[] = $staff['s_no'];
if ($staff['s_name']) $address[] = $staff['s_name'];
if ($staff['district']) $address[] = $staff['district'];
if ($staff['state']) $address[] = $staff['state'];
if ($staff['pincode']) $address[] = $staff['pincode'];
if ($staff['country']) $address[] = $staff['country'];
echo !empty($address) ? htmlspecialchars(implode(', ', $address)) : 'N/A';
?>
                        </textarea>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

