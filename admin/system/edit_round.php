<?php
require_once '../../config/db.php';
$conn = connectDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_round = $_POST['id_round'];
    $name_round = $_POST['name_round'];
    $date_round = $_POST['date_round'];
    $end_round = $_POST['end_round'];

    $sql = "UPDATE round SET name_round = :name_round, date_round = :date_round, end_round = :end_round WHERE id_round = :id_round";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_round', $id_round);
    $stmt->bindParam(':name_round', $name_round);
    $stmt->bindParam(':date_round', $date_round);
    $stmt->bindParam(':end_round', $end_round);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'แก้ไขข้อมูลสำเร็จ']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล']);
    }
}
?>