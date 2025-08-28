<?php
require 'Connection.php';
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve username and password from POST data
$username = isset($_POST['txt1']) ? $_POST['txt1'] : '';
$password = isset($_POST['psd1']) ? $_POST['psd1'] : '';

// Admin credentials
$adminUsername = 'DRPadmin';
$adminPassword = '@12345';

// IT Supporter credentials
$itSupportUsername = 'DRPIT';
$itSupportPassword = 'shamika';

$message = "";
$location = "";

// Check if the user is admin
if ($username === $adminUsername && $password === $adminPassword) {
    $_SESSION['isAdmin'] = true;
    $_SESSION['User_Name'] = $username;
    $_SESSION['login_success'] = "Login successful! Welcome Admin.";
    header("Location: ../NIC-issuing-/HTML/Home_Admin.html");
    exit;
}

// Check if the user is IT Supporter
if ($username === $itSupportUsername && $password === $itSupportPassword) {
    $_SESSION['isAdmin'] = false; 
    $_SESSION['User_Name'] = $username;
    $_SESSION['login_success'] = "Login successful! Welcome IT Support.";
    header("Location: ../NIC-issuing-/HTML/Home_Admin.html"); 
    exit;
}

// SQL query to fetch the user data based on username
$sql = "SELECT User_Name, Password FROM users WHERE User_Name = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            $_SESSION['User_Name'] = $username;
            $_SESSION['isAdmin'] = false;
            $_SESSION['login_success'] = "Login successful! Welcome " . $username . ".";
            $_SESSION['username'] = $username;

            header("Location: ../NIC-issuing-/HTML/Home_page.html?username=" . urlencode($username));
            exit();
        } else {
            $message = "Invalid password!";
            $location = "../NIC-issuing-/HTML/Login.html";
        }
    } else {
        $message = "User not found!";
        $location = "../NIC-issuing-/HTML/Login.html";
    }

    $stmt->close();
} else {
    echo "Error preparing statement: " . htmlspecialchars($conn->error);
}

$conn->close();
?>

<html>
<head>
<?php if (!empty($message)): ?>
    <script type="text/javascript">
    window.onload = function() {
        alert('<?php echo $message; ?>');
        window.location = '<?php echo $location; ?>';
    }
    </script>
<?php endif; ?>
</head>
<body>
</body>
</html>
