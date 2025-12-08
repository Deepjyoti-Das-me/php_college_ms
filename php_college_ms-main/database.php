<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "college_ms1";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

?>