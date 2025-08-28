<?php
session_start();
require 'Connection.php';

// CSRF bootstrap (add hidden input in your form)
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

// Only handle on submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid request (CSRF).");
    }

    $fName     = trim($_POST["text01"]);
    $nwInitial = trim($_POST["text02"]);
    $pAddress  = trim($_POST["text03"]);
    $cOfbirth  = trim($_POST["text04"]);
    $natinal   = trim($_POST["text05"]);
    $gender    = trim($_POST["rbt1"]);
    $DofB      = trim($_POST["dOfb"]);
    $dAcknow   = isset($_POST['cbox01']) ? 1 : 0;

    // Validate DOB (must be after 2006-12-31)
    $cutoff = strtotime("2007-01-01");
    if (!strtotime($DofB) || strtotime($DofB) <= $cutoff) {
        echo "<script>alert('Your date of birth must be after 2006.'); window.location='../NIC-issuing-/HTML/Form_Obtaining_NIC_for_a_Lost_NIC.html';</script>";
        exit;
    }

    // Duplicate check on lost table: Full_Name + Date_of_Birth
    $stmt = $conn->prepare("SELECT ID FROM lost WHERE Full_Name = ? AND Date_of_Birth = ?");
    $stmt->bind_param("ss", $fName, $DofB);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('This entry already exists.'); window.location='../NIC-issuing-/HTML/Form_Obtaining_NIC_for_a_Lost_NIC.html';</script>";
        exit;
    }
    $stmt->close();

    // File uploads — move to filesystem and store paths
    $uploadBase = __DIR__ . "/uploads/lost/";
    if (!is_dir($uploadBase)) { mkdir($uploadBase, 0775, true); }

    // Helper
    function save_upload($key, $allowed=['image/jpeg','image/png','application/pdf'], $max=3_000_000) {
        if (empty($_FILES[$key]['name'])) return null;
        if (!is_uploaded_file($_FILES[$key]['tmp_name'])) return null;

        $type = mime_content_type($_FILES[$key]['tmp_name']);
        $size = $_FILES[$key]['size'];
        if (!in_array($type, $allowed)) { throw new RuntimeException("Invalid file type for $key."); }
        if ($size > $max) { throw new RuntimeException("File too large for $key (max 3MB)."); }

        $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION) ?: 'bin';
        $name = $key . '_' . bin2hex(random_bytes(8)) . '.' . strtolower($ext);
        global $uploadBase;
        $dest = $uploadBase . $name;
        if (!move_uploaded_file($_FILES[$key]['tmp_name'], $dest)) {
            throw new RuntimeException("Failed to upload $key.");
        }
        // Return relative path (public)
        return 'uploads/lost/' . $name;
    }

    try {
        $path1 = save_upload('file01'); // Imag
        $path2 = save_upload('file02'); // Birth_Certificate
        $path3 = save_upload('file03'); // Police_Report
        $path4 = save_upload('file04'); // Recident_verfication_certificate
        $path5 = save_upload('file05'); // Signature
    } catch (RuntimeException $e) {
        echo "<script>alert(".json_encode($e->getMessage())."); window.location='../NIC-issuing-/HTML/Form_Obtaining_NIC_for_a_Lost_NIC.html';</script>";
        exit;
    }

    // Insert
    $stmt = $conn->prepare("
        INSERT INTO lost
        (Full_Name, Name_with_Initial, Permenent_Address, Country_of_Birth, Nationality, Sex, Date_of_Birth,
         Imag, Birth_Certificate, Police_Report, Recident_verfication_certificate, Signature, Acknowledgment)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssssssssssi",
        $fName, $nwInitial, $pAddress, $cOfbirth, $natinal, $gender, $DofB,
        $path1, $path2, $path3, $path4, $path5, $dAcknow
    );

    if ($stmt->execute()) {
        // PRG: redirect to payment or success page
        header("Location: ../NIC-issuing-/HTML/Bank_Payment.html?message=success");
        exit;
    } else {
        echo "<script>alert('Error saving data.'); window.location='../NIC-issuing-/HTML/Form_Obtaining_NIC_for_a_Lost_NIC.html';</script>";
        exit;
    }
}
