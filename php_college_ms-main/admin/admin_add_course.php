<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "admin") {
   header("Location: ../login.php");
   exit();
}
require_once "../database.php";

$success = "";
$error = "";

if (isset($_POST["add_course"])) {
    $course_code = mysqli_real_escape_string($conn, $_POST["course_code"]);
    $cname = mysqli_real_escape_string($conn, $_POST["cname"]);
    $major = mysqli_real_escape_string($conn, $_POST["major"] ?? "");
    $duration_years = intval($_POST["duration_years"] ?? 4);
    $total_semesters = intval($_POST["total_semesters"] ?? 8);
    $fees = floatval($_POST["fees"] ?? 0);
    
    // Multi-valued attributes
    $vac = isset($_POST["vac"]) ? $_POST["vac"] : [];
    $sec = isset($_POST["sec"]) ? $_POST["sec"] : [];
    $aec = isset($_POST["aec"]) ? $_POST["aec"] : [];
    $idc = isset($_POST["idc"]) ? $_POST["idc"] : [];
    $minor = isset($_POST["minor"]) ? $_POST["minor"] : [];
    
    if (empty($course_code) || empty($cname)) {
        $error = "Course code and name are required!";
    } else {
        $check_code = "SELECT * FROM courses WHERE course_code = '$course_code'";
        $result = mysqli_query($conn, $check_code);
        if (mysqli_num_rows($result) > 0) {
            $error = "Course code already exists!";
        } else {
            $sql = "INSERT INTO courses (course_code, cname, major, duration_years, total_semesters, fees) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssiid", $course_code, $cname, $major, $duration_years, $total_semesters, $fees);
            
            if (mysqli_stmt_execute($stmt)) {
                $cid = mysqli_insert_id($conn);
                
                // Insert VAC
                foreach ($vac as $vac_name) {
                    if (!empty(trim($vac_name))) {
                        mysqli_query($conn, "INSERT INTO course_vac (cid, vac_name) VALUES ($cid, '" . mysqli_real_escape_string($conn, trim($vac_name)) . "')");
                    }
                }
                
                // Insert SEC
                foreach ($sec as $sec_name) {
                    if (!empty(trim($sec_name))) {
                        mysqli_query($conn, "INSERT INTO course_sec (cid, sec_name) VALUES ($cid, '" . mysqli_real_escape_string($conn, trim($sec_name)) . "')");
                    }
                }
                
                // Insert AEC
                foreach ($aec as $aec_name) {
                    if (!empty(trim($aec_name))) {
                        mysqli_query($conn, "INSERT INTO course_aec (cid, aec_name) VALUES ($cid, '" . mysqli_real_escape_string($conn, trim($aec_name)) . "')");
                    }
                }
                
                // Insert IDC
                foreach ($idc as $idc_name) {
                    if (!empty(trim($idc_name))) {
                        mysqli_query($conn, "INSERT INTO course_idc (cid, idc_name) VALUES ($cid, '" . mysqli_real_escape_string($conn, trim($idc_name)) . "')");
                    }
                }
                
                // Insert Minor
                foreach ($minor as $minor_name) {
                    if (!empty(trim($minor_name))) {
                        mysqli_query($conn, "INSERT INTO course_minor (cid, minor_name) VALUES ($cid, '" . mysqli_real_escape_string($conn, trim($minor_name)) . "')");
                    }
                }
                
                // Record admin management
                $aid_query = "SELECT aid FROM admin WHERE user_id = (SELECT id FROM users WHERE email = '" . $_SESSION['user_email'] . "') LIMIT 1";
                $aid_result = mysqli_query($conn, $aid_query);
                if ($aid_row = mysqli_fetch_assoc($aid_result)) {
                    mysqli_query($conn, "INSERT INTO admin_manages_course (aid, cid) VALUES (" . $aid_row['aid'] . ", $cid)");
                }
                
                $success = "Course added successfully! Course Code: $course_code";
                $_POST = array();
            } else {
                $error = "Error adding course: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course - Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book"></i> Add New Course</h1>
            <a href="admin_manage_course.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Manage Courses</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="" class="admin-form" id="courseForm">
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Course Code <span class="required">*</span></label>
                            <input type="text" name="course_code" value="<?php echo $_POST['course_code'] ?? ''; ?>" required placeholder="e.g., CS101">
                        </div>
                        <div class="form-group">
                            <label>Course Name <span class="required">*</span></label>
                            <input type="text" name="cname" value="<?php echo $_POST['cname'] ?? ''; ?>" required placeholder="e.g., Computer Science">
                        </div>
                        <div class="form-group">
                            <label>Major</label>
                            <input type="text" name="major" value="<?php echo $_POST['major'] ?? ''; ?>" placeholder="e.g., Computer Science">
                        </div>
                        <div class="form-group">
                            <label>Duration (Years)</label>
                            <input type="number" name="duration_years" value="<?php echo $_POST['duration_years'] ?? 4; ?>" min="1" max="6">
                        </div>
                        <div class="form-group">
                            <label>Total Semesters</label>
                            <input type="number" name="total_semesters" value="<?php echo $_POST['total_semesters'] ?? 8; ?>" min="1" max="12">
                        </div>
                        <div class="form-group">
                            <label>Fees (₹)</label>
                            <input type="number" name="fees" value="<?php echo $_POST['fees'] ?? ''; ?>" step="0.01" min="0">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-list"></i> Course Components (Multi-valued Attributes)</h3>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>VAC (Value Added Courses)</label>
                        <div id="vac-container">
                            <div class="multi-input-group">
                                <input type="text" name="vac[]" placeholder="VAC1">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="multi-input-group">
                                <input type="text" name="vac[]" placeholder="VAC2">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-field" onclick="addField('vac-container', 'vac')">
                            <i class="fas fa-plus"></i> Add VAC
                        </button>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>SEC (Skill Enhancement Courses)</label>
                        <div id="sec-container">
                            <div class="multi-input-group">
                                <input type="text" name="sec[]" placeholder="SEC1">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="multi-input-group">
                                <input type="text" name="sec[]" placeholder="SEC2">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="multi-input-group">
                                <input type="text" name="sec[]" placeholder="SEC3">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-field" onclick="addField('sec-container', 'sec')">
                            <i class="fas fa-plus"></i> Add SEC
                        </button>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>AEC (Ability Enhancement Courses)</label>
                        <div id="aec-container">
                            <div class="multi-input-group">
                                <input type="text" name="aec[]" placeholder="AEC1">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="multi-input-group">
                                <input type="text" name="aec[]" placeholder="AEC2">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="multi-input-group">
                                <input type="text" name="aec[]" placeholder="AEC3">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-field" onclick="addField('aec-container', 'aec')">
                            <i class="fas fa-plus"></i> Add AEC
                        </button>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>IDC (Interdisciplinary Courses)</label>
                        <div id="idc-container">
                            <div class="multi-input-group">
                                <input type="text" name="idc[]" placeholder="IDC1">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="multi-input-group">
                                <input type="text" name="idc[]" placeholder="IDC2">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="multi-input-group">
                                <input type="text" name="idc[]" placeholder="IDC3">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-field" onclick="addField('idc-container', 'idc')">
                            <i class="fas fa-plus"></i> Add IDC
                        </button>
                    </div>

                    <div class="form-group">
                        <label>Minor</label>
                        <div id="minor-container">
                            <div class="multi-input-group">
                                <input type="text" name="minor[]" placeholder="Minor Course">
                                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-field" onclick="addField('minor-container', 'minor')">
                            <i class="fas fa-plus"></i> Add Minor
                        </button>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="add_course" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Course
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset Form
                    </button>
                </div>
            </form>
        </div>
    </main>

    <style>
        .multi-input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .multi-input-group input {
            flex: 1;
        }
        .btn-remove {
            padding: 12px 16px;
            background: #E64A4A;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-remove:hover {
            background: #c0392b;
        }
        .btn-add-field {
            padding: 8px 16px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }
        .btn-add-field:hover {
            background: #45a049;
        }
    </style>

    <script>
        function addField(containerId, fieldName) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = 'multi-input-group';
            div.innerHTML = `
                <input type="text" name="${fieldName}[]" placeholder="${fieldName.toUpperCase()}">
                <button type="button" class="btn-remove" onclick="removeField(this)"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(div);
        }
        
        function removeField(btn) {
            btn.closest('.multi-input-group').remove();
        }
    </script>
</body>
</html>


