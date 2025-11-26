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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
        $lasted_class = $result['lasted_class'];
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
        <h3 class="mt-5"><a href="main_reg.php?id_card=<?= $id_card ?>">
                << </a>&nbsp;รายชื่อวิชาสาขาที่เปิดรับสมัคร</h3>
        <hr>


        <?php
        if (isset($_GET['level'])) {
            $level = $_GET['level'];
            ?>

            <select class="form-select mt-3 mb-3" name="level" id="level">
                <option disabled value="" <?php echo empty($selected_level) ? 'selected' : ''; ?>>เลือกแผนการเรียน
                </option>
                <?php
                // ตรวจสอบว่ามีการส่งค่าผ่าน URL หรือไม่
                $selected_level = isset($_GET['level']) ? $_GET['level'] : '';

                // ตัวเลือกปกติ
                $options = ['ปกติ', 'ทวิภาคี'];

                foreach ($options as $option) {
                    // ถ้าค่าที่ได้จาก URL ตรงกับตัวเลือก ให้ตั้ง selected
                    $selected = ($option === $selected_level) ? 'selected' : '';
                    echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                }
                ?>
            </select>
            <h1>แผนการเรียน <u><?= $level ?></u></h1>
            <table id="reg" class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">ชื่อสาขา</th>
                        <th scope="col">แผนก</th>
                        <th scope="col">แผนการเรียน</th>
                        <th scope="col">จำนวน</th>
                        <th scope="col">วุฒิการศึกษาที่รับ</th>
                        <th scope="col">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT rs.*, dp.*, mp.*, rd.*
               FROM reg_show AS rs
               INNER JOIN department AS dp ON rs.depart_ref = dp.id_depart
               INNER JOIN major_depart AS mp ON dp.id_major_ref = mp.id_major
               INNER JOIN round AS rd ON rs.round_ref = rd.id_round
               WHERE dp.class = :class AND :now BETWEEN date_round AND end_round AND dp.level = :level
               ";

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
                            <th scope="row"><?= $count ?></th>
                            <td><?= $d['name_depart'] ?></td>
                            <td><?= $d['name_major'] ?></td>
                            <td><?= $d['level'] ?></td>
                            <td><?= $d['total'] ?></td>
                            <td><?= $d['class'] ?></td>
                            <td>

                                <form action="system_reg/reg_mtc.php" method="post">
                                    <input type="hidden" name="id_card" value="<?= $id_card ?>">
                                    <input type="hidden" name="class_assign" value="<?= $d['class'] ?>">
                                    <input type="hidden" name="round_assign" value="<?= $d['id_round'] ?>">
                                    <input type="hidden" name="level" value="<?= $level ?>">
                                    <?php if ($result['depart_major'] == null || $result['depart_major'] == "") { ?>
                                        <input type="hidden" name="depart_major" value="<?= $d['id_depart'] ?>">
                                        <button id="submit" name="submit" class="btn btn-primary">สมัครเรียนอันดับ 1</button>
                                    <?php } else if ($result['depart_minor'] == "" || $result['depart_minor'] == null) { ?>

                                            <input type="hidden" name="depart_minor" value="<?= $d['id_depart'] ?>">
                                            <button id="submit2" name="submit2" class="btn btn-primary">สมัครเรียนอันดับ 2</button>

                                    <?php }

                                    ?>

                                
                                </form>
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert text-center fs-2  alert-danger">กรุณาเลือกแผนการเรียนที่ต้องการสมัคร

                <select class="form-select mt-3 mb-3" name="level" id="level">
                    <option disabled value="" <?php echo empty($selected_level) ? 'selected' : ''; ?>>เลือกแผนการเรียน
                    </option>
                    <?php
                    // ตรวจสอบว่ามีการส่งค่าผ่าน URL หรือไม่
                    $selected_level = isset($_GET['level']) ? $_GET['level'] : '';

                    // ตัวเลือกปกติ
                    $options = ['ปกติ', 'ทวิภาคี'];

                    foreach ($options as $option) {
                        // ถ้าค่าที่ได้จาก URL ตรงกับตัวเลือก ให้ตั้ง selected
                        $selected = ($option === $selected_level) ? 'selected' : '';
                        echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                    }
                    ?>
                </select>

            </div>







        <?php }
        ?>

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
                }
            });
        });

    </script>
</body>

</html>