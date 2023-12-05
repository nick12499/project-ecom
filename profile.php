<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();


// ตรวจสอบว่ามีการเข้าสู่ระบบหรือไม่
if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}



// คำสั่งเชื่อมต่อฐานข้อมูล
require_once "config.php";

$user_id = $_SESSION['id'];

$sql = "SELECT id, firstname, role, email FROM users WHERE id = ?";

// ใช้ prepared statement เพื่อป้องกัน SQL injection
$stmt = mysqli_prepare($conn, $sql);

// ผูกตัวแปรกับ prepared statement
mysqli_stmt_bind_param($stmt, "i", $user_id);

// ประมวลผลคำสั่ง SQL
mysqli_stmt_execute($stmt);

// รับผลลัพธ์จากคำสั่ง SQL
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
} else {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit;
}



// อย่าลืมปิด prepared statement และการเชื่อมต่อฐานข้อมูล
mysqli_stmt_close($stmt);
mysqli_close($conn);





?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
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
    label {
        font-family: 'IBM Plex Sans Thai', sans-serif;
    }

    body {
        background-color: #FFF;
    }

    h1 span {
        color: #d4001a;
    }
    </style>

    <title>Profile</title>
</head>

<body>


    <div class="container mt-5">
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" style="font-family: 'IBM Plex Sans Thai', sans-serif;">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
        <?php endif; ?>

        <div class="container mt-5">
            <h1 style="font-family: 'Poppins', sans-serif;"><span>My</span> Profile</h1>

            <div class="mb-3">
                <label for="firstname" class="form-label">ชื่อ</label>
                <input type="text" class="form-control" id="firstname" name="firstname"
                    value="<?php echo $row['firstname']; ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>"
                    readonly>
            </div>
            <!-- <a href="edit_profile.php" class="btn btn-primary"
                style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขข้อมูลส่วนตัว</a> -->
            <a href="user.php" class="btn btn-success"
                style="font-family: 'IBM Plex Sans Thai', sans-serif;">กลับสู่หน้าเเรก</a>

        </div>




</body>

</html>