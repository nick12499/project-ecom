<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to the database
include 'config.php';
// Check if a product ID is received from ii.php
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Check if there is an existing cart in the session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Check if the product is already in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += 1; // Increment the quantity
    } else {
        $_SESSION['cart'][$product_id] = 1; // Add the product with a quantity of 1
    }
}

if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
} else {
    header("Location: login.php");
    exit;
}

// Handle increase/decrease/remove product quantity and empty cart actions
if (isset($_GET['action']) && isset($_GET['product_id'])) {
    $action = $_GET['action'];
    $product_id = $_GET['product_id'];

    if ($action == 'increase') {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += 1; // Increment the quantity
        } else {
            $_SESSION['cart'][$product_id] = 1; // Add the product with a quantity of 1
        }
        if ($_SESSION['cart'][$product_id] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }

    } elseif ($action == 'remove') {
        unset($_SESSION['cart'][$product_id]);
    } elseif ($action == 'empty') {
        $_SESSION['cart'] = array();
    }
}



if (isset($_GET['action']) && isset($_GET['product_id'])) {
    $action = $_GET['action'];
    $product_id = $_GET['product_id'];

    if ($action == 'decrease') {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] -= 1; // Increment the quantity
        } else {
            $_SESSION['cart'][$product_id] = 1; // Add the product with a quantity of 1
        }
        if ($_SESSION['cart'][$product_id] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }

    } elseif ($action == 'remove') {
        unset($_SESSION['cart'][$product_id]);
    } elseif ($action == 'empty') {
        $_SESSION['cart'] = array();
    }
}


if (isset($_POST['submit_order'])) {
    add_order_to_database($conn, $user_id, $_SESSION['cart']);
    
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $address = $_POST["address"];
    $phoneNumber = $_POST["phoneNumber"];
    $payment_method = $_POST["payment_method"];

    if ($payment_method === "โอนผ่านธนาคาร") {
        // Check if a file was uploaded
        if ($_FILES["slip"]["error"] == UPLOAD_ERR_OK) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $upload_dir = "slip/";
            $upload_file = $upload_dir . basename($_FILES["slip"]["name"]);
            $file_extension = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));

            if (in_array($file_extension, $allowed_extensions)) {
                if (move_uploaded_file($_FILES["slip"]["tmp_name"], $upload_file)) {
                    $slip = $upload_file;  // Set the value of "slip"
                } else {
                    echo "Upload failed.";
                    exit();
                }
            } else {
                echo "ไม่อนุญาตให้อัปโหลดไฟล์รูปภาพนามสกุลนี้.";
                exit();
            }
        } else {
            echo "No file uploaded.";
            exit();
        }
    } else {
        // Handle other payment methods here
        $slip = ""; // Set "slip" to an empty string if payment method is not "โอนผ่านธนาคาร"
    }

    $sql = "INSERT INTO order_address (user_id ,firstName, lastName, address, slip, phoneNumber, payment_method) VALUES (?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $user_id, $firstName, $lastName, $address, $slip, $phoneNumber, $payment_method);

    if ($stmt->execute()) {
        echo '<script>alert("คำสั่งซื้อของคุณถูกยืนยันแล้ว"); location.href="?action=empty&product_id";</script>';    
            exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}


// สร้างตัวแปร $user_id จาก $_SESSION['id']
$user_id = $_SESSION['id'];

// สร้างตัวแปร $cart จาก $_SESSION['cart']
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

function add_order_to_database($conn, $user_id, $cart) {
    echo "Cart contents:";
    print_r($cart);

    // เพิ่มรายการสั่งซื้อ
    // ดึงค่า address จากฟอร

    // สร้างคำสั่ง SQL สำหรับการเพิ่มข้อมูลในตาราง orders
    $insert_order = "INSERT INTO orders (user_id, status) VALUES ($user_id, 'รอตรวจสอบ')";
    $conn->query($insert_order);

    // รับ order_id ที่สร้างขึ้นล่าสุด
    $order_id = $conn->insert_id;

    // เพิ่มรายการสินค้าในตะกร้าสินค้าลงในตาราง order_items
    foreach ($cart as $product_id => $quantity) {
        // เพิ่มคอลัมน์ "size" จากตาราง "cart"
        $sql = "SELECT products.price, cart.size
        FROM products
        INNER JOIN cart ON products.id = cart.product_id
        WHERE products.id = $product_id
        ORDER BY cart.id DESC
        LIMIT 1";

    
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $total_price = $row["price"] * $quantity;
            $size = $row["size"];
    
            // แทน $order_id, $product_id, $quantity, $total_price ด้วยข้อมูลที่คุณต้องการ
            $insert_order_item = "INSERT INTO order_items (order_id, product_id, quantity, total_price, size) VALUES ($order_id, $product_id, $quantity, $total_price, '$size')";
            $conn->query($insert_order_item);
        }
    }
    
}





