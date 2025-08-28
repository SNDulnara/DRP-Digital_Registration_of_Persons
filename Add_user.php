<?php
require 'Connection.php';


// Function to sanitize user input
function sanitize_input($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

// Display all users
if (isset($_POST['show_all_users'])) {
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        echo "<center><h1>User Details!</h1></center>";
        echo "<table border='1' align='center'>";
echo "<tr><th>ID</th><th>Full Name</th><th>User Name</th><th>Email</th><th>Phone Number</th><th>Password</th><th>Actions</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['ID'] . "</td>";
    echo "<td>" . $row['Full_Name'] . "</td>";
    echo "<td>" . $row['User_Name'] . "</td>";
    echo "<td>" . $row['Email'] . "</td>";
    echo "<td>" . $row['Phone_Number'] . "</td>";
    echo "<td>" . $row['Password'] . "</td>";  // Display hashed password
    echo "<td><button class='update'>Update</button><button class='delete'>Delete</button></td>";
    echo "</tr>";
}
echo "</table>";

    } else {
        echo "<center><h3>No users found.</h3></center>";
    }
}

// Add a new user
if (isset($_POST['add_user'])) {
    $username = sanitize_input($_POST['username']);
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $phonenumber = sanitize_input($_POST['phonenumber']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password
    $insertQuery = "INSERT INTO users (Full_Name, User_Name, Email, Phone_Number, Password) VALUES ('$fullname', '$username', '$email', '$phonenumber', '$password')";
    if (mysqli_query($conn, $insertQuery)) {
        echo "<center><h3>User added successfully!</h3></center>";
    } else {
        echo "<center><h3>Error adding user.</h3></center>";
    }
}

// Delete a user
if (isset($_POST['delete_user'])) {
    $userId = sanitize_input($_POST['userId']);
    $deleteQuery = "DELETE FROM users WHERE ID = '$userId'";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<center><h3>User deleted successfully!</h3></center>";
    } else {
        echo "<center><h3>Error deleting user.</h3></center>";
    }
}

// Update a user
if (isset($_POST['update_user'])) {
    $userId = sanitize_input($_POST['userId']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $updateQuery = "UPDATE users SET User_Name = '$username', Email = '$email' WHERE ID = '$userId'";
    if (mysqli_query($conn, $updateQuery)) {
        echo "<center><h3>User updated successfully!</h3></center>";
    } else {
        echo "<center><h3>Error updating user.</h3></center>";
    }
}
?>
<div class="container">

<p class="login-card"><strong><h3>Show All User Details</h3></strong></p>
<div class="form">
    <form id="showUsersForm" method="POST">
        <input class="Rbutton" name="show_all_users" id="submit" tabindex="5" value="Show All Account Details!" type="submit">
    </form>
</div>
<br>

<p class="login-card"><strong><h3>Delete User Details</h3></strong></p>
<div class="form">
    <form id="deleteUserForm" method="POST">
        <input class="buttom" id="ID" name="userId" placeholder="Enter ID number" required="" tabindex="1" type="text">
        <input class="dbutton" name="delete_user" id="submit" tabindex="5" value="Delete" type="submit">
        <br>
    </form>
</div>

<br>
<hr style="margin: 20px auto; width: 100%; border: 2px solid;">

<p><strong>Click to go back to Home</strong></p>
<a href="../NIC-issuing-/HTML/Home_page.html"><button>Back</button></a>
</div>

<!-- Modal for Updating User Details -->
<div id="updateModal" class="modal">
<div class="modal-content">
<span class="close">&times;</span>
<h2>Update User Details</h2>
<form id="updateForm" method="POST">
  <input type="hidden" id="userId" name="userId">
  <label for="fullname">Full Name:</label>
  <input type="text" id="updated_fullname" name="fullname"><br><br>
  <!-- Additional fields can be updated here -->

  <input type="submit" name="update_user" value="Update">
</form>
</div>
</div>

<style>
body {
    background-image: url("1157b6fd85f1ff2.gif");
    background-color: white;
}

td, th {
    border: 1px solid red;
    border-radious: 15px;
}

/* Style the modal */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(255,65,75); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
}

/* The Close Button */
.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.Rbuttom {
    height: 4vh;
    border-radius: 1vw;
    cursor: pointer;
    background-color: #2aaEEEEE;
}

.buttom {
    height: 4vh;
    border-radius: 1vw;
}

.dbutton {
    height: 3.5vh;
    border-radius: 0.5vw;
    cursor: pointer;
}

.sbt {
    cursor: pointer;
}
</style>

<script>
// Get the modal
var modal = document.getElementById("updateModal");

// Get the button that opens the modal
var btns = document.getElementsByClassName("updateBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];



// When the user clicks on <span> (x), close the modal
span.onclick = function() {
modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
if (event.target == modal) {
    modal.style.display = "none";
}
}
</script>
