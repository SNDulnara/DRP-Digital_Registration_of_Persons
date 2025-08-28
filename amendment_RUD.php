<?php
require 'connection.php';

// Function to sanitize user input
function sanitize_input($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

if (isset($_POST['show_all_info'])) {
    $checkphone = "SELECT * FROM amendment";
    $run_check = mysqli_query($conn , $checkphone) or die(mysqli_error($conn));
    $countphone = mysqli_num_rows($run_check); 
    if ($countphone > 0 ) {
        echo  "<center><font color='red'><h1>User Details!</h1></font></center>";
        echo "<table border='1' align='center'>";
        echo "<tr><th>  ID  </th><th>  Full Name  </th><th>  Name with Initial  </th><th>  Permenent_Address  </th><th>  Country_of_Birth  </th><th>  Nationality  </th><th>  Sex  </th><th>  Date of birth  </th><th>  Actions  </th></tr>";
        while ($row = mysqli_fetch_assoc($run_check)) {
            echo "<tr>";
            echo "<td>" . $row['ID'] . "</td>";
            echo "<td>" . $row['Full_Name'] . "</td>";
            echo "<td>" . $row['Name_with_Initial'] . "</td>";
            echo "<td>" . $row['Permenent_Address'] . "</td>";
            echo "<td>" . $row['Country_of_Birth'] . "</td>";
            echo "<td>" . $row['Nationality'] . "</td>";
            echo "<td>" . $row['Sex'] . "</td>";
            echo "<td>" . $row['Date_of_Birth'] . "</td>";
            // Add buttons to open modal for update
            echo "<td><button class='updateBtn' data-id='".$row['ID']."'>Update</button></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo  "<center><font color='red'>Empty User List!</font></center>";
    } 
}

if (isset($_POST['remove_use_account'])) {
    $username = sanitize_input($_POST['ID']);
    $checkusername = "SELECT * FROM amendment WHERE ID = '$username'";
    $run_check = mysqli_query($conn, $checkusername) or die(mysqli_error($conn));
    $countusername = mysqli_num_rows($run_check); 
    if ($countusername > 0) {
        // User exists, proceed with deletion
        $deleteQuery = "DELETE FROM amendment WHERE ID = '$username'";
        $result = mysqli_query($conn, $deleteQuery);
        if ($result) {
            echo "<center><font color='green'>User deleted successfully!</font></center>";
        } else {
            echo "<center><font color='red'>Failed to delete user!</font></center>";
        }
    } else {
        echo "<center><font color='blue'>Unable to find User!</font></center>";
    }
}

if (isset($_POST['update_user'])) {
    
    $userId = sanitize_input($_POST['userId']);
    $fullname = sanitize_input($_POST['fullname']);
    $name_with_initial = sanitize_input($_POST['name_with_initial']);

    $pAddress = sanitize_input($_POST['text03']);
    $cOfbirth = sanitize_input($_POST['text04']);
    $natinal = sanitize_input($_POST['text05']);
    $gender = sanitize_input($_POST['rbt1']);
    $DofB = sanitize_input($_POST['dOFb']);
    // Update other fields similarly
    
    $sql = "UPDATE amendment SET Full_Name = '$fullname', Name_with_Initial = '$name_with_initial', Permenent_Address = '$pAddress', Country_of_Birth = '$cOfbirth', Nationality = '$natinal', Sex = '$gender', Date_of_Birth = '$DofB' WHERE ID = $userId";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "<script>alert('User details updated successfully!');</script>";
        echo "<meta http-equiv='refresh' content='0'>"; // Refresh page to show updated details
    } else {
        echo "<script>alert('Failed to update user details!');</script>";
    }
}
?>

<br>

<div class="container">

    <p class="login-card"><strong><h3> Show All User Details</h3></strong></p> 
        <div class="form">
            <form id="contactform" method="POST"> 
                <input class="Rbuttom" name="show_all_info" id="submit" tabindex="5" value="Show All Account Details!" type="submit">    
            </form> 
        </div>
    <br>
    <p class="login-card"><strong><h3> Delete user Details</h3></strong></p> 
    <div class="form">
        <form id="contactform" method="POST"> 

            <p class="contact"><label for="Full Name"></label></p> 
            <input class="buttom" id="ID" name="ID" placeholder="Enter ID number" required="" tabindex="1" type="text"> 
            <input class="dbutton" name="remove_use_account" id="submit" tabindex="5" value="Delete" type="submit">
            <br>
        </form> 
    </div> 

    <br>
    <hr style="border-color: ; border-width: 2px; border-style: solid; margin: 20px auto; width: 100%; font-weight: bold;">
    
    <p><strong>click to go to form</strong></p>
    <a href = "../NIC-issuing-/HTML/Form_Amendment_of_NIC.html" ><button>Back</button></a>
    
</div>

<!-- Modal for updating user details -->
<div id="updateModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Update User Details</h2>
    <form id="updateForm" method="POST">
    <input type="hidden" id="userId" name="userId">
        <label for="Full Name">Full Name:</label>
        <input type="text" id="updated_fullname" name="fullname"><br><br>
        <label for="Name with Initial">Name with Initial:</label>
        <input type="text" id="updated_name_with_initial" name="name_with_initial"><br><br>

        <label for="Name with Initial">Permenent Address:</label>
        <input type="text" id="text03" name="text03"><br><br>

        <label for="Name with Initial">Country of Birth:</label>
        <input type="text" id="text04" name="text04"><br><br>

        <label for="Name with Initial">Nationality:</label>
        <input type="text" id="text05" name="text05"><br><br>

        <a class="f1">Sex: <input type="radio" name = "rbt1" value="male">male <input type="radio" name = "rbt1" id="rbt1" value="female">female</a><br><br>

        <a class="f1">Date of Birth: <input class="bdate" type="date" id="#" name="dOFb"></a><br><br>

        <!-- Add more fields as required -->
        <input type="submit" class="sbt" name="update_user" value="Update">
    </form>
  </div>
</div>

<style>
    body {
        background-image: url("1157b6fd85f1ff2.gif");
        background-color: #aaabbbbb;
    }

    td,
    th {
        border: 3px solid white;
    }

    td,
    th {
        border: 1.5px solid black;
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
        background-color: rgb(0,0,0); /* Fallback color */
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

    .Rbuttom{
        height: 4vh;
        border-radius: 1vw;
        cursor: pointer;
        background-color: #2aaEEEEE;
    }

    .buttom{
        height: 4vh;
        border-radius: 1vw;
    }

    .dbutton{
        height: 3.5vh;
        border-radius: 0.5vw;
        cursor: pointer;
    }

    .sbt{
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

// When the user clicks on the button, open the modal
for (var i = 0; i < btns.length; i++) {
    btns[i].onclick = function() {
        var userId = this.getAttribute("data-id");
        var fullname = this.parentElement.parentElement.cells[1].innerText;
        var nameWithInitial = this.parentElement.parentElement.cells[2].innerText;

        var Permenent_Address = this.parentElement.parentElement.cells[3].innerText;
        var Country_of_Birth = this.parentElement.parentElement.cells[4].innerText;
        var Nationality = this.parentElement.parentElement.cells[5].innerText;

        document.getElementById("userId").value = userId;
        document.getElementById("updated_fullname").value = fullname;
        document.getElementById("updated_name_with_initial").value = nameWithInitial;

        document.getElementById("text03").value = Permenent_Address;
        document.getElementById("text04").value = Country_of_Birth;
        document.getElementById("text05").value = Nationality;

        modal.style.display = "block";
    }
}

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