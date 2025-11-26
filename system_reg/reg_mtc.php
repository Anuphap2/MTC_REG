<?php
require_once '../config/db.php';
$conn = connectDB();

if (isset($_POST['submit'])) {
    $id_card = $_POST['id_card'];
    $class_assign = $_POST['class_assign'];
    $round_assign = $_POST['round_assign'];
    $depart_major = $_POST['depart_major'];
    $level = $_POST['level'];
    $status = 1; // สมัครแล้ว

    $sql = "UPDATE u_reg 
    SET round_assign = :round_assign, 
    depart_major = :depart_major, 
    class_assign = :class_assign,
    status = :status
    WHERE id_card = :id_card";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_card', $id_card);
    $stmt->bindParam(':round_assign', $round_assign);
    $stmt->bindParam(':depart_major', $depart_major);
    $stmt->bindParam(':class_assign', $class_assign);
    $stmt->bindParam(':status', $status);
    $stmt->execute();

    $_SESSION['success'] = "สมัครเรียนสาขาอันดับ 1 เสร็จสิ้น";
    header("location: ../reg_mtc.php?id_card=$id_card&level=$level");
}

if (isset($_POST['submit2'])) {
    $id_card = $_POST['id_card'];
    $depart_minor = $_POST['depart_minor'];
    $level = $_POST['level'];

    $status = 1;


    $sql = "UPDATE u_reg 
    SET
    depart_minor = :depart_minor
    WHERE id_card = :id_card";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_card', $id_card);
    $stmt->bindParam(':depart_minor', $depart_minor);
    $stmt->execute();

    $_SESSION['success'] = "สมัครเรียนสาขาอันดับ 2 เสร็จสิ้น";
    header("location: ../main_reg.php?id_card=$id_card&level=$level");
}
?>