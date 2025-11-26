<?php
include 'components/navbar.php';
include 'components/head.php';
include 'components/footer.php';
require_once 'config/db.php';
include 'components/sessionShow.php';
unset($_SESSION['user_log']);
$conn = connectDB();
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background-color: #f8f9fa;
        }

        .main-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: white;
            overflow: hidden;
        }

        .status-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .status-card:hover {
            transform: translateY(-5px);
        }

        .btn-check {
            background-color: #0d6efd;
            color: white;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 600;
            z-index: 8000;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
            transition: all 0.3s;
        }

        .btn-check:hover {
            background-color: #0b5ed7;
            transform: scale(1.05);
        }

        .icon-status {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1rem;
        }

        .form-control-lg {
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 1.1rem;
        }

        .input-group-text {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            background-color: #e9ecef;
        }
    </style>
</head>

<body>
    <?php showStatus(); ?>
    <header class="sticky-top shadow-sm">
        <?php Navbar(); ?>
    </header>
    <main class="container py-5">

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="text-center mb-4">
                    <img src="image/logo/logo.png" alt="Logo" width="100" height="100" class="mb-3">
                    <h2 class="fw-bold text-dark">ตรวจสอบสถานะการสมัคร</h2>
                    <p class="text-muted">กรอกเลขบัตรประชาชนเพื่อตรวจสอบผลการคัดเลือก</p>
                </div>

                <div class="card main-card mb-5">
                    <div class="card-body p-4 p-md-5">
                        <form action="system_reg/check.php" method="post">
                            <?php
                            $now = date('Y-m-d');
                            $sql = "SELECT * FROM round WHERE :now BETWEEN date_round AND end_round";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':now', $now);
                            $stmt->execute();
                            $round = $stmt->fetch(PDO::FETCH_ASSOC);

                            if (!$round) { ?>
                                <div class="row g-3 align-items-center justify-content-center">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text"><i
                                                    class="fa-solid fa-id-card text-secondary"></i></span>
                                            <input minlength="13" maxlength="13" required type="text"
                                                class="form-control form-control-lg" name="id_card"
                                                placeholder="เลขบัตรประชาชน 13 หลัก">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center text-md-start">
                                        <button type="submit" name="submit" class="btn btn-primary w-100">
                                            <i class="fa-solid fa-magnifying-glass me-2"></i> ตรวจสอบ
                                        </button>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-warning text-center border-0 bg-warning bg-opacity-10 text-warning mb-0"
                                    role="alert">
                                    <i class="fa-solid fa-clock me-2"></i> <span
                                        class="fw-bold">ยังไม่ถึงกำหนดเวลาประกาศผล</span> หรือหมดเขตตรวจสอบแล้ว
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>

                <?php if (isset($_GET['pass'])) {
                    $id_card = $_GET['id_card'];
                    $sql = "SELECT * FROM u_reg AS user INNER JOIN round AS r ON user.round_assign = r.id_round WHERE id_card = :id_card";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id_card', $id_card);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$user) {
                        $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
                        echo "<script>window.location.href='checkStatus.php';</script>";
                        exit;
                    }
                    ?>
                    <div class="card status-card border-success border-2 border-top-0 border-end-0 border-bottom-0">
                        <div class="card-body p-5 text-center">
                            <img src="image/pass.png" alt="Passed" class="icon-status">
                            <h2 class="text-success fw-bold mb-3">ยินดีด้วย! คุณผ่านการคัดเลือก</h2>
                            <h5 class="text-dark mb-4">
                                คุณ <?= $user['prefix'] . $user['fname'] . ' ' . $user['lname'] ?> <br>
                                <span class="badge bg-success mt-2"><?= $user['name_round'] ?></span>
                            </h5>

                            <hr class="w-50 mx-auto my-4">

                            <a href="print.php?id_card=<?= $user['id_card'] ?>" target="_blank"
                                class="btn btn-outline-success btn-lg shadow-sm">
                                <i class="fa-solid fa-print me-2"></i> พิมพ์ใบรายงานตัว
                            </a>
                        </div>
                    </div>

                <?php } else if (isset($_GET['not_pass'])) {
                    $id_card = $_GET['id_card'];
                    $sql = "SELECT * FROM u_reg AS user INNER JOIN round AS r ON user.round_assign = r.id_round WHERE id_card = :id_card";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id_card', $id_card);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$user) {
                        $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
                        echo "<script>window.location.href='checkStatus.php';</script>";
                        exit;
                    }
                    ?>
                        <div class="card status-card border-danger border-2 border-top-0 border-end-0 border-bottom-0">
                            <div class="card-body p-5 text-center">
                                <img src="image/notpass.png" alt="Not Passed" class="icon-status">
                                <h3 class="text-danger fw-bold mb-3">ขอแสดงความเสียใจ</h3>
                                <p class="fs-5 text-muted">
                                    คุณไม่ผ่านการคัดเลือกใน <?= $user['name_round'] ?>
                                </p>
                                <div class="alert alert-light mt-4">
                                    <small class="text-muted">อย่างไรก็ตาม ขอให้คุณพยายามต่อไปในรอบถัดไป (ถ้ามี)</small>
                                </div>
                            </div>
                        </div>

                <?php } else if (isset($_GET['error'])) { ?>
                            <div class="alert alert-danger d-flex align-items-center justify-content-center p-4 shadow-sm rounded-3"
                                role="alert">
                                <i class="fa-solid fa-circle-exclamation fa-2x me-3"></i>
                                <div>
                                    <h4 class="alert-heading fw-bold mb-1">ไม่พบข้อมูลการสมัคร</h4>
                                    <p class="mb-0">กรุณาตรวจสอบเลขบัตรประชาชนใหม่อีกครั้ง</p>
                                </div>
                            </div>
                <?php } ?>

            </div>
        </div>

    </main>

    <footer class="mt-auto">
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>