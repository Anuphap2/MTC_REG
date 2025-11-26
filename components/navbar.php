<?php
function Navbar()
{ ?>

    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <div class="d-flex justify-content-between">
                    <img src="image/logo/logo.png" alt="Logo" width="60" height="60" class="align-text-top">
                    <div class="mt-3 ms-3">
                        <h5 style="font-size: 12px" class="fw-bold">ระบบรับสมัครนักเรียน นักศึกษาใหม่<br>
                            <hr style="border: 2px solid blue;" class="my-0">
                            ระดับ ปวช. ปวส. วิทยาลัยเทคนิคแม่สอด
                        </h5>
                    </div>
                </div>
            </a>
        </div>
    </nav>
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg bg-mtc">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item me-5">
                        <a class="nav-link" href="index.php">หน้าแรก</a>
                    </li>
                    <li class="nav-item me-5">
                        <a class="nav-link" href="login.php">เข้าสู่ระบบสมัครเรียน</a>
                    </li>


                    <li class="nav-item me-5">
                        <a class="nav-link" href="checkStatus.php">ตรวจสอบสถานะการสมัคร</a>
                    </li>
                    <li class="nav-item me-5">
                        <a class="nav-link" href="example.php">คู่มือการสมัคร</a>
                    </li>


                </ul>

            </div>
        </div>
    </nav>

<?php }
?>
<?php function Navbar_admin()
{ ?>
    <style>
        /* CSS สำหรับ Responsive Sidebar */
        /* บนมือถือ: ซ่อน Sidebar ไว้ (ใช้ Offcanvas จัดการ) */

        /* บนหน้าจอ PC (กว้างกว่า 768px): ให้ Sidebar แสดงตลอด */
        @media (min-width: 768px) {
            .sidebar {
                width: 250px;
                min-height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 100;
                visibility: visible !important;
                transform: none !important;
                /* Reset ค่าของ offcanvas */
                padding-top: 0;
            }

            /* ขยับเนื้อหาหลักหนี Sidebar */
            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px);
            }

            /* ซ่อนปุ่ม Toggle บน PC */
            .btn-toggle-sidebar {
                display: none;
            }
        }

        /* บนมือถือ */
        @media (max-width: 767.98px) {
            .main-content {
                width: 100%;
                margin-left: 0;
            }

            /* ปรับให้ Sidebar แสดงเหนือทุกอย่างเมื่อเปิด */
            .offcanvas-md {
                background-color: var(--bs-primary);
                /* ใช้สีหลักของ Bootstrap */
            }
        }

        .nav-link {
            color: white !important;
            padding: 15px 20px;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            padding: 20px;
            transition: margin-left 0.3s;
        }
    </style>
    <button class="btn btn-primary d-md-none mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"
        aria-controls="sidebarMenu">
        <i class="bi bi-list"></i> เมนูหลัก
    </button>

    <nav class="sidebar bg-primary offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu"
        aria-labelledby="sidebarMenuLabel">

        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-white" id="sidebarMenuLabel">เมนูระบบ</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-0">
            <ul class="nav flex-column w-100">
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="main.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="dataStudent.php">ข้อมูลผู้สมัคร</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="addSubject.php">เพิ่มข้อมูลสาขาวิชา</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="addRound.php">จัดการรอบเปิดรับสมัคร</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="addNews.php">จัดการข่าวสาร</a>
                </li>

                <?php
                if (isset($_SESSION['admin_log'])) {
                    require_once '../config/db.php';
                    $conn = connectDB();
                    $username1 = $_SESSION['admin_log'];
                    $sql = "SELECT status FROM admin WHERE username = :username";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":username", $username1);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($result && $result['status'] == 1) { ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="addUser.php">เพิ่มเจ้าหน้าที่</a>
                        </li>
                    <?php }
                }
                ?>

                <li class="nav-item mt-3 border-top border-light pt-2">
                    <a href="system/logout.php" class="nav-link text-white opacity-75">
                        <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                    </a>
                </li>
            </ul>
        </div>
    </nav>

<?php }
?>