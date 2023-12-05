<?php
// ตรวจสอบว่ามีการส่งข้อมูลมาจาก POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // เชื่อมต่อกับฐานข้อมูล (ตั้งค่าตามที่คุณใช้)
  include 'config.php';
    // รับข้อมูลที่ส่งมา
    $productId = $_POST["productId"];
    $size = $_POST["size"];

    // ทำการบันทึกข้อมูลลงในฐานข้อมูล (ตัวอย่างเท่านี้เท่านั้น)
    $sql = "INSERT INTO cart (product_id, size) VALUES ('$productId', '$size')";

    if ($conn->query($sql) === TRUE) {
        echo "Product added to cart successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // ปิดการเชื่อมต่อ
    $conn->close();
}
?>