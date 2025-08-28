<!DOCTYPE html>
<html lang="en">
<head>
    <!--Add a title and a logo for the web page-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRP Login</title>
    <link rel="shortcut icon" href="../Pictures/DRP_logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../CSS/User_profile.css">
    <link rel="stylesheet" href="../CSS/Header_and_footer.css">
</head>
<body class="bg">
    <div class="form-cont">
        <h1><center>User Profile</center></h1>
        <?php
        require '../Connection.php';

        // Use $conn instead of $con (matches your Connection.php)
        if (!isset($conn) || $conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        // Update user if form submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
            $username = $_POST['username'];
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $newpass = $_POST['newPassword'];

            $hash_pass = password_hash($newpass, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET Full_Name='$fullname', Email='$email', Phone_Number='$phone', Password='$hash_pass' WHERE User_Name='$username'";
            if ($conn->query($sql) === TRUE) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }

        // Retrieve username from URL
        $username = isset($_GET['username']) ? $_GET['username'] : '';

        // Fetch user details
        $sql = "SELECT Full_Name, Email, Phone_Number FROM users WHERE User_Name = '$username'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<form method='post'>";
                echo "<div class='mb-3'>";
                echo "<label for='fullname' class='form-label'>User Name<br><span id='username'>$username<br></span></label>";
                echo "<input type='hidden' name='username' value='$username'>";
                echo "</div><br>";
                echo "<div class='mb-3'>";
                echo "<label for='fullname' class='form-label'>Full Name</label>";
                echo "<input type='text' name='fullname' class='form-control' value='" . $row["Full_Name"] . "'>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label for='email' class='form-label'>Email</label>";
                echo "<input type='email' name='email' class='form-control' value='" . $row["Email"] . "'>";
                echo "</div>";
                echo "<div class='mb-3'>";
                echo "<label class='form-label'>Phone Number</label>";
                echo "<input type='text' name='phone' class='form-control' value='" . $row["Phone_Number"] . "'>";
                echo "</div>";
                echo "<label for='fullname' class='form-label'>New Password</label>";
                echo "<input type='text' name='newPassword' class='form-control'>";
                echo "<br><br><br><br>";
                echo "<input type='submit' class='login login-submit btn btn-primary' name='update' value='Update profile'>";
                echo "<a href='Home.html?=' class='btn btn-secondary'>Back</a>";
                echo "</form>";
            }
        } else {
            echo "0 results";
        }

        $conn->close();
        ?>
    </div>
    <script>
        // Function to get URL parameters
        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

        // Get the username from URL and display it
        var username = getParameterByName('username');
        if (username) {
            document.getElementById('username').innerText = username;
        }
    </script>
</body>
</html>
