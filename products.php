<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'config.php';


if (!isset($_SESSION['id'])) {
    // ถ้าไม่ได้เข้าสู่ระบบ ให้เด้งไปที่หน้า login.php
    header('Location: login.php');
    exit;
}



$user_id = $_SESSION['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();




$sql = "SELECT id, firstname, role, email FROM users WHERE id = ?";

// ใช้ prepared statement เพื่อป้องกัน SQL injection
$stmt = mysqli_prepare($conn, $sql);

// ผูกตัวแปรกับ prepared statement
mysqli_stmt_bind_param($stmt, "i", $user_id);

// ประมวลผลคำสั่ง SQL
mysqli_stmt_execute($stmt);

// รับผลลัพธ์จากคำสั่ง SQL
$resultfirstname = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultfirstname) > 0) {
    $row = mysqli_fetch_assoc($resultfirstname);
} else {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit;
}



function get_total_items_in_cart() {
    if (isset($_SESSION['cart'])) {
        return array_sum($_SESSION['cart']);
    } else {
        return 0;
    }
}


$sql = "SELECT category FROM category";
$resultcategory = $conn->query($sql);


$sql = "SELECT * FROM products";
$resultsearch = $conn->query($sql);




$sql = "SELECT * FROM products WHERE 1=1";

if (isset($_GET['category'])) {
    // เชื่อมต่อกับฐานข้อมูล
    include 'config.php';

    // ตรวจสอบว่ามีค่า category ที่ถูกส่งมาหรือไม่
    $category = $_GET['category'];
    // เพิ่มเงื่อนไขค้นหาสำหรับ category ถ้ามีการส่ง category มา
    $sql .= " AND category LIKE '%$category%'";
}

// ประมวลผลคำสั่ง SQL
$resultsearch = $conn->query($sql);



?>

<!doctype html>
<html lang="en">


<style>
nav {
    width: 100%;
    background-color: #FFF;
    box-shadow: 0 0 10px #000;
}

nav a {
    text-decoration: none;
    color: #000;
}

nav a i {
    text-decoration: none;
    color: #000;
}

.card {
    cursor: pointer;
    border-radius: 0.5rem;
    box-shadow: 1px 1px 4px #e0e0e0;
    position: relative;
}

.card-footer {
    background-color: #FFF;
    border: none;
    display: flex;
    text-align: center;
}

.card-footer a {
    text-decoration: none;
    font-size: 13px;
    font-family: 'IBM Plex Sans Thai', sans-serif;
    width: 100%;
    padding: 5px 0;
    border: 1px solid #d4001a;
    border-radius: 7px;
    color: #d4001a;
    transition: all 0.3s ease-in-out;

}



.card-footer a:hover {
    background-color: #d4001a;
    color: #FFF;
}


.align-middle {
    display: flex;
    align-items: center;
    justify-content: center;
}


.align-middle img {
    height: 185px;
    object-fit: cover;
    width: 170px;
}

.card-body {
    display: flex;
    flex-direction: column;
    padding: 0px 30px;
}

