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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: white;
            overflow: hidden;
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
            border: 1px solid #ced4da;
        }

        .btn-login {
            background-color: #0d6efd;
            color: white;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            color: white;
        }

        .btn-disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
            color: white;
            border-radius: 50px;
            padding: 12px 30px;
        }
    </style>
</head>

<body>
    <?php showStatus(); ?>
    <header class="sticky-top shadow-sm">
        <?php Navbar(); ?>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
            <div class="col-lg-5 col-md-8">

                <div class="card login-card p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="image/logo/logo.png" alt="Logo" width="100" height="100" class="mb-3">
                            <h3 class="fw-bold text-dark">เข้าสู่ระบบรับสมัคร</h3>
                            <p class="text-muted">สำหรับนักเรียนนักศึกษาใหม่</p>
                        </div>

                        <?php if (!$round) { ?>
                            <div class="alert alert-danger text-center border-0 bg-danger bg-opacity-10 text-danger mb-4"
                                role="alert">
                                <i class="fa-solid fa-circle-xmark me-2"></i> ขณะนี้ปิดรับสมัคร หรืออยู่นอกเวลาทำการ
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-success text-center border-0 bg-success bg-opacity-10 text-success mb-4"
                                role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i> เปิดรับสมัคร:
                                <strong><?= $round['name_round'] ?></strong>
                            </div>
                        <?php } ?>

                        <form action="system_reg/reg.php" method="post">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">เลขบัตรประชาชน</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-secondary">
                                        <i class="fa-solid fa-id-card"></i>
                                    </span>
                                    <?php if (!$round) { ?>
                                        <input disabled minlength="13" maxlength="13" required type="text"
                                            class="form-control form-control-lg bg-light"
                                            placeholder="ระบบปิดรับสมัครชั่วคราว">
                                    <?php } else { ?>
                                        <input minlength="13" maxlength="13" required type="text"
                                            class="form-control form-control-lg" name="id_card"
                                            placeholder="กรอกเลขบัตร 13 หลัก" autofocus>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="d-grid">
                                <?php if (!$round) { ?>
                                    <button type="button" class="btn btn-disabled w-100" disabled>
                                        <i class="fa-solid fa-lock me-2"></i> ไม่สามารถสมัครได้
                                    </button>
                                <?php } else { ?>
                                    <button type="submit" name="submit" class="btn btn-login w-100">
                                        <i class="fa-solid fa-right-to-bracket me-2"></i> สมัครเรียน / เข้าสู่ระบบ
                                    </button>
                                <?php } ?>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none text-muted small">
                        <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้าหลัก
                    </a>
                </div>

            </div>
        </div>
    </main>

    <footer class="mt-auto">
        <?php Footer() ?>
    </footer>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>