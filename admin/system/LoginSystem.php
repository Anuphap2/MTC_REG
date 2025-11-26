<?php
require_once '../../config/db.php';
$conn = connectDB();

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM admin WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() > 0) {
            if ($check['username'] == $username && password_verify($password, $check['password'])) {
                $_SESSION['admin_log'] = $check['username'];
                header('location: ../main.php');
            } else {
                $_SESSION['error'] = 'ชื่อผู้ใช้หรือรหัสผ่านผิด';
                header('location: ../index.php');
            }
        } else {
            $_SESSION['error'] = 'ไม่พบข้อมูลในระบบ';
            header('location: ../index.php');
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
