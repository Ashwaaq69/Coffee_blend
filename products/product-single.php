<?php
require "../includes/header.php";
require "../config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch data for a single product
    $product = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $product->execute([':id' => $id]);
    $singelProduct = $product->fetch(PDO::FETCH_OBJ);

    // Add to cart
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $image = $_POST['image'];
        $price = $_POST['price'];
        $pro_id = $_POST['pro_id'];
        $description = $_POST['description'];
        $quantity = (int)$_POST['quantity']; // Casting quantity as integer
        $user_id = $_SESSION['user_id'];

        // Insert the product as a new entry each time
        $insert_cart = $conn->prepare("INSERT INTO cart (name, image, price, pro_id, description, quantity, user_id) VALUES (:name, :image, :price, :pro_id, :description, :quantity, :user_id)");

        $insert_cart->execute([
            ":name" => $name,
            ":image" => $image,
            ":price" => $price,
            ":pro_id" => $pro_id,
            ":description" => $description,
            ":quantity" => $quantity,
            ":user_id" => $user_id
        ]);

        // Increment cart count in session
        $_SESSION['cart_count'] += $quantity;  // Add the quantity to the cart count

        echo "<script>alert('Added to cart successfully');</script>";
        // echo "<script>window.location.href = '" . APPURL . "';</script>";
    }
} else {
    header("location: " . APPURL . "/404.php");
}
?>

<!-- HTML for Product Details and Add to Cart Form -->
<section class="ftco-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5 ftco-animate">
                <a href="h-50 images/menu-2.jpg" class="image-popup">
                    <img src="<?php echo IMAGEPRODUCTS; ?>/<?php echo $singelProduct->image; ?>" class="img-fluid" alt="Product Image">
                </a>
            </div>
            <div class="col-lg-6 product-details pl-md-5 ftco-animate">
                <h3><?php echo $singelProduct->name; ?></h3>
                <p class="price"><span>$<?php echo $singelProduct->price; ?></span></p>
                <p><?php echo $singelProduct->description; ?></p>

                <!-- Add to Cart Form -->
                <form method="POST" action="product-single.php?id=<?php echo $id; ?>">
                    <div class="row mt-4">
                        <div class="input-group col-md-6 d-flex mb-3">
                            <span class="input-group-btn mr-2">
                                <button type="button" class="quantity-left-minus btn" data-type="minus" data-field="">
                                    <i class="icon-minus"></i>
                                </button>
                            </span>
                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1" min="1" max="100">
                            <span class="input-group-btn ml-2">
                                <button type="button" class="quantity-right-plus btn" data-type="plus" data-field="">
                                    <i class="icon-plus"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <!-- Hidden Inputs -->
                    <input name="name" value="<?php echo $singelProduct->name; ?>" type="hidden">
                    <input name="image" value="<?php echo $singelProduct->image; ?>" type="hidden">
                    <input name="price" value="<?php echo $singelProduct->price; ?>" type="hidden">
                    <input name="pro_id" value="<?php echo $singelProduct->id; ?>" type="hidden">
                    <input name="description" value="<?php echo $singelProduct->description; ?>" type="hidden">

                    <button name="submit" type="submit" class="btn btn-primary py-3 px-5">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</section>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const minusButton = document.querySelector(".quantity-left-minus");
    const plusButton = document.querySelector(".quantity-right-plus");
    const quantityInput = document.getElementById("quantity");

    minusButton.addEventListener("click", function() {
        let quantity = parseInt(quantityInput.value);
        if (quantity > 1) {
            quantityInput.value = quantity - 1;
        }
    });

    plusButton.addEventListener("click", function() {
        let quantity = parseInt(quantityInput.value);
        if (quantity < 100) { // Assuming max quantity is 100
            quantityInput.value = quantity + 1;
        }
    });
});
</script>

<?php require "../includes/footer.php"; ?>