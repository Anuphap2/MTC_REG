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

</head>

<body>
    <!-- header start  -->
    <header>
        <?php
        Navbar();
        showStatus();
        ?>
    </header>
    <!-- header end  -->

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
            header("location: login.php");
        }

    } else {
        $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
        header("location: login.php");
    }
    ?>
    <main class="container">
        <form action="system_reg/reg_update.php" method="post">
            <div class="row gx-4 mt-5">
                <h1><b>ขั้นตอนที่ 1 กรอกข้อมูลส่วนตัว</b></h1>
                <hr>
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <div class="input-group mb-3">
                        <span class="input-group-text">เลขบัตรประชาชน</span>
                        <input style="background-color: #E7EBEB;" readonly type="text" value="<?= $result['id_card'] ?>"
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
                                <option disabled value="" selected>เลือกคำนำหน้า</option>
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
                        <input required type="text" value="<?= $result['fname'] ?>" class="form-control" name="fname">
                    </div>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <div class="input-group mb-3">
                        <span class="input-group-text">นามสกุล</span>
                        <input required type="text" value="<?= $result['lname'] ?>" class="form-control" name="lname">
                    </div>
                </div>

                <div class="col-sm-3 col-md-3 col-lg-3">
                    <div class="input-group mb-3">
                        <span class="input-group-text">เกิดวันที่</span>
                        <input required type="number" value="<?= $result['b_day'] ?>" class="form-control" name="b_day">
                    </div>
                </div>

                <div class="col-sm-3 col-md-3 col-lg-3">
                    <div class="input-group mb-3">
                        <span class="input-group-text">เดือน</span>
                        <select required class="form-control" name="m_day">
                            <option disabled value="">เลือกเดือน</option>
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
                            <option disabled value="">เลือกปี</option>
                            <?php
                            $current_year = date('Y');
                            $start_year = $current_year - 30; // แสดงปีย้อนหลัง 30 ปี
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
                        <span class="input-group-text">ถนน (ถ้ามี)</span>
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
                            <!-- <option value="ประกาศนียบัตรวิชาชีพชั้นสูง"
                                <?= ($result['lasted_class'] == 'ประกาศนียบัตรวิชาชีพชั้นสูง' ? 'selected' : '') ?>>
                                ประกาศนียบัตรวิชาชีพชั้นสูง (ปวส.)</option> -->
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

                <div class="d-flex justify-content-center">
                    <a style="width: 250px;" href="main_reg.php?id_card=<?= $result['id_card'] ?>"
                        class="btn btn-secondary me-3">ยกเลิก</a>
                    <div class="d-grid gap-3">
                        <button style="width: 250px;" type="submit" name="submit"
                            class="btn btn-primary">ยืนยัน</button>
                    </div>
                </div>
            </div>
        </form>

      

    </main>

    <footer>
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>