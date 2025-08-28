<?php
require '../Connection.php';

$sql = "SELECT Imag FROM first_time";
$result = $con->query($sql);

echo "<html><body>";
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $path = htmlspecialchars($row['Imag']); 
        echo '<img src="'. $path .'" style="max-width:200px; margin:10px;" />';
    }
} else {
    echo "No images found.";
}
echo "</body></html>";

$con->close();
?>