$sql = "SELECT id, number_bank, name_bank, user_bank FROM bank";
$bank = $conn->query($sql);




?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>

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


    <style>
    * {
        font-family: 'IBM Plex Sans Thai', sans-serif;
    }

    .card .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card {
        border: none;
        box-shadow: 1px 1px 4px #e0e0e0;
    }

    .btn {
        box-shadow: 1px 1px 4px #e0e0e0;
        transition: all 0.3s ease-in-out;

    }

    .btn:hover {
        background-color: #d4001a;
        color: #fff;
    }

    a.backbtn {
        box-shadow: 1px 1px 4px #e0e0e0;
        background-color: #d4001a;
        text-decoration: none;
        color: #fff;
        padding: 3px 13px;
        transition: all 0.3s ease-in-out;
        border-radius: 5px;
    }

    .boxform {
        box-shadow: 0 0 10px #e0e0e0;
        padding: 20px;
        border-radius: 10px;
    }

    .cartbox {
        background-color: #BEADFA;
        box-shadow: 1px 1px 4px #e0e0e0;
        border-radius: 15px;
        color: #fff;
    }
    </style>
</head>

<body style="background-color: #fff;">




    <div class="container mt-5">


        <!---------------------- เเสดงะนาคาร ----------------------------->

        <div class="container mb-5">
            <h2 style="text-transform: uppercase;"><span style="color: #d4001a;">Shopping</span> Cart</h2>
            <div class="row">
                <?php
            if ($bank->num_rows > 0) {
                while ($row = $bank->fetch_assoc()) {
            ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row["name_bank"]; ?></h5>
                            <h6 class="card-subtitle text-muted">
                                <span id="numberBank<?php echo $row["id"]; ?>"><?php echo $row["number_bank"]; ?></span>
                                <button class="btn btn-sm copy-button"
                                    data-clipboard-target="#numberBank<?php echo $row["id"]; ?>">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </h6>
                            <p class="card-text"><?php echo $row["user_bank"]; ?></p>
                        </div>
                    </div>
                </div>
                <?php
                }
            } else {
                echo "ไม่พบข้อมูลธนาคาร";
            }
            $conn->close();
            ?>
            </div>
        </div>


        <!---------------------- เเสดงะนาคาร ----------------------------->
        <div class="row">
            <div class="col-md-6">
                <a href="?action=empty&product_id" class="btn mb-3">ล้างตะกร้า <i class="fa-solid fa-trash"></i></a>







                <?php
            $total = 0;

        
            include 'config.php';

            foreach ($cart as $product_id => $quantity) {
                $sql = "SELECT * FROM products WHERE id = " . $product_id;
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $subtotal = $row["price"] * $quantity;
                    $total += $subtotal;
                   
                }
            }

            
            $conn->close();
            ?>









                <table class="cartbox table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>




                        <?php
                      

                        include 'config.php';

                        foreach ($cart as $product_id => $quantity) {
                            $sql = "SELECT products.*, cart.size
                            FROM products
                            INNER JOIN cart ON products.id = cart.product_id
                            WHERE cart.product_id = " . $product_id . "
                            ORDER BY cart.product_id DESC
                            LIMIT 1";

                             $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                    ?>




                        <tr>
                            <td><?php
                                    $name = $row["name"];
                                    $max_length = 20; // ตั้งค่าความยาวสูงสุดที่คุณต้องการ
                                    if (strlen($name) > $max_length) {
                                        $name = substr($name, 0, $max_length) . '...'; // ลดความยาวและเพิ่มเครื่องหมาย ... เมื่อข้อความยาวเกิน
                                    }
                                    echo $name;
                                    ?></td>
                            <td><img src="<?php echo $row["image"]; ?>" alt="<?php echo $row["name"]; ?>" width="50"
                                    height="50"></td>
                            <td><?php echo $row["price"]; ?></td>
                            <td><?php echo $row["size"]; ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td>
                                <a href="?action=increase&product_id=<?php echo $product_id; ?>"
                                    class="btn btn-success btn-sm">+</a>
                                <a href="?action=decrease&product_id=<?php echo $product_id; ?>"
                                    class="btn btn-warning btn-sm">-</a>
                                <a href="?action=remove&product_id=<?php echo $product_id; ?>"
                                    class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                        <?php
                }
            }
            ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th colspan="2" style="display: flex;"><?php echo $total; ?><h6 style="margin-left: 1rem;">
                                    THB</h6>
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <a class="backbtn" href="user.php">
                    กลับหน้าหลัก <i class="fa-solid fa-rotate-left"></i></a>
            </div>



            <div class="boxform col-md-6 mt-5">

                <h1 class="mb-4" style="font-size: 20px;">ยืนยันสินค้า</h1>
                <form method="post" name="order" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="payment_method">เลือกช่องทางการชำระ</label>
                        <select class="form-control" name="payment_method" id="payment_method">
                            <option value="โอนผ่านธนาคาร">โอนผ่านธนาคาร</option>
                            <option value="เก็บปลายทาง">เก็บปลายทาง</option>
                        </select>
                    </div>

                    <div id="เก็บปลายทาง" style="display: none;">
                        <!-- Your delivery form fields here -->
                    </div>

                    <div id="โอนผ่านธนาคาร" style="display: none;">
                        <div class="form-group" style="margin-top: 1rem ; display: flex; flex-direction: column;">
                            <label for="slip">อัพโหลดสลิป</label>
                            <input type="file" class="form-control-file" name="slip" id="slip">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="firstName">ชื่อ</label>
                        <input type="text" class=" form-control" name="firstName" id="firstName" placeholder="ป้อนชื่อ">
                    </div>
                    <div class="form-group mb-3">
                        <label for="lastName">นามสกุล</label>
                        <input type="text" class="form-control" name="lastName" id="lastName" placeholder="ป้อนนามสกุล">
                    </div>
                    <div class="form-group mb-3">
                        <label for="address">ที่อยู่</label>
                        <input type="text" class="form-control" name="address" id="address" placeholder="ป้อนที่อยู่">
                    </div>

                    <div class="form-group mb-3">
                        <label for="phoneNumber">เบอร์โทร</label>
                        <input type="tel" class="form-control" name="phoneNumber" id="phoneNumber"
                            placeholder="ป้อนเบอร์โทร">
                    </div>
                    <button type="submit" name="submit_order" class="btn btn-primary">ยืนยันการสั่งซื้อ</button>





                </form>
            </div>
        </div>
    </div>


    <!-- <div class="buy" style="display: flex; justify-content: space-between ;">
            <a href="user.php" style="text-decoration: none; color: #000; padding: 7px 25px; border: 1px solid #000;">Go
                Back To Shop</a>
            <form method="post">
                <button type="submit" name="submit_order"
                    style="text-decoration: none; color: #000; padding: 7px 25px; border: 1px solid #000;">Confirm
                    Order</button>
            </form>
        </div> -->


    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    var paymentMethodSelect = document.getElementById("payment_method");
    var deliveryForm = document.getElementById("เก็บปลายทาง");
    var bankTransferForm = document.getElementById("โอนผ่านธนาคาร");

    paymentMethodSelect.addEventListener("change", function() {
        if (paymentMethodSelect.value === "โอนผ่านธนาคาร") {
            deliveryForm.style.display = "none";
            bankTransferForm.style.display = "block";
        } else if (paymentMethodSelect.value === "เก็บปลายทาง") {
            deliveryForm.style.display = "block";
            bankTransferForm.style.display = "none";
        } else {
            deliveryForm.style.display = "none";
            bankTransferForm.style.display = "none";
        }
    });
    </script>


    <script>
    // กำหนดค่า Clipboard.js
    var clipboard = new ClipboardJS('.copy-button');

    // จัดการเหตุการณ์เมื่อคัดลอกสำเร็จ
    clipboard.on('success', function(e) {
        e.clearSelection();
        alert("คัดลอกข้อมูลสำเร็จ!");
    });
    </script>


</body>

</body>

</html>