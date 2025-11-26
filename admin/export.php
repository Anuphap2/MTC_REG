<?php
require_once '../config/db.php';
require '../vendor/autoload.php';
require_once '../vendor/setasign/fpdf/fpdf.php';
$conn = connectDB();

use setasign\Fpdi\Fpdi;

$pdf = new FPDF();
$pdf->SetTitle('report');

// เพิ่มหน้าใหม่
$pdf->AddPage();

// ตั้งค่าฟอนต์
$pdf->AddFont('THSarabun', '', 'thsarabun.php');
$pdf->SetFont('THSarabun', '', 20);
if (isset($_GET['round']) && isset($_GET['depart'])) {
    $round_id = $_GET['round'];
    $major_id = $_GET['depart'];
}
$sql = "SELECT * 
FROM u_reg 
INNER JOIN round ON u_reg.round_assign = round.id_round
WHERE round_assign = :round AND depart_major = :major AND status = 1";
$result = $conn->prepare($sql);
$result->bindParam(':round', $round_id);
$result->bindParam(':major', $major_id);
$result->execute();
$rows = $result->fetchAll(PDO::FETCH_ASSOC);



$sql = "SELECT * 
FROM u_reg 
INNER JOIN round ON u_reg.round_assign = round.id_round
INNER JOIN department ON u_reg.depart_major = department.id_depart
WHERE round_assign = :round AND depart_major = :major AND status = 1";
$result2 = $conn->prepare($sql);
$result2->bindParam(':round', $round_id);
$result2->bindParam(':major', $major_id);
$result2->execute();
$data = $result2->fetch(PDO::FETCH_ASSOC);
$round_name = $data['name_round'];
$class_name = $data['class_assign'];
$depart_name = $data['name_depart'];

if ($class_name == "มัธยมศึกษาปีที่ 3") {
    $class_name = "ประกาศนียบัตรวิชาชีพ (ปวช.)";
} else {
    $class_name = "ประกาศนียบัตรวิชาชีพ (ปวส.)";
}
$now = date('Y') + 543;
// เขียนข้อความหัวเรื่อง
$text = iconv('UTF-8', 'TIS-620//IGNORE', "บัญชีรายชื่อผู้ผ่านการคัดเลือกเข้าศึกษาต่อระดับ$class_name $round_name สาขาวิชา $depart_name ประจำปีการศึกษา $now (ออนไลน์)");

// กำหนดความกว้างของหน้า
$page_width = $pdf->GetPageWidth();

// กำหนดความกว้างของข้อความที่ต้องการแสดงผล
$text_width = $page_width - 20; // ลบออก 20 เพื่อเว้นระยะขอบซ้ายและขวา

// คำนวณตำแหน่ง X เพื่อให้ข้อความอยู่ตรงกลาง
$x = 10; // เริ่มที่ 10 เพื่อเว้นระยะขอบซ้าย

// ตั้งค่าตำแหน่ง Y
$y = $pdf->GetY();

// แสดงข้อความที่จัดให้อยู่ตรงกลาง และตัดบรรทัดอัตโนมัติ
$pdf->SetXY($x, $y);
$pdf->MultiCell($text_width, 10, $text, 0, 'C');
$pdf->Ln(10); // เพิ่มบรรทัดว่าง

// สร้างตาราง
$widths = array(20, 80, 90); // ความกว้างของคอลัมน์
$header = array('ลำดับที่', 'ชื่อ-นามสกุล', 'โรงเรียนเดิม');
$pdf->SetFont('THSarabun', '', 18);
// พิมพ์หัวข้อของตาราง
foreach ($header as $col) {
    $pdf->Cell($widths[array_search($col, $header)], 10, iconv('UTF-8', 'TIS-620//IGNORE', $col), 1, 0, 'C');
}
$pdf->Ln(); // ขึ้นบรรทัดใหม่

// ดึงข้อมูลจากฐานข้อมูล

// พิมพ์ข้อมูลในตาราง
$index = 1;
$pdf->SetFont('THSarabun', '', 16);

if (!$rows) {
    $_SESSION['error'] = "ไม่มีข้อมูลผู้สมัคร";
    header("location: dataStudent.php");
} else {
    foreach ($rows as $row) {
        $pdf->Cell($widths[0], 8, iconv('UTF-8', 'TIS-620//IGNORE', $index), 1, 0, 'C'); // ลำดับที่
        $pdf->Cell($widths[1], 8, iconv('UTF-8', 'TIS-620//IGNORE', $row['prefix'] . $row['fname'] . ' ' . $row['lname']), 1); // ชื่อ-นามสกุล
        $pdf->Cell($widths[2], 8, iconv('UTF-8', 'TIS-620//IGNORE', $row['lasted_school']), 1); // โรงเรียนเดิม
        $pdf->Ln(); // ขึ้นบรรทัดใหม่
        $index++;
    }
    $pdf->Cell(array_sum($widths), 8, iconv('UTF-8', 'TIS-620//IGNORE', 'จำนวนผู้สมัครทั้งหมด: ' . count($rows) . ' คน'), 1, 0, 'R');
}
// จบการพิมพ์
$pdf->Ln();
$pdf->SetFont('THSarabun', '', 12);

// กำหนดข้อความ
$text = iconv('UTF-8', 'TIS-620//IGNORE', "หมายเหตุ : สำหรับผู้ที่ไม่ผ่านการคัดเลือกโปรดตรวจสอบการบันทึกข้อมูลส่วนตัว ข้อมูลที่อยู่ ข้อมูลการศึกษา แก้ไขข้อมูลของท่านให้เรียบร้อย");

// กำหนดความกว้างของหน้า
$page_width = $pdf->GetPageWidth();

// กำหนดความกว้างของข้อความที่ต้องการแสดงผล
$text_width = $page_width - 20; // ลบออก 20 เพื่อเว้นระยะขอบซ้ายและขวา

// แสดงข้อความและตัดบรรทัดอัตโนมัติ
$pdf->MultiCell($text_width, 10, $text, 0, 'L');

$credit = iconv('UTF-8', 'TIS-620//IGNORE', "สร้างและพัฒนาโดยนายอานุภาพ ศรเทียน");

$text_width = $pdf->GetStringWidth($credit);

// กำหนดความกว้างของหน้า
$page_width = $pdf->GetPageWidth();

// คำนวณตำแหน่ง X เพื่อให้ข้อความอยู่ชิดขวา
$x = $page_width - $text_width - 10; // ลบออก 10 เพื่อเว้นระยะขอบขวา

// กำหนดตำแหน่ง X
$pdf->SetX($x);

// แสดงข้อความ
$pdf->Cell($text_width, 10, $credit, 0, 0, 'R');
$pdf->Output();
?>