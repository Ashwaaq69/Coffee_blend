<?php require "../layouts/header.php"; ?>
<?php require "../../config/config.php"; ?>

<?php 
  if(!isset($_SESSION['admin_name'])) {
    header("location: ".ADMINURL."/admins/login-admins.php");
  }

  if(isset($_POST['submit'])) {

    if(empty($_POST['name']) OR empty($_POST['price']) OR empty($_POST['type']) OR empty($_FILES['image']['name'])) {
      echo "<script>alert('One or more inputs are empty');</script>";
    } elseif (!preg_match("/^[a-zA-Z\s]*$/", $_POST['name'])) {
      echo "<script>alert('Product name must contain only letters and spaces');</script>";
    } elseif (!is_numeric($_POST['price'])) {
      echo "<script>alert('Price must be a valid number');</script>";
    } else {
      $name = $_POST['name'];
      $price = $_POST['price'];
      $type = $_POST['type'];
      $image = $_FILES['image']['name'];
      $image_tmp = $_FILES['image']['tmp_name'];

      move_uploaded_file($image_tmp, "images/$image");

      $insert = $conn->prepare("INSERT INTO products (name, price, type, image)
       VALUES (:name, :price, :type, :image)");

      $insert->execute([
        ":name" => $name,
        ":price" => $price,
        ":type" => $type,
        ":image" => $image
      ]);

      header("location: show-products.php");
    }
  }

  // Get the current page number, default to 1
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $perPage = 10; // Number of products per page
  $offset = ($page - 1) * $perPage;

  // Get the search term from the GET request
  $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

  // SQL Query to fetch filtered products with LIMIT and OFFSET for pagination
  $query = "SELECT * FROM products WHERE 
            name LIKE :search OR 
            type LIKE :search
            LIMIT :limit OFFSET :offset";

  // Prepare the query
  $stmt = $conn->prepare($query);

  // Bind the search term and pagination values
  $stmt->bindValue(':search', "%$searchTerm%");
  $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

  // Execute the query
  $stmt->execute();

  // Fetch the products
  $products = $stmt->fetchAll(PDO::FETCH_OBJ);

  // Get total count of products for pagination
  $countQuery = "SELECT COUNT(*) FROM products WHERE 
                 name LIKE :search OR 
                 type LIKE :search";

  $countStmt = $conn->prepare($countQuery);
  $countStmt->bindValue(':search', "%$searchTerm%");
  $countStmt->execute();
  $totalProducts = $countStmt->fetchColumn();
  $totalPages = ceil($totalProducts / $perPage);
?>

<!-- Filter Form -->
<div class="row mb-4">
  <div class="col-md-6">
    <form action="" method="GET">
      <div class="form-group">
        <input style="width:300px;" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo $searchTerm; ?>">
      </div>
      <!-- <button type="submit" class="btn btn-primary">Search</button> -->
    </form>
  </div>
</div>

<!-- Products Table -->
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-4 d-inline">Products</h5>
        <a href="create-products.php" class="btn btn-primary mb-4 text-center float-right">Create Products</a>

        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Image</th>
              <th scope="col">Price</th>
              <th scope="col">Type</th>
              <th scope="col">Update</th>
              <th scope="col">Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($products as $product) : ?>
              <tr>
                <th scope="row"><?php echo $product->id; ?></th>
                <td><?php echo $product->name; ?></td>
                <td><img src="images/<?php echo $product->image; ?>" style="width: 60px; height: 60px"></td>
                <td>$<?php echo $product->price; ?></td>
                <td><?php echo $product->type; ?></td>
                <td>
                  <a href="update-products.php?id=<?php echo $product->id; ?>" class="btn btn-warning text-center">Update</a>
                </td>
                <td>
                  <a href="delete-products.php?id=<?php echo $product->id; ?>" class="btn btn-danger text-center">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Pagination -->
<div class="row">
  <div class="col text-center">
    <nav aria-label="Page navigation">
      <ul class="pagination">
        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $searchTerm; ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $searchTerm; ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>
        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $searchTerm; ?>">Next</a>
        </li>
      </ul>
    </nav>
  </div>
</div>

<?php require "../layouts/footer.php"; ?>