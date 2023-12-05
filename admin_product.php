<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
$resultallproduct = $conn->query($sql);


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
$result = $conn->query($sqlProducts); // แก้เป็น $result แทน $resultProducts

$pCountProducts = 0;
if ($result->num_rows > 0) { // แก้เป็น $result แทน $resultProducts
    while ($row = $result->fetch_assoc()) {
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






// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$host = "localhost";
$username = "root";
$password = "";
$database = "ecom";

// เชื่อมต่อฐานข้อมูล
$connection = new mysqli($host, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($connection->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $connection->connect_error);
}

// รับข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST["category"];
    
    // เพิ่มข้อมูลในตาราง "category"
    $sql = "INSERT INTO category (category) VALUES ('$category')";
    
    if ($connection->query($sql) === TRUE) {
       
    } else {
        echo "ผิดพลาด: " . $sql . "<br>" . $connection->error;
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$connection->close();



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
            <li><a href="admin.php"><i class='bx bxs-dashboard'></i>Dashboard</a></li>
            <li class="active"><a href="admin_product.php"><i class='bx bx-store-alt'></i>Products</a></li>
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
                    <h1>Products</h1>
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
                        <h3>Products</h3>

                        <form action="admin_product.php" method="post">
                            <label for="category"
                                style="font-family: 'IBM Plex Sans Thai', sans-serif;">หมวดหมู่</label>
                            <input type="text" name="category" id="category" required>
                            <input style="font-family: 'IBM Plex Sans Thai', sans-serif; font-weight: 400;"
                                type="submit" class="btn btn-primary" value="เพิ่มหมวดหมู่">
                        </form>
                        <a href="add_product.php"
                            style="text-decoration: none; color: #000; transform: translateY(3px);"><i
                                class='bx bx-plus-circle'></i></a>

                    </div>




                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $resultallproduct->fetch_assoc()) : ?>
                            <tr>
                                <th><?= $row['id']; ?></th>
                                <td>
                                    <?php
                                    $name = $row["name"];
                                    $max_length = 20; // ตั้งค่าความยาวสูงสุดที่คุณต้องการ
                                    if (strlen($name) > $max_length) {
                                        $name = substr($name, 0, $max_length) . '...'; // ลดความยาวและเพิ่มเครื่องหมาย ... เมื่อข้อความยาวเกิน
                                    }
                                    echo $name;
                                    ?>
                                </td>

                                <td><img src="<?= $row['image']; ?>" alt="<?= $row['name']; ?>"
                                        style="width: 70px; height: auto;">
                                </td>
                                <td><?= number_format($row['price'], 2); ?> THB</td>
                                <td> <?php
                                    $description = $row["description"];
                                    $max_length = 20; // ตั้งค่าความยาวสูงสุดที่คุณต้องการ
                                    if (strlen($description) > $max_length) {
                                        $description = substr($description, 0, $max_length) . '...'; // ลดความยาวและเพิ่มเครื่องหมาย ... เมื่อข้อความยาวเกิน
                                    }
                                    echo $description;
                                    ?></td>
                                <td><?= $row['category']; ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-warning">Edit</a>
                                    <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>



                <!-- <h1>Welcome, Admin!</h1>
    <a href="logout.php">Logout</a> -->

                <!-- End of Reminders-->

            </div>

        </main>


    </div>

    <script src="index.js"></script>
</body>

</html>