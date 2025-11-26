<?php
require_once '../../config/db.php';
$conn = connectDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_depart = $_POST['id_depart'];
    $name_depart = $_POST['name_depart'];
    $name_major = $_POST['name_major'];
    $total = $_POST['total'];
    $class = $_POST['class'];
    $level = $_POST['level'];

    $sql = "UPDATE department SET name_depart = :name_depart, id_major_ref = :name_major , total = :total, class = :class , level = :level WHERE id_depart = :id_depart";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name_depart', $name_depart);
    $stmt->bindParam(':name_major', $name_major);
    $stmt->bindParam(':total', $total);
    $stmt->bindParam(':class', $class);
    $stmt->bindParam(':level', $level);
    $stmt->bindParam(':id_depart', $id_depart);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'แก้ไขข้อมูลสำเร็จ']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล']);
    }
}
?>