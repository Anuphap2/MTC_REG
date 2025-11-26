<?php
// 1. เริ่ม Session และเชื่อมต่อ DB ก่อนทำอย่างอื่น
require_once '../config/db.php';
$conn = connectDB();

// 2. ตรวจสอบสิทธิ์ Admin (ย้ายมาบนสุด)
if (isset($_SESSION['admin_log'])) {
    $username1 = $_SESSION['admin_log'];
    $sql = "SELECT status FROM admin WHERE username = :username"; // เลือกแค่ status ก็พอ
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":username", $username1);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้งาน';
        header('location: login.php'); // แก้เป็น login.php หรือ index.php ตามระบบคุณ
        exit;
    }
} else {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('location: index.php');
    exit;
}

// 3. จัดการ Logic การลบข้อมูล (ต้องอยู่ก่อน HTML)
if (isset($_GET['id_reg'])) {
    $id_reg = $_GET['id_reg'];
    $sql_delete = "UPDATE u_reg SET status = '' , class_assign = '' , round_assign = '' , depart_major = '' , depart_minor = '' WHERE id_reg = :id_reg";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id_reg', $id_reg);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลผู้สมัครเรียบร้อยแล้ว";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    }
    // Redirect กลับมาที่เดิมเพื่อเคลียร์ URL
    $redirect_round = isset($_GET['round']) ? "?round=" . $_GET['round'] : "";
    header("location: dataStudent.php" . $redirect_round);
    exit;
}

if (isset($_GET['deleteAll'])) { // แก้คำผิด deteteAll -> deleteAll
    $deleteAll = $_GET['deleteAll'];
    $sql_delete = "DELETE FROM u_reg WHERE round_assign = :deleteAll";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':deleteAll', $deleteAll);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลผู้สมัครรอบนี้เรียบร้อยแล้ว";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    }
    header("location: dataStudent.php?round=" . $deleteAll);
    exit;
}

// 4. เตรียมข้อมูลรอบ (Round) ก่อนเริ่ม HTML
$round_id = null;
$round_name = "";

if (isset($_GET['round'])) {
    $round_param = $_GET['round'];
    $sql = "SELECT * FROM round WHERE id_round = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $round_param);
    $stmt->execute();
    $r_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r_data) {
        $round_name = $r_data['name_round'];
        $round_id = $r_data['id_round'];
    }
} else {
    // ถ้าไม่เลือกรอบ ให้ดึงรอบล่าสุดมาแสดง
    $sql = "SELECT * FROM round ORDER BY id_round DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $r_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r_data) {
        $round_name = $r_data['name_round'];
        $round_id = $r_data['id_round'];
        // Optional: ถ้าอยากให้ URL สวยๆ อาจจะไม่ต้อง Redirect ก็ได้ แต่ถ้าจะ Redirect ให้เปิดบรรทัดล่าง
        // header("location: dataStudent.php?round=$round_id"); exit;
    }
}

