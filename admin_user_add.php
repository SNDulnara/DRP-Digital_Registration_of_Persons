<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit user</title>
    <style>
        fieldset{
            padding-right: 500px;
        }
    </style>
</head>
<body>
    <h2>Add User</h2>


    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add User Details</h2>
            <form id="updateForm" method="POST">
                <fieldset>
            
                    <label for="username">User Name:</label><br>
                    <input type="text" id="username" name="username"><br><br>

                    <label for="fullname">Full Name:</label><br>
                    <input type="text" id="fullname" name="fullname"><br><br>

                    <label for="text03">Email:</label><br>
                    <input type="email" id="text03" name="text03"><br><br>

                    <label for="text04">Phone number:</label><br>
                    <input type="text" id="text04" name="text04"><br><br>

                    <label for="text05">Password:</label><br>
                    <input type="password" id="text05" name="text05"><br><br>

                    <!-- Add more fields as required -->
                    <input type="submit" class="sbt" name="add_user" value="Create">
                </fieldset>
            </form>
        </div>
    </div>

</body>
</html>

<?php

require_once('Connection.php');

function sanitize_input($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $userId = sanitize_input($_POST['userId']);
    $fullname = sanitize_input($_POST['fullname']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['text03']);
    $Phonenumber = sanitize_input($_POST['text04']);
    $password = password_hash(sanitize_input($_POST['text05']), PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (User_Name, Full_Name, Email, Phone_Number, Password) VALUES ('$username', '$fullname', '$email', '$Phonenumber', '$password')";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "<script>alert('User details updated successfully!');</script>";
        header("Location: Admit_test.php");
    } else {
        echo "<script>alert('Failed to update user details!');</script>";
    }
}

?>