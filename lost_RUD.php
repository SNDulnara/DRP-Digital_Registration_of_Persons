<?php
session_start();
require 'Connection.php';

// TODO (optional): Restrict access
// if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) { http_response_code(403); exit('Forbidden'); }

// CSRF token bootstrap
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Handle Delete via GET (PRG pattern)
if (isset($_GET['delete'])) {
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(400);
        exit('CSRF token mismatch.');
    }

    $id = intval($_GET['delete']);

    // If you store file paths in lost table, fetch and unlink them here (example fields shown)
    $stmt = $conn->prepare("SELECT Imag, Birth_Certificate, Police_Report, Recident_verfication_certificate, Signature FROM lost WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($p1, $p2, $p3, $p4, $p5);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM lost WHERE ID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // If you switched to filesystem paths, uncomment the unlink lines:
        // foreach ([$p1,$p2,$p3,$p4,$p5] as $fp) { if ($fp && file_exists($fp)) { @unlink($fp); } }
        header("Location: lost_RUD.php?msg=Record deleted");
        exit;
    } else {
        header("Location: lost_RUD.php?msg=Delete failed");
        exit;
    }
}

// Handle Update via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(400);
        exit('CSRF token mismatch.');
    }

    $id                 = intval($_POST['userId']);
    $fullname           = trim($_POST['fullname']);
    $name_with_initial  = trim($_POST['name_with_initial']);
    $perm_addr          = trim($_POST['text03']);
    $country_birth      = trim($_POST['text04']);
    $nationality        = trim($_POST['text05']);
    $sex                = trim($_POST['rbt1']);
    $dob                = trim($_POST['dOFb']);

    // Optional: validate fields further here

    $stmt = $conn->prepare("
        UPDATE lost
        SET Full_Name = ?, Name_with_Initial = ?, Permenent_Address = ?, Country_of_Birth = ?, Nationality = ?, Sex = ?, Date_of_Birth = ?
        WHERE ID = ?
    ");
    $stmt->bind_param("sssssssi", $fullname, $name_with_initial, $perm_addr, $country_birth, $nationality, $sex, $dob, $id);

    if ($stmt->execute()) {
        header("Location: lost_RUD.php?msg=Update successful");
        exit;
    } else {
        header("Location: lost_RUD.php?msg=Update failed");
        exit;
    }
}

// Fetch rows only when showing table
$rows = [];
if (isset($_POST['show_all_info']) || isset($_GET['msg'])) {
    $res = $conn->query("SELECT ID, Full_Name, Name_with_Initial, Permenent_Address, Country_of_Birth, Nationality, Sex, Date_of_Birth FROM lost ORDER BY ID DESC");
    if ($res) {
        while ($r = $res->fetch_assoc()) { $rows[] = $r; }
        $res->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Lost NIC — Manage Records</title>
<style>
    body { background:#bbb; font-family: system-ui, Arial, sans-serif; }
    table { border-collapse: collapse; width:95%; margin:20px auto; background:#fff; }
    th, td { border:1px solid #ddd; padding:8px; text-align:center; }
    th { background:#222; color:#fff; }
    .toolbar { width:95%; margin:20px auto; display:flex; gap:12px; align-items:center; }
    .msg { text-align:center; font-weight:600; color: #0a7; }
    .btn { padding:8px 14px; border-radius:8px; border:1px solid #555; background:#eee; cursor:pointer; }
    .btn-primary { background:#2aa; color:#fff; border-color:#299; }
    .btn-danger { background:#e33; color:#fff; border-color:#c22; }
    input[type="text"], input[type="date"] { width:100%; box-sizing:border-box; padding:6px; }
    .action-cell { display:flex; gap:8px; justify-content:center; }
</style>
</head>
<body>

<div class="toolbar">
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
        <button class="btn btn-primary" name="show_all_info" value="1" type="submit">Show All Account Details</button>
    </form>
    <form method="post" action="lost_RUD.php" onsubmit="return confirm('Delete this ID?');" style="margin-left:auto; display:flex; gap:8px; align-items:center;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
        <input class="input" name="ID" placeholder="Enter ID number" required type="text" style="padding:6px;">
        <button class="btn btn-danger" name="remove_use_account" value="1" type="submit">Delete</button>
    </form>
</div>

<?php if (isset($_GET['msg'])): ?>
    <p class="msg"><?php echo htmlspecialchars($_GET['msg']); ?></p>
<?php endif; ?>

<?php
// Safe Delete via POST (ID box)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_use_account'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<p class='msg' style='color:#e33;'>CSRF token mismatch.</p>";
    } else {
        $idToDelete = intval($_POST['ID']);
        $stmt = $conn->prepare("DELETE FROM lost WHERE ID = ?");
        $stmt->bind_param("i", $idToDelete);
        if ($stmt->execute()) {
            header("Location: lost_RUD.php?msg=Record deleted");
            exit;
        } else {
            echo "<p class='msg' style='color:#e33;'>Delete failed.</p>";
        }
    }
}
?>

<?php if (!empty($rows)): ?>
    <h2 style="text-align:center; color:#a00;">User Details</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Name with Initial</th>
            <th>Permenent Address</th>
            <th>Country of Birth</th>
            <th>Nationality</th>
            <th>Sex</th>
            <th>Date of Birth</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($rows as $row): ?>
        <tr>
            <form method="post" action="lost_RUD.php">
                <td><?php echo (int)$row['ID']; ?><input type="hidden" name="userId" value="<?php echo (int)$row['ID']; ?>"></td>
                <td><input type="text" name="fullname" value="<?php echo htmlspecialchars($row['Full_Name']); ?>"></td>
                <td><input type="text" name="name_with_initial" value="<?php echo htmlspecialchars($row['Name_with_Initial']); ?>"></td>
                <td><input type="text" name="text03" value="<?php echo htmlspecialchars($row['Permenent_Address']); ?>"></td>
                <td><input type="text" name="text04" value="<?php echo htmlspecialchars($row['Country_of_Birth']); ?>"></td>
                <td><input type="text" name="text05" value="<?php echo htmlspecialchars($row['Nationality']); ?>"></td>
                <td>
                    <select name="rbt1">
                        <option value="male"   <?php if (strtolower($row['Sex'])==='male')   echo 'selected'; ?>>male</option>
                        <option value="female" <?php if (strtolower($row['Sex'])==='female') echo 'selected'; ?>>female</option>
                    </select>
                </td>
                <td><input type="date" name="dOFb" value="<?php echo htmlspecialchars($row['Date_of_Birth']); ?>"></td>
                <td class="action-cell">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                    <button class="btn btn-primary" type="submit" name="update_user" value="1">Update</button>
                    <a class="btn btn-danger" href="lost_RUD.php?delete=<?php echo (int)$row['ID']; ?>&csrf_token=<?php echo urlencode($csrf); ?>" onclick="return confirm('Delete this record?')">Delete</a>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
<?php elseif (isset($_POST['show_all_info'])): ?>
    <p style="text-align:center; font-weight:600; color:#a00;">Empty User List!</p>
<?php endif; ?>

<div style="width:95%; margin:20px auto;">
    <p><strong>Go back to form</strong></p>
    <a href="../NIC-issuing-/HTML/Form_Obtaining_NIC_for_a_Lost_NIC.html"><button class="btn">Back</button></a>
</div>

</body>
</html>
