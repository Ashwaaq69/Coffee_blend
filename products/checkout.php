
<?php ob_start(); // Start output buffering

 require "../includes/header.php"; ?>

<?php require "../config/config.php"; ?>
<?php 


	if(!isset($_SERVER['HTTP_REFERER'])){
		// redirect them to your desired location
		header('location: http://localhost/coffee-blend');
		exit;
	}

	if(!isset($_SESSION['user_id'])) {
		header("location: ".APPURL."");
	}


	if(isset($_POST['submit'])) {

		if(empty($_POST['first_name']) OR empty($_POST['last_name']) OR empty($_POST['state'])
		 OR empty($_POST['street_address']) OR empty($_POST['town']) OR empty($_POST['zip_code'])
		 OR empty($_POST['phone']) OR empty($_POST['email'])) {
			echo "<script>alert('one or more inputs are empty');</script>";
		} else {

			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$state = $_POST['state'];
			$street_address = $_POST['street_address'];
			$town = $_POST['town'];
			$zip_code = $_POST['zip_code'];
			$phone = $_POST['phone'];
			$user_id = $_SESSION['user_id'];
			$status = "Pending";
			$total_price = $_SESSION['total_price'];

			

			$place_orders = $conn->prepare("INSERT INTO orders (first_name, last_name, state, street_address,
			town, zip_code, phone, user_id, status, total_price) VALUES (:first_name, :last_name,
			:state, :street_address, :town, :zip_code, :phone, :user_id, :status, :total_price)");

			$place_orders->execute([
				":first_name" => $first_name,
				":last_name" => $last_name,
				":state" => $state,
				":street_address" => $street_address,
				":town" => $town,
				":zip_code" => $zip_code,
				":phone" => $phone,
				":user_id" => $user_id,
				":status" => $status,
				":total_price" => $total_price,
			]);

			header("location: pay.php");

		}
	}


?>
    <section class="home-slider owl-carousel">

      <div class="slider-item" style="background-image: url(<?php echo APPURL; ?>/images/bg_3.jpg);" data-stellar-background-ratio="0.5">
      	<div class="overlay"></div>
        <div class="container">
          <div class="row slider-text justify-content-center align-items-center">

            <div class="col-md-7 col-sm-12 text-center ftco-animate">
            	<h1 class="mb-3 mt-5 bread">Checkout</h1>
	            <p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home</a></span> <span>Checout</span></p>
            </div>

          </div>
        </div>
      </div>
    </section>

    <section class="ftco-section">
      <div class="container">
        <div class="row">
          <div class="col-md-12 ftco-animate">
			<form action="checkout.php" method="POST" class="billing-form ftco-bg-dark p-3 p-md-5">
				<h3 class="mb-4 billing-heading">Billing Details</h3>
	          	<div class="row align-items-end">
	          		<div class="col-md-6">
	                <div class="form-group">
	                	<label for="firstname">Firt Name</label>
	                  <input name="first_name" type="text" class="form-control" placeholder="">
	                </div>
	              </div>
	              <div class="col-md-6">
	                <div class="form-group">
	                	<label for="lastname">Last Name</label>
	                  <input name="last_name" type="text" class="form-control" placeholder="">
	                </div>
                </div>
                <div class="w-100"></div>
		            <div class="col-md-12">
		            	<div class="form-group">
		            		<label for="country">State / Country</label>
		            		<div class="select-wrap">
		                  <div class="icon"><span class="ion-ios-arrow-down"></span></div>
		                  <select name="state" id="" class="form-control">
		                  	<option value="Somalia">Somalia</option>
		                    <option value="hargeysa">hargeysa</option>
		                    <option value="saudia">saudia</option>
		                    <option value="Dubai">Dubai</option>
		                    <option value="Hongkong">Hongkong</option>
		                    <option value="Japan">Japan</option>
		                  </select>
		                </div>
		            	</div>
		            </div>
		            <div class="w-100"></div>
		            <div class="col-md-12">
		            	<div class="form-group">
	                	<label for="streetaddress">Street Address</label>
	                  <input name="street_address" type="text" class="form-control" placeholder="House number and street name">
	                </div>
		            </div>
		        
		            </div>
		            <div class="w-100"></div>
		            <div class="col-md-12">
		            	<div class="form-group">
	                	<label for="towncity">Town / City</label>
	                  <input name="town" type="text" class="form-control" placeholder="">
	                </div>
		            </div>
		            <div class="col-md-12">
		            	<div class="form-group">
		            		<label for="postcodezip">Postcode / ZIP *</label>
	                  <input name="zip_code" type="text" class="form-control" placeholder="">
	                </div>
		            </div>
		            <div class="w-100"></div>
		            <div class="col-md-12">
	                <div class="form-group">
	                	<label for="phone">Phone</label>
	                  <input name="phone" type="text" class="form-control" placeholder="">
	                </div>
	              </div>
	              <div class="col-md-12">
	                <div class="form-group">
	                	<label for="emailaddress">Email Address</label>
	                  <input name="email" type="text" class="form-control" placeholder="">
	                </div>
                </div>
                <div class="w-100"></div>
                <div class="col-md-12">
                	<div class="form-group mt-4">
					<div class="radio">
                      <p><button type="submit" name="submit" class="btn btn-primary py-3 px-4">Place an order an pay</button></p>

						</div>
					</div>
                </div>
	            </div>
	          </form><!-- END -->



          </div> <!-- .col-md-8 -->

           
          </div>

        </div>
      </div>
    </section> <!-- .section -->

<?php require "../includes/footer.php"; ?>
