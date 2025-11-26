<?php
require_once '../config/db.php';
$conn = connectDB();

if (isset($_POST['submit'])) {
    $id_card = $_POST['id_card'];
    $prefix = $_POST['prefix'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $b_day = $_POST['b_day'];
    $m_day = $_POST['m_day'];
    $y_day = $_POST['y_day'];
    $age = $_POST['age'];
    $tel = $_POST['tel'];

    // ข้อมูลที่อยู่เพิ่มเติม
    $h_number = $_POST['h_number'];
    $moo = $_POST['moo'];
    $village = $_POST['Village'];
    $soi = $_POST['soi'];
    $road = $_POST['road'];
    $a_district = $_POST['A_district'];
    $s_district = $_POST['S_district'];
    $district = $_POST['District'];
    $postal_code = $_POST['postal_code'];

    $lasted_school = $_POST['lasted_school'];
    $lasted_class = $_POST['lasted_class'];
    $lasted_school_district = $_POST['lasted_school_district'];
    $lasted_gpax = $_POST['lasted_gpax'];



    $sql = "SELECT * FROM u_reg WHERE id_card = :id_card";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_card', $id_card);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $sql = "UPDATE u_reg 
                SET prefix = :prefix, 
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
                    lasted_gpax = :lasted_gpax
                WHERE id_card = :id_card";
        $stmt = $conn->prepare($sql);
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


        $stmt->bindParam(':id_card', $id_card);
        $stmt->execute();

        $_SESSION['success'] = "บันทึกข้อมูลสำเร็จ";
        $_SESSION['user_log'] = $id_card;
        header("location: ../main_reg.php?id_card=$id_card");
    } else {
        $_SESSION['error'] = "ตรวจพบข้อผิดพลาด";
        header("location: ../register.php?id_card=$id_card");
    }
}

if (isset($_POST['uploadData'])) {  // แก้ไขจาก uplodaData เป็น uploadData
    $id_card = $_POST['id_card'];
    $type = $_POST['type'];
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $uploadDir = 'uploads/';

    // อัปโหลดรูปภาพไปยังไดเรกทอรี
    move_uploaded_file($fileTmpName, $uploadDir . $fileName);

    // อัปเดตข้อมูลในฐานข้อมูล
    $sql = "UPDATE u_reg SET type_reg = :type, image = :image WHERE id_card = :id_card";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':image', $fileName);
    $stmt->bindParam(':id_card', $id_card);
    if ($stmt->execute()) {
        $_SESSION['success'] = "อัพโหลดสำเร็จ";
        header("location: ../main_reg.php?id_card=$id_card");
    } else {
        $_SESSION['error'] = "อัพโหลดไม่สำเร็จ";
        header("location: ../main_reg.php?id_card=$id_card");
    }



}
?>