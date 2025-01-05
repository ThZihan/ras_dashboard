<?php 
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAS Parameter Ranges Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .range-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .success-message {
            color: green;
            margin: 10px 0;
        }
        .error-message {
            color: red;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
<div class="hero">
  <div class="title">Recirculating Aquaculture System (RAS)</div>
  <div class="title t2">Admin Dashboard</div>

  <a href="logout.php" class="btn btn-primary customBtn loginBtn">Logout</a>
  
</div>

  <div class="project-info">
    <img src="image/fablab.png" alt="FabLab IUB Logo">
    <div>
      <p>Developed by FabLab IUB</p>
      <p>A joint project by IUB & SAU</p>
    </div>
    <img src="image/iub.png" alt="IUB Logo">
    <img src="image/sau.png" alt="SAU Logo">
  </div>
  <div class="download-container">
    <button class="download-button customBtn" onclick="downloadData()">Download Latest Data</button>
    <a href="download_local_data.php" class="download-button customBtn">Download All Data</a>
  </div>
    <div class="container mt-5">
        <h1 class="mb-4">RAS Parameter Ranges Management</h1>
        <a href="logout.php" class="btn btn-secondary mb-4">LogOut & Back to Dashboard</a>

        <?php
        require_once 'db_conn.php';
        
        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $parameter_id = $_POST['parameter_id'];
            $ideal_min = $_POST['ideal_min'];
            $ideal_max = $_POST['ideal_max'];
            $warning_min = $_POST['warning_min'];
            $warning_max = $_POST['warning_max'];

            $sql = "UPDATE parameter_ranges SET 
                    ideal_min = ?, 
                    ideal_max = ?, 
                    warning_min = ?, 
                    warning_max = ? 
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ddddi", $ideal_min, $ideal_max, $warning_min, $warning_max, $parameter_id);

            if ($stmt->execute()) {
                echo "<div class='success-message'>Parameters updated successfully!</div>";
            } else {
                echo "<div class='error-message'>Error updating parameters: " . $conn->error . "</div>";
            }
        }

        // Fetch current ranges
        $sql = "SELECT * FROM parameter_ranges ORDER BY parameter_name";
        $result = $conn->query($sql);
        ?>

        <div class="row">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-6">
                <div class="range-form">
                    <h3 class="mb-3"><?= ucfirst($row['parameter_name']) ?> <?= $row['unit'] ?></h3>
                    <form method="POST" action="">
                        <input type="hidden" name="parameter_id" value="<?= $row['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Ideal Range:</label>
                            <div class="row">
                                <div class="col">
                                    <input type="number" step="0.01" class="form-control" name="ideal_min" value="<?= $row['ideal_min'] ?>" required>
                                    <small class="form-text text-muted">Min</small>
                                </div>
                                <div class="col">
                                    <input type="number" step="0.01" class="form-control" name="ideal_max" value="<?= $row['ideal_max'] ?>" required>
                                    <small class="form-text text-muted">Max</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Warning Range:</label>
                            <div class="row">
                                <div class="col">
                                    <input type="number" step="0.01" class="form-control" name="warning_min" value="<?= $row['warning_min'] ?>" required>
                                    <small class="form-text text-muted">Min</small>
                                </div>
                                <div class="col">
                                    <input type="number" step="0.01" class="form-control" name="warning_max" value="<?= $row['warning_max'] ?>" required>
                                    <small class="form-text text-muted">Max</small>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Ranges</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <div class="footer">
  <p>© 2024 RAS Monitoring System - Jointly Developed by IUB & SAU</p>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>
</html>
<?php 
}else{
     header("Location: index.php");
     exit();
}
 ?>