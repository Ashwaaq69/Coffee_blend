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

  // Handle report generation
  if (isset($_POST['generate_report'])) {
      $start_date = $_POST['start_date'];
      $end_date = $_POST['end_date'];

      // Fetch filtered data based on criteria
      $reportQuery = "SELECT * FROM orders WHERE created_at BETWEEN :start_date AND :end_date";
      $stmt = $conn->prepare($reportQuery);
      $stmt->bindParam(':start_date', $start_date);
      $stmt->bindParam(':end_date', $end_date);
      $stmt->execute();
      $reportData = $stmt->fetchAll(PDO::FETCH_OBJ);

      // Export to CSV
      if ($_POST['export_format'] == 'csv') {
          // Clear the output buffer
          ob_clean();
          header('Content-Type: text/csv');
          header('Content-Disposition: attachment;filename=report.csv');
          $output = fopen('php://output', 'w');
          fputcsv($output, array('ID', 'First Name', 'Last Name', 'State', 'Street Address', 'Town', 'Zip Code', 'Phone', 'User ID', 'Status', 'Total Price', 'Product Names', 'Created At'));
          foreach ($reportData as $row) {
              fputcsv($output, (array)$row);
          }
          fclose($output);
          exit;
      }

      // Export to PDF
      if ($_POST['export_format'] == 'pdf') {
          require_once('../vendor/autoload.php');
          try {
              $mpdf = new \Mpdf\Mpdf();
              $html = '<h1>Report</h1><table border="1"><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>State</th><th>Street Address</th><th>Town</th><th>Zip Code</th><th>Phone</th><th>User ID</th><th>Status</th><th>Total Price</th><th>Product Names</th><th>Created At</th></tr>';
              foreach ($reportData as $row) {
                  $html .= '<tr>';
                  foreach ($row as $column) {
                      $html .= '<td>' . $column . '</td>';
                  }
                  $html .= '</tr>';
              }
              $html .= '</table>';
              $mpdf->WriteHTML($html);
              $mpdf->Output('report.pdf', 'D');
          } catch (\Mpdf\MpdfException $e) {
              echo "Error generating PDF: " . $e->getMessage();
          }
          exit;
      }
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

<!-- Report Generation Form -->
<div class="row mt-5">
  <div class="col-md-12">
    <h3>Generate Report</h3>
    <form method="POST" action="">
      <div class="form-group">
        <label for="start_date">Start Date</label>
        <input type="date" name="start_date" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="end_date">End Date</label>
        <input type="date" name="end_date" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="export_format">Export Format</label>
        <select name="export_format" class="form-control">
          <option value="csv">CSV</option>
          <option value="pdf">PDF</option>
        </select>
      </div>
      <button type="submit" name="generate_report" class="btn btn-primary">Generate Report</button>
    </form>
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