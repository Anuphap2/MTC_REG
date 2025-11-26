<?php
require_once '../../config/db.php';
$conn = connectDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_major = $_POST['id_major'];
    $name_major = $_POST['name_major'];


    $sql = "UPDATE major_depart SET name_major = :name_major WHERE id_major = :id_major";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name_major', $name_major);
    $stmt->bindParam(':id_major', $id_major);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'แก้ไขข้อมูลสำเร็จ']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล']);
    }
}
?>