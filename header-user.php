<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT firstname FROM users WHERE id = $user_id";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://poseidon-code.github.io/supacons/dist/supacons.all.css" >

    <title>User page</title>
  </head>

  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light" style="border: 5px solid black; background-color: #FFF;">
      <div class="container">
        <a class="navbar-brand" href="index.php">
          <img src="" width="30" height="24" class="d-inline-block align-text-top">
          LOGO
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">

          <li class="nav-item"  style="font-size: 1.5rem; display: flex;">
          
              <a class="nav-link" href="profile.php" style="margin-right: 1rem;">
              <i class="fa-solid fa-user"></i>
              
              <?php if (isset($user)):
                 ?>
        <li class="nav-item">
          <span class="nav-link"><?= $user['firstname']; ?></span>
        </li>
      <?php endif; ?>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="cart.php">
              <i class="fa-solid fa-cart-shopping" style="font-size: 1.5rem;"></i>
              </a>


            <li class="nav-item">
              <a class="nav-link" href="logout.php" style="padding: 7px 15px; background: #000; color: #FFF; border: 1px solid #000; margin-right: 10px;">Logout</a>
            </li>
          
           
            </li>
          </ul>
        </div>
      </div>
    </nav>

