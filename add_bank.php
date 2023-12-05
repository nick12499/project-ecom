<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $number_bank = $_POST["number_bank"];
    $name_bank = $_POST["name_bank"];
    $user_bank = $_POST["user_bank"];
   
  
    // รับค่า "birthdate" จากแบบฟอร์ม HTML (อาจมีหลายค่า)
  
    $sql = "INSERT INTO bank (number_bank, name_bank, user_bank) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $number_bank, $name_bank, $user_bank);
    
  
    if ($stmt->execute()) {
        echo '<script>alert("เพิ่มธนาคารสำเร็จ"); location.href="admin_order.php";</script>';
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
  
    // ปิดการเชื่อมต่อฐานข้อมูล
    $stmt->close();
    $conn->close();
  }


?>