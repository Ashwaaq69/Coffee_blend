<?php require "../layouts/header.php"; ?>
<?php require "../../config/config.php"; ?>

<?php 
  if(!isset($_SESSION['admin_name'])) {
    header("location: ".ADMINURL."/admins/login-admins.php");
  }

  // Get the current page number, default to 1
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $perPage = 10; // Number of orders per page
  $offset = ($page - 1) * $perPage;

  // Get the search term from the GET request
  $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

  // SQL Query to fetch filtered orders with LIMIT and OFFSET for pagination
  $query = "SELECT * FROM orders WHERE 
            first_name LIKE :search OR 
            town LIKE :search OR 
            state LIKE :search OR 
            zip_code LIKE :search OR 
            phone LIKE :search OR 
            street_address LIKE :search OR 
            status LIKE :search
            LIMIT :limit OFFSET :offset";

  // Prepare the query
  $stmt = $conn->prepare($query);

  // Bind the search term and pagination values
  $stmt->bindValue(':search', "%$searchTerm%");
  $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

  // Execute the query
  $stmt->execute();

  // Fetch the orders
  $orders = $stmt->fetchAll(PDO::FETCH_OBJ);

  // Get total count of orders for pagination
  $countQuery = "SELECT COUNT(*) FROM orders WHERE 
                 first_name LIKE :search OR 
                 town LIKE :search OR 
                 state LIKE :search OR 
                 zip_code LIKE :search OR 
                 phone LIKE :search OR 
                 street_address LIKE :search OR 
                 status LIKE :search";

  $countStmt = $conn->prepare($countQuery);
  $countStmt->bindValue(':search', "%$searchTerm%");
  $countStmt->execute();
  $totalOrders = $countStmt->fetchColumn();
  $totalPages = ceil($totalOrders / $perPage);
?>

<!-- Filter Form -->
<div class="row mb-4">
    <div class="col-md-6">
        <form action="" method="GET">
            <div class="form-group">
                <input style="width: 300px;" type="text" name="search" class="form-control" placeholder="Search..."
                    value="<?php echo $searchTerm; ?>">
            </div>
            <!-- <button type="submit" class="btn btn-primary">Search</button> -->
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Orders</h5>

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">First Name</th>
                            <th scope="col">Town</th>
                            <th scope="col">State</th>
                            <th scope="col">Zip Code</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Street Address</th>
                            <th scope="col">Total Price</th>
                            <th scope="col">Status</th>
                            <th scope="col">Update</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order) : ?>
                        <tr>
                            <td><?php echo $order->first_name; ?></td>
                            <td><?php echo $order->town; ?></td>
                            <td><?php echo $order->state; ?></td>
                            <td><?php echo $order->zip_code; ?></td>
                            <td><?php echo $order->phone; ?></td>
                            <td><?php echo $order->street_address; ?></td>
                            <td>$<?php echo $order->total_price; ?></td>
                            <td><?php echo $order->status; ?></td>
                            <td><a href="change-status.php?id=<?php echo $order->id; ?>"
                                    class="btn btn-warning text-white">Update</a></td>
                            <td><a href="delete-orders.php?id=<?php echo $order->id; ?>"
                                    class="btn btn-danger">Delete</a></td>
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
                    <a class="page-link"
                        href="?page=<?php echo $page - 1; ?>&search=<?php echo $searchTerm; ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link"
                        href="?page=<?php echo $i; ?>&search=<?php echo $searchTerm; ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $page + 1; ?>&search=<?php echo $searchTerm; ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<?php require "../layouts/footer.php"; ?>