<?php
session_start();
include("Config.php");

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    // Collect form data
    $fullname = trim($_POST['fullname']);
    $nic = trim($_POST['nic']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);

    // ✅ Validate Date of Birth
    $cutoffDate = strtotime("2007-01-01");
    if (strtotime($dob) <= $cutoffDate) {
        echo "<script>alert('Your date of birth must be after 2006.'); window.location.href='first.html';</script>";
        exit;
    }

    // ✅ File Upload Security
    $uploads_dir = "uploads/";
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    $file_path = null;

    if (!empty($_FILES['file01']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = mime_content_type($_FILES['file01']['tmp_name']);
        $file_size = $_FILES['file01']['size'];

        if (!in_array($file_type, $allowed_types)) {
            die("Invalid file type. Only JPG and PNG allowed.");
        }

        if ($file_size > 2 * 1024 * 1024) { // 2MB limit
            die("File too large. Max size 2MB.");
        }

        $ext = pathinfo($_FILES['file01']['name'], PATHINFO_EXTENSION);
        $new_name = uniqid("doc_", true) . "." . $ext;
        $file_path = $uploads_dir . $new_name;

        if (!move_uploaded_file($_FILES['file01']['tmp_name'], $file_path)) {
            die("Error uploading file.");
        }
    }

    // ✅ Check for duplicate (Fullname + DOB)
    $stmt = $conn->prepare("SELECT ID FROM first_time WHERE Full_Name = ? AND Date_of_Birth = ?");
    $stmt->bind_param("ss", $fullname, $dob);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Your information already exists. Please wait for your NIC.'); window.location.href='first.html';</script>";
        exit;
    }
    $stmt->close();

    // ✅ Insert record securely
    $stmt = $conn->prepare("INSERT INTO first_time 
        (Full_Name, NIC, Date_of_Birth, Gender, Email, Mobile, File_Path) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fullname, $nic, $dob, $gender, $email, $mobile, $file_path);

    if ($stmt->execute()) {
        echo "<script>alert('Your details have been submitted successfully.'); window.location.href='first.html';</script>";
    } else {
        echo "<script>alert('Error saving data. Please try again.'); window.location.href='first.html';</script>";
    }

    $stmt->close();
}
?>
