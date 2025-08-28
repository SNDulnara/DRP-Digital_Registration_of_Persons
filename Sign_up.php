<?php
require 'Connection.php';

// Only process on POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../NIC-issuing-/HTML/Sign up.html");
    exit;
}

$fullname = trim($_POST["fullName"] ?? '');
$username = trim($_POST["username"] ?? '');
$email    = trim($_POST["email"] ?? '');
$contact  = trim($_POST["phoneNumber"] ?? '');
$pass     = $_POST["password"] ?? '';
$confirm  = $_POST["confirmPassword"] ?? '';

// Basic validation
if ($pass !== $confirm) {
    echo "<script>alert('Passwords do not match'); window.location='../NIC-issuing-/HTML/Sign up.html';</script>";
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email'); window.location='../NIC-issuing-/HTML/Sign up.html';</script>";
    exit;
}
if (strlen($username) < 3 || strlen($pass) < 6) {
    echo "<script>alert('Username or password too short'); window.location='../NIC-issuing-/HTML/Sign up.html';</script>";
    exit;
}

// Uniqueness check
$stmt = $conn->prepare("SELECT 1 FROM users WHERE User_Name = ? OR Email = ? LIMIT 1");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "<script>alert('Username or Email already exists'); window.location='../NIC-issuing-/HTML/Sign up.html';</script>";
    exit;
}
$stmt->close();

// Hash and insert
$hash_pass = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (Full_Name, User_Name, Email, Phone_Number, Password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $fullname, $username, $email, $contact, $hash_pass);

if ($stmt->execute()) {
    header("Location: ../NIC-issuing-/HTML/Login.html?message=success");
    exit;
} else {
    echo "<script>alert('Registration failed'); window.location='../NIC-issuing-/HTML/Sign up.html';</script>";
    exit;
}
