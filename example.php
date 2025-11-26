<?php
include 'components/navbar.php';
include 'components/head.php';
include 'components/footer.php';
require_once 'config/db.php';
include 'components/sessionShow.php';

// ลบโค้ด SQL ดึงรูปภาพ Background ออก เพราะหน้านี้ไม่ได้ใช้
// ลบ session user_log ออกเพื่อให้เหมือนหน้า index (ถ้าต้องการ)
unset($_SESSION['user_log']);
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

        .pdf-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: white;
        }

        .pdf-frame {
            width: 100%;
            height: 80vh;
            /* สูง 80% ของหน้าจอ */
            border: none;
        }

        .btn-back {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background-color: #e9ecef;
            transform: translateX(-5px);
        }
    </style>
</head>

<body>
    <?php showStatus(); ?>

    <header class="sticky-top shadow-sm">
        <?php Navbar(); ?>
    </header>

    <main class="container py-5">

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div class="d-flex align-items-center mb-3 mb-md-0">

                <div>
                    <h3 class="fw-bold mb-0 text-dark">คู่มือการสมัครเรียน</h3>
                    <p class="text-muted mb-0">ขั้นตอนและวิธีการใช้งานระบบรับสมัครออนไลน์</p>
                </div>
            </div>

            <a href="image/คู่มือ.pdf" download class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="bi bi-download me-2"></i> ดาวน์โหลดไฟล์ PDF
            </a>
        </div>

        <div class="pdf-card">
            <object data="image/คู่มือ.pdf" type="application/pdf" class="pdf-frame">
                <div class="d-flex flex-column justify-content-center align-items-center h-100 bg-light">
                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">เบราว์เซอร์ของคุณไม่รองรับการแสดงตัวอย่าง PDF</h5>
                    <p class="text-muted">กรุณาดาวน์โหลดไฟล์เพื่อเปิดอ่าน</p>
                    <a href="image/คู่มือ.pdf" class="btn btn-outline-primary">ดาวน์โหลดคู่มือ</a>
                </div>
            </object>
        </div>

    </main>

    <footer>
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>