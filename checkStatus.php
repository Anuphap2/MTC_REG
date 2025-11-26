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
</head>

<body>
    <!-- header start  -->
    <?php showStatus(); ?>
    <header>
        <?php Navbar(); ?>
    </header>
    <!-- header end  -->
    <main class="container">
        <div class="d-flex justify-content-center mt-3">
            <img src="image/logo/logo.png" alt="Logo" width="250" height="250" class="align-text-top">
        </div>
        <form action="system_reg/check.php" method="post">
            <div class="row gx-4 mt-5">

                <h3>เช็คสถานะ</h3>
                <div class="col-sm-12 col-md-8 col-lg-8">
                    <?php
                    $now = date('Y-m-d');
                    $sql = "SELECT * FROM round WHERE :now BETWEEN date_round AND end_round";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':now', $now);
                    $stmt->execute();
                    $round = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$round) { ?>
                        <div class="input-group mb-3">
                            <span class="input-group-text">เลขบัตรประชาชน</span>
                            <input minlength="13" maxlength="13" required type="text" class="form-control" name="id_card">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4 col-lg-4">
                        <div class="d-grid gap-3">
                            <button type="submit" name="submit" class="btn btn-primary">ตรวจสอบ</button>
                        </div>
                    </div>
                <?php } else { ?>
                    <h4 class="alert alert-danger">ยังไม่ถึงเวลาตรวจสอบ</h4>


                <?php }
                    ?>

            </div>
        </form>

        <?php
        if (isset($_GET['pass'])) {
            $id_card = $_GET['id_card'];
            $sql = "SELECT * 
            FROM u_reg AS user
            INNER JOIN round AS r ON user.round_assign = r.id_round
            WHERE id_card = :id_card";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_card', $id_card);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
                header("location: checkStatus.php");
            }
            ?>
            <div class="alert alert-success">
                <div class="d-flex justify-content-center mb-3">
                    <img width="100" height="100" src="image/pass.png" alt="">
                </div>
                <h1 class="text-center"><q><u>ยินดีด้วย<?= $user['prefix'] . $user['fname'] . ' ' . $user['lname'] ?>
                            คุณผ่านการคัดเลือก<?= $user['name_round'] ?></u></q></h1>
                <h3 class="text-center">
                    <a href="print.php?id_card=<?= $user['id_card'] ?>" target="_blank"><i
                            class="fa-solid fa-print fa-rotate-180 fa-lg" style="color: #000000;"></i> ปริ้นใบรายงานตัว</a>
                </h3>
            </div>
        <?php } else if (isset($_GET['not_pass'])) {
            $id_card = $_GET['id_card'];
            $sql = "SELECT * 
             FROM u_reg AS user
             INNER JOIN round AS r ON user.round_assign = r.id_round
             WHERE id_card = :id_card";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_card', $id_card);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
                header("location: checkStatus.php");
            }
            ?>
                <div class="alert alert-danger">
                    <div class="d-flex justify-content-center mb-3">
                        <img width="100" height="100" src="image/notpass.png" alt="">
                    </div>
                    <h1 class="text-center"><q><u>เสียใจด้วยคุณไม่ผ่านการคัดเลือก<?= $user['name_round'] ?></u></q></h1>

                </div>

        <?php } else if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger">
                        <h1 class="text-center">ไม่พบข้อมูล</h1>
                    </div>
        <?php }
        ?>


    </main>

    <footer>
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>