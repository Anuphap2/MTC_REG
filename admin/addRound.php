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
    $id_round = $_GET['id'];
    $sql_delete = "DELETE FROM round WHERE id_round = :id_round";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id_round', $id_round);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
        header("location: addRound.php");
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
        header("location: addRound.php");
    }
}

if (isset($_GET['idReg'])) {
    $id_round = $_GET['idReg'];
    $sql_delete = "DELETE FROM reg_show WHERE id = :id";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $id_round);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
        header("location: addRound.php");
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
        header("location: addRound.php");
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
                    <h1>จัดการรอบรับสมัคร</h1>
                    <hr>
                    <form action="system/insertRound.php" method="post">
                        <div class="mb-3">
                            <label for="name_depart" class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" name="name_round">
                        </div>
                        <div class="mb-3">
                            <label for="total" class="form-label">วันที่รับ</label>
                            <input type="date" class="form-control" name="date_round">
                        </div>
                        <div class="mb-3">
                            <label for="total" class="form-label">วันที่ปิด</label>
                            <input type="date" class="form-control" name="end_round">
                        </div>

                        <button type="submit" name="insert" class="btn btn-primary">เพิ่มข้อมูล</button>
                    </form>
                </div>
                <div class="col-8">
                    <table id="round" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ชื่อรอบ</th>
                                <th>วันที่รับ</th>
                                <th>วันที่ปิด</th>
                                <th>การจัดการ</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM round";

                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $round = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($round as $d) {
                                $date_round = date('d/m/Y', strtotime($d['date_round']));
                                $end_round = date('d/m/Y', strtotime($d['end_round']));
                                ?>
                                <tr>
                                    <td><?= $d['name_round']; ?></td>
                                    <td><?= $date_round; ?></td>
                                    <td><?= $end_round; ?></td>
                                    <td>
                                        <button class="btn btn-warning" onclick="editDepartment('<?php echo $d['id_round']; ?>', 
                                       '<?= $d['name_round']; ?>', 
                                       '<?= $d['date_round']; ?>', 
                                       '<?= $d['end_round']; ?>')">
                                            แก้ไข
                                        </button>

                                        <a href="#" class="btn btn-danger"
                                            onclick="confirmDeletion('<?php echo $d['id_round']; ?>')">ลบ</a>
                                    </td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                </div>
                <hr class="mt-5 mb-5">

                <div class="col-4">
                    <h1>เพิ่มข้อมูลการสมัคร</h1>
                    <hr>
                    <form action="system/insertRound.php" method="post">
                        <div class="mb-3">
                            <label for="total" class="form-label">สาขาวิชา</label>
                            <select required class="form-select" name="depart_ref">
                                <option selected disabled value="">สาขาวิชา</option>
                                <?php
                                $sql = "SELECT * FROM department";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $depart = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($depart as $d) { ?>
                                    <option value="<?= $d['id_depart']; ?>">
                                        <?= $d['name_depart'] . ' ( วุฒิที่รับ' . $d['class'] . ' )' . ' แผนการเรียน : ' . $d['level'] ?>
                                    </option>
                                <?php }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label">รอบที่เปิดรับสมัคร</label>
                            <select required class="form-select" name="round_ref">
                                <option selected disabled value="">รอบที่เปิดรับสมัคร</option>
                                <?php
                                $sql = "SELECT * FROM round";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $round = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($round as $d) { ?>
                                    <option value="<?= $d['id_round']; ?>"><?= $d['name_round']; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>

                        <button type="submit" name="insertReg" class="btn btn-primary">เพิ่มข้อมูล</button>
                    </form>
                </div>
                <div class="col-8">
                    <table id="reg" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ชื่อรอบ</th>
                                <th>แผนก</th>
                                <th>สาขาวิชา</th>
                                <th>แผนการเรียน</th>
                                <th>วุฒิที่รับ</th>
                                <th>จำนวนที่รับ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT rs.*, dp.*,rd.*,mp.*
                             FROM reg_show AS rs
                             INNER JOIN department AS dp ON rs.depart_ref = dp.id_depart
                             INNER JOIN round AS rd ON rs.round_ref = rd.id_round
                             INNER JOIN major_depart AS mp ON dp.id_major_ref = mp.id_major
                             ";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $show_reg = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($show_reg as $d) { ?>
                                <tr>
                                    <td><?= $d['id']; ?></td>
                                    <td><?= $d['name_round']; ?></td>
                                    <td><?= $d['name_major']; ?></td>
                                    <td><?= $d['name_depart']; ?></td>

                                    <td><?= $d['level']; ?></td>
                                    <td><?= $d['class']; ?></td>
                                    <td>
                                        <button class="btn btn-warning" onclick="editReg('<?php echo $d['id']; ?>', 
                                       '<?= $d['round_ref']; ?>', 
                                       '<?= $d['depart_ref']; ?>')">
                                            แก้ไข
                                        </button>
                                        <a href="#" class="btn btn-danger" onclick="confirmReg('<?php echo $d['id']; ?>')">
                                            ลบ
                                        </a>
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
    <?php
    $sql = "SELECT * FROM round";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $round = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM department";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $depart = $stmt->fetchAll(PDO::FETCH_ASSOC);


    ?>
    <script>
        function editReg(id, round_ref, depart_ref) {
            Swal.fire({
                title: 'แก้ไขข้อมูลสาขา',
                html: `
            <select required id="round_ref" class="swal2-select">
            <option disabled value="">รอบที่เปิดรับสมัคร</option>
                <?php foreach ($round as $d) { ?>
                                                    <option value="<?= $d['id_round']; ?>" ${round_ref == "<?= $d['id_round']; ?>" ? 'selected' : ''}>
                                                        <?= $d['name_round']; ?>
                                                    </option>
                <?php } ?>
            </select>     

            <select required id="depart_ref" class="swal2-select">
            <option disabled value="">สาขาวิชา</option>
                <?php foreach ($depart as $d) { ?>
                                                    <option value="<?= $d['id_depart']; ?>" ${depart_ref == "<?= $d['id_depart']; ?>" ? 'selected' : ''}>
                                                        <?= $d['name_depart']; ?>
                                                    </option>
                <?php } ?>
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
                        id: id,
                        round_ref: document.getElementById('round_ref').value,
                        depart_ref: document.getElementById('depart_ref').value,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งข้อมูลไปยัง PHP เพื่อแก้ไข
                    let data = result.value;
                    $.ajax({
                        url: 'system/edit_regShow.php',
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

        function confirmReg(id_round) {
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
                    window.location.href = '?idReg=' + id_round;
                }
            })
        }
    </script>
    <script>
        function editDepartment(id_round, name_round, date_round, end_round) {
            Swal.fire({
                title: 'แก้ไขข้อมูลสาขา',
                html: `
            <input type="text" id="name_round" class="swal2-input" value="${name_round}" placeholder="ชื่อสาขา">
            <input type="date" id="date_round" class="swal2-input" value="${date_round}">
            <input type="date" id="end_round" class="swal2-input" value="${end_round}">
           
        `,
                showCancelButton: true,
                confirmButtonColor: '#28a745',  // สีเขียวสำหรับปุ่ม confirm
                cancelButtonColor: '#6c757d',   // สีเทาสำหรับปุ่ม cancel
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        id_round: id_round,
                        name_round: document.getElementById('name_round').value,
                        date_round: document.getElementById('date_round').value,
                        end_round: document.getElementById('end_round').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งข้อมูลไปยัง PHP เพื่อแก้ไข
                    let data = result.value;
                    $.ajax({
                        url: 'system/edit_round.php',
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

        function confirmDeletion(id_round) {
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
                    window.location.href = '?id=' + id_round;
                }
            })
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#round').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json"
                }
            });
        });
        $(document).ready(function () {
            $('#reg').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json"
                },
                "order": [[0, "desc"]]  // คอลัมน์แรก (0) จะถูกเรียงลำดับจากมากไปน้อย
            });
        });

    </script>
</body>

</html>