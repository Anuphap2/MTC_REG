<?php
require_once '../../config/db.php';
$conn = connectDB();

if (isset($_POST['insert'])) {
    $name_depart = $_POST['name_depart'];
    $name_major = $_POST['name_major'];
    $total = $_POST['total'];
    $class = $_POST['class'];
    $level = $_POST['level'];

    // ตรวจสอบว่ามีชื่อสาขานี้อยู่ในฐานข้อมูลแล้วหรือไม่
    $sql_check = "SELECT COUNT(*) FROM department WHERE name_depart = :name_depart AND class = :class AND level = :level";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':name_depart', $name_depart);
    $stmt_check->bindParam(':class', $class);
    $stmt_check->bindParam(':level', $level);
    $stmt_check->execute();
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        // หากชื่อสาขาซ้ำ
        $_SESSION['error'] = "ชื่อสาขา $name_depart ระดับ $class มีอยู่แล้วในระบบ";
        header("location: ../addSubject.php");
    } else {
        // หากชื่อสาขาไม่ซ้ำ ดำเนินการเพิ่มข้อมูล
        $sql = "INSERT INTO department (name_depart, id_major_ref, total, class,level) VALUES (:name_depart, :name_major, :total, :class,:level)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name_depart', $name_depart);
        $stmt->bindParam(':name_major', $name_major);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':level', $level);

        if ($stmt->execute()) {
            $_SESSION['success'] = "เพิ่มข้อมูล $name_depart สำเร็จ";
            header("location: ../addSubject.php");
        } else {
            $_SESSION['error'] = "พบข้อผิดพลาดในการเพิ่มข้อมูล";
            header("location: ../addSubject.php");
        }
    }
}
if (isset($_POST['insertMajor'])) {
    $name_major = $_POST['name_major'];


    // ตรวจสอบว่ามีชื่อสาขานี้อยู่ในฐานข้อมูลแล้วหรือไม่
    $sql_check = "SELECT COUNT(*) FROM major_depart WHERE name_major = :name_major";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':name_major', $name_major);
    $stmt_check->execute();
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        // หากชื่อสาขาซ้ำ
        $_SESSION['error'] = "ชื่อแผนก $name_major มีอยู่แล้วในระบบ";
        header("location: ../addSubject.php");
    } else {
        // หากชื่อสาขาไม่ซ้ำ ดำเนินการเพิ่มข้อมูล
        $sql = "INSERT INTO major_depart (name_major) VALUES (:name_major)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name_major', $name_major);


        if ($stmt->execute()) {
            $_SESSION['success'] = "เพิ่มข้อมูล $name_major สำเร็จ";
            header("location: ../addSubject.php");
        } else {
            $_SESSION['error'] = "พบข้อผิดพลาดในการเพิ่มข้อมูล";
            header("location: ../addSubject.php");
        }
    }
}
?>