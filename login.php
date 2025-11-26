<?php
include 'components/navbar.php';
include 'components/head.php';
include 'components/footer.php';
require_once 'config/db.php';
include 'components/sessionShow.php';
unset($_SESSION['user_log']);
$conn = connectDB();
$now = date("Y-m-d");
$sql = "SELECT * FROM round WHERE :now BETWEEN date_round AND end_round"; #curdate() 
$stmt = $conn->prepare($sql);
$stmt->bindParam(':now', $now);
$stmt->execute();
$round = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head(); ?>

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
        <form action="system_reg/reg.php" method="post">
            <div class="row gx-4 mt-5">
                <div class="col-sm-12 col-md-8 col-lg-8">
                    <div class="input-group mb-3">
                        <span class="input-group-text">เลขบัตรประชาชน</span>
                        <?php
                        if (!$round) { ?>
                            <input disabled minlength="13" maxlength="13" required type="text" class="form-control" name="id_card">
                        <?php } else { ?>
                            <input minlength="13" maxlength="13" required type="text" class="form-control" name="id_card">
                        <?php } ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="d-grid gap-3">
                        <button type="submit" name="submit" class="btn btn-primary">สมัครเรียน</button>
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