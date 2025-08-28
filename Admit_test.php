<?php
require 'Connection.php';

function sanitize_input($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle Delete Operation
    if (isset($_POST['delete'])) {
        $uID = $_POST['delete'];
        // SQL query to delete user details by id
        $deleteSql = "DELETE FROM users WHERE ID='$uID'";
        if ($conn->query($deleteSql)) {
            // echo "Record deleted successfully";
            
        } else {
            echo "<script>alert('Error deleting record: ".$conn->error."');</script>"; 
        }

    }
    
}



// Fetching all reservations
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Details</title>
    <style>
        table {
            width: 90%;
            border-collapse: collapse;
            margin-left: 5vw;
            background-color: rgb(176, 176, 214);
            border-radius: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2;

        }


        .btn {
            padding: 9px 16px;
            background-color: rgb(93, 93, 213);
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        
        }

        body{
            background-color: rgb(208, 214, 213);
        }
    </style>
</head>
<body>
    <h2>Details</h2>
    

    <form method="get" action="./admin_user_add.php">
        <button class="btn" type="submit">Add User</button>
    </form>


    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <!-- <th>Password</th> -->
                <th>Actions</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['Full_Name'] . "</td>";
                    echo "<td>" . $row['User_Name'] . "</td>";
                    echo "<td>" . $row['Email'] . "</td>";
                    echo "<td>" . $row['Phone_Number'] . "</td>";
                    // echo "<td>" . $row['Password'] . "</td>";
                    echo "<td>";
                    echo "<form method='get' action='./admin_user_edit.php'>";
                    echo "<input type='hidden' id='user_id' name='user_id' value='".$row['ID']."'>";
                    echo "<button class='btn' type='submit'>Edit</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "<td>";
                    echo "<form method='post'>";
                    echo "<button class='btn' onclick='myFunction()' style='background-color: red;' type='submit' name='delete' value='".$row['ID']."'>Delete</button>";
                    echo "</form>";
                    echo "<td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No user found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <script>
    function myFunction() {
        alert("Click 'OK' to delete user details!");
        // Add your desired actions here
    }
</script>
</body>
</html>

<?php
$conn->close();
?>
