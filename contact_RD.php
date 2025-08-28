<?php
require 'Connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        $uID = intval($_POST['delete']); // convert to int for safety

        $stmt = $conn->prepare("DELETE FROM contact WHERE ID = ?");
        $stmt->bind_param("i", $uID);

        if ($stmt->execute()) {
            header("Location: contact_RD.php?deleted=1");
            exit;
        } else {
            echo "<script>alert('Error deleting record: ".$conn->error."');</script>";
        }

        $stmt->close();
    }
}

$sql = "SELECT * FROM contact";
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


    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row['Name']."</td>";
                    echo "<td>".$row['Email']."</td>";
                    echo "<td>".$row['Description']."</td>";
                    echo "<td>";
                    echo "<form method='post'>";
                    echo "<button class='btn' onclick='myFunction()' ; type='submit' name='delete' value='".$row['ID']."'>Delete</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No inquiry found</td></tr>";
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