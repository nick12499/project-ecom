<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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


$sql = "SELECT * FROM users";
$alluser = $conn->query($sql);


// 3. เขียนคำสั่ง SQL ในการดึงข้อมูลจากตาราง orders, order_items และ products
$sql = "SELECT orders.id as order_id, orders.status as order_status, products.name as product_name,  products.image as product_img , users.firstname as username, order_items.quantity as quantity, order_items.total_price as total_price, orders.created_at as order_date
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN products ON order_items.product_id = products.id
        JOIN users ON orders.user_id = users.id
        WHERE orders.user_id";


$result = $conn->query($sql);



$sqlProducts = "SELECT COUNT(*) as pCount FROM products";
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
</head>

<body>



    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="logo">

            <div class="logo-name"><span>Ecommerc</span></div>
        </a>
        <ul class="side-menu">
            <li class="active"><a href="admin.php"><i class='bx bxs-dashboard'></i>Dashboard</a></li>
            <li><a href="admin_product.php"><i class='bx bx-store-alt'></i>Products</a></li>
            <li><a href="admin_order.php"><i class='bx bxs-package'></i>Orders</a></li>
            <li><a href="admin_number_order.php"><i class='bx bxs-purchase-tag'></i>Parcel number
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
                    <h1>Dashboard</h1>
                    <ul class="breadcrumb">

                    </ul>
                </div>

            </div>

            <!-- Insights -->
            <ul class="insights">
                <li>
                    <i class='bx bxs-basket'></i>
                    <span class="info">
                        <h3>
                            <?php echo $pCountProducts; ?>
                        </h3>
                        <p style="font-family: 'IBM Plex Sans Thai', sans-serif;">ประเภทสินค้า</p>
                    </span>
                </li>

                <li><i class='bx bxs-checkbox-checked'></i>
                    <span class="info">
                        <h3>
                            <?php echo $quantitySum; ?><span
                                style="font-family: 'IBM Plex Sans Thai', sans-serif; font-size: 15px; font-weight: 400; margin-left: 10px;">
                                ชิ้น</span>
                        </h3>
                        <p style="font-family: 'IBM Plex Sans Thai', sans-serif;">สินค้าขายออก</p>
                    </span>
                </li>
                <li><i class='bx bx-dollar-circle'></i>
                    <span class="info">
                        <h3 style="display: flex;">

                            <i class="fa-solid fa-baht-sign"></i><?php echo $totalSum; ?>
                        </h3>
                        <p style="font-family: 'IBM Plex Sans Thai', sans-serif;">ยอดขาย</p>
                    </span>
                </li>
            </ul>
            <!-- End of Insights -->

            <div class="bottom-data">
                <div class="orders">
                    <div class="header">
                        <i class='bx bx-receipt'></i>
                        <h3>Orders</h3>
                        <!-- <i class='bx bx-filter'></i>
                        <i class='bx bx-search'></i> -->
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>product</th>
                                <th>Price</th>
                                <th>quantity</th>
                                <th>Status</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td>

                                    <p><?= $row["username"] ?></p>
                                </td>
                                <td><img src="<?= $row['product_img']; ?>"></td>
                                <td><?= $row["total_price"] ?> THB</td>
                                <td><?= $row["quantity"] ?></td>
                                <td><span class="status completed"><?= $row["order_status"] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Reminders -->
                <div class="reminders">
                    <div class="header">
                        <i class='bx bx-user'></i>
                        <h3>Member</h3>
                        <i class='bx bx-filter'></i>
                        <!-- <i class='bx bx-plus'></i> -->
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>email</th>
                                <th>role</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $alluser->fetch_assoc()) : ?>
                            <tr>
                                <td><?= $row["id"] ?></td>
                                <td>

                                    <p><?= $row["firstname"] ?></p>
                                </td>
                                <td><?= $row["email"] ?></td>
                                <td><?= $row["role"] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- End of Reminders-->

            </div>

        </main>


    </div>

    <script src="index.js"></script>
</body>

</html>