<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$user_id = $_SESSION['id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecom";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["address"]) && isset($_POST["phoneNumber"]) && isset($_POST["payment_method"])) {
    // รับค่าจากแบบฟอร์ม
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $address = $_POST["address"];
    $phoneNumber = $_POST["phoneNumber"];
    $payment_method = $_POST["payment_method"];


    if (isset($_FILES["slip"]) && $_FILES["slip"]["error"] == UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 5 * 1024 * 1024; // 5 MB
    
        $upload_dir = "slip/";
        $upload_file = $upload_dir . basename($_FILES["slip"]["name"]);
        $file_extension = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));
    
        if (in_array($file_extension, $allowed_extensions) && $_FILES["slip"]["size"] <= $max_file_size) {
            if (move_uploaded_file($_FILES["slip"]["tmp_name"], $upload_file)) {
                $slip = $upload_file;
            } else {
                echo "Upload failed.";
                exit();
            }
        } else {
            echo "ไม่อนุญาตให้อัปโหลดไฟล์รูปภาพนามสกุลนี้หรือไฟล์ใหญ่เกินไป.";
            exit();
        }
    } else {
        $slip = null;
    }
    



    
    // สร้างคำสั่ง SQL เพื่อเพิ่มข้อมูลลงในตาราง order_address
    $sql = "INSERT INTO order_address (user_id, firstName, lastName, address, phoneNumber, payment_method ,slip) VALUES ('$user_id', '$firstName', '$lastName', '$address', '$phoneNumber', '$payment_method' , '$slip')";

    if ($conn->query($sql) === true) {
        echo '<script>alert("บันทึกข้อมูลสำเร็จ");</script>';
        header("Location: cart.php");
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }

    // ปิดการเชื่อมต่อกับฐานข้อมูล
    $conn->close();

}



?>