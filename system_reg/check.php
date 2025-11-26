<?php
require_once '../config/db.php';
$conn = connectDB();

if (isset($_POST['submit'])) {
    $id_card = $_POST['id_card'];
    $status = 1; // สมัครแล้ว

    $sql = "SELECT * FROM u_reg WHERE id_card = :id_card";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_card', $id_card);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['status'] == 1) {
        header("location: ../checkStatus.php?pass&id_card=$id_card");
    } else if ($result['status'] == 2) {
        header("location: ../checkStatus.php?not_pass&id_card=$id_card");
    } else {
        header("location: ../checkStatus.php?error");
    }

}