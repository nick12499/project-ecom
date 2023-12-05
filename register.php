<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'config.php';

$registration_success = false;
$email_exists = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // เพิ่มเงื่อนไขสำเร็จของการลงทะเบียน
    if (empty($role)) {
        $role = "user";
    }
    
    // เพิ่มเงื่อนไขสำเร็จของการลงทะเบียน
    $sql = "INSERT INTO users (firstname, role, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $firstname, $role, $email, $password);
    
    if ($stmt->execute()) {
        $registration_success = true;
        header("Location: login.php");
        exit();
    }
    

    // เงื่อนไขว่าอีเมลนี้มีอยู่แล้วในฐานข้อมูล
    $check_email_sql = "SELECT * FROM users WHERE email = ?";
    $check_email_stmt = $conn->prepare($check_email_sql);
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $result = $check_email_stmt->get_result();

    if ($result->num_rows > 0) {
        $email_exists = true;
    }

    $stmt->close();
    $check_email_stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Register</h1>
        <?php if($registration_success): ?>
        <div class="alert alert-success mt-3">
            สมัครสมาชิกสำเร็จ
        </div>
        <?php endif; ?>

        <?php if($email_exists): ?>
        <div class="alert alert-danger mt-3">
            คุณมีบัญชีนี้เเล้ว
        </div>
        <?php endif; ?>
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="firstname">Firstname</label>
                <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary">Register</button>
                <h4 class="ml-5" style="font-size: 15px;">มีสมาชิกเเล้วเข้าสู่ระบบ </h4>
                <a href="login.php" class="ml-5">Login</a>
            </div>


        </form>
    </div>
</body>

</html>