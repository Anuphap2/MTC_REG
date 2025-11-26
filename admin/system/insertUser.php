<?php
require_once '../../config/db.php';
$conn = connectDB();

if (isset($_POST['insertUser'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $status = 0;

    $sql = "SELECT * FROM admin WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $_SESSION['error'] = "ชื่อผู้ใช้งานนี้มีอยู่แล้ว";
        header("location: ../addUser.php");
        exit();
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO admin (username, password,status) VALUES (:username, :password,:status)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            $_SESSION['success'] = "เพิ่มข้อมูลสำเร็จ";
            header("location: ../addUser.php");
        } else {
            $_SESSION['error'] = "พบข้อผิดพลาดในการเพิ่มข้อมูล";
            header("location: ../addUser.php");
        }
    }


}
