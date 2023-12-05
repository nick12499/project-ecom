<?php
include 'config.php';

session_start();
$user_id = $_SESSION['id'];


header("Location: user_order_history.php");
exit();
?>