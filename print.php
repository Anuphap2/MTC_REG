<?php
require_once 'config/db.php';
require 'vendor/autoload.php';
require_once 'vendor/setasign/fpdf/fpdf.php';

$conn = connectDB();

use setasign\Fpdi\Fpdi;
$randomFileName = 'report_' . uniqid() . '.pdf';
// Get the ID from the URL
$id = $_GET['id_card'];

// Prepare and execute the SQL statement
$sql = "SELECT u.*, d1.*, d2.* , d1.level as major_level ,d2.level as major_level2 , d1.name_depart as major_depart, d2.name_depart as minor_depart
FROM u_reg AS u
LEFT JOIN department AS d1 ON u.depart_major = d1.id_depart
LEFT JOIN department AS d2 ON u.depart_minor = d2.id_depart
WHERE id_card = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

$data = $stmt->fetch(PDO::FETCH_ASSOC);
$currentYear = date("Y");
$currentD = date("d");
$currentM = date("m");
$birthYear = $data['y_day'];
$age = $currentYear - $birthYear;

$current_month = date('n'); // ดึงเดือนปัจจุบันเป็นตัวเลข 1-12
$thai_year = $currentYear + 543;

if ($current_month >= 5) {
    $thai_year = $thai_year + 1;
} else {
    // ถ้าเปิดระบบก่อนเดือน พ.ค. (ม.ค.-เม.ย.) ให้ใช้ปีการศึกษาปัจจุบัน
    $thai_year = $thai_year;
}





$pdf = new FPDF();
$pdf->SetTitle($randomFileName);

// เพิ่มหน้าใหม่
$pdf->AddPage();

// ตั้งค่าฟอนต์
$pdf->AddFont('THSarabun', '', 'thsarabun.php');
$pdf->SetFont('THSarabun', '', 16);

