<?php
session_start();

// Ensure CSRF token is set before form submission
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BRAHMAKUMARI</title>
  <link rel="icon" type="image/x-icon" href="../../assets/images/bk-logo-fav.png">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/login.css">
  <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
  <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">

  <style>
    ul li {
      list-style-type: none;
      padding: 0px 20px;
    }
    ul li a {
      color: #757575;
      font-size: 12px;
    }
    @media (max-width: 575px) {
      ul li {
        padding: 0px 7px;
      }
      ul li a {
        font-size: 10px;
      }
    }
  </style>
</head>

<body>
  <main>
    <div class="container-fluid">
      <div class="row">
        <!-- Left Side (Image Carousel) -->
        <div class="col-sm-6 px-0 d-none d-sm-block">
          <div class="position-relative">
            <div class="owl-carousel">
              <div class="position-relative">
                <img src="assets/images/final-home-carousel-meditation.jpg" alt="login image" class="login-img">
                <div class="login-img-text">
                  <h2 class="text-white">Get Everything You Want</h2>
                  <p class="text-white">You can get everything you want if you work hard, trust the process, and stick to the plan.</p>
                </div>
              </div>
              <div class="position-relative">
                <img src="assets/images/AVHP-Images-for-website-5.jpg" alt="login image" class="login-img">
                <div class="login-img-text">
                  <h2 class="text-white">Get Everything You Want</h2>
                  <p class="text-white">You can get everything you want if you work hard, trust the process, and stick to the plan.</p>
                </div>
              </div>
              <div class="position-relative">
                <img src="assets/images/AVHP-Images-for-website-6.jpg" alt="login image" class="login-img">
                <div class="login-img-text">
                  <h2 class="text-white">Get Everything You Want</h2>
                  <p class="text-white">You can get everything you want if you work hard, trust the process, and stick to the plan.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Side (Login Form) -->
        <div class="col-sm-6 login-section-wrapper">
          <div class="login-wrapper my-auto">
            <img src="assets/images/bk-logo.png" alt="logo" class="logo">
            <h1 class="login-title">Login</h1>
            <h3 class="login-subtitle mb-4">Hi, welcome back</h3>

            <form id="loginForm" autocomplete="off" action="login_action.php" method="post">
              <!-- ✅ CSRF Token -->
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

              <!-- ✅ Fixed 'email' field -->
              <div class="form-group">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
              </div>

              <div class="form-group mb-4">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
              </div>

              <!-- ✅ Added name="login" -->
              <button id="loginBtn" class="btn btn-block login-btn" type="submit" name="login">Login</button>
            </form>
          </div>

          <ul class="d-flex p-0">
            <li class="pl-0"><a href="privacy_policy.php">Privacy Policy</a></li>
            <li><a href="refund_policy.php">Refund Policy</a></li>
            <li><a href="terms_and_conditions.php">Terms & Conditions</a></li>
            <li><a href="about_us.php">About Us</a></li>
          </ul>
        </div>
      </div>
    </div>
  </main>

  <script src="assets/scripts/jquery-3.5.1.min.js"></script>
  <script src="assets/scripts/owl.carousel.min.js"></script>
  <script src="assets/scripts/sweetalert.min.js"></script>

  <script>
    $(document).ready(function () {
      // Check for login error message
      const urlParams = new URLSearchParams(window.location.search);
      const message = urlParams.get('error'); // Ensure this matches the query parameter in the URL

      if (message === 'Invalid email or password!') {
        swal({
          title: "Error!",
          text: "Invalid email or password. Please try again.",
          icon: "error"
        }).then(() => {
          const url = new URL(window.location.href);
          url.searchParams.delete('error'); // Correcting the parameter name
          window.history.replaceState({}, document.title, url.toString());
        });
      }

      // Initialize the Owl Carousel
      $(".owl-carousel").owlCarousel({
        items: 1,
        loop: true,
        autoplay: true,
        autoplayTimeout: 4500,
        smartSpeed: 1000,
        nav: false,
        dots: false
      });
    });
</script>


</body>
</html>
