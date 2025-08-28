<?php
$servername = "localhost";
$username   = "root";   // default root user
$password   = "";       // no password
$dbname     = "drp";    // make sure this DB exists in phpMyAdmin

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
