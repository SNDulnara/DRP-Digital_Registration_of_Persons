<?php

require 'Connection.php';


$fName = $_POST["text01"];//submit Full name
$nwInitial = $_POST["text02"];//submit name with initial
$pAddress = $_POST["text03"];//submit permenet address
$cOfbirth = $_POST["text04"];//country of address
$natinal = $_POST["text05"];//submit Nationality
$gender = $_POST["rbt1"];//submit sex
$DofB = $_POST["dOFb"];//submit Date of Birth
$dAcknow = isset($_POST['cbox01']) ? 1 : 0; //acknowledgment checkbox

if(isset($_POST['submit'])){

    // Check for duplicate entries before inserting
    $query = "SELECT * FROM first_time WHERE Full_Name = '$fName'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // If a record with the same full name already exists
        echo "<script> alert('This entry already exists.'); window.location = 'some_page.html'; </script>";
        exit; // Exit script to prevent further execution
    } 
    else {
        // If no matching record found, proceed with inserting the new record


        $imgData1 = addslashes(file_get_contents($_FILES['file01']['tmp_name']));
        $imageType = $_FILES['file01']['type'];

        $imgData2 = addslashes(file_get_contents($_FILES['file02']['tmp_name']));
        $imageType = $_FILES['file02']['type'];

        $imgData3 = addslashes(file_get_contents($_FILES['file03']['tmp_name']));
        $imageType = $_FILES['file03']['type'];

        $imgData4 = addslashes(file_get_contents($_FILES['file04']['tmp_name']));
        $imageType = $_FILES['file04']['type'];

        $imgData5 = addslashes(file_get_contents($_FILES['file05']['tmp_name']));
        $imageType = $_FILES['file05']['type'];

    }   

    $sql = "INSERT INTO amendment(Full_Name, Name_with_Initial, Permenent_Address, Country_of_Birth, Nationality, Sex, Date_of_Birth, Imag, Birth_Certificate, Previously_used_NIC, Recident_verfication_certificate, Signature, Acknowledgment) VALUES ('$fName', '$nwInitial', '$pAddress', '$cOfbirth', '$natinal', '$gender', '$DofB', '$imgData1', '$imgData2', '$imgData3', '$imgData4', '$imgData5', '$dAcknow')";

    if ($conn->query($sql)){
        header("Location: ../NIC-issuing-/HTML/Bank_Payment.html? message = success");
    }

    else{
        echo "<script> alert('Error: ". $conn->error. "'); window.location = '../NIC-issuing-/HTML/Form_Amendment_of_NIC.html';</script>";
    }

    $conn->close();

}    

?>     
