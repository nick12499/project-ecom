<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

// ตรวจสอบว่ามี user_id ใน session หรือไม่
if (!isset($_SESSION['id'])) {
    echo "You must be logged in to view order history.";
    exit;
}

// 1. สร้าง connection กับฐานข้อมูล
$servername = "localhost";
include 'config.php';

// 2. ใช้ user_id จาก session
// $user_id = $_SESSION['id'];
$order_id = $_GET['id'];

// 3. เขียนคำสั่ง SQL ในการดึงข้อมูลจากตาราง orders, order_items และ products
$sql = "SELECT orders.id as order_id, orders.status as order_status, products.name as product_name, products.image as product_img, users.firstname as username, order_items.quantity as quantity, order_items.total_price as total_price, orders.created_at as order_date, order_address.address as address, order_address.payment_method as payment, order_address.slip as slip, order_address.phoneNumber as phone, order_items.size as size
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN products ON order_items.product_id = products.id
        JOIN users ON orders.user_id = users.id
        JOIN order_address ON orders.user_id = order_address.user_id
        WHERE orders.id = $order_id
        ORDER BY orders.id";


$result = $conn->query($sql);



function get_total_items_in_cart() {
    if (isset($_SESSION['cart'])) {
        return array_sum($_SESSION['cart']);
    } else {
        return 0;
    }
}




$conn->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <!-- Add Bootstrap 5 CSS -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://poseidon-code.github.io/supacons/dist/supacons.all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Andika:ital@1&family=Audiowide&family=IBM+Plex+Sans+Thai:wght@400;500;700&family=Josefin+Sans&family=Pattaya&family=Pridi:wght@300;400&family=Roboto+Slab:wght@300;600&family=Source+Serif+Pro:ital,wght@0,300;1,400&family=Teko:wght@300&family=Ubuntu:wght@300&family=Zen+Dots&display=swap"
        rel="stylesheet">
    <style>
    * {
        font-family: 'IBM Plex Sans Thai', sans-serif;
    }

    body {
        background-color: #FFF;
    }

    html {
        font-family: 'IBM Plex Sans Thai', sans-serif;
    }

    /* ซ่อนรูปภาพ */
    .hidden {
        display: none;
    }

    /* แสดงรูปภาพเป็นลองคลิก */
    .lightbox {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        text-align: center;
    }

    .lightbox img {
        max-width: 90%;
        max-height: 90%;
        margin: auto;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }
    </style>

</head>

<body>



    <!----------- HISTORY ORDERS ----------------->

    <div class="container" style="padding: 60px; font-family: 'IBM Plex Sans Thai', sans-serif;">
        <div class="goback" style="display: flex; justify-content: space-between;">
            <h1 class="my-4">Order History</h1>
            <a href="admin_order.php"
                style="text-decoration: none; font-size: 15px; padding: 7px 15px; border: 2px solid #000; height: 40px; transform: translateY(30px); font-family: 'IBM Plex Sans Thai', sans-serif; font-weight: 300; color: #000;">กลับ</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>วันที่การสั่งซื้อ</th>
                        <th>ชื่อ</th>
                        <th>สินค้า</th>
                        <th>รูปภาพ</th>
                        <th>size</th>
                        <th>จำนวน</th>
                        <th>รวมราคา</th>
                        <th>ที่อยู่</th>
                        <th>เบอร์ติดต่อ</th>
                        <th>การจัดส่ง</th>
                        <th>สลิป</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["order_date"] ?></td>
                        <td><?= $row["username"] ?></td>
                        <td>
                            <?php
                                    $product_name = $row["product_name"];
                                    $max_length = 20; // ตั้งค่าความยาวสูงสุดที่คุณต้องการ
                                    if (strlen($product_name) > $max_length) {
                                        $product_name = substr($product_name, 0, $max_length) . '...'; // ลดความยาวและเพิ่มเครื่องหมาย ... เมื่อข้อความยาวเกิน
                                    }
                                    echo $product_name;
                                    ?>
                        </td>
                        <td>
                            <img src="<?= $row['product_img']; ?>" style="max-width: 70px;" class="img-fluid">
                        </td>
                        <td><?= $row["size"] ?> </td>

                        <td><?= $row["quantity"] ?> ชิ้น</td>
                        <td><?= $row["total_price"] ?> บาท</td>
                        <td><?= $row["address"] ?></td>
                        <td><?= $row["phone"] ?></td>
                        <td><?= $row["payment"] ?></td>
                        <td>
                            <a style="color: #000;" href="#" onclick="showImage('<?= $row["slip"] ?>')">
                                <i class="fas fa-eye"></i> ดู</a>
                            <!-- เพิ่ม div สำหรับแสดงรูปภาพ และเปิดเมื่อคลิกลิงก์ -->
                            <div id="imageViewer" class="hidden" onclick="closeImage()">
                                <div class="lightbox">
                                    <img src="<?= $row["slip"] ?>" class="img-fluid">
                                </div>
                            </div>
                        </td>
                        <td><?= $row["order_status"] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>

    <!-- Add Bootstrap 5 JS -->
    <script>
    function showImage(imageSrc) {
        var imageViewer = document.getElementById("imageViewer");
        var image = imageViewer.querySelector("img");

        image.src = imageSrc;
        imageViewer.classList.remove("hidden");
    }

    // เพิ่มฟังก์ชันเพื่อซ่อนรูปภาพ
    function closeImage() {
        var imageViewer = document.getElementById("imageViewer");
        imageViewer.classList.add("hidden");
    }
    </script>
    <?php include 'footer.php'; ?>