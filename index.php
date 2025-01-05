<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RAS Monitoring System Dashboard</title>
  <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
  <link rel="stylesheet" href="style.css">

</head>
<body>
<!-- Hero Section -->
<div class="hero">
  <div class="title">Recirculating Aquaculture System (RAS)</div>
  <div class="title t2">Monitoring Dashboard</div>

  <a href="#login-modal" class="btn btn-primary customBtn loginBtn">Log In</a>
  <!-- Login Modal -->
  <div id="login-modal" class="custom-modal loginModal">
    <div class="modal__content">
      <h2 class="modal__title">Login to <br> Admin Panel</h2>
      <!-- Login Form -->
  <form action="login.php" method="post" class="login-form" style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px;">

    <!-- Error Message -->
    <?php if (isset($_GET['error'])) { ?>
      <p class="alert alert-danger"><?php echo $_GET['error']; ?></p>
    <?php } ?>

    <div class="mb-3">
      <label for="uname" class="form-label">User ID</label>
      <input type="text" class="form-control" id="uname" name="uname" placeholder="Enter your User Name" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" placeholder="Enter your Password" required>
    </div>

    <div class="d-flex justify-content-between align-items-center">
      <a href="#" onclick="contactDev()" class="text-muted">Forgot Password?</a>
      <button type="submit" class="btn btn-primary customBtn">Login</button>
    </div>
  </form>
      <a href="#" class="modal__close" aria-label="Close">×</a>
    </div>
  </div>

  <div class="info-box" id="current-time"></div>
  <div class="environmental-data" id="environmental-data">
    <strong>Environmental Data:</strong>
    <p id="current-temp">Temperature: -- °C</p>
    <p id="current-humidity">Humidity: -- %</p>
  </div>
</div>

<!-- Main Content Wrapper -->
<div class="main-content">
  <h2>Real Time Data from Tank</h2>
  <div class="parameter-container" id="latestData"></div>
  <div class="info-box" id="dataInfo"></div>


  <div class="project-info">
    <img src="image/fablab.png" alt="FabLab IUB Logo">
    <div>
      <p>Developed by FabLab IUB</p>
      <p>A joint project by IUB & SAU</p>
    </div>
    <img src="image/iub.png" alt="IUB Logo">
    <img src="image/sau.png" alt="SAU Logo">
  </div>


  <div class="chart-container">
    <div id="temperatureChart"></div>
    <div id="phChart"></div>
    <div id="turbidityChart"></div>
    <div id="DOChart"></div>
    <div id="ECChart"></div>
    <div id="ORPChart"></div>
    <div id="TDSChart"></div>
  </div>
</div>

<div class="footer">
  <p>© 2024 RAS Monitoring System - Jointly Developed by IUB & SAU</p>
</div>
<script src="main.js"></script>
<!-- Bootstrap JS and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
