<?php
require_once '../../config/db.php';
$conn = connectDB();

if (isset($_POST['save'])) {
    try {
        $id_card = $_POST['id_card'];

        // รับค่าและตัดช่องว่างซ้ายขวาออก (Trim) ป้องกัน error
        $prefix = trim($_POST['prefix']);
        $fname = trim($_POST['fname']);
        $lname = trim($_POST['lname']);
        $b_day = $_POST['b_day'];
        $m_day = $_POST['m_day'];
        $y_day = $_POST['y_day'];
        $age = $_POST['age'];
        $tel = trim($_POST['tel']);

        // เช็คชื่อตัวแปรให้ตรงกับ name="" ใน HTML ของคุณ (ผมแก้เป็นตัวเล็กให้เป็นมาตรฐาน ถ้าของคุณตัวใหญ่ให้แก้กลับ)
        $h_number = $_POST['h_number'];
        $moo = $_POST['moo'];
        $village = $_POST['Village']; // เช็คว่า HTML name="Village" จริงไหม
        $soi = $_POST['soi'];
        $road = $_POST['road'];
        $a_district = $_POST['A_district']; // เช็ค HTML
        $s_district = $_POST['S_district']; // เช็ค HTML
        $district = $_POST['District'];     // เช็ค HTML
        $postal_code = $_POST['postal_code'];

        $lasted_school = $_POST['lasted_school'];
        $lasted_class = $_POST['lasted_class'];
        $lasted_school_district = $_POST['lasted_school_district'];
        $lasted_gpax = $_POST['lasted_gpax'];
        $depart_major = $_POST['depart_major'];
        $depart_minor = $_POST['depart_minor'];

        // อัพเดทข้อมูลเลย ไม่ต้อง SELECT เช็คก่อนก็ได้ (ประหยัดเวลา)
        // ถ้ามี ID นี้มันจะอัพเดท ถ้าไม่มีมันก็แค่ไม่ทำอะไร (Affected rows = 0)
        $sql = "UPDATE u_reg SET 
                    prefix = :prefix, 
                    fname = :fname, 
                    lname = :lname, 
                    b_day = :b_day, 
                    m_day = :m_day, 
                    y_day = :y_day, 
                    age = :age, 
                    tel = :tel,
                    h_number = :h_number,
                    moo = :moo,
                    village = :village,
                    soi = :soi,
                    road = :road,
                    a_district = :a_district,
                    s_district = :s_district,
                    district = :district,
                    postal_code = :postal_code,
                    lasted_school = :lasted_school,
                    lasted_class = :lasted_class,
                    lasted_school_district = :lasted_school_district,
                    lasted_gpax = :lasted_gpax,
                    depart_major = :depart_major,
                    depart_minor = :depart_minor
                WHERE id_card = :id_card";

        $stmt = $conn->prepare($sql);

        // Bind Parameters
        $stmt->bindParam(':prefix', $prefix);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':b_day', $b_day);
        $stmt->bindParam(':m_day', $m_day);
        $stmt->bindParam(':y_day', $y_day);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':tel', $tel);
        $stmt->bindParam(':h_number', $h_number);
        $stmt->bindParam(':moo', $moo);
        $stmt->bindParam(':village', $village);
        $stmt->bindParam(':soi', $soi);
        $stmt->bindParam(':road', $road);
        $stmt->bindParam(':a_district', $a_district);
        $stmt->bindParam(':s_district', $s_district);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':postal_code', $postal_code);
        $stmt->bindParam(':lasted_school', $lasted_school);
        $stmt->bindParam(':lasted_class', $lasted_class);
        $stmt->bindParam(':lasted_school_district', $lasted_school_district);
        $stmt->bindParam(':lasted_gpax', $lasted_gpax);
        $stmt->bindParam(':depart_major', $depart_major);
        $stmt->bindParam(':depart_minor', $depart_minor);
        $stmt->bindParam(':id_card', $id_card);

        if ($stmt->execute()) {
            $_SESSION['success'] = "บันทึกข้อมูลสำเร็จ";
            header("location: ../dataStudent.php?round=" . $_POST['round_id']); // ถ้ามี round_id ส่งกลับไปด้วยจะดีมาก
            exit(); // ต้องมี exit หลัง header เสมอ
        } else {
            $_SESSION['error'] = "อัพเดทไม่สำเร็จ";
            header("location: ../dataStudent.php");
            exit();
        }

    } catch (PDOException $e) {
        // ถ้ามี Error SQL ให้แสดงออกมาดูเลย
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>