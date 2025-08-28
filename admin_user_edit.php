<?php
require 'Connection.php';

$user_id = $_GET['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit user</title>
</head>
<body>
    <h2>Edit User: <?php echo $row["User_Name"]; ?></h2>


    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add User Details</h2>
            <form id="updateForm" method="POST">
                <fieldset>
                    <input type="hidden" id="userId" name="userId" value='<?php echo $row["ID"]; ?>'>
                    
                    <label for="username">User Name:</label><br>
                    <input type="text" id="username" name="username" value='<?php echo $row["User_Name"]; ?>' readonly><br><br>

                    <label for="fullname">Full Name:</label><br>
                    <input type="text" id="fullname" name="fullname" value='<?php echo $row["Full_Name"]; ?>'><br><br>

                    <label for="text03">Email:</label><br>
                    <input type="email" id="text03" name="text03" value='<?php echo $row["Email"]; ?>'><br><br>

                    <label for="text04">Phone number:</label><br>
                    <input type="text" id="text04" name="text04" value='<?php echo $row["Phone_Number"]; ?>'><br><br>

                    <!-- Add more fields as required -->
                    <input type="submit" class="sbt" name="update_user" value="Update">
                </fieldset>
            </form>
        </div>
    </div>

</body>
</html>

<?php

function sanitize_input($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $userId = sanitize_input($_POST['userId']);
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['text03']);
    $Phonenumber = sanitize_input($_POST['text04']);
    

    $sql = "UPDATE users SET Full_Name = '$fullname', Email = '$email', Phone_Number = '$Phonenumber' WHERE ID = '$userId'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "<script>alert('User details updated successfully!');</script>";
        header("Location: Admit_test.php");
    } else {
        echo "<script>alert('Failed to update user details!');</script>";
    }
}

?>