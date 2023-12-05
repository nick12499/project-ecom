<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$sql = "SELECT * FROM products";
$result = $conn->query($sql);


$sql = "SELECT * FROM bank";
$bank = $conn->query($sql);


// 3. เขียนคำสั่ง SQL ในการดึงข้อมูลจากตาราง orders, order_items และ products
$sql = "SELECT orders.id as order_id, orders.status as order_status, products.name as product_name,  products.image as product_img , users.firstname as username, order_items.quantity as quantity, order_items.total_price as total_price, orders.created_at as order_date
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN products ON order_items.product_id = products.id
        JOIN users ON orders.user_id = users.id
        WHERE orders.user_id";


$result = $conn->query($sql);



$sqlProducts = "SELECT COUNT(*) as pCount FROM orders";
$resultProducts = $conn->query($sqlProducts);

$pCountProducts = 0;
if ($resultProducts->num_rows > 0) {
    while ($row = $resultProducts->fetch_assoc()) {
        $pCountProducts = $row["pCount"];
    }
}

// สร้างคำสั่ง SQL เพื่อรวมจำนวนเงินทั้งหมดในคอลัมน์ total_price
$sql = "SELECT SUM(total_price) AS total_sum FROM order_items";

$resulttoatal = $conn->query($sql);

if ($resulttoatal) {
    // ดึงผลลัพธ์
    $row = $resulttoatal->fetch_assoc();
    $totalSum = $row["total_sum"];

} else {
    echo "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $conn->error;
}



$sql = "SELECT COUNT(orders.id) as total_pending_orders
        FROM orders
        WHERE orders.status = 'รอตรวจสอบ'";

$resultstatus = $conn->query($sql);
if ($resultstatus->num_rows > 0) {
    $row = $resultstatus->fetch_assoc();
    $totalPendingOrders = $row['total_pending_orders'];
} else {
    echo "ไม่มีคำสั่งซื้อที่รอตรวจสอบในขณะนี้";
}


$sql = "SELECT COUNT(orders.id) as total_wait_orders
        FROM orders
        WHERE orders.status = 'ผู้ส่งกำลังเตรียมจัดส่ง'";

$resultwait = $conn->query($sql);
if ($resultwait->num_rows > 0) {
    $row = $resultwait->fetch_assoc();
    $totalwaitOrders = $row['total_wait_orders'];
} else {
    echo "ไม่มีคำสั่งซื้อที่รอตรวจสอบในขณะนี้";
}



$sql = "SELECT COUNT(orders.id) as total_check_orders
        FROM orders
        WHERE orders.status = 'อยู่ระหว่างจัดส่ง'";

$resultcheck = $conn->query($sql);
if ($resultcheck->num_rows > 0) {
    $row = $resultcheck->fetch_assoc();
    $totalcheckOrders = $row['total_check_orders'];
} else {
    echo "ไม่มีคำสั่งซื้อที่รอตรวจสอบในขณะนี้";
}



$sql = "SELECT COUNT(orders.id) as total_success_orders
        FROM orders
        WHERE orders.status = 'จัดส่งสำเร็จ'";

$resultsuccess = $conn->query($sql);
if ($resultsuccess->num_rows > 0) {
    $row = $resultsuccess->fetch_assoc();
    $totalsuccessOrders = $row['total_success_orders'];
} else {
    echo "ไม่มีคำสั่งซื้อที่รอตรวจสอบในขณะนี้";
}



$sql = "SELECT COUNT(orders.id) as total_cancle_orders
        FROM orders
        WHERE orders.status = 'ยกเลิก'";

$resultcancle = $conn->query($sql);
if ($resultcancle->num_rows > 0) {
    $row = $resultcancle->fetch_assoc();
    $totalcancleOrders = $row['total_cancle_orders'];

    // ตรวจสอบค่า status และกำหนดคลาส CSS
    $statusClass = ($totalcancleOrders > 0) ? 'completed red-text' : 'completed';

} else {
    echo "ไม่มีคำสั่งซื้อที่รอตรวจสอบในขณะนี้";
}





$sql = "SELECT SUM(quantity) AS quantity_sum FROM order_items";

$resultquantity = $conn->query($sql);

if ($resultquantity) {
    // ดึงผลลัพธ์
    $row = $resultquantity->fetch_assoc();
    $quantitySum = $row["quantity_sum"];

} else {
    echo "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $conn->error;
}


?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://poseidon-code.github.io/supacons/dist/supacons.all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai&display=swap" rel="stylesheet">


    <title>Admin Page</title>

    <style>

    </style>
