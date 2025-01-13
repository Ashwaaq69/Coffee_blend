<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>
<?php 

  if(isset($_SESSION['username'])) {
    header("location: ".APPURL."");
  }
  
  if(isset($_POST['submit'])) {

    if(empty($_POST['email']) OR empty($_POST['password'])) {
      echo "<script>alert('One or more inputs are empty');</script>";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      echo "<script>alert('Invalid email format');</script>";
    } else { 

      $email = $_POST['email'];
      $password = $_POST['password'];

      //write a query to check for email
      $login = $conn->query("SELECT * FROM users WHERE email='$email'");
      $login->execute();

      $fetch = $login->fetch(PDO::FETCH_ASSOC);

      if($login->rowCount() > 0) {

        if(password_verify($password, $fetch['password'])) {
          //start session
          $_SESSION['username'] = $fetch['username'];
          $_SESSION['email'] = $fetch['email'];
          $_SESSION['user_id'] = $fetch['id'];

          header("location: ".APPURL."");

        } else {
          echo "<script>alert('Email or password is wrong');</script>";
        }
      } else {
        echo "<script>alert('Email or password is wrong');</script>";
      }
    }
  }
?>

<section class="home-slider owl-carousel">
  <div class="slider-item" style="background-image: url(<?php echo APPURL; ?>/images/bg_1.jpg);" data-stellar-background-ratio="0.5">
    <!-- <div class="overlay"></div> -->
    <section class="ftco-section" style="margin-top: 100px;">
      <div class="container">
        <div class="row">
          <div class="col-md-12 ftco-animate">
            <form action="login.php" method="POST" class="billing-form ftco-bg-dark p-3 p-md-5" onsubmit="return validateForm()">
              <h3 class="mb-4 billing-heading">Login</h3>
              <div class="row align-items-end">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="Email">Email</label>
                    <input name="email" type="email" class="form-control" placeholder="Email" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="Password">Password</label>
                    <input name="password" type="password" class="form-control" placeholder="Password" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group mt-4">
                    <div class="radio">
                      <button type="submit" name="submit" class="btn btn-primary py-3 px-4">Login</button>
                    </div>
                  </div>
                </div>
              </div> <!-- .row -->
            </form><!-- END -->
          </div> <!-- .col-md-12 -->
        </div> <!-- .row -->
      </div> <!-- .container -->
    </section>
  </div>
</section>

<script>
  function validateForm() {
    const email = document.forms["login"]["email"].value;
    const password = document.forms["login"]["password"].value;

    if (email == "" || password == "") {
      alert("One or more inputs are empty");
      return false;
    }

    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
      alert("Invalid email format");
      return false;
    }

    return true;
  }
</script>

<?php require "../includes/footer.php"; ?>