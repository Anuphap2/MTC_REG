<?php
require_once '../../config/db.php';
$conn = connectDB();

if (isset($_POST['insert'])) {
    $name_round = $_POST['name_round'];
    $date_round = $_POST['date_round'];
    $end_round = $_POST['end_round'];

    // ตรวจสอบว่ามีชื่อสาขานี้อยู่ในฐานข้อมูลแล้วหรือไม่

    // หากชื่อสาขาไม่ซ้ำ ดำเนินการเพิ่มข้อมูล
    $sql = "INSERT INTO round (name_round, date_round, end_round) VALUES (:name_round, :date_round, :end_round)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name_round', $name_round);
    $stmt->bindParam(':date_round', $date_round);
    $stmt->bindParam(':end_round', $end_round);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มข้อมูล $name_round สำเร็จ";
        header("location: ../addRound.php");
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาดในการเพิ่มข้อมูล";
        header("location: ../addRound.php");
    }

}
if (isset($_POST['insertReg'])) {
    $depart_ref = $_POST['depart_ref'];
    $round_ref = $_POST['round_ref'];

    // ตรวจสอบว่ามีชื่อสาขานี้อยู่ในฐานข้อมูลแล้วหรือไม่

    // หากชื่อสาขาไม่ซ้ำ ดำเนินการเพิ่มข้อมูล
    $sql = "INSERT INTO reg_show (depart_ref, round_ref) VALUES (:depart_ref, :round_ref)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':depart_ref', $depart_ref);
    $stmt->bindParam(':round_ref', $round_ref);

    if ($stmt->execute()) {
        $_SESSION['success'] = "เพิ่มข้อมูลสำเร็จ";
        header("location: ../addRound.php");
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาดในการเพิ่มข้อมูล";
        header("location: ../addRound.php");
    }

}
?>