</head>

<body>



    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="logo">

            <div class="logo-name"><span>Ecommerc</span></div>
        </a>
        <ul class="side-menu">
            <li><a href="admin.php"><i class='bx bxs-dashboard'></i>Dashboard</a></li>
            <li><a href="admin_product.php"><i class='bx bx-store-alt'></i>Products</a></li>
            <li><a href="admin_order.php"><i class='bx bxs-package'></i>Orders</a></li>
            <li class="active"><a href="admin_number_order.php"><i class='bx bxs-purchase-tag'></i>Parcel number
                </a></li>
            <li><a href="#"><i class='bx bx-message-square-dots'></i>Tickets</a></li>
            <li><a href="admin_user.php"><i class='bx bx-group'></i>Users</a></li>
            <li><a href="#"><i class='bx bx-cog'></i>Settings</a></li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="logout.php" class="logout">
                    <i class='bx bx-log-out-circle'></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
    <!-- End of Sidebar -->

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <nav>
            <i class='bx bx-menu'></i>
        </nav>

        <!-- End of Navbar -->

        <main id="admin">
            <div class="header">
                <div class="left">
                    <h1>Parcel number</h1>
                    <ul class="breadcrumb">

                    </ul>
                </div>

            </div>

            <!-- Insights -->

            <!-- End of Insights -->

            <div class="bottom-data">
                <div class="orders">
                    <div class="header">
                        <i class='bx bx-receipt'></i>
                        <h3>Orders</h3>
                        <form action="update_number_order.php" method="post">
                            <label for="order_id">Order Uid:</label>
                            <input style="width: 100px;" type="number" id="order_id" name="order_id" required>


                            <label for="number_order">กรอกเลขพัสดุ :</label>
                            <input style="width: 100px;" type="text" id="number_order" name="number_order" required>


                            <!-- <select id="order_status" name="order_status" required>
                                <option value="รอตรวจสอบ">รอตรวจสอบ</option>
                                <option value="ผู้ส่งกำลังเตรียมจัดส่ง">ผู้ส่งกำลังเตรียมจัดส่ง</option>
                                <option value="อยู่ระหว่างจัดส่ง">อยู่ระหว่างจัดส่ง</option>
                                <option value="จัดส่งสำเร็จ">จัดส่งสำเร็จ</option>
                                <option value="ยกเลิก">ยกเลิก</option>
                            </select> -->

                            <button type="submit" class=" btn btn-primary">Update</button>
                        </form>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Uid</th>
                                <th>User</th>
                                <th>product</th>
                                <th>Price</th>
                                <th>quantity</th>
                                <th>Status</th>
                                <th>Check</th>



                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= $row["order_id"] ?></td>
                                <td>

                                    <p><?= $row["username"] ?></p>
                                </td>
                                <td><img src="<?= $row['product_img']; ?>"></td>
                                <td><?= $row["total_price"] ?> THB</td>
                                <td><?= $row["quantity"] ?></td>
                                <td>
                                    <span class="status 
                                            <?php if ($row["order_status"] === "ยกเลิก"): ?>
                                                cancle
                                            <?php elseif ($row["order_status"] === "รอตรวจสอบ"): ?>
                                                wait
                                            <?php elseif ($row["order_status"] === "ผู้ส่งกำลังเตรียมจัดส่ง"): ?>
                                                process
                                                <?php elseif ($row["order_status"] === "อยู่ระหว่างจัดส่ง"): ?>
                                                 pending
                                                 <?php elseif ($row["order_status"] === "จัดส่งสำเร็จ"): ?>
                                                 completed
                                            <?php else: ?>
                                                <!-- คลาสเริ่มต้นหรือคลาสที่คุณต้องการในกรณีอื่น ๆ -->
                                                default
                                            <?php endif; ?>
                                        ">
                                        <?= $row["order_status"] ?>
                                    </span>
                                </td>

                                <td><a href="order_history.php?id=<?= $row['order_id']; ?>" style=" color: #000;"><i
                                            class="fa-solid fa-eye"></i></a>
                                </td>


                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Reminders -->


                <!-- End of Reminders-->

            </div>

        </main>


    </div>

    <script src="index.js"></script>
    <script>
    // เรียก Modal เมื่อคลิกที่ลิงก์
    document.getElementById('openModal').addEventListener('click', function() {
        $('#myModal').modal('show');
    });
    </script>

    <!-- เพิ่ม jQuery ต่อท้ายส่วน body ของเอกสาร -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>




</body>

</html>