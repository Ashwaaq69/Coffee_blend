<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>
<?php 

  if(isset($_SESSION['username'])) {
    header("location: ".APPURL."");
  }

  if(isset($_POST['submit'])) {

    if(empty($_POST['username']) OR empty($_POST['email']) OR empty($_POST['password'])) {
      echo "<script>alert('One or more inputs are empty');</script>";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      echo "<script>alert('Invalid email format');</script>";
    } elseif (strlen($_POST['password']) < 6) {
      echo "<script>alert('Password must be at least 6 characters long');</script>";
    } elseif (!preg_match("/^[a-zA-Z]*$/", $_POST['username'])) {
      echo "<script>alert('Username must contain only letters');</script>";
    } else {
      $username = $_POST['username'];
      $email = $_POST['email'];
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

      $insert = $conn->prepare("INSERT INTO users (username, email, password)
       VALUES (:username, :email, :password)");

      $insert->execute([
        ":username" => $username,
        ":email" => $email,
        ":password" => $password
      ]);

      header("location: login.php");
    }
  }
?>

<section class="home-slider owl-carousel">
  <div class="slider-item" style="background-image: url(<?php echo APPURL; ?>/images/bg_2.jpg);" data-stellar-background-ratio="0.5">
    <!-- <div class="overlay"></div> -->
    <section class="ftco-section" style="margin-top: 50px;">
      <div class="container">
        <div class="row">
          <div class="col-md-12 ftco-animate">
            <form action="register.php" method="POST" class="billing-form ftco-bg-dark p-3 p-md-5" onsubmit="return validateForm()">
              <h3 class="mb-4 billing-heading">Register</h3>
              <div class="row align-items-end">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="Username">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Username" >
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="Email">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="Password">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" >
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group mt-4">
                    <div class="radio">
                      <button type="submit" name="submit" class="btn btn-primary py-3 px-4">Register</button>
                    </div>
                  </div>
                </div>
              </div>
            </form><!-- END -->
          </div> <!-- .col-md-8 -->
        </div>
      </div>
    </section> 
    <!-- .section -->
  </div>
</section>

<script>
  function validateForm() {
    const username = document.forms["register"]["username"].value;
    const email = document.forms["register"]["email"].value;
    const password = document.forms["register"]["password"].value;

    if (username == "" || email == "" || password == "") {
      alert("One or more inputs are empty");
      return false;
    }

    const usernamePattern = /^[a-zA-Z]*$/;
    if (!usernamePattern.test(username)) {
      alert("Username must contain only letters");
      return false;
    }

    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
      alert("Invalid email format");
      return false;
    }

    if (password.length < 6) {
      alert("Password must be at least 6 characters long");
      return false;
    }

    return true;
  }
</script>

<?php require "../includes/footer.php"; ?>