<?php

function get_total_items_in_cart() {
  if (isset($_SESSION['cart'])) {
      return array_sum($_SESSION['cart']);
  } else {
      return 0;
  }
}

session_start();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category ii Products</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap 5 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://poseidon-code.github.io/supacons/dist/supacons.all.css" >


   <style>
.banner-container {
  width: 40%;
  max-width: 1500px; /* adjust max-width as needed */
  height: 250px;
  overflow: hidden;
  position: relative;
  margin-bottom: 25px;
  border: 2px solid #000;
}

.slider {
  width: 400%; /* adjust width based on number of images */
  height: 100%;
  position: relative;
  left: 0;
  transition: left 0.5s ease-in-out;
}

.slider img {
  width: 25%; /* adjust width based on number of images */
  height: 100%;
  float: left;
}

.slider-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1;
}

.slider-nav button {
  border-radius: 20px;
  border: none;
  color: #000;
  font-size: 24px;
  padding: 10px;
  cursor: pointer;
  outline: none;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
}

.slider-nav .prev {
  left: 0;
  transform: translateY(6px);
}

.slider-nav .next {
  transform: translateX(480px);
  margin-bottom: 60%;
  display: flex;
}

</style>


</head>
<body>
   
            <?php
            // เชื่อมต่อกับฐานข้อมูล
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "nshop";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // สร้างคำสั่ง SQL ในการดึงข้อมูลจากตาราง products ที่มี category เป็น 'ii'
            $sql = "SELECT * FROM products WHERE category = 'vv'";

            // ดำเนินการค้นหาและเก็บข้อมูลไว้ในตัวแปร
            $result = $conn->query($sql);

            // แสดงข้อมูลในหน้าเว็บ ii.php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>

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

          <div class="ac" style="display: flex;">
              <a class="nav-link" href="profile.php" style="margin-right: 2rem;">
              <i class="fa-solid fa-user" style="display: flex; padding: 5px 13px; border: 2px solid #000; border-radius: 25px; align-items: center;"><h5 style="margin-left: 1rem; transform: translateY(3px);">My Account</h5></i>
              </a>
          </div>
            </li>

         <div class="cart" style="display: flex; align-items: center;">
            <li class="nav-item">
              <a class="nav-link" href="cart.php" style="margin-right: 10px;">
              <span class="badge bg-danger"><?php echo get_total_items_in_cart(); ?></span>

              <i class="fa-solid fa-cart-shopping" style="font-size: 1.5rem;"></i>
              </a>


            <li class="nav-item">
              <a class="nav-link" href="logout.php" style="padding: 7px 15px; background: #000; color: #FFF; border: 1px solid #000; margin-right: 10px;">Logout</a>
            </li>
         </div>
          
           
            </li>
          </ul>
        </div>
      </div>
    </nav>
<!-- คัดลอกโค้ด navbar จากหน้า index.php มาวางที่นี่ -->

<div class="container mt-5">


<div class="catgory">
<a href="../user.php" class="btn btn-primary mb-3">HOME</a>
    <a href="kk.php" class="btn  mb-3" style="padding: 6px 15px; border: 2px solid #000;">kk</a>
    <a href="nn.php" class="btn  mb-3" style="padding: 6px 15px; border: 2px solid #000;">nn</a>
    <a href="ii.php" class="btn  mb-3" style="padding: 6px 15px; border: 2px solid #000;">ii</a>
    <a href="vv.php" class="btn  mb-3" style="padding: 6px 15px; border: 2px solid #000;">vv</a>
    </div>



     <!-- BANNER -->

     <div class="bannerbox" style="display: flex;">

<div class="banner-container">
<div class="slider">
<img src="https://media.tacdn.com/media/attractions-splice-spp-674x446/09/c3/33/97.jpg">
<img src="https://media.tacdn.com/media/attractions-splice-spp-674x446/09/c3/33/97.jpg">
<img src="https://media.tacdn.com/media/attractions-splice-spp-674x446/09/c3/33/97.jpg">
</div>
<div class="slider-nav">
<button class="prev">&#10094;</button>
<button class="next">&#10095;</button>
</div>
</div>

