<?php require "../layouts/header.php"; ?>    
<?php require "../../config/config.php"; ?> 

<?php 
  if(!isset($_SESSION['admin_name'])) {
    header("location: ".ADMINURL."/admins/login-admins.php");
  }

  // Get current page number
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $perPage = 10; // Number of bookings per page
  $offset = ($page - 1) * $perPage;

  // Get the search term from the GET request
  $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

  // Build the query with a wildcard search for all columns
  $query = "SELECT * FROM bookings WHERE 
            first_name LIKE :search OR 
            last_name LIKE :search OR 
            date LIKE :search OR 
            time LIKE :search OR 
            phone LIKE :search OR 
            status LIKE :search";

  // Add LIMIT and OFFSET for pagination
  $query .= " LIMIT :limit OFFSET :offset";

  // Prepare the query
  $stmt = $conn->prepare($query);

  // Bind the search term parameter
  $stmt->bindValue(':search', "%$searchTerm%");
  $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

  $stmt->execute();

  // Fetch the filtered bookings
  $bookings = $stmt->fetchAll(PDO::FETCH_OBJ);

  // Get total count of bookings for pagination
  $countQuery = "SELECT COUNT(*) FROM bookings WHERE 
                 first_name LIKE :search OR 
                 last_name LIKE :search OR 
                 date LIKE :search OR 
                 time LIKE :search OR 
                 phone LIKE :search OR 
                 status LIKE :search";

  // Prepare the count query
  $countStmt = $conn->prepare($countQuery);
  $countStmt->bindValue(':search', "%$searchTerm%");
  $countStmt->execute();
  $totalBookings = $countStmt->fetchColumn();
  $totalPages = ceil($totalBookings / $perPage);
?>

<!-- Filter Form -->
<div class="row mb-4">
  <div class="col-md-6">
    <form action="" method="GET">
      <div class="form-group">
        <input style="width: 300px;" type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo $searchTerm; ?>">
      </div>
      <!-- <button type="submit" class="btn btn-primary">Search</button> -->
    </form>
  </div>
</div>

<!-- Bookings Table -->
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-4">Bookings</h5>
        
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">First Name</th>
              <th scope="col">Last Name</th>
              <th scope="col">Date</th>
              <th scope="col">Time</th>
              <th scope="col">Phone</th>
              <th scope="col">Message</th>
              <th scope="col">Status</th>
              <th scope="col">Change Status</th>
              <th scope="col">Created At</th>
              <th scope="col">Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($bookings as $booking) : ?>
            <tr>
              <th scope="row"><?php echo $booking->id; ?></th>
              <td><?php echo $booking->first_name; ?></td>
              <td><?php echo $booking->last_name; ?></td>
              <td><?php echo $booking->date; ?> </td>
              <td><?php echo $booking->time; ?></td>
              <td><?php echo $booking->phone; ?></td>
              <td><?php echo $booking->message; ?></td>
              <td><?php echo $booking->status; ?></td>
              <td><a href="change-status.php?id=<?php echo $booking->id; ?>" class="btn btn-warning text-white">Change Status</a></td>
              <td><?php echo $booking->created_at; ?></td>
              <td><a href="delete-bookings.php?id=<?php echo $booking->id; ?>" class="btn btn-danger">Delete</a></td>
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
