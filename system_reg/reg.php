<?php
require_once '../config/db.php';
$conn = connectDB();
if (isset($_POST['submit'])) {
    $id_card = $_POST['id_card'];

    $now = date("Y-m-d");

    $sql = "SELECT * FROM round WHERE :now BETWEEN date_round AND end_round";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':now', $now);
    $stmt->execute();
    $round = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$round) {
        $_SESSION['error'] = "หมดเวลาสมัครเรียน";
        header("location: ../login.php");
        exit();
    } else {
        $sql = "SELECT * FROM u_reg WHERE id_card = :id_card";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_card', $id_card);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $_SESSION['user_log'] = $result['id_card'];
            header("location: ../main_reg.php?id_card=$id_card");
        } else {
            $_SESSION['user_log'] = $result['id_card'];
            $_SESSION['success'] = "เข้าสู่ระบบสมัครเรียนสำเร็จ";
            insertData("u_reg", ["id_card" => $id_card]);
            header("location: ../register.php?id_card=$id_card");
        }
    }


}
?>