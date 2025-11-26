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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

</head>
<style>
    #pdf-viewer {
        width: 100%;
        height: 600px;
        overflow: auto;
        border: 1px solid #ccc;
    }

    canvas {
        display: block;
        margin: 10px auto;
    }

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
        <h1 class="text-center mt-3">คู่มือการสมัคร</h1>
        <div id="pdf-viewer"></div>
        <script>
            var url = 'image/คู่มือ.pdf';  // URL ของไฟล์ PDF ที่ต้องการแสดง

            var pdfjsLib = window['pdfjsLib'];
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

            // โหลด PDF ไฟล์
            var loadingTask = pdfjsLib.getDocument(url);
            loadingTask.promise.then(function (pdf) {
                var totalPages = pdf.numPages; // เก็บจำนวนหน้าทั้งหมดของ PDF

                // ลูปเรนเดอร์แต่ละหน้า
                for (var pageNumber = 1; pageNumber <= totalPages; pageNumber++) {
                    renderPage(pageNumber, pdf);
                }

                function renderPage(pageNum, pdf) {
                    pdf.getPage(pageNum).then(function (page) {
                        var scale = 1;
                        var viewport = page.getViewport({ scale: scale });

                        // สร้าง canvas สำหรับหน้า PDF แต่ละหน้า
                        var canvas = document.createElement('canvas');
                        var context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        // ใส่ canvas ไว้ใน div
                        document.getElementById('pdf-viewer').appendChild(canvas);

                        // เรนเดอร์ PDF ลงใน canvas
                        var renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
                        page.render(renderContext);
                    });
                }
            });
        </script>
    </main>

    <footer>
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>