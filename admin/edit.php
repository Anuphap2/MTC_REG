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
    <!-- header start  -->
    <?php showStatus_admin(); ?>
    <div class="d-flex">
        <?php
        if (isset($_GET['user'])) {
            $id_card = $_GET['user'];
            $sql = "SELECT * FROM u_reg WHERE id_card = :id_card";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_card', $id_card);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

        } else {
            $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
            header("location: dataStudent.php");
        }
        ?>

        <?php Navbar_admin(); ?>

        <div class="main-content">
            <form action="system/reg_update_data.php" method="post">

                <div class="row gx-4 mt-5">
                    <h1><b>ขั้นตอนที่ 1 กรอกข้อมูลส่วนตัว</b></h1>
                    <hr>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">เลขบัตรประชาชน</span>
                            <input style="background-color: #E7EBEB;" type="text" value="<?= $result['id_card'] ?>"
                                class="form-control" name="id_card">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="inputGroupSelect01">คำนำหน้า</label>
                            <select required name="prefix" class="form-select" id="inputGroupSelect01">

                                <?php if ($result['prefix'] == 'นาย') { ?>
                                    <option selected value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                <?php } elseif ($result['prefix'] == 'นาง') { ?>
                                    <option value="นาย">นาย</option>
                                    <option selected value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                <?php } elseif ($result['prefix'] == 'นางสาว') { ?>
                                    <option value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option selected value="นางสาว">นางสาว</option>
                                <?php } else { ?>
                                    <option value="" selected>เลือกคำนำหน้า</option>
                                    <option value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                <?php } ?>


                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">ชื่อจริง</span>
                            <input required type="text" value="<?= $result['fname'] ?>" class="form-control"
                                name="fname">
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">นามสกุล</span>
                            <input required type="text" value="<?= $result['lname'] ?>" class="form-control"
                                name="lname">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">เกิดวันที่</span>
                            <input required type="number" value="<?= $result['b_day'] ?>" class="form-control"
                                name="b_day">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">เดือน</span>
                            <select required class="form-control" name="m_day">
                                <option value="">เลือกเดือน</option>
                                <?php
                                $months = [
                                    1 => 'มกราคม',
                                    2 => 'กุมภาพันธ์',
                                    3 => 'มีนาคม',
                                    4 => 'เมษายน',
                                    5 => 'พฤษภาคม',
                                    6 => 'มิถุนายน',
                                    7 => 'กรกฎาคม',
                                    8 => 'สิงหาคม',
                                    9 => 'กันยายน',
                                    10 => 'ตุลาคม',
                                    11 => 'พฤศจิกายน',
                                    12 => 'ธันวาคม'
                                ];
                                foreach ($months as $num => $name) {
                                    $selected = ($result['m_day'] == $num) ? 'selected' : '';
                                    echo "<option value='$num' $selected>$name</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">ปี</span>
                            <select required class="form-control" name="y_day">
                                <option value="">เลือกปี</option>
                                <?php
                                $current_year = date('Y');
                                $start_year = $current_year - 30; // แสดงปีย้อนหลัง 100 ปี
                                for ($year = $start_year; $year <= $current_year; $year++) {
                                    $selected = ($result['y_day'] == $year) ? 'selected' : '';
                                    echo "<option value='$year' $selected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">อายุ</span>
                            <input required type="number" value="<?= $result['age'] ?>" class="form-control" name="age">
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">เบอร์ติดต่อ</span>
                            <input required type="text" value="<?= $result['tel'] ?>" class="form-control" name="tel">
                        </div>
                    </div>
                    <hr>

                    <!-- ขั้นตอนที่ 2 -->
                    <h1><b>ขั้นตอนที่ 2 กรอกที่อยู่ส่วนตัว</b></h1>
                    <hr>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">บ้านเลขที่</span>
                            <input required type="text" value="<?= $result['h_number'] ?>" class="form-control"
                                name="h_number">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">หมู่ (ถ้ามี)</span>
                            <input type="text" value="<?= $result['moo'] ?>" class="form-control" name="moo">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">หมู่บ้าน (ถ้ามี)</span>
                            <input type="text" value="<?= $result['Village'] ?>" class="form-control" name="Village">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">ซอย (ถ้ามี)</span>
                            <input type="text" value="<?= $result['soi'] ?>" class="form-control" name="soi">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">ถนน</span>
                            <input type="text" value="<?= $result['road'] ?>" class="form-control" name="road">
                        </div>
                    </div>


                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">ตำบล/แขวง</span>
                            <input required type="text" value="<?= $result['A_district'] ?>" class="form-control"
                                name="A_district">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">อำเภอ/เขต</span>
                            <input required type="text" value="<?= $result['S_district'] ?>" class="form-control"
                                name="S_district">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">จังหวัด</span>
                            <input required type="text" value="<?= $result['District'] ?>" class="form-control"
                                name="District">
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">รหัสไปรษณีย์</span>
                            <input required type="text" value="<?= $result['postal_code'] ?>" class="form-control"
                                name="postal_code">
                        </div>
                    </div>
                    <hr>

                    <!-- ขั้นตอนที่ 3 -->
                    <h1><b>ขั้นตอนที่ 3 ข้อมูลการศึกษา</b></h1>
                    <hr>
                    <div class="col-sm-4 col-md-4 col-lg-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">โรงเรียน</span>
                            <input required type="text" value="<?= $result['lasted_school'] ?>" class="form-control"
                                name="lasted_school">
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4 col-lg-4">
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="inputGroupSelect01">กำลังศึกษา / วุฒิการศึกษา</label>

                            <select required name="lasted_class" class="form-select" id="inputGroupSelect01">
                                <option disabled value="">เลือกวุฒิการศึกษา</option>
                                <option value="มัธยมศึกษาปีที่ 6" <?= ($result['lasted_class'] == 'มัธยมศึกษาปีที่ 6' ? 'selected' : '') ?>>
                                    มัธยมศึกษาปีที่ 6</option>

                                <option value="ประกาศนียบัตรวิชาชีพ" <?= ($result['lasted_class'] == 'ประกาศนียบัตรวิชาชีพ' ? 'selected' : '') ?>>ประกาศนียบัตรวิชาชีพ (ปวช.)</option>
                                <option value="มัธยมศึกษาปีที่ 3" <?= ($result['lasted_class'] == 'มัธยมศึกษาปีที่ 3' ? 'selected' : '') ?>>
                                    มัธยมศึกษาปีที่ 3</option>
                            </select>

                        </div>
                    </div>

                    <div class="col-sm-4 col-md-4 col-lg-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">จังหวัด</span>
                            <input required type="text" value="<?= $result['lasted_school_district'] ?>"
                                class="form-control" name="lasted_school_district">
                        </div>
                    </div>

                    <div class="col-sm-4 col-md-4 col-lg-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">เกรดเฉลี่ยสะสม</span>
                            <input required type="text" value="<?= $result['lasted_gpax'] ?>" class="form-control"
                                name="lasted_gpax">
                        </div>
                    </div>


                    <div class="row gx-4 mt-3">
                        <div class="col-12 mb-3">
                            <h4>ข้อมูลการสมัครเรียน</h4>
                            <hr>
                        </div>

                        <?php
                        // 1. ดึงรายชื่อสาขาที่เปิดรับในรอบนี้ (เพื่อให้ User เลือกได้ถูกต้อง)
                        // ถ้าอยากให้เลือกได้ทุกสาขา โดยไม่สนรอบ ให้ตัด WHERE ทิ้งได้ครับ
                        $sql_dept = "SELECT * FROM department 
                 INNER JOIN reg_show ON department.id_depart = reg_show.depart_ref 
                 WHERE reg_show.round_ref = :round_id AND department.class = :class";
                        $stmt_dept = $conn->prepare($sql_dept);
                        $stmt_dept->bindParam(':round_id', $result['round_assign']);
                        $stmt_dept->bindParam(':class', $result['lasted_class']);
                        $stmt_dept->execute();
                        $departments = $stmt_dept->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-primary">สาขาที่สมัครลำดับที่ 1</label>
                            <select required name="depart_major" class="form-select">
                                <option value="">-- เลือกสาขาหลัก --</option>
                                <?php foreach ($departments as $dept) {
                                    // เช็คว่าตรงกับค่าเดิมไหม ถ้าตรงให้เลือก (selected)
                                    $sel = ($result['depart_major'] == $dept['id_depart']) ? 'selected' : '';
                                    ?>
                                    <option value="<?= $dept['id_depart'] ?>" <?= $sel ?>>
                                        <?= $dept['name_depart'] ?> (<?= $dept['class'] ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">สาขาที่สมัครลำดับที่ 2 (สำรอง)</label>
                            <select name="depart_minor" class="form-select">
                                <option value="">-- ไม่ประสงค์เลือกสาขารอง --</option>
                                <?php foreach ($departments as $dept) {
                                    $sel = ($result['depart_minor'] == $dept['id_depart']) ? 'selected' : '';
                                    ?>
                                    <option value="<?= $dept['id_depart'] ?>" <?= $sel ?>>
                                        <?= $dept['name_depart'] ?> (<?= $dept['class'] ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                    <div class="d-flex justify-content-center">
                        <div class="d-grid gap-3">
                            <button style="width: 250px;" type="submit" name="save" class="btn btn-success">
                                บันทึก
                            </button>

                        </div>

            </form>
        </div>
    </div>

    </div>
    </div>


    <footer>
        <?php Footer() ?>
    </footer>
    <script src="../css/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>