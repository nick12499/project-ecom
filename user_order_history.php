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
include 'config.php';
// 2. ใช้ user_id จาก session
$user_id = $_SESSION['id'];

// 3. เขียนคำสั่ง SQL ในการดึงข้อมูลจากตาราง orders, order_items และ products
$sql = "SELECT
            orders.id as order_id,
            orders.status as order_status,
            products.name as product_name,
            products.image as product_img,
            users.firstname as username,
            order_items.quantity as quantity,
            order_items.total_price as total_price,
            orders.created_at as order_date,
            orders.number_order as number_order,
            order_items.size as size
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN products ON order_items.product_id = products.id
        JOIN users ON orders.user_id = users.id
        WHERE orders.user_id = $user_id";


$result = $conn->query($sql);

function get_total_items_in_cart() {
    if (isset($_SESSION['cart'])) {
        return array_sum($_SESSION['cart']);
    } else {
        return 0;
    }
}






if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = $_POST["address"];

    $sql = "UPDATE order_address SET address=? WHERE $user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $address, $user_id);

    if ($stmt->execute()) {
        echo '<script>alert("Address updated successfully");</script>';
    } else {
        echo '<script>alert("Error updating address");</script>';
    }
    
    $stmt->close();
}


$sql = "SELECT number_order FROM order_address  WHERE user_id = $user_id";
$address = $conn->query($sql);






$conn->close();



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <!-- Add Bootstrap 5 CSS -->
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
        background-color: #FFf;
    }

    .goback h1.my-4 span {
        color: #d4001a;
    }

    .container a {
        cursor: pointer;
        border-radius: 0.5rem;
        position: relative;
    }



    .card {
        border-radius: 0.5rem;
        box-shadow: 1px 1px 4px #e0e0e0;
    }

    .cardaddress {
        border-radius: 0.5rem;
        box-shadow: 1px 1px 4px #e0e0e0;
        padding: 20px;
        margin-top: 10px;
    }

    a.modaltext {
        text-decoration: none;
        color: #d4001a;
        transition: all 0.2s ease-in-out;
    }

    .card img {
        width: 100px;
        height: auto;
        margin: auto;
        border-radius: 0.5rem;
        box-shadow: 1px 1px 4px #e0e0e0;
    }

    .card .card-body {
        margin: auto;
    }

    .card .card-body h5 {
        font-size: 1rem;
        margin-bottom: 0.25rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .card .cardh {
        margin: auto;
    }

    .fcard {
        border-radius: 0.5rem;
        box-shadow: 1px 1px 4px #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0px 20px;
    }

    .fcard h5 {
        font-size: 15px;
    }

    .fcard p.ptext {
        font-size: 13px;
        transform: translateY(10px);
    }

    .chang {
        display: flex;
        margin-bottom: 1rem;
        flex-direction: column;
    }

    .chang a {
        background: none;
        margin-left: 10px;
    }

    .conorderbox {
        border: 1px solid #000;
        padding: 20px;
    }
    </style>

</head>

<body>

    <!----------- HISTORY ORDERS ----------------->

    <div class="container" style="padding: 60px; font-family: 'IBM Plex Sans Thai', sans-serif;">

        <div class="goback" style="display: flex; justify-content: space-between;">
            <h1 class="my-4" style="font-family: 'Poppins', sans-serif;">
                <span>Order</span> History

            </h1>
            <h6></h6>

        </div>

        <!-- <div class="chang">



            <a class="modaltext" href="#" data-toggle="modal" data-target="#editModal"><i
                    class="fa-solid fa-pen-to-square"></i>
            </a>
        </div> -->

        <div class="container">
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php if ($row !== null): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card">
                        <img src="<?= $row['product_img']; ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= $row["product_name"] ?></h5>
                            <p class="card-text">ID: <?= $row["order_id"] ?></p>
                            <p class="card-text">size: <?= $row["size"] ?></p>

                            <p class="card-text">วันที่การสั่งซื้อ: <?= $row["order_date"] ?></p>
                            <p class="card-text">สถานะ: <?= $row["order_status"] ?></p>
                            <p class="card-text">เลขพัสดุ : <?= $row["number_order"] ?></p>

                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">จำนวน: <?= $row["quantity"] ?> ชิ้น</li>
                            <li class="list-group-item">ราคา: <?= $row["total_price"] ?> บาท</li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                <?php endwhile; ?>
            </div>
        </div>







        <a href="user.php" class="btn"
            style="font-family: 'IBM Plex Sans Thai', sans-serif; padding: 3px 13px;  box-shadow: 1px 1px 4px #e0e0e0;">หน้าหลัก</a>
    </div>


    <div class="modal" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form method="post">
                        <div class="form-group">
                            <label class="mb-3" for="address">เเก้ไขที่อยู่(จะสามารถเเก้ไขได้เมื่อสถานะสินค้าเป็น
                                รอตรวจสอบ)</label>
                            <input type="text" class="mb-3 form-control" id="address" placeholder="ใส่ที่อยู่ใหม่ของคุณ"
                                name="address">
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>

                    </form>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>





    <!-- Add Bootstrap 5 JS -->

    <!-- ไลบรารี Bootstrap CSS -->

    <!-- ไลบรารี jQuery และ Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>