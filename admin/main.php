<?php
include '../components/navbar.php'; // *หมายเหตุ: ถ้าในไฟล์นี้มี code navbar เก่า ให้ไปแก้ในไฟล์นี้ด้วย หรือใช้ code ด้านล่างแทนการเรียกฟังก์ชัน
include '../components/head.php';
include '../components/footer.php';
include '../components/sessionShow.php';
include '../config/db.php';

$conn = connectDB();

// 1. ตรวจสอบสิทธิ์ Admin
if (isset($_SESSION['admin_log'])) {
    $username1 = $_SESSION['admin_log'];
    $sql = "SELECT status FROM admin WHERE username = :username"; // เลือกแค่ status ก็พอ
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":username", $username1);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // เสริม: เช็คว่ามี user นี้จริงไหม
    if (!$result) {
        $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้งาน';
        header('location: login.php');
        exit;
    }
} else {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('location: login.php');
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head_admin(); ?>

</head>

<body>
    <?php showStatus_admin(); ?>

    <div class="d-md-none p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-bold">ระบบรับสมัคร</span>
        <button class="btn btn-primary btn-toggle-sidebar" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="bi bi-list"></i> เมนู
        </button>
    </div>

    <div class="d-flex">

        <?php Navbar_admin(); ?>

        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <?php
                        // Logic การดึงรอบสมัคร (เหมือนเดิม ดีแล้วครับ)
                        $round_id = null;
                        $round_name = "";

                        if (isset($_GET['round']) && !empty($_GET['round'])) {
                            $round = $_GET['round'];
                            $sql = "SELECT * FROM round WHERE id_round = :id";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(":id", $round);
                            $stmt->execute();
                            $r_data = $stmt->fetch(PDO::FETCH_ASSOC);
                        } else {
                            // ดึงรอบล่าสุด
                            $sql = "SELECT * FROM round ORDER BY id_round DESC LIMIT 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $r_data = $stmt->fetch(PDO::FETCH_ASSOC);
                        }

                        if ($r_data) {
                            $round_name = $r_data['name_round'];
                            $round_id = $r_data['id_round'];
                        }
                        ?>

                        <h2 class="mb-4 mt-3">สรุปข้อมูลผู้สมัคร <span class="text-primary"><?= $round_name ?></span>
                        </h2>

                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <label for="round" class="form-label">เลือกรอบการสมัคร:</label>
                                <select class="form-select" name="" id="round">
                                    <option selected disabled value="">-- กรุณาเลือก --</option>
                                    <?php
                                    $sql = "SELECT id_round, name_round FROM round ORDER BY id_round DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $rounds = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($rounds as $d) {
                                        $selected = ($d['id_round'] == $round_id) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $d['id_round'] ?>" <?= $selected ?>><?= $d['name_round'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <script>
                            document.getElementById('round').addEventListener('change', function () {
                                var id = this.value;
                                if (id !== '') {
                                    window.location.href = '?round=' + id;
                                }
                            });
                        </script>

                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="departmentsTable" class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>แผนก</th>
                                                <th>สาขา</th>
                                                <th class="text-center">จำนวน (คน)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($round_id) {
                                                // SQL ที่ Optimized แล้ว
                                                $sql = "SELECT 
                                                            depart.name_depart, 
                                                            major.name_major, 
                                                            COUNT(user.id_reg) AS count
                                                        FROM u_reg AS user
                                                        INNER JOIN department AS depart ON user.depart_major = depart.id_depart
                                                        INNER JOIN major_depart AS major ON depart.id_major_ref = major.id_major
                                                        WHERE user.round_assign = :round AND user.status = 1
                                                        GROUP BY depart.id_depart, depart.name_depart, major.name_major
                                                        ORDER BY depart.id_depart ASC"; // เรียงลำดับหน่อยจะได้สวยงาม
                                            
                                                $stmt = $conn->prepare($sql);
                                                $stmt->bindParam(":round", $round_id);
                                                $stmt->execute();
                                                $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                if (count($departments) > 0) {
                                                    foreach ($departments as $department) { ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($department['name_depart']) ?></td>
                                                            <td><?= htmlspecialchars($department['name_major']) ?></td>
                                                            <td class="text-center fw-bold text-primary"><?= $department['count'] ?>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } else { ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-4">
                                                            ยังไม่มีผู้สมัครในรอบนี้</td>
                                                    </tr>
                                                <?php }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-auto py-3 bg-light text-center">
        <div class="container">
            <?php Footer() ?>
        </div>
    </footer>

    <script src="../css/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>