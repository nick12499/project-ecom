<?php
include 'config.php';

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en">

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

    <title>Product Management System</title>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
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
        padding: 10px 30px;
    }

    .card-body h5,
    h6,
    p {
        font-family: 'IBM Plex Sans Thai', sans-serif;
    }

    .card-body h6 {
        font-size: 13px;
        color: #606060;
        font-size: 1rem;
        text-align: start;
        margin-bottom: 0.25rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .card-body h5 {
        font-size: 17px;
        color: #606060;
        text-transform: uppercase;
        font-size: 1rem;
        text-align: start;
        margin-bottom: 0.25rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .card-body h5:hover {
        color: #d4001a;
    }

    .card-body p {
        color: #d4001a;
    }
    </style>
</head>

<body>



    <nav class="navbar navbar-expand-lg navbar-light bg-light w-100"
        style=" background-color: #FFF; box-shadow: 0 0 10px #000; position: fixed; z-index: 999; transform: translateY(-80px);">
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
                        <a class="navlogin nav-link" href="login.php"
                            style="padding: 7px 7px; margin-right: 10px; color: #d4001a;"><i
                                class="fa-sharp fa-regular fa-right-to-bracket"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php" style="padding: 7px 7px; color: #000;"> <i
                                class="fa-regular fa-user"></i> Register</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>




    <div class="container" style="margin-top: 100px;">



        <!-- BANNER -->







        <!-- BANNER -->


        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100">

                    <div class="align-middle">
                        <img src="<?= $row['image']; ?>" class="card-img-top img-fluid" alt="<?= $row['name']; ?>">

                    </div>
                    <div class="card-body">
                        <h5><?= $row['name']; ?></h5>
                        <h6><?= $row['description']; ?></h6>
                        <p class="card-text"><i class="fa-light fa-baht-sign"></i>
                            <?= number_format($row['price'], 2); ?></p>
                    </div>
                    <div class="card-footer" style="display: flex; justify-content: center;">
                        <a href="javascript:void(0);" onclick="addToCart(<?php echo $row['id']; ?>);">ADD CART</a>
                    </div>
                </div>
            </div>






            <!-- Add the Modal code here -->
            <div class="modal fade" id="productModal-<?= $row['id']; ?>" tabindex="-1"
                aria-labelledby="productModalLabel-<?= $row['id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="productModalLabel-<?= $row['id']; ?>"><?= $row['name']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="color: #000;"></button>
                        </div>
                        <div class="modal-body"
                            style="display: flex; justify-content: space-between; padding: 5px 30px;">
                            <img src="<?= $row['image']; ?>" class="img-fluid mb-3" alt="<?= $row['name']; ?>"
                                style="width: 200px;">
                            <div class="pbox"
                                style="width: 60%; border: 1px solid #000; align-items: center; display: flex; text-align: center; justify-content: center;">
                                <p style=" align-items: center; justify-content: center;"><?= $row['description']; ?>
                                </p>
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






    <?php include 'footer.php'; ?>