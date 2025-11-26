<?php
function Navbar()
{ ?>
    <style>
        /* กำหนดสีหลักของวิทยาลัย (ตัวอย่าง: สีน้ำเงิน MTC) */
        :root {
            --mtc-primary: #0d6efd;
            /* ปรับสีตาม logo วิทยาลัยได้เลย */
            --mtc-hover: #0b5ed7;
        }

        .navbar-mtc {
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            /* เงาให้ดูมีมิติ */
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
        }

        /* ปรับแต่ง Logo และชื่อ */
        .brand-text h5 {
            color: var(--mtc-primary);
            font-weight: 800;
            margin-bottom: 0;
            line-height: 1.2;
        }

        .brand-text small {
            color: #6c757d;
            font-size: 0.85rem;
        }

        /* ปรับแต่งลิงก์เมนู */
        .nav-link {
            color: #333 !important;
            font-weight: 500;
            margin: 0 5px;
            position: relative;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: var(--mtc-primary) !important;
        }

        /* ปุ่ม CTA (Call to Action) */
        .btn-login {
            background-color: var(--mtc-primary);
            color: white !important;
            border-radius: 50px;
            /* ปุ่มมน */
            padding: 8px 25px !important;
            box-shadow: 0 4px 6px rgba(13, 110, 253, 0.3);
        }

        .btn-login:hover {
            background-color: var(--mtc-hover);
            color: white !important;
            transform: translateY(-2px);
            /* ขยับขึ้นเล็กน้อยเมื่อเอาเมาส์ชี้ */
        }

        .btn-status {
            border: 2px solid var(--mtc-primary);
            color: var(--mtc-primary) !important;
            border-radius: 50px;
            padding: 6px 20px !important;
        }

        .btn-status:hover {
            background-color: var(--mtc-primary);
            color: white !important;
        }
    </style>

    <nav class="navbar navbar-expand-lg navbar-mtc sticky-top">
        <div class="container">

            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="image/logo/logo.png" alt="MTC Logo" width="55" height="55"
                    class="d-inline-block align-text-top me-3">
                <div class="brand-text d-flex flex-column">
                    <h5 style="font-size: 16px;">ระบบรับสมัครนักเรียน นักศึกษาใหม่</h5>
                    <small>วิทยาลัยเทคนิคแม่สอด (ระดับ ปวช. / ปวส.)</small>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMTC"
                aria-controls="navbarMTC" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMTC">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">

                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="example.php"><i class="bi bi-book"></i> คู่มือการสมัคร</a>
                    </li>

                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="nav-link btn-status" href="checkStatus.php">
                            <i class="bi bi-search"></i> ตรวจสอบสถานะ
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="nav-link btn-login" href="login.php">
                            <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

<?php } ?>

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


        .main-content {
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: var(--bs-primary);
            font-weight: bold;
            border-bottom: 3px solid var(--bs-primary);
        }

        .btn-action {
            width: 50px;
            height: 35px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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