   <?php require "layouts/header.php"; ?>    
<?php require "../config/config.php"; ?> 

<?php 
  // Check if the user is logged in as admin
  if(!isset($_SESSION['admin_name'])) {
    header("location: ".ADMINURL."/admins/login-admins.php");
  }

  // Fetch data from the database
  // Products
  $products = $conn->query("SELECT COUNT(*) AS count_products FROM products");
  $products->execute();
  $productsCount = $products->fetch(PDO::FETCH_OBJ);

  // Orders
  $orders = $conn->query("SELECT COUNT(*) AS count_orders FROM orders");
  $orders->execute();
  $ordersCount = $orders->fetch(PDO::FETCH_OBJ);

  // Bookings
  $bookings = $conn->query("SELECT COUNT(*) AS count_bookings FROM bookings");
  $bookings->execute();
  $bookingsCount = $bookings->fetch(PDO::FETCH_OBJ);

  // Admins
  $admins = $conn->query("SELECT COUNT(*) AS count_admins FROM admins");
  $admins->execute();
  $adminsCount = $admins->fetch(PDO::FETCH_OBJ);

  // Calculate total income from delivered orders
  try {
      $query = $conn->prepare("SELECT SUM(total_price) AS total_income FROM orders WHERE status = 'Delivered'");
      $query->execute();
      $result = $query->fetch(PDO::FETCH_ASSOC);
      $total_income = $result['total_income'] ?? 0; // Default to 0 if no income data
  } catch (Exception $e) {
      echo "Error calculating income: " . $e->getMessage();
      $total_income = 0; // Set to 0 in case of an error
  }
?>

<!-- Dashboard Overview -->
<div class="row">
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Products</h5>
          <p class="card-text">Number of products: <?php echo $productsCount->count_products; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Orders</h5>
          <p class="card-text">Number of orders: <?php echo $ordersCount->count_orders; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Bookings</h5>
          <p class="card-text">Number of bookings: <?php echo $bookingsCount->count_bookings; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Admins</h5>
          <p class="card-text">Number of admins: <?php echo $adminsCount->count_admins; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3"> <!-- New card for Total Income -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Total Income</h5>
          <p class="card-text">Total income from delivered orders: <strong><?php echo number_format($total_income, 2); ?> USD</strong></p>
        </div>
      </div>
    </div>
</div>

<!-- Chart.js for visualizations -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Reports Section with Chart -->
<div class="row mt-5">
  <div class="col-md-6">
    <h3>Summary Report</h3>
    <canvas id="summaryChart"></canvas>
  </div>

  <div class="col-md-6">
    <h3>Detailed Product Report</h3>
    <canvas id="productChart"></canvas>
  </div>
</div>

<script>
  // Data passed from PHP to JavaScript
  const productsCount = <?php echo $productsCount->count_products; ?>;
  const ordersCount = <?php echo $ordersCount->count_orders; ?>;
  const bookingsCount = <?php echo $bookingsCount->count_bookings; ?>;
  const adminsCount = <?php echo $adminsCount->count_admins; ?>;
  const totalIncome = <?php echo $total_income; ?>;

  // Create the summary chart
  const ctx1 = document.getElementById('summaryChart').getContext('2d');
  const summaryChart = new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: ['Products', 'Orders', 'Bookings', 'Admins', 'Total Income'],
      datasets: [{
        label: 'Count',
        data: [productsCount, ordersCount, bookingsCount, adminsCount, totalIncome],
        backgroundColor: [
          'rgba(75, 192, 192, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 99, 132, 0.2)' // Different color for total income
        ],
        borderColor: [
          'rgba(75, 192, 192, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 99, 132, 1)' // Different color for total income
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Detailed Product Report Chart
  const ctx2 = document.getElementById('productChart').getContext('2d');
  const productChart = new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: ['Products', 'Orders', 'Bookings'],
      datasets: [{
        label: 'Product Report',
        data: [productsCount, ordersCount, bookingsCount],
        backgroundColor: [
          'rgba(75, 192, 192, 0.2)',
          'rgba(255, 99, 132, 0.2)',
          'rgba(255, 206, 86, 0.2)'
        ],
        borderColor: [
          'rgba(75, 192, 192, 1)',
          'rgba(255, 99, 132, 1)',
          'rgba(255, 206, 86, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>

<?php require "layouts/footer.php"; ?>
