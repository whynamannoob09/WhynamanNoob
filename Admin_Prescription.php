<?php
session_start();

include("connection.php");
include("function.php");

$user_data = check_login($con);

$query = "SELECT * FROM prescriptions_data";
$result = $con->query($query);
$prescriptions = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Prescription</title>

  <!-- Local Stylesheets -->
  <link rel="stylesheet" href="plugins/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

  <style>
    .table-responsive {
        width: 100%;
        text-align: center;
    }
    .content-wrapper {
        padding-left: 5%;
        padding-right: 5%;
        padding-top: 3%;
    }
    .nav-treeview .nav-item {
        padding-left: 3%;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src=".//img/logo.png" alt="Logo" height="200" width="200">
    <h2>Loading...</h2>
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link">Home</a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <!-- Search form -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#">
            <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
            <form class="form-inline" action="patientRecords.php" method="post">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" name="search" placeholder="Search by PID or Name" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
      </li>

      <!-- Fullscreen -->
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#"><i class="fas fa-expand-arrows-alt"></i></a>
      </li>

      <!-- Logout with confirmation -->
      <li class="nav-item">
        <a href="logout.php" class="nav-link" onclick="return confirmLogout(event);">
          <i class="nav-icon fas fa-sign-out-alt"></i> Log out
        </a>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <img src=".//img/logo.png" alt="Logo" class="brand-image img-circle elevation-4">
      <span class="brand-text font-weight-light">IMSClinic_HRMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <li class="nav-item">
            <a href="Dashboard_Admin.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-folder"></i>
              <p>Services <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="patientRecords.php" class="nav-link">
                  <i class="nav-icon fas fa-user"></i>
                  <p>Patient Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="Admin_Prescription.php" class="nav-link active">
                  <i class="nav-icon fas fa-prescription"></i>
                  <p>Prescription</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="medical_Records.php" class="nav-link">
                  <i class="nav-icon fas fa-file-medical"></i>
                  <p>Add Medical Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="genReports.php" class="nav-link">
                  <i class="nav-icon fas fa-print"></i>
                  <p>Generate Reports</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="setCalendar.php" class="nav-link">
                  <i class="nav-icon fas fa-calendar-alt"></i>
                  <p>Set Calendar</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="container mt-4">
      <h2 class="mb-4">Prescriptions</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>PID</th>
              <th>Medicine Name</th>
              <th>Dosage</th>
              <th>Frequency</th>
              <th>Time to Take</th>
              <th>Set Prescription</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($prescriptions as $prescription): ?>
            <tr>
              <td><?php echo htmlspecialchars($prescription['pid']); ?></td>
              <td><?php echo htmlspecialchars($prescription['medicine_name']); ?></td>
              <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
              <td><?php echo htmlspecialchars($prescription['frequency']); ?></td>
              <td><?php echo htmlspecialchars($prescription['time_to_take']); ?></td>
              <td>
                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#sendSMSModal" onclick="setPatientId('<?php echo htmlspecialchars($prescription['pid']); ?>')">Send Prescription</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal for Sending SMS -->
    <div class="modal fade" id="sendSMSModal" tabindex="-1" role="dialog" aria-labelledby="sendSMSModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="sendSMSModalLabel">Send SMS</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="send_sms.php" method="post">
              <div class="form-group">
                <label for="number">Phone Number:</label>
                <input type="text" class="form-control" id="number" name="number" placeholder="+63" pattern="^\+63[0-9]{10}$" title="Phone number must start with +63 followed by 10 digits" required>
              </div>
              <div class="form-group">
                <label for="message">Message:</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Your message here..." required></textarea>
              </div>
              <input type="hidden" id="patientId" name="pid">
              <button type="submit" class="btn btn-primary">Send SMS</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Local Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/chart.js/Chart.min.js"></script>
<script src="plugins/sparklines/sparkline.js"></script>
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="dist/js/adminlte.js"></script>

<!-- Custom JS -->
<script>
  function setPatientId(patientId) {
    document.getElementById('patientId').value = patientId;
  }

  function confirmLogout(event) {
    if (!confirm("Are you sure you want to log out?")) {
      event.preventDefault();
    }
  }
</script>

</body>
</html>
