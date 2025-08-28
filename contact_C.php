<?php
require 'Connection.php';

if(isset($_POST['c-btn2'])){
    $Name  = trim($_POST["txt1"]);
    $Email = trim($_POST["mail1"]);
    $Desc  = trim($_POST["txa1"]);

    if (empty($Name) || empty($Email) || empty($Desc)) {
        echo "<script>alert('All fields are required!'); window.location='../NIC-issuing-/HTML/Contact Us.html';</script>";
        exit;
    }

    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location='../NIC-issuing-/HTML/Contact Us.html';</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO contact (Name, Email, Description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $Name, $Email, $Desc);

    if ($stmt->execute()) {
        header("Location: ../NIC-issuing-/HTML/Contact Us.html?message=Success");
        exit;
    } else {
        echo "<script>alert('Error: ".$conn->error."'); window.location='../NIC-issuing-/HTML/Contact Us.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
