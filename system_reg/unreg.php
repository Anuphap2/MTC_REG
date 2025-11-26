<?php
require_once '../config/db.php';
$conn = connectDB();

if (isset($_GET['id'])) {
    $id_card = $_GET['id'];
    $class_assign = null;
    $round_assign = null;
    $depart_major = null;
    $depart_minor = null;
    $status = null;

    $sql = "UPDATE u_reg 
    SET round_assign = :round_assign, 
    depart_major = :depart_major, 
    depart_minor = :depart_minor, 
    class_assign = :class_assign,
    status = :status
    WHERE id_card = :id_card";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_card', $id_card);
    $stmt->bindParam(':round_assign', $round_assign);
    $stmt->bindParam(':depart_major', $depart_major);
    $stmt->bindParam(':depart_minor', $depart_minor);
    $stmt->bindParam(':class_assign', $class_assign);
    $stmt->bindParam(':status', $status);
    $stmt->execute();
    $_SESSION['success'] = "ยกเลิกการสมัครเรียนเรียบร้อยแล้ว";
    header("location: ../main_reg.php?id_card=$id_card");
}

?>