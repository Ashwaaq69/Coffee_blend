<?php require "../layouts/header.php"; ?>
<?php require "../../config/config.php"; ?>
<?php

try {
    // Prepare and execute the SQL statement to calculate total income
    $query = $conn->prepare("SELECT SUM(total_price) AS total_income FROM orders WHERE status = 'Delivered'");
    $query->execute();

    // Fetch the result
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $total_income = $result['total_income'] ?? 0; // Default to 0 if no income data

} catch (Exception $e) {
    echo "Error calculating income: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Income</title>
    <link rel="stylesheet" href="path/to/your/styles.css"> <!-- Link to your CSS file -->
</head>
<body>

<div class="container">
    <h1>Total Income</h1>
    <p>Total income from delivered orders: <strong><?php echo number_format($total_income, 2); ?> USD</strong></p>
</div>

</body>
</html>

<?php require "../layouts/footer.php"; ?> <!-- Include footer -->
