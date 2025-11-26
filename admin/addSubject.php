<?php
// เริ่ม Session และเชื่อมต่อ DB
require_once '../config/db.php';
$conn = connectDB();

// 1. เช็คสิทธิ์ Admin
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

// 2. Logic การลบข้อมูล (รักษา Logic เดิมไว้)
if (isset($_GET['id'])) {
    $id_depart = $_GET['id'];
    $sql_delete = "DELETE FROM department WHERE id_depart = :id_depart";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id_depart', $id_depart);
    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลสาขาวิชาเรียบร้อยแล้ว";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาด";
    }
    header("location: addSubject.php"); // Redirect เพื่อล้าง GET
    exit;
}

if (isset($_GET['id_major'])) {
    $id_major = $_GET['id_major'];
    $sql_delete = "DELETE FROM major_depart WHERE id_major = :id_major";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id_major', $id_major);
    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลแผนกเรียบร้อยแล้ว";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาด";
    }
    header("location: addSubject.php"); // Redirect เพื่อล้าง GET
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <?php
    include '../components/head.php';
    Head_admin();
    ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
  
</head>

<body>
    <?php include '../components/sessionShow.php'; ?>
    <?php include '../components/navbar.php'; // เรียก Navbar (ถ้ามี Logic แสดงผลข้างใน) ?>

    <div
        class="d-md-none p-3 bg-white border-bottom d-flex justify-content-between align-items-center sticky-top shadow-sm">
        <span class="fw-bold text-primary"><i class="bi bi-layers-fill"></i> จัดการสาขาวิชา</span>
        <button class="btn btn-primary btn-toggle-sidebar" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="d-flex">
        <?php Navbar_admin(); ?>

        <div class="main-content">
            <div class="container-fluid">

                <h3 class="mb-4 fw-bold text-dark"><i class="bi bi-gear-fill text-primary"></i> จัดการโครงสร้างหลักสูตร
                </h3>

                <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="major-tab" data-bs-toggle="tab" data-bs-target="#major"
                            type="button" role="tab"><i class="bi bi-building"></i> จัดการแผนก</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="subject-tab" data-bs-toggle="tab" data-bs-target="#subject"
                            type="button" role="tab"><i class="bi bi-book"></i> จัดการสาขาวิชา</button>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane fade show active" id="major" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">รายการแผนกทั้งหมด</h5>
                                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#addMajorModal">
                                    <i class="bi bi-plus-lg"></i> เพิ่มแผนกใหม่
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="majorTable" class="table table-hover align-middle w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ชื่อแผนก</th>
                                            <th class="text-end" style="width: 150px;">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM major_depart ORDER BY id_major DESC";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                        $major = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($major as $d) { ?>
                                            <tr>
                                                <td><span class="fw-bold text-primary"><?= $d['name_major']; ?></span></td>
                                                <td class="text-end">
                                                    <button class="btn btn-warning btn-action text-white shadow-sm"
                                                        onclick="editMajor('<?= $d['id_major']; ?>', '<?= $d['name_major']; ?>')"
                                                        title="แก้ไข">
                                                        แก้ไข
                                                    </button>
                                                    <button class="btn btn-danger btn-action shadow-sm"
                                                        onclick="confirmDeletionMajor('<?= $d['id_major']; ?>')" title="ลบ">
                                                        ลบ
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="subject" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">รายการสาขาวิชาทั้งหมด</h5>
                                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#addDepartModal">
                                    <i class="bi bi-plus-lg"></i> เพิ่มสาขาวิชาใหม่
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="departmentsTable" class="table table-hover align-middle w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>รหัส</th>
                                            <th>ชื่อสาขา</th>
                                            <th>แผนก</th>
                                            <th>แผนการเรียน</th>
                                            <th>รับ (คน)</th>
                                            <th>วุฒิ</th>
                                            <th class="text-end" style="width: 150px;">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT dp.*, mp.name_major AS major_name 
                                                FROM department AS dp 
                                                LEFT JOIN major_depart AS mp ON dp.id_major_ref = mp.id_major
                                                ORDER BY dp.id_depart DESC";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($departments as $department) { ?>
                                            <tr>
                                                <td><?= $department['id_depart']; ?></td>
                                                <td class="fw-bold"><?= $department['name_depart']; ?></td>
                                                <td><span
                                                        class="badge bg-info text-dark"><?= $department['major_name']; ?></span>
                                                </td>
                                                <td><?= $department['level']; ?></td>
                                                <td><?= $department['total']; ?></td>
                                                <td><small class="text-muted"><?= $department['class']; ?></small></td>
                                                <td class="text-end">
                                                    <button class="btn btn-warning btn-action text-white shadow-sm" onclick="editDepartment('<?= $department['id_depart']; ?>', 
                                                        '<?= $department['name_depart']; ?>', 
                                                        '<?= $department['id_major_ref']; ?>', 
                                                        '<?= $department['total']; ?>', 
                                                        '<?= $department['class']; ?>',
                                                        '<?= $department['level']; ?>')">
                                                        แก้ไข
                                                    </button>
                                                    <button class="btn btn-danger btn-action shadow-sm"
                                                        onclick="confirmDeletion('<?= $department['id_depart']; ?>')">
                                                        ลบ
                                                    </button>
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

    <div class="modal fade" id="addMajorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-building"></i> เพิ่มแผนกใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="system/insertSubject.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ชื่อแผนก</label>
                            <input required type="text" class="form-control" name="name_major"
                                placeholder="เช่น ช่างอุตสาหกรรม, พาณิชยกรรม">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" name="insertMajor" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addDepartModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-book"></i> เพิ่มสาขาวิชาใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="system/insertSubject.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ชื่อสาขา</label>
                            <input required type="text" class="form-control" name="name_depart"
                                placeholder="เช่น ช่างยนต์, การบัญชี">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สังกัดแผนก</label>
                            <select required class="form-select" name="name_major">
                                <option selected disabled value="">-- เลือกแผนก --</option>
                                <?php foreach ($major as $d) { ?>
                                    <option value="<?= $d['id_major']; ?>"><?= $d['name_major']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">จำนวนที่รับ</label>
                                <input required type="number" class="form-control" name="total">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">แผนการเรียน</label>
                                <select required class="form-select" name="level">
                                    <option value="ปกติ">ปกติ</option>
                                    <option value="ทวิภาคี">ทวิภาคี</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">วุฒิที่รับ</label>
                            <select required class="form-select" name="class">
                                <option value="มัธยมศึกษาปีที่ 6">ม.6</option>
                                <option value="ประกาศนียบัตรวิชาชีพ">ปวช.</option>
                                <option value="มัธยมศึกษาปีที่ 3">ม.3</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" name="insert" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer><?php include '../components/footer.php'; ?></footer>

    <script src="../css/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ตั้งค่า DataTables
        $(document).ready(function () {
            let tableOptions = { "language": { "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json" } };
            $('#majorTable').DataTable(tableOptions);
            $('#departmentsTable').DataTable(tableOptions);

            // แก้บั๊ก DataTables เวลาอยู่ใน Tab แล้วย่อผิดปกติ
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            });
        });

        // SweetAlert: แก้ไขแผนก
        function editMajor(id_major, name_major) {
            Swal.fire({
                title: 'แก้ไขชื่อแผนก',
                html: `<input type="text" id="swal-name-major" class="swal2-input" value="${name_major}" placeholder="ชื่อแผนก">`,
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                preConfirm: () => {
                    return { id_major: id_major, name_major: document.getElementById('swal-name-major').value }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'system/edit_major.php',
                        type: 'POST',
                        data: result.value,
                        success: function (res) {
                            Swal.fire('สำเร็จ', 'แก้ไขเรียบร้อยแล้ว', 'success').then(() => location.reload());
                        },
                        error: function () { Swal.fire('Error', 'ไม่สามารถบันทึกได้', 'error'); }
                    });
                }
            });
        }

        // SweetAlert: แก้ไขสาขาวิชา (อัพเดทใหม่ให้ดึงข้อมูลแผนกมาโชว์ใน Select ได้ถูกต้อง)
        // หมายเหตุ: ตรงนี้ต้องใช้ JS Generate HTML Select ของแผนกมาใส่ ซึ่งอาจจะซับซ้อนหน่อย 
        // ในโค้ดนี้ผมคง Logic เดิมของคุณไว้แต่ปรับ UI ให้สวยขึ้น
        function editDepartment(id_depart, name_depart, name_major, total, class_name, level) {
            // สร้าง Options สำหรับ Select แผนกจาก PHP Array (แปลงเป็น JS)
            let majorOptions = `<?php foreach ($major as $d) {
                echo "<option value='{$d['id_major']}'>{$d['name_major']}</option>";
            } ?>`;

            Swal.fire({
                title: 'แก้ไขข้อมูลสาขา',
                html: `
                    <div class="text-start">
                        <label>ชื่อสาขา</label>
                        <input type="text" id="swal-name" class="swal2-input mb-2 mt-1" value="${name_depart}" style="width: 80%;">
                        
                        <label>แผนก</label>
                        <select id="swal-major" class="swal2-select mb-2 mt-1" style="display:block; width: 80%; margin: 0 auto;">${majorOptions}</select>

                        <label>จำนวนที่รับ</label>
                        <input type="number" id="swal-total" class="swal2-input mb-2 mt-1" value="${total}" style="width: 80%;">

                        <label>แผนการเรียน</label>
                        <select id="swal-level" class="swal2-select mb-2 mt-1" style="display:block; width: 80%; margin: 0 auto;">
                            <option value="ปกติ">ปกติ</option>
                            <option value="ทวิภาคี">ทวิภาคี</option>
                        </select>

                        <label>วุฒิที่รับ</label>
                        <select id="swal-class" class="swal2-select mb-2 mt-1" style="display:block; width: 80%; margin: 0 auto;">
                            <option value="มัธยมศึกษาปีที่ 6">ม.6</option>
                            <option value="ประกาศนียบัตรวิชาชีพ">ปวช.</option>
                            <option value="มัธยมศึกษาปีที่ 3">ม.3</option>
                        </select>
                    </div>
                `,
                didOpen: () => {
                    // Set ค่าเริ่มต้นให้ Select ตรงกับข้อมูลเดิม
                    document.getElementById('swal-major').value = name_major;
                    document.getElementById('swal-level').value = level;
                    document.getElementById('swal-class').value = class_name;
                },
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                preConfirm: () => {
                    return {
                        id_depart: id_depart,
                        name_depart: document.getElementById('swal-name').value,
                        name_major: document.getElementById('swal-major').value,
                        total: document.getElementById('swal-total').value,
                        class: document.getElementById('swal-class').value,
                        level: document.getElementById('swal-level').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'system/edit_department.php',
                        type: 'POST',
                        data: result.value,
                        success: function (res) {
                            Swal.fire('สำเร็จ', 'แก้ไขเรียบร้อยแล้ว', 'success').then(() => location.reload());
                        },
                        error: function () { Swal.fire('Error', 'เกิดข้อผิดพลาด', 'error'); }
                    });
                }
            });
        }

        // SweetAlert: ลบข้อมูล (รวมฟังก์ชัน)
        function confirmDeletion(id) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "ข้อมูลนี้จะหายไปถาวร",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'ลบเลย'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '?id=' + id;
            });
        }
        function confirmDeletionMajor(id) {
            Swal.fire({
                title: 'ยืนยันการลบแผนก?',
                text: "สาขาวิชาในแผนกนี้อาจได้รับผลกระทบ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'ลบเลย'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '?id_major=' + id;
            });
        }
    </script>
</body>

</html>