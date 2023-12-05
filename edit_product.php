<?php
include 'config.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $description = $_POST["description"];
  $image = $_POST["image"];
  $price = $_POST["price"];

  $sql = "UPDATE products SET name=?, description=?, image=?, price=? WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssdi", $name, $description, $image, $price, $id);

  if ($stmt->execute()) {
    header("Location: admin.php");
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

$sql = "SELECT * FROM products WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <h1>Edit Product</h1>
    <form method="post">
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?= $product['name']; ?>" required>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description"><?= $product['description']; ?></textarea>
      </div>
      <div class="mb-3">
        <label for="image" class="form-label">Image URL</label>
        <input type="text" class="form-control" id="image" name="image" value="<?= $product['image']; ?>">
      </div>
      <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $product['price']; ?>" required>
      </div>
      <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
  </div>
<?php include 'footer.php'; ?>