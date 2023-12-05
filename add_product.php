<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//   $name = $_POST["name"];
//   $description = $_POST["description"];
//   $price = $_POST["price"];
//   $category = $_POST["category"];

//   $sql = "INSERT INTO products (name, description, image, price, category) VALUES (?, ?, ?, ?, ?)";
//   $stmt = $conn->prepare($sql);
//   $stmt->bind_param("ssssd", $name, $description, $image, $price, $category);

//   if ($stmt->execute()) {
//     header("Location: admin.php");
//   } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
//   }
// }


// $folder_path = 'uploadimg';

// // ให้สิทธิ์ให้ได้เขียน
// if (chmod($folder_path, 0777)) {
//   echo "เปลี่ยนสิทธิ์ของโฟลเดอร์ $folder_path เพื่อให้สามารถเขียนได้";
// } else {
//   echo "ไม่สามารถเปลี่ยนสิทธิ์ของโฟลเดอร์ $folder_path";
// }





if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $description = $_POST["description"];
  $price = $_POST["price"];
  $category = $_POST["category"];

  // รับค่า "birthdate" จากแบบฟอร์ม HTML (อาจมีหลายค่า)

  // ... (ส่วนที่เกี่ยวข้องกับการรับวันเดือนปีหมดอายุ)

  // ตรวจสอบว่ามีการอัปโหลดไฟล์เข้ามาหรือไม่
  if ($_FILES["image"]["error"] == UPLOAD_ERR_OK) {
      $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
      $upload_dir = "uploadimg/";
      $upload_file = $upload_dir . basename($_FILES["image"]["name"]);
      $file_extension = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));

      // ตรวจสอบนามสกุลของไฟล์
      if (in_array($file_extension, $allowed_extensions)) {
          if (move_uploaded_file($_FILES["image"]["tmp_name"], $upload_file)) {
              $image = $upload_file;  // กำหนดค่าให้กับ image
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

  $sql = "INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssss", $name, $description, $price, $image, $category);

  if ($stmt->execute()) {
      header("Location: admin.php");
      exit();
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }

  // ปิดการเชื่อมต่อฐานข้อมูล
  $stmt->close();
  $conn->close();
}








$sql = "SELECT category FROM category";
$category = $conn->query($sql);
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://poseidon-code.github.io/supacons/dist/supacons.all.css">

    <title>Product Management System</title>
</head>

<body>


    <div class="container mt-5">
        <h1>Add Product</h1>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label"
                    style="font-family: 'IBM Plex Sans Thai', sans-serif;">รูปภาพ:</label>
                <input type="file" name="image" id="image" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="category">Category</label>
                <select class="form-control" id="category" name="category">
                    <?php while ($row = $category->fetch_assoc()) : ?>

                    <option value="<?= $row["category"]; ?>"><?= $row["category"]; ?></option>

                    <?php endwhile; ?>

                </select>
            </div>


            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>