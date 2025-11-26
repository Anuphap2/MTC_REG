<?php
include '../config/db.php';
$conn = connectDB();
$round_id = $_GET['round'];
// ข้อมูลที่ต้องการใส่ในไฟล์ Excel
$sql = "SELECT user.*, depart.*, round.*, major.*
        FROM u_reg AS user
        LEFT JOIN department AS depart ON user.depart_major = depart.id_depart
        LEFT JOIN round AS round ON user.round_assign = round.id_round
        LEFT JOIN major_depart AS major ON depart.id_major_ref = major.id_major
        WHERE user.round_assign = :round AND user.status = 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":round", $round_id);
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = 0;
$data = []; // สร้างอาร์เรย์เพื่อเก็บข้อมูล

foreach ($departments as $department) {
    $count++;
    $data[] = [
        $count,
        $department['prefix'] . $department['fname'] . ' ' . $department['lname'],
        $department['name_major'],
        $department['name_depart'],
        $department['level'],
        $department['lasted_school'],
        $department['class'],
        $department['name_round']
    ];

}


// ชื่อไฟล์ที่ต้องการสร้าง
$filename = "ข้อมูลผู้สมัครของรอบ " . $departments[0]['name_round'] . ".csv";


// บังคับให้บราวเซอร์ดาวน์โหลดไฟล์
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// เปิด output stream
$output = fopen('php://output', 'w');

// ใส่ BOM (Byte Order Mark) สำหรับ UTF-8 เพื่อให้ Excel รู้ว่าเป็น UTF-8
fwrite($output, "\xEF\xBB\xBF");

// เขียนบรรทัดแรก (Header) ลงในไฟล์
fputcsv($output, array('#', 'ชื่อ', 'แผนก', 'สาขา', 'แผนการเรียน', 'สถาบันเดิม', 'วุฒิที่ใช้สมัคร', 'รอบการสมัคร'));

// เขียนข้อมูลลงในไฟล์
foreach ($data as $row) {
    fputcsv($output, $row);
}

// ปิด output stream
fclose($output);
?>