// ใส่รูปภาพเป็นพื้นหลัง
if ($data['lasted_class'] == 'มัธยมศึกษาปีที่ 3') {
    $pdf->Image('image/ปวช.jpg', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
    function arabicToThaiNumbers($number)
    {
        $thaiNumbers = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
        return str_replace(range(0, 9), $thaiNumbers, $number);
    }

    $thai_year = arabicToThaiNumbers($thai_year);

    $pdf->SetXY(162, 60);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $thai_year));

    $pdf->SetXY(60, 75);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['prefix'] . $data['fname'] . ' ' . $data['lname']));

    $birthday = arabicToThaiNumbers($data['b_day']);

    $pdf->SetXY(30, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $birthday));

    function getThaiMonth($monthNumber)
    {
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม'
        ];

        return $thaiMonths[(int) $monthNumber];
    }




    $thai_month = getThaiMonth($data['m_day']);

    $pdf->SetXY(50, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $thai_month));
    $y_day = arabicToThaiNumbers($data['y_day'] + 543);

    $pdf->SetXY(90, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $y_day));

    $age = arabicToThaiNumbers($age);

    $pdf->SetXY(120, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $age));


    $id_card_convert = arabicToThaiNumbers($data['id_card']);
    $pdf->SetFont('THSarabun', '', 14);


    $pdf->SetXY(166.5, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $id_card_convert));

    $pdf->SetFont('THSarabun', '', 16);

    $h_number = arabicToThaiNumbers($data['h_number']);

    $pdf->SetXY(75, 90);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $h_number));

    $pdf->SetXY(95, 90);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['moo']));

    $pdf->SetFont('THSarabun', '', 12);
    $pdf->SetXY(116, 90);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['Village']));


    $soi = arabicToThaiNumbers($data['soi']);

    $pdf->SetXY(160, 90);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $soi));

    $pdf->SetFont('THSarabun', '', 16);

    $pdf->SetXY(20, 97);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['road']));

    $pdf->SetXY(72, 97);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['A_district']));

    $pdf->SetXY(115, 97);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['S_district']));

    $pdf->SetXY(155, 97);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['District']));

    $postal_code = arabicToThaiNumbers($data['postal_code']);

    $pdf->SetXY(20, 105);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $postal_code));


    $tel = arabicToThaiNumbers($data['tel']);

    $pdf->SetXY(75, 105);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $tel));

    $pdf->SetXY(55, 112);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['lasted_school']));



    $pdf->SetXY(125, 112);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['lasted_school_district']));

    $lasted_gpax = arabicToThaiNumbers($data['lasted_gpax']);
    $pdf->SetXY(175, 112);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $lasted_gpax));


    $pdf->SetXY(45, 126);
    $text = iconv('UTF-8', 'TIS-620//IGNORE', $data['major_depart'] . ' ' . $data['major_level']);
    $pdf->Write(0, $text);


    $pdf->SetXY(45, 134);
    $text2 = iconv('UTF-8', 'TIS-620//IGNORE', $data['minor_depart'] . ' ' . $data['major_level2']);
    $pdf->Write(0, $text2);

    $pdf->SetXY(85, 227.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['prefix'] . $data['fname'] . ' ' . $data['lname']));
    // ... (ใส่ข้อมูลต่างๆ ลงใน PDF)
} else if ($data['lasted_class'] == 'มัธยมศึกษาปีที่ 6' || $data['lasted_class'] == 'ประกาศนียบัตรวิชาชีพ') {
    $pdf->Image('image/ปวส.jpg', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
    function arabicToThaiNumbers($number)
    {
        $thaiNumbers = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
        return str_replace(range(0, 9), $thaiNumbers, $number);
    }

    $thai_year = arabicToThaiNumbers($thai_year);

    $pdf->SetXY(168, 60);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $thai_year));

    $pdf->SetXY(60, 75);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['prefix'] . $data['fname'] . ' ' . $data['lname']));

    $birthday = arabicToThaiNumbers($data['b_day']);

    $pdf->SetXY(30, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $birthday));

    function getThaiMonth($monthNumber)
    {
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม'
        ];

        return $thaiMonths[(int) $monthNumber];
    }




    $thai_month = getThaiMonth($data['m_day']);

    $pdf->SetXY(50, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $thai_month));
    $y_day = arabicToThaiNumbers($data['y_day'] + 543);

    $pdf->SetXY(90, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $y_day));

    $age = arabicToThaiNumbers($age);

    $pdf->SetXY(120, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $age));


    $id_card_convert = arabicToThaiNumbers($data['id_card']);
    $pdf->SetFont('THSarabun', '', 14);


    $pdf->SetXY(166.5, 82.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $id_card_convert));

    $pdf->SetFont('THSarabun', '', 16);

    $h_number = arabicToThaiNumbers($data['h_number']);

    $pdf->SetXY(75, 90);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $h_number));

    $pdf->SetXY(95, 90);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['moo']));

    $pdf->SetFont('THSarabun', '', 12);
    $pdf->SetXY(116, 90);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['Village']));


    $soi = arabicToThaiNumbers($data['soi']);

    $pdf->SetXY(160, 90);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $soi));

    $pdf->SetFont('THSarabun', '', 16);

    $pdf->SetXY(20, 97);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['road']));

    $pdf->SetXY(72, 97);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['A_district']));

    $pdf->SetXY(115, 97);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['S_district']));

    $pdf->SetXY(155, 97);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['District']));

    $postal_code = arabicToThaiNumbers($data['postal_code']);

    $pdf->SetXY(20, 105);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $postal_code));


    $tel = arabicToThaiNumbers($data['tel']);

    $pdf->SetXY(75, 105);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $tel));

    $pdf->SetXY(55, 112);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['lasted_school']));



    $pdf->SetXY(125, 112);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['lasted_school_district']));

    $lasted_gpax = arabicToThaiNumbers($data['lasted_gpax']);
    $pdf->SetXY(175, 112);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $lasted_gpax));


    
    $pdf->SetXY(45, 126);
    $text = iconv('UTF-8', 'TIS-620//IGNORE', $data['major_depart'] . ' ' . $data['major_level']);
    $pdf->Write(0, $text);


    $pdf->SetXY(45, 134);
    $text2 = iconv('UTF-8', 'TIS-620//IGNORE', $data['minor_depart'] . ' ' . $data['major_level2']);
    $pdf->Write(0, $text2);


    $pdf->SetXY(85, 227.5);
    $pdf->Write(0, iconv('UTF-8', 'TIS-620//IGNORE', $data['prefix'] . $data['fname'] . ' ' . $data['lname']));
}






// สร้าง PDF
$pdf->Output('D', $randomFileName);
?>