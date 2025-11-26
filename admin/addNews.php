<?php
include '../components/navbar.php';
include '../components/head.php';
include '../components/footer.php';
include '../components/sessionShow.php';
include '../config/db.php';
$conn = connectDB();

if (isset($_SESSION['admin_log'])) {
    $username1 = $_SESSION['admin_log'];
    $sql = "SELECT * FROM admin WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":username", $username1);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('location: index.php');
    exit;
}

// การลบภาพ
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT filename FROM images WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $filename = $stmt->fetchColumn();
    
    if ($filename) {
        unlink('uploads/' . $filename); // ลบไฟล์จากเซิร์ฟเวอร์
        $stmt = $conn->prepare("DELETE FROM images WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['message'] = "Image deleted.";
        header('Location: ' . $_SERVER['PHP_SELF']); // รีเฟรชหน้าเพื่อแสดงข้อความ
        exit;
    }
}

// การอัพโหลดรูปภาพ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($file['name']);

    // ตรวจสอบการอัพโหลดไฟล์
    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        $stmt = $conn->prepare("INSERT INTO images (filename) VALUES (:filename)");
        $stmt->execute(['filename' => $file['name']]);
        $_SESSION['message'] = "Image uploaded successfully.";
        header('Location: ' . $_SERVER['PHP_SELF']); // รีเฟรชหน้าเพื่อแสดงข้อมูลใหม่
        exit;
    } else {
        $_SESSION['message'] = "Failed to upload file.";
    }
}

$stmt = $conn->query("SELECT * FROM images ORDER BY upload_time DESC");
$images = $stmt->fetchAll();
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head_admin(); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }

        .main-content {
            width: 100%;
            margin-left: 250px;
            /* ความกว้างของ sidebar */
            padding: 20px;
        }

        table.dataTable thead th {
            text-align: center;
        }

        .alert {
            color: green;
        }
    </style>
</head>

<body>
    <?php showStatus_admin(); ?>
    <div class="d-flex">
        <?php Navbar_admin(); ?>

        <div class="main-content">
            <h1>Image Management</h1>

            <!-- ฟอร์มอัพโหลดรูปภาพ -->
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="image" class="form-label">Upload Image</label>
                    <input type="file" name="image" id="image" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert">
                    <?= htmlspecialchars($_SESSION['message']) ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <table id="majorTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Upload Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($images as $image): ?>
                        <tr>
                            <td><img src="uploads/<?= htmlspecialchars($image['filename']) ?>" width="200"></td>
                            <td><?= htmlspecialchars($image['upload_time']) ?></td>
                            <td>
                                <a href="?delete=<?= $image['id'] ?>" onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#majorTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json"
                }
            });
        });
    </script>
</body>

</html>
