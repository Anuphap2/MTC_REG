<?php
include 'components/navbar.php';
include 'components/head.php';
include 'components/sessionShow.php';
include 'components/footer.php';
require_once 'config/db.php';
$conn = connectDB();
$now = date("Y-m-d");

if (isset($_SESSION['user_log'])) {
    $id_card = $_SESSION['user_log'];
    $sql = "SELECT * FROM u_reg WHERE id_card = :id_card";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id_card", $id_card);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM round WHERE :now BETWEEN date_round AND end_round";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':now', $now);
    $stmt->execute();
    $round = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result['round_assign'] != $round['id_round']) {
        $sql = "UPDATE u_reg SET round_assign = :round_assign , depart_major = '' , class_assign = '' , depart_minor = '' WHERE id_card = :id_card";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':round_assign', $round['id_round']);
        $stmt->bindParam(':id_card', $id_card);
        $stmt->execute();
    }

    if (!$round) {
        $_SESSION['error'] = "หมดเวลาสมัครเรียน";
        header("location: login.php");
        exit();
    }

} else {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('location: login.php');
}

?>

<!doctype html>
<html lang="en">

<head>
    <?php Head(); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">

</head>

<body>
    <?php showStatus(); ?>
    <!-- header start  -->
    <header>
        <?php Navbar(); ?>
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
        <h3 class="mt-5">ข้อมูลผู้ลงทะเบียน</h3>
        <hr>
        <div style="border: none; background-color: #AFEEEF" class="card w-100 mb-3">
            <div class="card-body">
                <?php
                $sql = "SELECT * FROM round WHERE date_round <= :now ORDER BY date_round DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':now', $now);
                $stmt->execute();
                $round = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <h5 class="card-title"><?= $round['name_round'] ?></h5>
                <p class="card-text">
                    ชื่อ : <?= $result['prefix'] . $result['fname'] . ' ' . $result['lname'] ?>
                    <br>
                    เบอร์ติดต่อ : <?= $result['tel'] ?>
                </p>
            </div>
        </div>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">รายการ</th>
                    <th scope="col">สถานะ</th>
                    <th scope="col">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>กรอกข้อมูลส่วนตัว</td>
                    <td>
                        <?php
                        if (
                            empty($result['id_card']) || empty($result['prefix']) || empty($result['fname']) || empty($result['lname']) ||
                            empty($result['b_day']) || empty($result['m_day']) || empty($result['y_day']) || empty($result['age']) ||
                            empty($result['tel']) || empty($result['h_number']) || empty($result['road']) || empty($result['A_district']) || empty($result['S_district']) || empty($result['District'])
                        ) {
                            echo "คุณยังกรอกข้อมูลไม่ครบถ้วน";
                        } else {
                            echo "กรอกข้อมูลส่วนตัวเรียบร้อยแล้ว";
                        }

                        ?>
                    </td>
                    <td>
                        <div class="d-grid gap-3">
                            <a href="register.php?id_card=<?= $result['id_card'] ?>"
                                class="btn btn-sm btn-warning">แก้ไขข้อมูล</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>เลือกสาขาวิชาที่ต้องการ</td>
                    <td>
                        <?php
                        if ($result['depart_major'] || $result['depart_minor']) {
                            $sql = "SELECT name_depart FROM department WHERE id_depart = :depart_major";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':depart_major', $result['depart_major']);
                            $stmt->execute();
                            $depart1 = $stmt->fetch(PDO::FETCH_ASSOC);

                            $sql = "SELECT name_depart FROM department WHERE id_depart = :depart_minor";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':depart_minor', $result['depart_minor']);
                            $stmt->execute();
                            $depart2 = $stmt->fetch(PDO::FETCH_ASSOC);


                            echo "คุณเลือกสาขาวิชา " . $depart1['name_depart'] . " เป็นอันดับ 1 และ อันดับ 2 ";

                            if (isset($depart2['name_depart']) && !empty($depart2['name_depart'])) {
                                echo $depart2['name_depart'];
                            } else {
                                echo "ไม่มี";
                            }

                        } else {
                            echo "คุณยังไม่ได้เลือกสาขาวิชา";
                        }
                        ?>

                    </td>
                    <td>
                        <div class="d-grid gap-3">
                            <?php
                            if ($result['depart_major'] && $result['depart_minor']) { ?>
                                <a href="#" class="btn btn-sm btn-danger"
                                    onclick="confirmDeletion('<?php echo $result['id_card']; ?>')">ยกเลิกการสมัคร</a>
                            <?php } else { ?>
                                <a href="reg_mtc.php?id_card=<?= $result['id_card'] ?>"
                                    class="btn btn-sm btn-secondary">สมัครเรียน</a>

                            <?php }
                            ?>
                        </div>
                    </td>
                </tr>
				<tr>
                    <th scope="row">3</th>
                    <td>สมัครประเภท</td>
                    <?php
                    if (!$result['type_reg']) {
                        echo '<td>ยังไม่ได้เลือกประเภทการสมัคร</td>';
                    } else {
                        echo '<td>' . $result['type_reg'] . '</td>';

                    }
                    ?>
                    <td>
                        <button type="button" class="btn btn-sm btn-secondary w-100" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">
                            เลือกประเภท
                        </button>

                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">เลือกประเภทและอัปโหลดรูปภาพ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="myForm" action="system_reg/reg_update.php" method="POST"
                                            enctype="multipart/form-data">
                                            <!-- Select Box -->
                                            <input type="hidden" name="id_card" value="<?= $result['id_card'] ?>"
                                                type="text">
                                            <div class="mb-3">
                                                <label for="typeSelect" class="form-label">เลือกประเภท</label>
                                                <select required class="form-select" id="typeSelect" name="type">
                                                    <option disabled value="">เลือกประเภท</option>
                                                    <option value="ประเภทผู้มีการเรียนดี"
                                                        <?= ($result['type_reg'] == 'ประเภทผู้มีการเรียนดี') ? 'selected' : '' ?>>
                                                        ประเภทผู้มีการเรียนดี
                                                    </option>
                                                    <option value="ประเภทผู้มีความสามารถด้านทักษะวิชาการ"
                                                        <?= ($result['type_reg'] == 'ประเภทผู้มีความสามารถด้านทักษะวิชาการ') ? 'selected' : '' ?>>
                                                        ประเภทผู้มีความสามารถด้านทักษะวิชาการ
                                                    </option>
                                                    <option value="ประเภทผู้มีความสามารถด้านกีฬา"
                                                        <?= ($result['type_reg'] == 'ประเภทผู้มีความสามารถด้านกีฬา') ? 'selected' : '' ?>>
                                                        ประเภทผู้มีความสามารถด้านกีฬา / กิจกรรมเด่น
                                                    </option>
                                                    <option value="ประเภทผู้มีความสามารถด้านศิลปวัฒนธรรม"
                                                        <?= ($result['type_reg'] == 'ประเภทผู้มีความสามารถด้านศิลปวัฒนธรรม') ? 'selected' : '' ?>>
                                                        ประเภทผู้มีความสามารถด้านศิลปวัฒนธรรม
                                                    </option>
                                                </select>
                                            </div>
                                            <!-- File Upload -->
                                            <div class="mb-3">
                                                <label for="fileUpload" class="form-label">รูปภาพหลักฐาน เกียรติบัตร
                                                    หรือ ใบเกรด <span>** อัพโหลดได้เพียงรูปเดียว **</span></label>
                                                <input class="form-control" type="file" id="fileUpload" name="file">
                                                <!-- แสดงรูปที่มีอยู่แล้ว (ถ้ามี) -->
                                                <?php if ($result['image']): ?>
                                                    <p>ไฟล์ที่อัปโหลดแล้ว: <a href="system_reg/uploads/<?= $result['image'] ?>"
                                                            target="_blank"><?= $result['image'] ?></a></p>
                                                <?php endif; ?>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">ปิด</button>
                                        <button type="submit" class="btn btn-primary" name="uploadData"
                                            form="myForm">บันทึก</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>

    <footer>
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDeletion(id) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณกำลังยกเลิกการสมัคร!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'system_reg/unreg.php?id=' + id;
                }
            })
        }
    </script>
</body>

</html>