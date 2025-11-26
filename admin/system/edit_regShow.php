<?php
require_once '../../config/db.php';
$conn = connectDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $round_ref = $_POST['round_ref'];
    $depart_ref = $_POST['depart_ref'];


    $sql = "UPDATE reg_show SET round_ref = :round_ref, 
    depart_ref = :depart_ref
    WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':round_ref', $round_ref);
    $stmt->bindParam(':depart_ref', $depart_ref);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'แก้ไขข้อมูลสำเร็จ']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล']);
    }
}
?>