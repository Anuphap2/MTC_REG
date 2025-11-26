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
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('location: index.php');
}


if (isset($_GET['id'])) {
    $id_depart = $_GET['id'];
    $sql_delete = "DELETE FROM department WHERE id_depart = :id_depart";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id_depart', $id_depart);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลสาขาวิชาเรียบร้อยแล้ว";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
        header("location: addSubject.php");

    }
}

if (isset($_GET['id_major'])) {
    $id_major = $_GET['id_major'];
    $sql_delete = "DELETE FROM major_depart WHERE id_major = :id_major";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id_major', $id_major);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลแผนกเรียบร้อยแล้ว";
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
        header("location: addSubject.php");

    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <?php Head_admin(); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">

    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }

        .main-content {
            width: 100%;
            margin-left: 250px;
            /* ความกว้างของ sidebar */
            padding: 20px;
        }
    </style>
</head>

<body>
    <?php showStatus_admin(); ?>
    <div class="d-flex">

        <?php Navbar_admin(); ?>

        <div class="main-content">
            <div class="row gx-5">
                <div class="col-4">
                    <h1>จัดการข้อมูลแผนก</h1>
                    <hr>
                    <form action="system/insertSubject.php" method="post">
                        <div class="mb-3">
                            <label for="name_major" class="form-label">ชื่อแผนก</label>
                            <input required type="text" class="form-control" name="name_major">
                        </div>

                        <button type="submit" name="insertMajor" class="btn btn-primary">เพิ่มข้อมูล</button>
                    </form>
                </div>
                <div class="col-8">
                    <table id="majorTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ชื่อแผนก</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM major_depart";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $major = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($major as $d) { ?>
                                <tr>

                                    <td><?= $d['name_major']; ?></td>

                                    <td>
                                        <button class="btn btn-warning" onclick="editMajor('<?php echo $d['id_major']; ?>', 
                                       '<?= $d['name_major']; ?>')">
                                            แก้ไข
                                        </button>

                                        <a href="#" class="btn btn-danger"
                                            onclick="confirmDeletionMajor('<?php echo $d['id_major']; ?>')">ลบ</a>
                                    </td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                </div>
                <hr class="mt-5 mb-5">
                <div class="col-4">
                    <h1>จัดการข้อมูลสาขา</h1>
                    <hr>
                    <form action="system/insertSubject.php" method="post">
                        <div class="mb-3">
                            <label for="name_depart" class="form-label">ชื่อสาขา</label>
                            <input type="text" class="form-control" name="name_depart">
                        </div>
                        <div class="mb-3">
                            <label for="total" class="form-label">แผนก</label>
                            <select required class="form-select" name="name_major">
                                <option selected disabled value="">แผนก</option>
                                <?php
                                $sql = "SELECT * FROM major_depart";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $major = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($major as $d) { ?>
                                    <option value="<?= $d['id_major']; ?>"><?= $d['name_major']; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="total" class="form-label">จำนวนที่รับ</label>
                            <input type="text" class="form-control" name="total">
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">แผนการเรียน</label>
                            <select required class="form-select" name="level">
                                <option selected disabled value="">แผนการเรียน</option>
                                <option value="ปกติ">
                                    ปกติ
                                </option>

                                <option value="ทวิภาคี">ทวิภาคี</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="total" class="form-label">วุฒิที่รับ</label>
                            <select required class="form-select" name="class">
                                <option selected disabled value="">วุฒิที่รับ</option>
                                <option value="มัธยมศึกษาปีที่ 6">
                                    มัธยมศึกษาปีที่ 6</option>

                                <option value="ประกาศนียบัตรวิชาชีพ">ประกาศนียบัตรวิชาชีพ (ปวช.)</option>
                                <option value="มัธยมศึกษาปีที่ 3">
                                    มัธยมศึกษาปีที่ 3</option>
                            </select>
                        </div>
                        <button type="submit" name="insert" class="btn btn-primary">เพิ่มข้อมูล</button>
                    </form>
                </div>
                <div class="col-8">
                    <table id="departmentsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>รหัสสาขา</th>
                                <th>ชื่อสาขา</th>
                                <th>แผนก</th>
                                <th>แผนการเรียน</th>
                                <th>จำนวนที่รับ</th>
                                <th>วุฒิที่รับ</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT dp.*, mp.name_major AS major_name,mp.id_major
                            FROM department AS dp
                            LEFT JOIN major_depart AS mp ON dp.id_major_ref = mp.id_major";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($departments as $department) { ?>
                                <tr>
                                    <td><?= $department['id_depart']; ?></td>
                                    <td><?= $department['name_depart']; ?></td>
                                    <td><?= $department['major_name']; ?></td>
                                    <td><?= $department['level']; ?></td>
                                    <td><?= $department['total']; ?></td>
                                    <td><?= $department['class']; ?></td>
                                    <td>
                                        <button class="btn btn-warning mb-3" onclick="editDepartment('<?php echo $department['id_depart']; ?>', 
                                       '<?= $department['name_depart']; ?>', 
                                       '<?= $department['id_major_ref']; ?>', 
                                       '<?= $department['total']; ?>', 
                                       '<?= $department['class']; ?>')">
                                            แก้ไข
                                        </button>

                                        <a href="#" class="btn btn-danger"
                                            onclick="confirmDeletion('<?php echo $department['id_depart']; ?>')">ลบ</a>
                                    </td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                </div>
            </div>



        </div>
    </div>


    <footer>
        <?php Footer() ?>
    </footer>
    <script src="../css/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- major -->
    <script>
        function editMajor(id_major, name_major) {
            Swal.fire({
                title: 'แก้ไขข้อมูลแผนก',
                html: `
            <input type="text" id="name_major" class="swal2-input" value="${name_major}" placeholder="ชื่อสาขา">
         
        `,
                showCancelButton: true,
                confirmButtonColor: '#28a745',  // สีเขียวสำหรับปุ่ม confirm
                cancelButtonColor: '#6c757d',   // สีเทาสำหรับปุ่ม cancel
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        id_major: id_major,
                        name_major: document.getElementById('name_major').value,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งข้อมูลไปยัง PHP เพื่อแก้ไข
                    let data = result.value;
                    $.ajax({
                        url: 'system/edit_major.php',
                        type: 'POST',
                        data: data,
                        success: function (response) {
                            Swal.fire('สำเร็จ', response.message, 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถแก้ไขข้อมูลได้', 'error');
                        }
                    });
                }
            });
        }

        function confirmDeletionMajor(id_major) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?idMajor=' + id_major;
                }
            })
        }


    </script>
    <?php
    $sql = "SELECT * FROM major_depart";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $major = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <script>
        function editDepartment(id_depart, name_depart, name_major, total, class_name, level) {


            Swal.fire({
                title: 'แก้ไขข้อมูลสาขา',
                html: `
            <input type="text" id="name_depart" class="swal2-input" value="${name_depart}" placeholder="ชื่อสาขา">

            <select required id="name_major" class="swal2-select">
            <option disabled value="">แผนก</option>
                <?php foreach ($major as $d) { ?>
                                                                    <option value="<?= $d['id_major']; ?>" ${name_major == "<?= $d['id_major']; ?>" ? 'selected' : ''}>
                                                                        <?= $d['name_major']; ?>
                                                                    </option>
                <?php } ?>
            </select>
   
            <input type="number" id="total" class="swal2-input" value="${total}" placeholder="จำนวน">
            <select id="level" class="swal2-select">
                <option value="ปกติ" ${class_name === "ปกติ" ? 'selected' : ''}>ปกติ</option>
                <option value="ทวิภาคี" ${class_name === "ทวิภาคี" ? 'selected' : ''}>ทวิภาคี</option>
            </select>

            <select id="class_name" class="swal2-select">
                <option value="มัธยมศึกษาปีที่ 6" ${class_name === "มัธยมศึกษาปีที่ 6" ? 'selected' : ''}>มัธยมศึกษาปีที่ 6</option>
                <option value="ประกาศนียบัตรวิชาชีพชั้นสูง" ${class_name === "ประกาศนียบัตรวิชาชีพชั้นสูง" ? 'selected' : ''}>ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)</option>
                <option value="ประกาศนียบัตรวิชาชีพ" ${class_name === "ประกาศนียบัตรวิชาชีพ" ? 'selected' : ''}>ประกาศนียบัตรวิชาชีพ (ปวช.)</option>
                <option value="มัธยมศึกษาปีที่ 3" ${class_name === "มัธยมศึกษาปีที่ 3" ? 'selected' : ''}>มัธยมศึกษาปีที่ 3</option>
            </select>
        `,
                showCancelButton: true,
                confirmButtonColor: '#28a745',  // สีเขียวสำหรับปุ่ม confirm
                cancelButtonColor: '#6c757d',   // สีเทาสำหรับปุ่ม cancel
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        id_depart: id_depart,
                        name_depart: document.getElementById('name_depart').value,
                        name_major: document.getElementById('name_major').value,
                        total: document.getElementById('total').value,
                        class: document.getElementById('class_name').value,
                        level: document.getElementById('level').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งข้อมูลไปยัง PHP เพื่อแก้ไข
                    let data = result.value;
                    $.ajax({
                        url: 'system/edit_department.php',
                        type: 'POST',
                        data: data,
                        success: function (response) {
                            Swal.fire('สำเร็จ', response.message, 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถแก้ไขข้อมูลได้', 'error');
                        }
                    });
                }
            });
        }

        function confirmDeletion(id_depart) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?id=' + id_depart;
                }
            })
        }

        function confirmDeletionMajor(id_major) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?id_major=' + id_major;
                }
            })
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#departmentsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json"
                }
            });
        });
        $(document).ready(function () {
            $('#majorTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json"
                }
            });
        });
    </script>
</body>

</html>