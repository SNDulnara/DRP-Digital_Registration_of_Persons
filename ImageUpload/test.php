<?php
require '../Connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        
        $fileTmp  = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $fileSize = $_FILES['image']['size'];
        $fileType = mime_content_type($fileTmp);

        // ✅ Allowed file types
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($fileType, $allowedTypes)) {
            die("❌ Only JPG, PNG, and GIF allowed.");
        }

        // ✅ Limit file size (2MB)
        if ($fileSize > 2 * 1024 * 1024) {
            die("❌ File too large. Max 2MB allowed.");
        }

        // ✅ Create upload folder if not exists
        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // ✅ Generate unique file name
        $newFileName = uniqid("img_", true) . "." . pathinfo($fileName, PATHINFO_EXTENSION);
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmp, $destination)) {
            // Save relative path in DB
            $dbPath = "uploads/" . $newFileName;

            $stmt = $con->prepare("INSERT INTO first_time (Imag) VALUES (?)");
            $stmt->bind_param("s", $dbPath);

            if ($stmt->execute()) {
                header("Location: image_view.php?message=success");
                exit;
            } else {
                echo "❌ Database Error.";
            }
            $stmt->close();
        } else {
            echo "❌ Upload failed.";
        }
    } else {
        echo "❌ No file uploaded.";
    }
}
$con->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Upload</title>
</head>
<body>
    <div class="cc">
        <h1>Image Upload</h1>
        <h3>Choose an image and click upload</h3>

        <form action="test.php" method="post" enctype="multipart/form-data">
            <input type="file" name="image" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
