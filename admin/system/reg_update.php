<?php
require_once '../../config/db.php';
$conn = connectDB();

if (isset($_POST['pass'])) {
    $id_card = $_GET['id_card'];
    $round = $_GET['round'];
    $sql = "UPDATE u_reg SET status = 1 WHERE id_card = :id_card";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_card', $id_card);

    if ($stmt->execute()) {
        $_SESSION['success'] = "อนุมัติผู้สมัครเรียบร้อยแล้ว";
        header("location: ../dataStudent.php?round=$round");
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาดในการอนุมัติผู้สมัคร";
        header("location: ../dataStudent.php?round=$round");
    }
}
if (isset($_POST['not_pass'])) {
    $id_card = $_GET['id_card'];
    $round = $_GET['round'];
    $sql = "UPDATE u_reg SET status = 2 WHERE id_card = :id_card";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_card', $id_card);

    if ($stmt->execute()) {
        $_SESSION['success'] = "ไม่อนุมัติผู้สมัครเรียบร้อยแล้ว";
        header("location: ../dataStudent.php?round=$round");
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาดในการอนุมัติผู้สมัคร";
        header("location: ../dataStudent.php?round=$round");
    }
}
?>