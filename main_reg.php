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

    if ($result['round_assign'] != $round['id_round']) {
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">

    <style>
        .card-info {
            border: none;
            border-left: 5px solid #0d6efd;
            /* สีหลัก */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .table-custom thead th {
            background-color: #e9ecef;
            color: #495057;
            border: none;
        }

        .step-number {
            background-color: #0d6efd;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php showStatus(); ?>
    <header>
        <?php Navbar(); ?>
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
            header("location: login.php");
        }

    } else {
        $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
        header("location: login.php");
    }
    ?>
    <main class="container mb-5">

        <div class="row mt-5 mb-4 align-items-center">
            <div class="col">
                <h3 class="fw-bold text-dark"><i class="bi bi-person-vcard-fill text-primary"></i> ข้อมูลผู้ลงทะเบียน
                </h3>
                <p class="text-muted mb-0">ตรวจสอบสถานะและดำเนินการสมัครเรียน</p>
            </div>
        </div>

        <div class="card card-info w-100 mb-4">
            <div class="card-body p-4">
                <?php
                $sql = "SELECT * FROM round WHERE date_round <= :now ORDER BY date_round DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':now', $now);
                $stmt->execute();
                $round = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-primary mb-2">รอบการรับสมัครปัจจุบัน</span>
                        <h4 class="card-title fw-bold text-primary mb-3"><?= $round['name_round'] ?></h4>

                        <div class="d-flex flex-column flex-md-row gap-3 text-secondary">
                            <div>
                                <i class="bi bi-person-fill"></i>
                                <span
                                    class="fw-bold text-dark"><?= $result['prefix'] . $result['fname'] . ' ' . $result['lname'] ?></span>
                            </div>
                            <div class="d-none d-md-block">|</div>
                            <div>
                                <i class="bi bi-telephone-fill"></i>
                                <?= $result['tel'] ?>
                            </div>
                        </div>
                    </div>
                    <i class="bi bi-calendar-check text-black-50" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-custom mb-0">
                        <thead class="text-uppercase">
                            <tr>
                                <th scope="col" class="py-3 ps-4" style="width: 80px;">ขั้นตอน</th>
                                <th scope="col" class="py-3">รายการดำเนินการ</th>
                                <th scope="col" class="py-3">สถานะ / รายละเอียด</th>
                                <th scope="col" class="py-3 pe-4 text-end" style="width: 200px;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4"><span class="step-number">1</span></td>
                                <td class="fw-bold">กรอกข้อมูลส่วนตัว</td>
                                <td>
                                    <?php
                                    if (
                                        empty($result['id_card']) || empty($result['prefix']) || empty($result['fname']) || empty($result['lname']) ||
                                        empty($result['b_day']) || empty($result['m_day']) || empty($result['y_day']) || empty($result['age']) ||
                                        empty($result['tel']) || empty($result['h_number']) || empty($result['road']) || empty($result['A_district']) || empty($result['S_district']) || empty($result['District'])
                                    ) {
                                        echo '<span class="badge bg-danger rounded-pill"><i class="bi bi-exclamation-circle"></i> ข้อมูลยังไม่ครบถ้วน</span>';
                                    } else {
                                        echo '<span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i> ครบถ้วนแล้ว</span>';
                                    }
                                    ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="register.php?id_card=<?= $result['id_card'] ?>"
                                        class="btn btn-outline-warning btn-sm w-100">
                                        <i class="bi bi-pencil-square"></i> แก้ไขข้อมูล
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td class="ps-4"><span class="step-number">2</span></td>
                                <td class="fw-bold">เลือกสาขาวิชา</td>
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
                                        ?>
                                        <div class="text-dark" style="font-size: 0.95rem;">
                                            <div><strong class="text-primary">อันดับ 1:</strong>
                                                <?= $depart1['name_depart'] ?></div>
                                            <div><strong class="text-secondary">อันดับ 2:</strong>
                                                <?= (isset($depart2['name_depart']) && !empty($depart2['name_depart'])) ? $depart2['name_depart'] : "-" ?>
                                            </div>
                                        </div>
                                    <?php } else {
                                        echo '<span class="text-muted fst-italic">ยังไม่ได้เลือกสาขาวิชา</span>';
                                    }
                                    ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <?php if ($result['depart_major'] && $result['depart_minor']) { ?>
                                        <a href="#" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="confirmDeletion('<?php echo $result['id_card']; ?>')">
                                            <i class="bi bi-x-circle"></i> ยกเลิกการสมัคร
                                        </a>
                                    <?php } else { ?>
                                        <a href="reg_mtc.php?id_card=<?= $result['id_card'] ?>"
                                            class="btn btn-primary btn-sm w-100 shadow-sm">
                                            <i class="bi bi-cursor-fill"></i> สมัครเรียน
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>

                            <tr>
                                <td class="ps-4"><span class="step-number">3</span></td>
                                <td class="fw-bold">ประเภทและหลักฐาน</td>
                                <td>
                                    <?php if (!$result['type_reg']) {
                                        echo '<span class="badge bg-secondary text-light"><i class="bi bi-hourglass"></i> รอการดำเนินการ</span>';
                                    } else {
                                        echo '<div class="text-success fw-bold mb-1"><i class="bi bi-check-circle-fill"></i> ' . $result['type_reg'] . '</div>';

                                        if ($result['image']) {
                                            echo '<a href="system_reg/uploads/' . $result['image'] . '" target="_blank" class="text-decoration-none small text-muted"><i class="bi bi-file-earmark-image"></i> ดูไฟล์แนบ</a>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <button type="button" class="btn btn-primary btn-sm w-100 shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        <i class="bi bi-upload"></i> เลือก/อัปโหลด
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-cloud-upload"></i>
                        เลือกประเภทและหลักฐาน</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="myForm" action="system_reg/reg_update.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_card" value="<?= $result['id_card'] ?>" type="text">

                        <div class="mb-4">
                            <label for="typeSelect" class="form-label fw-bold">ประเภทการสมัคร</label>
                            <select required class="form-select bg-light" id="typeSelect" name="type">
                                <option disabled value="" <?= (!$result['type_reg']) ? 'selected' : '' ?>>--
                                    กรุณาเลือกประเภท --</option>
                                <option value="ประเภทผู้มีการเรียนดี" <?= ($result['type_reg'] == 'ประเภทผู้มีการเรียนดี') ? 'selected' : '' ?>>ประเภทผู้มีการเรียนดี</option>
                                <option value="ประเภทผู้มีความสามารถด้านทักษะวิชาการ"
                                    <?= ($result['type_reg'] == 'ประเภทผู้มีความสามารถด้านทักษะวิชาการ') ? 'selected' : '' ?>>ประเภทผู้มีความสามารถด้านทักษะวิชาการ</option>
                                <option value="ประเภทผู้มีความสามารถด้านกีฬา"
                                    <?= ($result['type_reg'] == 'ประเภทผู้มีความสามารถด้านกีฬา') ? 'selected' : '' ?>>
                                    ประเภทผู้มีความสามารถด้านกีฬา / กิจกรรมเด่น</option>
                                <option value="ประเภทผู้มีความสามารถด้านศิลปวัฒนธรรม"
                                    <?= ($result['type_reg'] == 'ประเภทผู้มีความสามารถด้านศิลปวัฒนธรรม') ? 'selected' : '' ?>>ประเภทผู้มีความสามารถด้านศิลปวัฒนธรรม</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="fileUpload" class="form-label fw-bold">หลักฐาน (เกียรติบัตร/ใบเกรด)</label>
                            <div class="input-group">
                                <input class="form-control" type="file" id="fileUpload" name="file">
                            </div>
                            <div class="form-text text-muted mt-2">
                                <i class="bi bi-info-circle"></i> อัพโหลดได้เพียงรูปเดียว (JPG, PNG)
                            </div>

                            <?php if ($result['image']): ?>
                                <div class="alert alert-secondary mt-3 d-flex align-items-center" role="alert">
                                    <i class="bi bi-image me-2"></i>
                                    <div class="text-truncate">
                                        ไฟล์ปัจจุบัน: <a href="system_reg/uploads/<?= $result['image'] ?>" target="_blank"
                                            class="fw-bold"><?= $result['image'] ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success px-4" name="uploadData" form="myForm"><i
                            class="bi bi-save"></i> บันทึกข้อมูล</button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDeletion(id) {
            Swal.fire({
                title: 'ยืนยันการยกเลิก?',
                text: "คุณต้องการยกเลิกการสมัครสาขานี้ใช่หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ยกเลิกเลย',
                cancelButtonText: 'ไม่ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'system_reg/unreg.php?id=' + id;
                }
            })
        }
    </script>
</body>

</html>