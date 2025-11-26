<?php
include '../components/navbar.php';
include '../components/head.php';
include '../components/footer.php';
include '../components/sessionShow.php';
include '../config/db.php';
$conn = connectDB();
if (isset($_SESSION['admin_log'])) {
    $username1 = $_SESSION['admin_log'];
    $sql = "SELECT * FROM admin WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":username", $username1);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('location: index.php');
}


if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $sql_delete = "DELETE FROM admin WHERE username = :username";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':username', $username);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
		header("location: addUser.php");
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
        header("location: addUser.php");

    }
}


?>

<!doctype html>
<html lang="en">

<head>
    <?php Head_admin(); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }

        .main-content {
            width: 100%;
            margin-left: 250px;
            /* ความกว้างของ sidebar */
            padding: 20px;
        }
    </style>
</head>

<body>
    <?php showStatus_admin(); ?>
    <div class="d-flex">

        <?php Navbar_admin(); ?>

        <div class="main-content">
            <div class="row gx-5">
                <div class="col-4">
                    <h1>จัดการข้อมูลเจ้าหน้าที่</h1>
                    <hr>
                    <form action="system/insertUser.php" method="post">
                        <div class="mb-3">
                            <label for="name_major" class="form-label">Username</label>
                            <input required type="text" class="form-control" name="username">
                        </div>
                        <div class="mb-3">
                            <label for="name_major" class="form-label">Password</label>
                            <input required type="password" class="form-control" name="password">
                        </div>

                        <button type="submit" name="insertUser" class="btn btn-primary">เพิ่มข้อมูล</button>
                    </form>
                </div>
                <div class="col-8">
                    <table id="majorTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM admin WHERE username != 'admin'";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $major = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($major as $d) { ?>
                                <tr>

                                    <td><?= $d['username']; ?></td>

                                    <td>

                                        <a href="#" class="btn btn-danger"
                                            onclick="confirmUser('<?php echo $d['username']; ?>')">ลบ</a>
                                    </td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
	
<script>
    function confirmUser(username) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?username=' + username;
            }
        })
    }
</script>

    <script>
        $(document).ready(function () {
            $('#departmentsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json"
                }
            });
        });
        $(document).ready(function () {
            $('#majorTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/th.json"
                }
            });
        });
    </script>
</body>

</html>