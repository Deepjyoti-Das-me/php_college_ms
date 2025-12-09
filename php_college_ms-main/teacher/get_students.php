<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user_type"] !== "teacher") {
    header("Content-Type: application/json");
    echo json_encode([]);
    exit();
}
require_once "../database.php";

$subject_id = intval($_GET['subject_id'] ?? 0);
if ($subject_id > 0) {
    $query = "SELECT s.sid, s.roll_no, s.fname, s.lname 
              FROM students s 
              JOIN subjects sub ON s.cid = sub.cid 
              WHERE sub.id = $subject_id AND s.status = 'active'
              ORDER BY s.roll_no";
    $result = mysqli_query($conn, $query);
    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    header("Content-Type: application/json");
    echo json_encode($students);
} else {
    header("Content-Type: application/json");
    echo json_encode([]);
}
?>

