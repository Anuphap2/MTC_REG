<?php
include 'components/navbar.php';
include 'components/head.php';
include 'components/footer.php';
require_once 'config/db.php';
include 'components/sessionShow.php';
unset($_SESSION['user_log']);
$conn = connectDB();
$now = date("Y-m-d");
$sql = "SELECT * FROM round WHERE :now BETWEEN date_round AND end_round"; 
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        /* Hero Section Styles */
        .hero-logo {
            transition: transform 0.3s ease;
        }
        .hero-logo:hover {
            transform: scale(1.05);
        }
        
        .welcome-text h1 {
            font-weight: 700;
            color: #2c3e50;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .btn-action {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        /* Image Banner Styles */
        .banner-wrapper {
            background: white;
            padding: 10px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 0;
            padding-top: 40%; /* ปรับสัดส่วนให้กว้างขึ้นเล็กน้อยเพื่อให้ดู Cinematic */
            position: relative;
            background-image: <?= $backgroundImage ?>;
            background-size: cover; /* เปลี่ยนเป็น cover เพื่อให้ภาพเต็มพื้นที่สวยงาม */
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 10px;
            background-color: #eee; /* สีพื้นหลังกรณีโหลดภาพไม่ทัน */
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .image-container { padding-top: 56.25%; /* 16:9 aspect ratio */ }
        }
        @media (max-width: 768px) {
            .image-container { padding-top: 75%; /* 4:3 aspect ratio มือถือ */ }
            .welcome-text h1 { font-size: 1.5rem; }
        }
    </style>
</head>

<body>
    <?php showStatus(); ?>
    
    <header class="sticky-top shadow-sm">
        <?php Navbar(); ?>
    </header>
    <main class="container py-5">
        
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                
                <div class="mb-4">
                    <img src="image/logo/logo.png" alt="MTC Logo" width="180" class="hero-logo align-text-top">
                </div>

                <div class="welcome-text mb-4">
                    <h5 class="text-muted text-uppercase ls-1">วิทยาลัยเทคนิคแม่สอด</h5>
                    <h1 class="mb-3">ระบบรับสมัครนักเรียน นักศึกษาใหม่</h1>
                    
                    <?php if($round): ?>
                        <div class="badge bg-success bg-opacity-10 text-success px-4 py-2 rounded-pill fs-6 border border-success">
                            <i class="bi bi-megaphone-fill me-2"></i> กำลังเปิดรับสมัคร: <?= $round['name_round'] ?>
                        </div>
                    <?php else: ?>
                        <div class="badge bg-secondary bg-opacity-10 text-secondary px-4 py-2 rounded-pill fs-6">
                            ยังไม่มีรอบการเปิดรับสมัครในขณะนี้
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
                    <a href="login.php" class="btn btn-primary btn-lg btn-action">
                        <i class="bi bi-box-arrow-in-right me-2"></i> เข้าสู่ระบบสมัครเรียน
                    </a>
                    <a href="example.php" class="btn btn-outline-info btn-lg btn-action text-dark border-2">
                        <i class="bi bi-book-half me-2"></i> คู่มือการสมัคร
                    </a>
                </div>

            </div>
        </div>

        <?php if ($image): ?>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="banner-wrapper">
                    <div class="image-container"></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </main>

    <footer class="mt-auto">
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script> </body>

</html>