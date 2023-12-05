<?php
session_start();

// ตรวจสอบว่ามีการเข้าสู่ระบบหรือไม่
if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}

// คำสั่งเชื่อมต่อฐานข้อมูล
require_once "config.php";

$user_id = $_SESSION['id'];

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $province = mysqli_real_escape_string($conn, $_POST['province']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  // เก็บข้อมูลอื่นๆ ด้วยวิธีเดียวกัน

  $sql = "UPDATE users SET firstname = '$firstname', lastname = '$lastname', address = '$address', province = '$province', phone = '$phone', email = '$email' WHERE id = $user_id";

if (mysqli_query($conn, $sql)) {
  $_SESSION['success_message'] = "อัปเดตข้อมูลสำเร็จ";
  header("Location: profile.php");
 
} else {
  echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . mysqli_error($conn);
}

mysqli_close($conn);
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Andika:ital@1&family=Audiowide&family=IBM+Plex+Sans+Thai:wght@400;500;700&family=Josefin+Sans&family=Pattaya&family=Pridi:wght@300;400&family=Roboto+Slab:wght@300;600&family=Source+Serif+Pro:ital,wght@0,300;1,400&family=Teko:wght@300&family=Ubuntu:wght@300&family=Zen+Dots&display=swap"
        rel="stylesheet">
    <style>
    label {
        font-family: 'IBM Plex Sans Thai', sans-serif;
    }
    </style>

    <title>Edit Profile</title>
</head>

<body>
    <div class="container mt-5">
        <h1 style="font-family: 'IBM Plex Sans Thai', sans-serif;">แก้ไขข้อมูลส่วนตัว</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="firstname" class="form-label">ชื่อ</label>
                <input type="text" class="form-control" id="firstname" name="firstname"
                    value="<?php echo $row['firstname']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="lastname" class="form-label">นามสกุล</label>
                <input type="text" class="form-control" id="lastname" name="lastname"
                    value="<?php echo $row['lastname']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่ (สำหรับการจัดส่ง)</label>
                <input type="text" class="form-control" id="address" name="address"
                    value="<?php echo $row['address']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="province" class="form-label">จังหวัด (สำหรับการจัดส่ง)</label>
                <input type="text" class="form-control" id="province" name="province"
                    value="<?php echo $row['province']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $row['phone']; ?>"
                    required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">อีเมล</label>
                <input type="text" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>"
                    required>
            </div>
            <!-- เพิ่มฟิลด์อื่นๆ ด้วยวิธีเดียวกัน -->
            <button type="submit" class="btn btn-primary"
                style="font-family: 'IBM Plex Sans Thai', sans-serif;">บันทึกการเปลี่ยนแปลง</button>
            <a href="profile.php" class="btn btn-danger"
                style="font-family: 'IBM Plex Sans Thai', sans-serif;">ยกเลิก</a>
        </form>
    </div>
</body>

</html>