// --- เริ่มส่วน HTML ---
include '../components/head.php';
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head_admin(); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <style>
        /* CSS Responsive Sidebar */
        @media (min-width: 768px) {
            .sidebar {
                width: 250px;
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 100;
                visibility: visible !important;
                transform: none !important;
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
</head>

<body>
    <?php include '../components/sessionShow.php'; // ย้ายมาแสดงใน body ?>
    <?php showStatus_admin(); ?>

    <div class="d-md-none p-3 bg-light border-bottom d-flex justify-content-between align-items-center sticky-top">
        <span class="fw-bold">ระบบรับสมัคร</span>
        <button class="btn btn-primary btn-toggle-sidebar" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="bi bi-list"></i> เมนู
        </button>
    </div>

    <div class="d-flex">

        <nav class="sidebar bg-primary offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu"
            aria-labelledby="sidebarMenuLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-white" id="sidebarMenuLabel">เมนูหลัก</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    data-bs-target="#sidebarMenu" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <ul class="nav flex-column w-100 mt-md-4 mt-0">
                    <li class="nav-item">
                        <a class="nav-link" href="main.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dataStudent.php">ข้อมูลผู้สมัคร</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="addSubject.php">เพิ่มข้อมูลสาขาวิชา</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="addRound.php">จัดการรอบเปิดรับสมัคร</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="addNews.php">จัดการข่าวสาร</a>
                    </li>
                    <?php if (isset($result['status']) && $result['status'] == 1) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="addUser.php">เพิ่มเจ้าหน้าที่</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item mt-auto">
                        <a href="system/logout.php" class="nav-link text-danger bg-light">ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main-content">
            <div class="container-fluid">
                <div class="row gx-5">
                    <div class="col-12">

                        <h2 class="mb-4 mt-2">ข้อมูลผู้สมัคร <span class="text-primary"><?= $round_name ?></span></h2>

                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ดูผู้สมัครแต่ละรอบ</label>
                                        <select class="form-select" id="roundSelect">
                                            <option selected disabled value="">เลือกรอบการสมัคร</option>
                                            <?php
                                            $sql_r = "SELECT * FROM round ORDER BY id_round DESC";
                                            $stmt_r = $conn->prepare($sql_r);
                                            $stmt_r->execute();
                                            $rounds = $stmt_r->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($rounds as $d) {
                                                $selected = ($d['id_round'] == $round_id) ? 'selected' : '';
                                                echo '<option value="' . $d['id_round'] . '" ' . $selected . '>' . $d['name_round'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ปริ้นใบสรุปผล (ตามสาขา)</label>
                                        <select class="form-select" id="exportSelect">
                                            <option selected disabled value="">เลือกสาขาวิชา</option>
                                            <?php
                                            // Query สาขาที่มีในรอบนั้นๆ
                                            $sql_dep = "SELECT * FROM department
                                                        INNER JOIN reg_show ON department.id_depart = reg_show.depart_ref
                                                        WHERE reg_show.round_ref = :round";
                                            $stmt_dep = $conn->prepare($sql_dep);
                                            $stmt_dep->bindParam(":round", $round_id);
                                            $stmt_dep->execute();
                                            $deps = $stmt_dep->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($deps as $d) {
                                                echo '<option value="' . $d['id_depart'] . '">' . $d['name_depart'] . ' (' . $d['class'] . ')</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <a href="export_excel.php?round=<?= $round_id ?>"
                                            class="btn btn-success w-100 w-md-auto">
                                            <i class="bi bi-file-earmark-excel"></i> Export Excel ข้อมูลรอบนี้ทั้งหมด
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <?php
                        $students = [];
                        if ($round_id) {
                            $sql_main = "SELECT user.*, depart.name_depart, depart.class,depart.level, major.name_major, round.name_round
                                         FROM u_reg AS user
                                         LEFT JOIN department AS depart ON user.depart_major = depart.id_depart
                                         LEFT JOIN round AS round ON user.round_assign = round.id_round
                                         LEFT JOIN major_depart AS major ON depart.id_major_ref = major.id_major
                                         WHERE user.round_assign = :round
                                         AND user.fname != '' AND user.lname != ''";
                            $stmt_main = $conn->prepare($sql_main);
                            $stmt_main->bindParam(":round", $round_id);
                            $stmt_main->execute();
                            $students = $stmt_main->fetchAll(PDO::FETCH_ASSOC);
                        }

                        $count = count($students); // นับจาก Array เลย ไม่ต้อง Query ใหม่
                        ?>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">จำนวนผู้สมัครทั้งหมด <span class="badge bg-primary"><?= $count ?></span> คน
                            </h5>
                            <?php if ($result['status'] == 1 && $count > 0) { ?>
                                <button class="btn btn-danger btn-sm" onclick="ClearAllData('<?= $round_id ?>')">
                                    <i class="bi bi-trash"></i> ลบข้อมูลรอบนี้ทั้งหมด
                                </button>
                            <?php } ?>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="departmentsTable" class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th>แผนก</th>
                                                <th>สาขา</th>
                                                <th>ระดับ</th>
                                                <th>สถาบันเดิม</th>
                                                <th>วุฒิ</th>
                                                <th class="text-center">สถานะ</th>
                                                <th class="text-center">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($students as $student) { ?>
                                                <tr>
                                                    <td><?= $student['prefix'] . $student['fname'] . ' ' . $student['lname']; ?>
                                                    </td>
                                                    <td><?= $student['name_major']; ?></td>
                                                    <td><?= $student['name_depart']; ?></td>
                                                    <td><?= $student['level']; ?></td>
                                                    <td><?= $student['lasted_school']; ?></td>
                                                    <td><?= $student['lasted_class']; ?></td>

                                                    <td class="text-center">
                                                        <?php if ($student['status'] == 0) { ?>
                                                            <span class="badge bg-danger">ยังไม่ตรวจ</span>
                                                        <?php } else if ($student['status'] == 1) { ?>
                                                                <span class="badge bg-success">ผ่านเกณฑ์</span>
                                                        <?php } else if ($student['status'] == 2) { ?>
                                                                    <span class="badge bg-secondary">ไม่ผ่าน</span>
                                                        <?php } ?>
                                                    </td>

                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <a href="allData.php?user=<?= $student['id_card']; ?>&round=<?= $round_id; ?>"
                                                                class="btn btn-primary btn-sm" title="ดูข้อมูล">ดูข้อมูล</a>
                                                            <a href="edit.php?user=<?= $student['id_card']; ?>"
                                                                class="btn btn-warning btn-sm" title="แก้ไข">แก้ไข</a>
                                                            <a target="_blank"
                                                                href="../print.php?id_card=<?= $student['id_card']; ?>"
                                                                class="btn btn-info btn-sm text-white"
                                                                title="พิมพ์">พิมพ์</a>
                                                            <button class="btn btn-danger btn-sm"
                                                                onclick="confirmDeletion('<?= $student['id_reg']; ?>')"
                                                                title="ลบ">ลบ</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <?php include '../components/footer.php'; ?>
    </footer>

    <script src="../css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Dropdown Logic
        document.getElementById('roundSelect').addEventListener('change', function () {
            if (this.value) window.location.href = '?round=' + this.value;
        });

        document.getElementById('exportSelect').addEventListener('change', function () {
            if (this.value) window.open('export.php?round=<?= $round_id ?>&depart=' + this.value, '_blank');
        });

        // SweetAlert Logic
        function confirmDeletion(id_reg) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "ข้อมูลผู้สมัครคนนี้จะถูกลบและกู้คืนไม่ได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่ง round ไปด้วยเพื่อให้อยู่หน้าเดิม
                    window.location.href = '?id_reg=' + id_reg + '&round=<?= $round_id ?>';
                }
            })
        }

        function ClearAllData(round_id) {
            Swal.fire({
                title: 'ยืนยันลบข้อมูลทั้งรอบ?',
                text: "ข้อมูลผู้สมัครทั้งหมดในรอบนี้จะหายไป!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ยืนยันลบทั้งหมด',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?deleteAll=' + round_id;
                }
            })
        }

        // DataTables
        $(document).ready(function () {
            $('#departmentsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json"
                },
                "order": [[0, "asc"]] // เรียงตามชื่อ
            });
        });
    </script>
</body>

</html>