<?php
include '../components/navbar.php';
include '../components/head.php';
include '../components/footer.php';
include '../components/sessionShow.php';
session_start();
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head_admin(); ?>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 350px;
            border-radius: 20px;
        }

        .shadows {
            box-shadow: -6px 7px 34px 1px rgba(0, 0, 0, 0.48);
            -webkit-box-shadow: -6px 7px 34px 1px rgba(0, 0, 0, 0.48);
            -moz-box-shadow: -6px 7px 34px 1px rgba(0, 0, 0, 0.48);
        }
    </style>
</head>

<body>
    <?php showStatus_admin(); ?>

    <div class="container p-5 shadows mt-5 mb-5 login-container">
        <div class="d-flex justify-content-center mb-3">
            <img width="150px" height="100%" src="../image/logo/logo.png" alt="">
        </div>
        <h2 class="text-center">เข้าสู่ระบบ</h2>


        <form action="system/LoginSystem.php" method="POST">
            <div class="form-floating mb-3">
                <input required type="text" class="form-control" id="floatingInput" name="username"
                    placeholder="name@example.com">
                <label for="floatingInput">ผู้ใช้งาน</label>
            </div>
            <div class="form-floating mb-3">
                <input required type="password" class="form-control" id="floatingPassword" name="password"
                    placeholder="Password">
                <label for="floatingPassword">รหัสผ่าน</label>
            </div>
            <div class="d-grid gap-3">
                <button type="submit" name="submit" class="btn btn-lg btn-primary p-3">เข้าสู่ระบบ</button>
            </div>
        </form>
    </div>

    <footer class="fixed-bottom">
        <?php Footer() ?>
    </footer>
    <script src="../css/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>