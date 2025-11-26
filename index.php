<?php
include 'components/navbar.php';
include 'components/head.php';
include 'components/footer.php';
require_once 'config/db.php';
include 'components/sessionShow.php';
unset($_SESSION['user_log']);
$conn = connectDB();
$now = date("Y-m-d");
$sql = "SELECT * FROM round WHERE :now BETWEEN date_round AND end_round"; #curdate() 
$stmt = $conn->prepare($sql);
$stmt->bindParam(':now', $now);
$stmt->execute();
$round = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM images ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$image = $stmt->fetch(PDO::FETCH_ASSOC);

$backgroundImage = '';
if ($image) {
    $backgroundImage = "url('admin/uploads/" . $image['filename'] . "')";
}
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head(); ?>

</head>
<style>
    .image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 0;
        padding-top: 46.15%;
        /* 1300px / 600px = 2.1667, 100 / 2.1667 = 46.15% */
        position: relative;
        background-image:
            <?= $backgroundImage ?>
        ;
        background-size: contain;
        /* ปรับขนาดภาพให้พอดีกับ container */
        background-position: center;
        background-repeat: no-repeat;
        /* เพิ่มสีพื้นหลังเพื่อดูภาพได้ชัด */
    }

    @media (max-width: 1300px) {
        .image-container {
            padding-top: 46.15%;
            /* คงไว้เพื่อรักษาสัดส่วน 16:9 */
        }
    }

    @media (max-width: 768px) {
        .image-container {
            padding-top: 75%;
            /* ปรับให้เหมาะสมกับขนาดหน้าจอเล็ก */
        }
    }
</style>

<body>
    <!-- header start  -->
    <?php showStatus(); ?>
    <header>
        <?php Navbar(); ?>
    </header>
    <!-- header end  -->
    <main class="container">
        <div class="d-flex justify-content-center mt-3">
            <img src="image/logo/logo.png" alt="Logo" width="250" height="250" class="align-text-top">
        </div>
        <h1 class="text-center mt-3">ยินดีต้อนรับเข้าสู่ระบบสมัครเรียนออนไลน์</h1>
        <div class="d-flex justify-content-center">
            <a href="login.php" class="btn btn-lg me-3 btn-outline-primary mb-3">เข้าสู่ระบบสมัครเรียน</a>
            <a href="example.php" class="btn btn-lg btn-outline-info mb-3">คู่มือการสมัคร</a>
        </div>
        <div class="image-container">
            <!-- ไม่มี <img> ที่นี่ -->
        </div>

       
    </main>

    <footer>
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>