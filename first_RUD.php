<?php
session_start();
include("Config.php");

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // CSRF validation
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token mismatch. Delete not allowed!");
    }

    // Fetch file path before deleting
    $stmt = $conn->prepare("SELECT File_Path FROM first_time WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    // Delete record
    $stmt = $conn->prepare("DELETE FROM first_time WHERE ID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($file_path && file_exists($file_path)) {
            unlink($file_path); // delete uploaded file
        }
        header("Location: first_RUD.php?msg=Record deleted successfully");
        exit;
    } else {
        header("Location: first_RUD.php?msg=Error deleting record");
        exit;
    }
}

// Handle Update
if (isset($_POST['update'])) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token mismatch. Update not allowed!");
    }

    $id = intval($_POST['id']);
    $fullname = trim($_POST['fullname']);
    $nic = trim($_POST['nic']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);

    $stmt = $conn->prepare("UPDATE first_time SET Full_Name=?, NIC=?, Date_of_Birth=?, Gender=?, Email=?, Mobile=? WHERE ID=?");
    $stmt->bind_param("ssssssi", $fullname, $nic, $dob, $gender, $email, $mobile, $id);

    if ($stmt->execute()) {
        header("Location: first_RUD.php?msg=Record updated successfully");
        exit;
    } else {
        header("Location: first_RUD.php?msg=Error updating record");
        exit;
    }
}

// Fetch all records
$result = $conn->query("SELECT * FROM first_time ORDER BY ID DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Records</title>
    <style>
        table {
            border-collapse: collapse;
            width: 95%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
        }
        th {
            background: #333;
            color: #fff;
        }
        a.delete {
            color: red;
            font-weight: bold;
        }
        .msg {
            text-align: center;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Manage Records</h2>
    <?php if (isset($_GET['msg'])): ?>
        <p class="msg"><?php echo htmlspecialchars($_GET['msg']); ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>NIC</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>File</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="POST" action="first_RUD.php">
                <td><?php echo $row['ID']; ?><input type="hidden" name="id" value="<?php echo $row['ID']; ?>"></td>
                <td><input type="text" name="fullname" value="<?php echo htmlspecialchars($row['Full_Name']); ?>"></td>
                <td><input type="text" name="nic" value="<?php echo htmlspecialchars($row['NIC']); ?>"></td>
                <td><input type="date" name="dob" value="<?php echo htmlspecialchars($row['Date_of_Birth']); ?>"></td>
                <td>
                    <select name="gender">
                        <option value="Male" <?php if($row['Gender']=="Male") echo "selected"; ?>>Male</option>
                        <option value="Female" <?php if($row['Gender']=="Female") echo "selected"; ?>>Female</option>
                    </select>
                </td>
                <td><input type="email" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>"></td>
                <td><input type="text" name="mobile" value="<?php echo htmlspecialchars($row['Mobile']); ?>"></td>
                <td>
                    <?php if ($row['File_Path']): ?>
                        <a href="<?php echo $row['File_Path']; ?>" target="_blank">View File</a>
                    <?php else: ?>
                        No File
                    <?php endif; ?>
                </td>
                <td>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="submit" name="update">Update</button>
                    <a class="delete" href="first_RUD.php?delete=<?php echo $row['ID']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
