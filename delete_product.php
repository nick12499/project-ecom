<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

$id = $_GET['id'];

// สร้างคำสั่ง SQL สำหรับลบรายการใน order_items
$sql_delete_order_items = "DELETE FROM order_items WHERE product_id = ?";

$stmt_delete_order_items = $conn->prepare($sql_delete_order_items);
$stmt_delete_order_items->bind_param("i", $id);

if ($stmt_delete_order_items->execute()) {
  // เมื่อลบรายการใน order_items เสร็จสิ้น
  // สร้างคำสั่ง SQL สำหรับลบรายการใน products
  $sql_delete_product = "DELETE FROM products WHERE id = ?";

  $stmt_delete_product = $conn->prepare($sql_delete_product);
  $stmt_delete_product->bind_param("i", $id);

  if ($stmt_delete_product->execute()) {
    header("Location: admin_product.php");
  } else {
    echo "Error deleting product: " . $stmt_delete_product->error;
  }
} else {
  echo "Error deleting order items: " . $stmt_delete_order_items->error;
}
?>