<?php
include 'components/navbar.php';
include 'components/head.php';
include 'components/footer.php';
include 'components/sessionShow.php';
require_once 'config/db.php';

$conn = connectDB();
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head(); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-section {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header-custom {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            padding: 1.5rem;
        }

        .card-header-custom h4 {
            margin: 0;
            font-weight: 700;
            color: #0d6efd;
            /* สีหลัก */
            font-size: 1.2rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #555;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .btn-action {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <header>
        <?php
        Navbar();
        showStatus();
        ?>
    </header>
    <?php
    if (isset($_GET['id_card'])) {
        $id_card = $_GET['id_card'];
        $sql = "SELECT * FROM u_reg WHERE id_card = :id_card";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_card', $id_card);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result['id_card']) {
            $_SESSION['error'] = "ไม่พบข้อมูล";
            echo "<script>window.location.href='login.php';</script>"; // ใช้ JS Redirect แทนเพื่อความชัวร์
            exit;
        }

    } else {
        $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
        echo "<script>window.location.href='login.php';</script>";
        exit;
    }
    ?>

    <main class="container py-5">

        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-dark">แก้ไขข้อมูลการสมัคร</h2>
                <p class="text-muted">กรุณาตรวจสอบความถูกต้องของข้อมูลก่อนบันทึก</p>
            </div>
        </div>

        <form action="system_reg/reg_update.php" method="post">

            <div class="card card-section">
                <div class="card-header-custom">
                    <h4><i class="bi bi-person-circle me-2"></i> ขั้นตอนที่ 1 ข้อมูลส่วนตัว</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">

                        <div class="col-12 mb-2">
                            <label class="form-label text-primary">เลขบัตรประชาชน (ไม่สามารถแก้ไขได้)</label>
                            <input style="background-color: #e9ecef; cursor: not-allowed;" readonly type="text"
                                value="<?= $result['id_card'] ?>" class="form-control form-control-lg" name="id_card">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">คำนำหน้า</label>
                            <select required name="prefix" class="form-select">
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
                                    <option disabled value="" selected>เลือกคำนำหน้า</option>
                                    <option value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">ชื่อจริง</label>
                            <input required type="text" value="<?= $result['fname'] ?>" class="form-control"
                                name="fname">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">นามสกุล</label>
                            <input required type="text" value="<?= $result['lname'] ?>" class="form-control"
                                name="lname">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">วันที่เกิด</label>
                            <input required type="number" value="<?= $result['b_day'] ?>" class="form-control"
                                name="b_day">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">เดือนเกิด</label>
                            <select required class="form-select" name="m_day">
                                <option disabled value="">เลือกเดือน</option>
                                <?php
                                $months = [1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'];
                                foreach ($months as $num => $name) {
                                    $selected = ($result['m_day'] == $num) ? 'selected' : '';
                                    echo "<option value='$num' $selected>$name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ปีเกิด (พ.ศ.)</label>
                            <select required class="form-select" name="y_day">
                                <option disabled value="">เลือกปี</option>
                                <?php
                                $current_year = date('Y');
                                $start_year = $current_year - 30;
                                for ($year = $start_year; $year <= $current_year; $year++) {
                                    $selected = ($result['y_day'] == $year) ? 'selected' : '';
                                    echo "<option value='$year' $selected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label class="form-label">อายุ</label>
                            <input required type="number" value="<?= $result['age'] ?>" class="form-control" name="age">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">เบอร์ติดต่อ</label>
                            <input required type="text" value="<?= $result['tel'] ?>" class="form-control" name="tel">
                        </div>

                    </div>
                </div>
            </div>

            <div class="card card-section">
                <div class="card-header-custom">
                    <h4><i class="bi bi-house-door-fill me-2"></i> ขั้นตอนที่ 2 ข้อมูลที่อยู่</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">บ้านเลขที่</label>
                            <input required type="text" value="<?= $result['h_number'] ?>" class="form-control"
                                name="h_number">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">หมู่</label>
                            <input type="text" value="<?= $result['moo'] ?>" class="form-control" name="moo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">หมู่บ้าน</label>
                            <input type="text" value="<?= $result['Village'] ?>" class="form-control" name="Village">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ซอย</label>
                            <input type="text" value="<?= $result['soi'] ?>" class="form-control" name="soi">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ถนน</label>
                            <input type="text" value="<?= $result['road'] ?>" class="form-control" name="road">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">ตำบล/แขวง</label>
                            <input required type="text" value="<?= $result['A_district'] ?>" class="form-control"
                                name="A_district">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">อำเภอ/เขต</label>
                            <input required type="text" value="<?= $result['S_district'] ?>" class="form-control"
                                name="S_district">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">จังหวัด</label>
                            <input required type="text" value="<?= $result['District'] ?>" class="form-control"
                                name="District">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">รหัสไปรษณีย์</label>
                            <input required type="text" value="<?= $result['postal_code'] ?>" class="form-control"
                                name="postal_code">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-section">
                <div class="card-header-custom">
                    <h4><i class="bi bi-mortarboard-fill me-2"></i> ขั้นตอนที่ 3 ประวัติการศึกษา</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">โรงเรียนเดิม</label>
                            <input required type="text" value="<?= $result['lasted_school'] ?>" class="form-control"
                                name="lasted_school">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">กำลังศึกษา / วุฒิการศึกษา</label>
                            <select required name="lasted_class" class="form-select">
                                <option disabled value="">เลือกวุฒิการศึกษา</option>
                                <option value="มัธยมศึกษาปีที่ 6" <?= ($result['lasted_class'] == 'มัธยมศึกษาปีที่ 6' ? 'selected' : '') ?>>มัธยมศึกษาปีที่ 6</option>
                                <option value="ประกาศนียบัตรวิชาชีพ" <?= ($result['lasted_class'] == 'ประกาศนียบัตรวิชาชีพ' ? 'selected' : '') ?>>ประกาศนียบัตรวิชาชีพ (ปวช.)</option>
                                <option value="มัธยมศึกษาปีที่ 3" <?= ($result['lasted_class'] == 'มัธยมศึกษาปีที่ 3' ? 'selected' : '') ?>>มัธยมศึกษาปีที่ 3</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">จังหวัดสถานศึกษา</label>
                            <input required type="text" value="<?= $result['lasted_school_district'] ?>"
                                class="form-control" name="lasted_school_district">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เกรดเฉลี่ยสะสม (GPAX)</label>
                            <input required type="text" value="<?= $result['lasted_gpax'] ?>" class="form-control"
                                name="lasted_gpax">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 mb-5">
                <div class="col-12 d-flex justify-content-center gap-3">
                    <a href="main_reg.php?id_card=<?= $result['id_card'] ?>"
                        class="btn btn-secondary btn-action shadow-sm px-5">
                        <i class="bi bi-x-lg me-2"></i> ยกเลิก
                    </a>
                    <button type="submit" name="submit" class="btn btn-primary btn-action shadow px-5">
                        <i class="bi bi-save me-2"></i> บันทึกข้อมูล
                    </button>
                </div>
            </div>

        </form>
    </main>

    <footer>
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>