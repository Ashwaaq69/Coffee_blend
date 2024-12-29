<?php require "../layouts/header.php"; ?>
<?php require "../../config/config.php"; ?>

<?php
if(!isset($_SESSION['admin_name'])) {
    header("location: ".ADMINURL."/admins/login-admins.php");
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch current product data
    $product = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $product->execute([':id' => $id]);
    $product = $product->fetch(PDO::FETCH_OBJ);

    if(!$product) {
        header("location: show-products.php");
        exit;
    }
}

if(isset($_POST['update'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $image = $product->image;

    // Check if a new image is uploaded
    if(!empty($_FILES['image']['name'])) {
        // Delete old image
        if(file_exists("images/".$image)) {
            unlink("images/".$image);
        }

        // Save new image
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "images/".$image);
    }

    // Update product information in the database
    $update = $conn->prepare("UPDATE products SET name = :name, price = :price, type = :type, image = :image WHERE id = :id");
    if ($update->execute([
        ':name' => $name,
        ':price' => $price,
        ':type' => $type,
        ':image' => $image,
        ':id' => $id
    ])) {
        header("location: show-products.php");
    } else {
        echo "Failed to update product.";
    }
}
?>

<div class="container mt-5">
    <h2>Update Product</h2>
    <form action="update-products.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo $product->name; ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" class="form-control" value="<?php echo $product->price; ?>" required>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <input type="text" name="type" class="form-control" value="<?php echo $product->type; ?>" required>
        </div>
        <div class="form-group">
            <label for="image">Product Image</label>
            <input type="file" name="image" class="form-control">
            <p>Current Image: <img src="images/<?php echo $product->image; ?>" width="60px" height="60px"></p>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Update Product</button>
    </form>
</div>

<?php require "../layouts/footer.php"; ?>
