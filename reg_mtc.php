<?php
include 'components/navbar.php';
include 'components/head.php';
include 'components/footer.php';
include 'components/sessionShow.php';
require_once 'config/db.php';
$now = date("Y-m-d");
$conn = connectDB();
?>

<!doctype html>
<html lang="en">

<head>
    <?php Head(); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-filter {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .table-card {
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background-color: #0d6efd;
            color: white;
            border: none;
            font-weight: 500;
        }

        .btn-back {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background-color: #e9ecef;
            transform: translateX(-5px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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
        $lasted_class = $result['lasted_class'];
        if (!$result['id_card']) {
            $_SESSION['error'] = "ไม่พบข้อมูล";
            echo "<script>window.location.href='login.php';</script>";
            exit;
        }

    } else {
        $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
        echo "<script>window.location.href='login.php';</script>";
        exit;
    }
    ?>
    <main class="container py-5">

        <div class="d-flex align-items-center mb-4">
            <a href="main_reg.php?id_card=<?= $id_card ?>" class="btn btn-light text-primary btn-back me-3 shadow-sm">
                <i class="bi bi-arrow-left"></i> ย้อนกลับ
            </a>
            <div>
                <h3 class="fw-bold mb-0 text-dark">รายชื่อสาขาวิชาที่เปิดรับ</h3>
                <p class="text-muted mb-0">เลือกสาขาที่ท่านต้องการสมัครเรียน</p>
            </div>
        </div>

        <?php
        if (isset($_GET['level'])) {
            $level = $_GET['level'];
            ?>

            <div class="card card-filter mb-4">
                <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between p-4">
                    <div>
                        <h5 class="mb-1 fw-bold text-primary"><i class="bi bi-mortarboard-fill"></i> แผนการเรียนปัจจุบัน:
                            <u><?= $level ?></u>
                        </h5>
                        <small class="text-muted">ท่านกำลังดูรายวิชาสำหรับแผนการเรียนนี้</small>
                    </div>
                    <div class="mt-3 mt-md-0" style="min-width: 250px;">
                        <select class="form-select form-select-lg border-primary" name="level" id="level">
                            <option disabled value="" <?php echo empty($selected_level) ? 'selected' : ''; ?>>
                                เปลี่ยนแผนการเรียน</option>
                            <?php
                            $selected_level = isset($_GET['level']) ? $_GET['level'] : '';
                            $options = ['ปกติ', 'ทวิภาคี'];
                            foreach ($options as $option) {
                                $selected = ($option === $selected_level) ? 'selected' : '';
                                echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card table-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="reg" class="table table-hover align-middle mb-0">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" style="width: 5%">#</th>
                                    <th scope="col" style="width: 25%" class="text-start ps-4">ชื่อสาขา</th>
                                    <th scope="col" style="width: 20%">แผนก</th>
                                    <th scope="col" style="width: 10%">แผน</th>
                                    <th scope="col" style="width: 10%">รับ (คน)</th>
                                    <th scope="col" style="width: 15%">วุฒิที่รับ</th>
                                    <th scope="col" style="width: 15%">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT rs.*, dp.*, mp.*, rd.*
                                FROM reg_show AS rs
                                INNER JOIN department AS dp ON rs.depart_ref = dp.id_depart
                                INNER JOIN major_depart AS mp ON dp.id_major_ref = mp.id_major
                                INNER JOIN round AS rd ON rs.round_ref = rd.id_round
                                WHERE dp.class = :class AND :now BETWEEN date_round AND end_round AND dp.level = :level";

                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':class', $lasted_class);
                                $stmt->bindParam(':now', $now);
                                $stmt->bindParam(':level', $level);
                                $stmt->execute();
                                $show_reg = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                $count = 0;
                                foreach ($show_reg as $d) {
                                    $count++;
                                    ?>
                                    <tr>
                                        <th scope="row" class="text-center"><?= $count ?></th>
                                        <td class="fw-bold text-primary ps-4"><?= $d['name_depart'] ?></td>
                                        <td class="text-center"><?= $d['name_major'] ?></td>
                                        <td class="text-center"><span class="badge bg-info text-dark"><?= $d['level'] ?></span>
                                        </td>
                                        <td class="text-center fw-bold"><?= $d['total'] ?></td>
                                        <td class="text-center text-muted small"><?= $d['class'] ?></td>
                                        <td class="text-center">
                                            <form action="system_reg/reg_mtc.php" method="post">
                                                <input type="hidden" name="id_card" value="<?= $id_card ?>">
                                                <input type="hidden" name="class_assign" value="<?= $d['class'] ?>">
                                                <input type="hidden" name="round_assign" value="<?= $d['id_round'] ?>">
                                                <input type="hidden" name="level" value="<?= $level ?>">

                                                <?php if ($result['depart_major'] == null || $result['depart_major'] == "") { ?>
                                                    <input type="hidden" name="depart_major" value="<?= $d['id_depart'] ?>">
                                                    <button id="submit" name="submit"
                                                        class="btn btn-success btn-sm w-100 shadow-sm">
                                                        <i class="bi bi-1-circle-fill"></i> เลือกอันดับ 1
                                                    </button>
                                                <?php } else if ($result['depart_minor'] == "" || $result['depart_minor'] == null) { ?>

                                                        <input type="hidden" name="depart_minor" value="<?= $d['id_depart'] ?>">
                                                        <button id="submit2" name="submit2"
                                                            class="btn btn-warning btn-sm w-100 shadow-sm text-dark">
                                                            <i class="bi bi-2-circle-fill"></i> เลือกอันดับ 2
                                                        </button>

                                                <?php } else { ?>
                                                        <button disabled class="btn btn-secondary btn-sm w-100">ครบจำนวน</button>
                                                <?php } ?>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php } else { ?>

            <div class="empty-state">
                <div class="mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 100px; height: 100px;">
                        <i class="bi bi-journal-bookmark-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark">กรุณาเลือกแผนการเรียน</h3>
                <p class="text-muted mb-4">เลือกแผนการเรียน "ปกติ" หรือ "ทวิภาคี" เพื่อแสดงรายชื่อสาขาที่เปิดรับ</p>

                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <select class="form-select form-select-lg shadow-sm border-primary" name="level" id="level">
                            <option disabled value="" <?php echo empty($selected_level) ? 'selected' : ''; ?>>--
                                คลิกเพื่อเลือกแผนการเรียน --</option>
                            <?php
                            $selected_level = isset($_GET['level']) ? $_GET['level'] : '';
                            $options = ['ปกติ', 'ทวิภาคี'];
                            foreach ($options as $option) {
                                $selected = ($option === $selected_level) ? 'selected' : '';
                                echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

        <?php } ?>

    </main>

    <footer>
        <?php Footer() ?>
    </footer>

    <script>
        document.getElementById('level').addEventListener('change', function () {
            var id2 = this.value;
            if (id2 !== '') {
                window.location.href = '?id_card=<?= $id_card ?>&level=' + id2;
            }
        });
    </script>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#reg').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json"
                },
                "pageLength": 25, // แสดง 25 แถวต่อหน้า
                "lengthMenu": [10, 25, 50, 100]
            });
        });
    </script>
</body>

</html>