<div class="text-hot" style="max-width: 60%; padding: 30px; border: 1px solid #000; max-height: 250px;">
<h2>Hot Sale<i class="fa-brands fa-hotjar" style="margin-left: 1rem; color: #FF7000;"></i></h2>
<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem nihil nam consequatur expedita laboriosam. Amet ducimus sequi libero labore modi?</p>
<a href="#" style="text-decoration: none; padding: 7px 16px; border: 1px solid #000; text-transform: uppercase ; color: #000;">Shop Now</a>
</div>


</div>



<script>
const slider = document.querySelector('.slider');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');
const slideWidth = slider.clientWidth / 4; /* adjust slideWidth based on number of images */
let slideIndex = 0;

prevBtn.addEventListener('click', () => {
slideIndex--;
if (slideIndex < 0) {
slideIndex = 3; /* adjust slideIndex based on number of images */
}
slider.style.left = -slideIndex * slideWidth + 'px';
});

nextBtn.addEventListener('click', () => {
slideIndex++;
if (slideIndex > 3) { /* adjust slideIndex based on number of images */
slideIndex = 0;
}
slider.style.left = -slideIndex * slideWidth + 'px';
});

</script>


<script>
const slider2 = document.querySelector('.slider2');
const prevBtn2 = document.querySelector('.prev2');
const nextBtn2 = document.querySelector('.next2');
const slideWidth2 = slider2.clientWidth / 4; /* adjust slideWidth based on number of images */
let slideIndex2 = 0;

prevBtn2.addEventListener('click', () => {
slideIndex2--;
if (slideIndex2 < 0) {
slideIndex2 = 3; /* adjust slideIndex based on number of images */
}
slider2.style.left = -slideIndex2 * slideWidth2 + 'px';
});

nextBtn2.addEventListener('click', () => {
slideIndex2++;
if (slideIndex2 > 3) { /* adjust slideIndex based on number of images */
slideIndex2 = 0;
}
slider2.style.left = -slideIndex2 * slideWidth2 + 'px';
});

</script>

<!-- BANNER -->


                    <!----------- PRODUCT --------->
                    <div class="container">
        <div class="row">


                    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="col">
          <div class="card h-100" style="border: 2px solid black; position: relative;">
          <h6 style="position: absolute; left: 85%;  top:3px; padding: 2px 7px; background-color: #FFBF00;">Sale</h6>
          <div class="align-middle" style="display: flex; align-items: center; justify-content: center;">
          <img src="<?= $row['image']; ?>" class="card-img-top w-75" alt="<?= $row['name']; ?>" style="max-height: 250px; object-fit: cover;">

          </div>
            <div class="card-body" style="display: flex; justify-content: space-between;">
              <h5 class="card-title"><?= $row['name']; ?></h5>
              <p class="card-text"><?= number_format($row['price'], 2); ?> <strong>THB</strong></p>
            </div>
            <div class="card-footer" style="display: flex; justify-content: space-between;">
              <a href="login.php" style="text-decoration: none; color: #000; padding: 5px 13px; border: 2px solid #000;">Add Cart</a>
              <a href="#" data-bs-toggle="modal" data-bs-target="#productModal-<?= $row['id']; ?>">Description</a>
              <!-- <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-warning">Edit</a> -->
              <!-- <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a> -->
            </div>
          </div>
        </div>

           <!-- Add the Modal code here -->
           <div class="modal fade" id="productModal-<?= $row['id']; ?>" tabindex="-1" aria-labelledby="productModalLabel-<?= $row['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="productModalLabel-<?= $row['id']; ?>"><?= $row['name']; ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: #000;"></button>
                </div>
                <div class="modal-body" style="display: flex; justify-content: space-between; padding: 5px 30px;">
                  <img src="<?= $row['image']; ?>" class="img-fluid mb-3" alt="<?= $row['name']; ?>" style="width: 200px;">
                  <div class="pbox" style="width: 60%; border: 1px solid #000; align-items: center; display: flex; text-align: center; justify-content: center;" >
                  <p style=" align-items: center; justify-content: center;"><?= $row['description']; ?></p>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End of Modal code -->



      <?php endwhile; ?>
    </div>


  </div>

                     <!----------- PRODUCT --------->

                    <?php
                }
            } else {
                echo "0 results";
            }

            $conn->close();
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
