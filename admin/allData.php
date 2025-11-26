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
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('location: index.php');
}


if (isset($_GET['id_reg'])) {
    $id_reg = $_GET['id_reg'];
    $round = $_GET['round'];
    $sql_delete = "UPDATE u_reg SET status = '' , class_assign = '' , round_assign = '' , depart_major = '' , depart_minor = '' WHERE id_reg = :id_reg";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id_reg', $id_reg);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลผู้สมัครเรียบร้อยแล้ว";
        header("location: dataStudent.php");
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
        header("location: dataStudent.php");

    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head_admin(); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <style>
        /* CSS Sidebar */
        @media (min-width: 768px) {
            .sidebar {
                width: 250px;
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 100;
                padding-top: 0;
            }

            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px);
            }

            .btn-toggle-sidebar {
                display: none;
            }
        }

        @media (max-width: 767.98px) {
            .main-content {
                width: 100%;
                margin-left: 0;
            }

            .sidebar {
                background-color: var(--bs-primary);
            }
        }

        .main-content {
            padding: 20px;
        }
    </style>
</head>

<body>
    <?php showStatus_admin(); ?>

    <div class="d-md-none p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-bold">ตรวจสอบข้อมูล</span>
        <button class="btn btn-primary btn-toggle-sidebar" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu">
            <i class="bi bi-list"></i> เมนู
        </button>
    </div>

    <div class="d-flex">

        <nav class="sidebar bg-primary offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-white">เมนูหลัก</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-0">
                <ul class="nav flex-column w-100 mt-md-5 mt-0">
                    <li class="nav-item"><a class="nav-link text-white p-3" href="main.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white p-3" href="dataStudent.php">ข้อมูลผู้สมัคร</a>
                    </li>
                    <li class="nav-item"><a class="nav-link text-white p-3"
                            href="addSubject.php">เพิ่มข้อมูลสาขาวิชา</a></li>
                    <li class="nav-item"><a class="nav-link text-white p-3"
                            href="addRound.php">จัดการรอบเปิดรับสมัคร</a></li>
                    <li class="nav-item"><a class="nav-link text-white p-3" href="addNews.php">จัดการข่าวสาร</a></li>
                    <li class="nav-item mt-auto"><a href="system/logout.php"
                            class="nav-link text-danger bg-light p-3">ออกจากระบบ</a></li>
                </ul>
            </div>
        </nav>

        <?php
        if (isset($_GET['user'])) {
            $id_card = $_GET['user'];
            $round = isset($_GET['round']) ? $_GET['round'] : ''; // กัน Error กรณีไม่มี round ส่งมา
        
            // --- แก้ไข SQL ตรงนี้ครับ (JOIN ตาราง Department เพื่อเอาชื่อสาขา) ---
            $sql = "SELECT u.*, 
                           d1.name_depart AS major_name, d1.class AS major_class,
                           d2.name_depart AS minor_name, d2.class AS minor_class
                    FROM u_reg AS u
                    LEFT JOIN department AS d1 ON u.depart_major = d1.id_depart
                    LEFT JOIN department AS d2 ON u.depart_minor = d2.id_depart
                    WHERE u.id_card = :id_card";
            // -------------------------------------------------------------
        
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_card', $id_card);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

        } else {
            $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
            echo "<script>window.location.href='dataStudent.php';</script>";
            exit;
        }
        ?>

        <div class="main-content">
            <div class="container-fluid">
                <div class="row gx-4 mt-4">

                    <div class="col-12">
                        <h1><b>ขั้นตอนที่ 1 กรอกข้อมูลส่วนตัว</b></h1>
                        <hr>
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label class="form-label">เลขบัตรประชาชน</label>
                        <input disabled style="background-color: #E7EBEB;" type="text" value="<?= $result['id_card'] ?>"
                            class="form-control">
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label class="form-label">คำนำหน้า</label>
                        <input disabled type="text" value="<?= $result['prefix'] ?>" class="form-control">
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label class="form-label">ชื่อจริง</label>
                        <input disabled type="text" value="<?= $result['fname'] ?>" class="form-control">
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label class="form-label">นามสกุล</label>
                        <input disabled type="text" value="<?= $result['lname'] ?>" class="form-control">
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label class="form-label">วันเกิด</label>
                        <input disabled type="text"
                            value="<?= $result['b_day'] ?>/<?= $result['m_day'] ?>/<?= $result['y_day'] ?>"
                            class="form-control">
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label class="form-label">อายุ</label>
                        <input disabled type="text" value="<?= $result['age'] ?>" class="form-control">
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label class="form-label">เบอร์โทร</label>
                        <input disabled type="text" value="<?= $result['tel'] ?>" class="form-control">
                    </div>


                    <div class="col-12 mt-4">
                        <h1><b>ขั้นตอนที่ 2 ข้อมูลที่อยู่</b></h1>
                        <hr>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-secondary">
                            <?= $result['h_number'] ?>
                            <?= $result['moo'] ? "หมู่ " . $result['moo'] : "" ?>
                            <?= $result['Village'] ? "หมู่บ้าน " . $result['Village'] : "" ?>
                            <?= $result['soi'] ? "ซอย " . $result['soi'] : "" ?>
                            ถนน <?= $result['road'] ?>
                            แขวง/ตำบล <?= $result['A_district'] ?>
                            เขต/อำเภอ <?= $result['S_district'] ?>
                            จังหวัด <?= $result['District'] ?>
                            <?= $result['postal_code'] ?>
                        </div>
                    </div>


                    <div class="col-12 mt-4">
                        <h1><b>ขั้นตอนที่ 3 ข้อมูลการศึกษา</b></h1>
                        <hr>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <label class="form-label">โรงเรียนเดิม</label>
                        <input disabled type="text" value="<?= $result['lasted_school'] ?>" class="form-control">
                    </div>
                    <div class="col-sm-4 mb-3">
                        <label class="form-label">วุฒิการศึกษา</label>
                        <input disabled type="text" value="<?= $result['lasted_class'] ?>" class="form-control">
                    </div>
                    <div class="col-sm-4 mb-3">
                        <label class="form-label">เกรดเฉลี่ย (GPAX)</label>
                        <input disabled type="text" value="<?= $result['lasted_gpax'] ?>" class="form-control">
                    </div>


                    <div class="col-12 mt-4">
                        <h1><b>สาขาวิชาที่สมัคร</b></h1>
                        <hr>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary fw-bold">สาขาลำดับที่ 1 (หลัก)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white"><i class="bi bi-1-circle"></i></span>
                            <input disabled type="text" class="form-control fw-bold"
                                value="<?= isset($result['major_name']) ? $result['major_name'] . ' (' . $result['major_class'] . ')' : '- ไม่พบข้อมูล -' ?>">
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">สาขาลำดับที่ 2 (สำรอง)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-2-circle"></i></span>
                            <input disabled type="text" class="form-control"
                                value="<?= isset($result['minor_name']) ? $result['minor_name'] . ' (' . $result['minor_class'] . ')' : '- ไม่ได้เลือก -' ?>">
                        </div>
                    </div>


                    <div class="col-12 mt-4">
                        <label class="form-label">หลักฐานการสมัคร (รูปภาพ/ใบเกรด)</label>
                        <div class="card">
                            <div class="card-body text-center">
                                <?php if ($result['image']): ?>
                                    <img src="../system_reg/uploads/<?= $result['image'] ?>" alt="หลักฐาน"
                                        class="img-fluid rounded shadow-sm" style="max-height: 400px;">
                                    <div class="mt-2">
                                        <a href="../system_reg/uploads/<?= $result['image'] ?>" target="_blank"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-zoom-in"></i> ดูรูปขนาดเต็ม
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted py-5">-- ยังไม่มีการอัปโหลดไฟล์ --</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-5 mb-5 d-flex justify-content-center">
                        <form action="system/reg_update.php?id_card=<?= $result['id_card'] ?>&round=<?= $round ?>"
                            method="post">
                            <div class="d-grid gap-3 d-md-flex">
                                <button type="submit" name="pass" class="btn btn-success btn-lg px-5 shadow">
                                    <i class="bi bi-check-circle"></i> ผ่านเกณฑ์
                                </button>
                                <button type="submit" name="not_pass" class="btn btn-danger btn-lg px-5 shadow">
                                    <i class="bi bi-x-circle"></i> ไม่ผ่านเกณฑ์
                                </button>
                                <a href="dataStudent.php?round=<?= $round ?>"
                                    class="btn btn-secondary btn-lg px-5 shadow">
                                    <i class="bi bi-arrow-left"></i> ย้อนกลับ
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <footer><?php Footer() ?></footer>
    <script src="../css/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>