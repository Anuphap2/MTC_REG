<?php
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

// 2. Logic ลบข้อมูล Round
if (isset($_GET['id'])) {
    $id_round = $_GET['id'];
    $sql_delete = "DELETE FROM round WHERE id_round = :id_round";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id_round', $id_round);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    }
    header("location: addRound.php");
    exit;
}

// 3. Logic ลบข้อมูล Reg_show
if (isset($_GET['idReg'])) {
    $id_reg = $_GET['idReg'];
    $sql_delete = "DELETE FROM reg_show WHERE id = :id";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $id_reg);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    }
    header("location: addRound.php");
    exit;
}

// เตรียมข้อมูลสำหรับ Select Option (ใช้ซ้ำหลายจุด)
$sql_r = "SELECT * FROM round";
$stmt_r = $conn->prepare($sql_r);
$stmt_r->execute();
$round_data = $stmt_r->fetchAll(PDO::FETCH_ASSOC);

$sql_d = "SELECT * FROM department";
$stmt_d = $conn->prepare($sql_d);
$stmt_d->execute();
$depart_data = $stmt_d->fetchAll(PDO::FETCH_ASSOC);
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

    <div
        class="d-md-none p-3 bg-white border-bottom d-flex justify-content-between align-items-center sticky-top shadow-sm">
        <span class="fw-bold text-primary"><i class="bi bi-calendar-check"></i> จัดการรอบรับสมัคร</span>
        <button class="btn btn-primary btn-toggle-sidebar" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="d-flex">
        <?php 
        include '../components/navbar.php';
        Navbar_admin(); ?>

        <div class="main-content">
            <div class="container-fluid">

                <h3 class="mb-4 fw-bold text-dark"><i class="bi bi-calendar-range text-primary"></i>
                    ระบบจัดการการรับสมัคร</h3>

                <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="round-tab" data-bs-toggle="tab"
                            data-bs-target="#roundContent" type="button" role="tab"><i class="bi bi-clock-history"></i>
                            จัดการรอบ (Round)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reg-tab" data-bs-toggle="tab" data-bs-target="#regContent"
                            type="button" role="tab"><i class="bi bi-list-check"></i> กำหนดสาขาที่เปิดรับ</button>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane fade show active" id="roundContent" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">รายการรอบการรับสมัคร</h5>
                                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#addRoundModal">
                                    <i class="bi bi-plus-lg"></i> เพิ่มรอบใหม่
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="roundTable" class="table table-hover align-middle w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ชื่อรอบ</th>
                                            <th>วันที่เปิดรับ</th>
                                            <th>วันที่ปิดรับ</th>
                                            <th class="text-end">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($round_data as $d) {
                                            $date_round = date('d/m/Y', strtotime($d['date_round']));
                                            $end_round = date('d/m/Y', strtotime($d['end_round']));
                                            ?>
                                            <tr>
                                                <td class="fw-bold text-primary"><?= $d['name_round']; ?></td>
                                                <td><i class="bi bi-calendar-check text-success"></i> <?= $date_round; ?>
                                                </td>
                                                <td><i class="bi bi-calendar-x text-danger"></i> <?= $end_round; ?></td>
                                                <td class="text-end">
                                                    <button class="btn btn-warning btn-action text-white shadow-sm"
                                                        onclick="editRound('<?= $d['id_round']; ?>', '<?= $d['name_round']; ?>', '<?= $d['date_round']; ?>', '<?= $d['end_round']; ?>')">
                                                        แก้ไข
                                                    </button>
                                                    <button class="btn btn-danger btn-action shadow-sm"
                                                        onclick="confirmDeletion('<?= $d['id_round']; ?>')">
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

                    <div class="tab-pane fade" id="regContent" role="tabpanel">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">สาขาวิชาที่เปิดให้สมัคร</h5>
                                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#addRegModal">
                                    <i class="bi bi-plus-lg"></i> เพิ่มการเปิดรับ
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="regTable" class="table table-hover align-middle w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>รอบ</th>
                                            <th>แผนก</th>
                                            <th>สาขาวิชา</th>
                                            <th>แผนการเรียน</th>
                                            <th>วุฒิ</th>
                                            <th>รับ (คน)</th>
                                            <th class="text-end">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT rs.*, dp.*, rd.name_round, mp.name_major 
                                                FROM reg_show AS rs
                                                INNER JOIN department AS dp ON rs.depart_ref = dp.id_depart
                                                INNER JOIN round AS rd ON rs.round_ref = rd.id_round
                                                INNER JOIN major_depart AS mp ON dp.id_major_ref = mp.id_major
                                                ORDER BY rs.id DESC";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                        $show_reg = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($show_reg as $d) { ?>
                                            <tr>
                                                <td><?= $d['id']; ?></td>
                                                <td><span class="badge bg-info text-dark"><?= $d['name_round']; ?></span>
                                                </td>
                                                <td><?= $d['name_major']; ?></td>
                                                <td class="fw-bold"><?= $d['name_depart']; ?></td>
                                                <td><?= $d['level']; ?></td>
                                                <td><small><?= $d['class']; ?></small></td>
                                                <td><?= $d['total']; ?></td>
                                                <td class="text-end">
                                                    <button class="btn btn-warning btn-action text-white shadow-sm"
                                                        onclick="editReg('<?= $d['id']; ?>', '<?= $d['round_ref']; ?>', '<?= $d['depart_ref']; ?>')">
                                                        แก้ไข
                                                    </button>
                                                    <button class="btn btn-danger btn-action shadow-sm"
                                                        onclick="confirmReg('<?= $d['id']; ?>')">
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

    <div class="modal fade" id="addRoundModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> เพิ่มรอบรับสมัครใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="system/insertRound.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ชื่อรอบ</label>
                            <input required type="text" class="form-control" name="name_round"
                                placeholder="เช่น รอบโควตา, รอบทั่วไป">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">วันที่เปิดรับ</label>
                                <input required type="date" class="form-control" name="date_round">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">วันที่ปิดรับ</label>
                                <input required type="date" class="form-control" name="end_round">
                            </div>
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

    <div class="modal fade" id="addRegModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-list-plus"></i> เปิดรับสมัครรายวิชา</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="system/insertRound.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">รอบที่เปิดรับสมัคร</label>
                            <select required class="form-select" name="round_ref">
                                <option selected disabled value="">-- เลือกรอบ --</option>
                                <?php foreach ($round_data as $d) { ?>
                                    <option value="<?= $d['id_round']; ?>"><?= $d['name_round']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สาขาวิชาที่เปิด</label>
                            <select required class="form-select" name="depart_ref">
                                <option selected disabled value="">-- เลือกสาขา --</option>
                                <?php foreach ($depart_data as $d) { ?>
                                    <option value="<?= $d['id_depart']; ?>">
                                        <?= $d['name_depart'] ?> (<?= $d['class'] ?> - <?= $d['level'] ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" name="insertReg" class="btn btn-primary">บันทึก</button>
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
        $(document).ready(function () {
            let tableOptions = { "language": { "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json" } };
            $('#roundTable').DataTable(tableOptions);
            $('#regTable').DataTable({
                ...tableOptions,
                "order": [[0, "desc"]]
            });

            // Fix DataTable width inside tabs
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            });
        });

        // --- SweetAlert Functions ---

        // 1. แก้ไขรอบ (เปลี่ยนชื่อจาก editDepartment เป็น editRound เพื่อความถูกต้อง)
        function editRound(id_round, name_round, date_round, end_round) {
            Swal.fire({
                title: 'แก้ไขข้อมูลรอบ',
                html: `
                    <div class="text-start">
                        <label class="form-label">ชื่อรอบ</label>
                        <input type="text" id="swal-name" class="swal2-input mb-3 mt-1" value="${name_round}" style="width: 80%; margin-left: 10%;">
                        <label class="form-label">วันที่เปิด</label>
                        <input type="date" id="swal-date" class="swal2-input mb-3 mt-1" value="${date_round}" style="width: 80%; margin-left: 10%;">
                        <label class="form-label">วันที่ปิด</label>
                        <input type="date" id="swal-end" class="swal2-input mt-1" value="${end_round}" style="width: 80%; margin-left: 10%;">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                preConfirm: () => {
                    return {
                        id_round: id_round,
                        name_round: document.getElementById('swal-name').value,
                        date_round: document.getElementById('swal-date').value,
                        end_round: document.getElementById('swal-end').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'system/edit_round.php',
                        type: 'POST',
                        data: result.value,
                        success: function (res) {
                            Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อย', 'success').then(() => location.reload());
                        },
                        error: function () { Swal.fire('Error', 'เกิดข้อผิดพลาด', 'error'); }
                    });
                }
            });
        }

        // 2. ลบรอบ
        function confirmDeletion(id_round) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "ข้อมูลรอบนี้จะหายไปถาวร",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'ลบเลย'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '?id=' + id_round;
            });
        }

        // 3. แก้ไข Reg Show
        function editReg(id, round_ref, depart_ref) {
            // Prepare Options JS
            let roundOptions = `<?php foreach ($round_data as $d) {
                echo "<option value='{$d['id_round']}'>{$d['name_round']}</option>";
            } ?>`;
            let departOptions = `<?php foreach ($depart_data as $d) {
                echo "<option value='{$d['id_depart']}'>{$d['name_depart']}</option>";
            } ?>`;

            Swal.fire({
                title: 'แก้ไขข้อมูลการเปิดรับ',
                html: `
                    <div class="text-start">
                        <label>รอบการรับสมัคร</label>
                        <select id="swal-round" class="swal2-select mb-3 mt-1" style="display:block; width: 80%; margin: 0 auto;">${roundOptions}</select>
                        <label>สาขาวิชา</label>
                        <select id="swal-depart" class="swal2-select mt-1" style="display:block; width: 80%; margin: 0 auto;">${departOptions}</select>
                    </div>
                `,
                didOpen: () => {
                    document.getElementById('swal-round').value = round_ref;
                    document.getElementById('swal-depart').value = depart_ref;
                },
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                preConfirm: () => {
                    return {
                        id: id,
                        round_ref: document.getElementById('swal-round').value,
                        depart_ref: document.getElementById('swal-depart').value,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'system/edit_regShow.php',
                        type: 'POST',
                        data: result.value,
                        success: function (res) {
                            Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อย', 'success').then(() => location.reload());
                        },
                        error: function () { Swal.fire('Error', 'เกิดข้อผิดพลาด', 'error'); }
                    });
                }
            });
        }

        // 4. ลบ Reg Show
        function confirmReg(id) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "รายการเปิดรับสมัครนี้จะถูกลบ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'ลบเลย'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '?idReg=' + id;
            });
        }
    </script>
</body>

</html>