.card-body h5,
h6 {
    font-family: 'IBM Plex Sans Thai', sans-serif;
    font-size: 1rem;
    text-align: start;
    margin-bottom: 0.25rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.card-body h6 {
    font-size: 13px;
    color: #606060;
}

.card-body h5 {
    font-size: 17px;
    color: #606060;
    text-transform: uppercase;

}

.card-body h5:hover {
    color: #d4001a;
}

.card-body p {
    color: #d4001a;
}

ul.dropdown-menu li.nav-item a.nav-link:hover {
    background-color: #d4001a;
    color: #FFF;
}

ul.dropdown-menu li.nav-item a.nav-link:hover i {
    background-color: #d4001a;
    color: #FFF;
}


.serchbox {
    display: flex;
}

.catgory {
    display: flex;

}

.catgory form {
    margin-right: 10px;
}

.catgory button {
    border-radius: 0.5em;
    border: none;
    box-shadow: 1px 1px 4px #e0e0e0;
    background-color: #d4001a;
    color: #FFF;

}

.catgory input {
    font-family: 'IBM Plex Sans Thai', sans-serif;
    padding: 0px 10px;
    box-shadow: 1px 1px 4px #e0e0e0;
    border-radius: 0.5em;
    border: 1px solid #d4001a;
    outline: none;

}
</style>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://poseidon-code.github.io/supacons/dist/supacons.all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai&display=swap" rel="stylesheet">

    <title>User page</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="" width="30" height="24" class="d-inline-block align-text-top">
                LOGO
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="user.php">หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">สินค้า</a>
                    </li>

                    <div class="iconbox" style="display: flex; align-items: center; border-right: 1px solid #000;">



                        <ul class="navbar-nav">

                            <li class="nav-item dropdown">



                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false"
                                    style="font-family: 'IBM Plex Sans Thai', sans-serif;">

                                    <i class="fa-solid fa-user"></i> <?php echo $row["firstname"] ?>
                                </a>



                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown"
                                    style=" background-color: rgb(255, 255, 255 / 0.1); backdrop-filter: blur(10px);">

                                    <li class="nav-item">
                                        <a class="nav-link" href="profile.php"
                                            style=" font-family: 'IBM Plex Sans Thai', sans-serif; font-size: 13px; text-align: center;">ข้อมูลส่วนตัว</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" href="user_order_history.php"
                                            style=" font-family: 'IBM Plex Sans Thai', sans-serif; font-size: 13px; text-align: center;">ประวัติการซื้อ</a>
                                    </li>


                                    <li class="nav-item">
                                        <a class="nav-link" href="logout.php"
                                            style=" font-family: 'IBM Plex Sans Thai', sans-serif; font-size: 13px; text-align: center;">Logout
                                            <i class="fa-sharp fa-solid fa-right-from-bracket"
                                                style="font-size: 10px;"></i></a>
                                    </li>


                                </ul>
                            </li>
                        </ul>






                    </div>



                    <a class="nav-link" href="cart.php">

                        <i class="fa-regular fa-cart-shopping"
                            style="font-size: 1.5rem; position: absolute; color: #000;"></i>

                        <span class="badge bg-danger"
                            style="position: relative; top: -8px; left: 15px; font-size: 10px; border-radius: 20px;"><?php echo get_total_items_in_cart(); ?></span>


                    </a>




                </ul>
            </div>
        </div>
    </nav>












    <!-- คัดลอกโค้ด navbar จากหน้า index.php มาวางที่นี่ -->

    <div class="container mt-5">


        <div class="catgory mb-5">


            <form action="user.php" method="GET">
                <input type="text" name="category" placeholder="ป้อนคำค้นหา">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>




            <div class="abox"
                style="width: 60px; height: 30px;  box-shadow: 1px 1px 4px #e0e0e0; border-radius: 0.5em;">


                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"
                    style="font-family: 'IBM Plex Sans Thai', sans-serif; text-align: center; width: 100%; height: 20px; color: #000;">
                </a>


                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">



                    <!------------ ตรงนี้ -------------------->
                    <li class="nav-item">
                        <?php while ($row = $resultcategory->fetch_assoc()): ?>
                        <a class="nav-link" href="user.php?category=<?= $row["category"] ?>"
                            style=" font-family: 'IBM Plex Sans Thai', sans-serif; font-size: 13px; text-align: center; color: #000;"><?= $row["category"] ?></a>
                        <?php endwhile; ?>
                    </li>

                    <!------------ ตรงนี้ -------------------->



                </ul>
            </div>





        </div>








        <!------- PRODUCT --------->
        <h1 style="font-size:20px; margin-bottom:2rem;">สินค้าทั้งหมดในร้าน</h1>
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
            <?php
                $products_per_page = 20;
                $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

                // Assume $products_result is the result set of products retrieved from the database
                $products_total = $resultsearch ->num_rows;
                $total_pages = ceil($products_total / $products_per_page);

                $offset = ($current_page - 1) * $products_per_page;

                if ($products_total > 0) {
                    $count = 0;
                    while ($row = $resultsearch ->fetch_assoc()) {
                        $count++;

                        // Skip products before the current page
                        if ($count <= $offset) {
                            continue;
                        }

                        // Stop displaying products after reaching the limit for the current page
                        if ($count > ($offset + $products_per_page)) {
                            break;
                        }
                        ?>
            <div class="col">


                <div class="card h-100" style="border-radius: 0.5em;">

                    <div class="align-middle">

                        <a href="product_description.php?id=<?= $row['id']; ?>" style="text-decoration: none;">
                            <img src="<?= $row['image']; ?>" class="card-img-top img-fluid" alt="<?= $row['name']; ?>">
                        </a>

                    </div>
                    <div class="card-body" style="padding: 10px 30px;">
                        <h5><?= $row['name']; ?></h5>
                        <h6><?= $row['description']; ?></h6>
                        <p class="card-text"><i
                                class="fa-light fa-baht-sign"></i><?= number_format($row['price'], 2); ?></p>

                        <!-- เพิ่มตัวเลือกไซส์ -->
                        <div class="size-options">
                            <label for="size" style="font-size: 10px;">Size:</label>
                            <select id="size" name="size" style="font-size:10px; padding: 2px 10px;">
                                <option value="s">S</option>
                                <option value="m">M</option>
                                <option value="l">L</option>
                                <option value="xl">XL</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer" style=" background-color: #FFF; border: none;">
                        <a href="javascript:void(0);" onclick="addToCart(<?php echo $row['id']; ?>);">ADD CART</a>
                    </div>
                </div>

            </div>






            <!-- Add the Modal code here -->

            <!-- End of Modal code -->
            <?php
            }
        } else {
            ?>
        </div>


        <?php
    }
    ?>






    </div>


    <script>
    function addToCart(productId) {
        var selectedSize = document.getElementById("size").value;

        // ส่งข้อมูลไปยังเซิร์ฟเวอร์
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "addToCart.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        // ส่งข้อมูลไปยังเซิร์ฟเวอร์
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // ทำสิ่งที่คุณต้องการหลังจากทำการเพิ่มในตะกร้า
                console.log(xhr.responseText);

                // ทำการเพิ่มสินค้าลงในตะกร้าของผู้ใช้ โดยใช้ Ajax หรืออื่น ๆ

                // ตั้งค่า URL ของหน้า cart.php พร้อมส่งพารามิเตอร์ที่ต้องการ
                var url = 'cart.php?id=' + productId + '&size=' + selectedSize;

                // ใช้ Ajax เพื่อส่งข้อมูลไปยัง cart.php โดยไม่ต้องรีไดเรกหน้า
                var xhr2 = new XMLHttpRequest();
                xhr2.open('GET', url, true);
                xhr2.onreadystatechange = function() {
                    if (xhr2.readyState === 4 && xhr2.status === 200) {
                        // ทำสิ่งที่คุณต้องการเมื่อการร้องขอเสร็จสมบูรณ์
                        alert("เพิ่มสินค้าลงตะกร้าสำเร็จ");
                    }
                };
                xhr2.send();
            }
        };

        // ส่งข้อมูลไปยังเซิร์ฟเวอร์
        xhr.send("productId=" + productId + "&size=" + selectedSize);
    }
    </script>



    <script>
    var profileLink = document.getElementById("profile-link");
    var profileDropdown = document.getElementById("profile-dropdown");

    profileLink.addEventListener("click", function(e) {
        e.preventDefault();
        profileDropdown.style.display = (profileDropdown.style.display === "block") ? "none" : "block";
    });
    </script>









    <!------- PRODUCT --------->
    <?php include 'footer.php'; ?>