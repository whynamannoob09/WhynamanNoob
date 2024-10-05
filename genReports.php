<?php
session_start();
include("connection.php");
include("function.php");


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pid'])) {
    $pid = $_POST['pid'];

    // Fetch patient details
    $patientQuery = "SELECT * FROM patient_records WHERE pid = '$pid'";
    $patientResult = mysqli_query($con, $patientQuery);
    $patient = mysqli_fetch_assoc($patientResult);

    // Fetch prescriptions
    $prescriptionQuery = "SELECT * FROM prescriptions_data WHERE pid = '$pid'";
    $prescriptionResult = mysqli_query($con, $prescriptionQuery);
    $prescriptions = mysqli_fetch_all($prescriptionResult, MYSQLI_ASSOC);

    // Fetch medical records
    $medicalRecordsQuery = "SELECT * FROM medical_records WHERE pid = '$pid'";
    $medicalRecordsResult = mysqli_query($con, $medicalRecordsQuery);
    $medicalRecords = mysqli_fetch_all($medicalRecordsResult, MYSQLI_ASSOC);

     // Fetch vital signs
     $vitalQuery = "SELECT * FROM vital_signs WHERE pid = $pid";
     $vitalResult = mysqli_query($con, $vitalQuery);
     $vital_signs = mysqli_fetch_all($vitalResult, MYSQLI_ASSOC);
    

    $noPatientFound = !$patient; // Set flag if no patient found
} else {
    $pid = null; // No PID provided
    $noPatientFound = false; // No alert needed
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Generate Report Admin</title>

 <!-- jQuery UI (local) -->
<link rel="stylesheet" href="plugins/jquery-ui/jquery-ui.min.css">
<!-- Font Awesome (local) -->
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<!-- Tempusdominus Bootstrap 4 (local) -->
<link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<!-- iCheck (local) -->
<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<!-- JQVMap (local) -->
<link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
<!-- Theme style (local) -->
<link rel="stylesheet" href="dist/css/adminlte.min.css">
<!-- overlayScrollbars (local) -->
<link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- Daterange picker (local) -->
<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
<!-- Summernote (local) -->
<link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

  <style>
    .content-wrapper{
        padding-left: 3%;
        padding-right: 3%;
    }
    #alert{
      text-align: center;
    }
    h2, h3{
      font-weight: bold;
    }
    @media print {
        #printButton {
            display: none;
        }
    }
    .nav-treeview .nav-item {
        padding-left: 3%;
    }
  </style>


</style>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src=".//img/logo.png" alt="image Logo" height="200" width="200">
    <h2>Loading...</h2>
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
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
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="logout.php" class="nav-link" onclick="return confirmLogout(event);">
          <i class="nav-icon fas fa-sign-out-alt"></i> Log out
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src=".//img/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-4" style="opacity: 1">
      <span class="brand-text font-weight-light">IMSClinic_HRMS</span>
    </a>

    <!-- Sidebar -->

    <div class="sidebar">
     

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Dashboard menu item -->
          <li class="nav-item">
            <a href="Dashboard_Admin.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link ">
              <i class="nav-icon fas fa-folder"></i>
              <p>
                Services
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="patientRecords.php" class="nav-link">
                  <i class="nav-icon fas fa-user"></i>
                  <p>Patient Records</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="Admin_Prescription.php" class="nav-link">
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
                <a href="genReports.php" class="nav-link active">
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
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

<br>
  <div class="content-wrapper">
    <div class="wrapper">
        <?php if ($pid === null): ?>
            <!-- Form to enter PID -->
            <div class="container mt-4">
                <h2>Generate Patient Report</h2>
                <form method="POST" action="genReports.php">
                    <div class="form-group">
                        <label for="pid">Enter Patient PID:</label>
                        <input type="text" class="form-control" name="pid" id="pid" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </form>
            </div>
                <?php else: ?>
            <!-- Display Patient Report -->
                <div class="container mt-4">
                    <?php if ($patient): ?>
                        <h2>Generate Reports for <?php echo htmlspecialchars($patient['name']); ?></h2>
                        
                        <!-- Patient Information -->
                        <h4>Patient Information</h4>
                        <table class="table table-bordered">
                            <tr><th>PID</th><td><?php echo htmlspecialchars($patient['pid']); ?></td></tr>
                            <tr><th>Name</th><td><?php echo htmlspecialchars($patient['name']); ?> <?php echo htmlspecialchars($patient['lastname']); ?></td></tr>
                            <tr><th>Address</th><td><?php echo htmlspecialchars($patient['address']); ?></td></tr>
                            <tr><th>Age</th><td><?php echo htmlspecialchars($patient['age']); ?></td></tr>
                            <tr><th>Birthdate</th><td><?php echo htmlspecialchars($patient['birthday']); ?></td></tr>
                            <tr><th>Phone Number</th><td><?php echo htmlspecialchars($patient['phone_number']); ?></td></tr>
                            <tr><th>Gender</th><td><?php echo htmlspecialchars($patient['gender']); ?></td></tr>
                            <tr><th>Status</th><td><?php echo htmlspecialchars($patient['status']); ?></td></tr>
                        </table>
<br>
<br>
                        <h3>Vital Signs</h3>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>BP</th>
                                    <th>CR</th>
                                    <th>RR</th>
                                    <th>T</th>
                                    <th>WT</th>
                                    <th>HT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vital_signs as $vital): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vital['date']); ?></td>
                                    <td><?php echo htmlspecialchars($vital['bp']); ?></td>
                                    <td><?php echo htmlspecialchars($vital['cr']); ?></td>
                                    <td><?php echo htmlspecialchars($vital['rr']); ?></td>
                                    <td><?php echo htmlspecialchars($vital['t']); ?></td>
                                    <td><?php echo htmlspecialchars($vital['wt']); ?></td>
                                    <td><?php echo htmlspecialchars($vital['ht']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
<br>
<br>
                        <h3>Diagnosis Records</h3>
                        <?php
                        // Fetch diagnosis records
                        if (isset($pid) && $pid > 0) {
                            $diagnosisQuery = "SELECT * FROM diagnosis WHERE pid = ?";
                            $stmt = $con->prepare($diagnosisQuery);
                            $stmt->bind_param("i", $pid);
                            $stmt->execute();
                            $diagnosisResult = $stmt->get_result();
                            
                            if ($diagnosisResult->num_rows > 0) {
                                echo '<table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Subjective</th>
                                            <th>Objective</th>
                                            <th>Assessment</th>
                                            <th>Plan</th>
                                            <th>Laboratory</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                while ($row = $diagnosisResult->fetch_assoc()) {
                                    echo '<tr>
                                        <td>' . htmlspecialchars($row['date']) . '</td>
                                        <td>' . htmlspecialchars($row['subjective']) . '</td>
                                        <td>' . htmlspecialchars($row['objective']) . '</td>
                                        <td>' . htmlspecialchars($row['assessment']) . '</td>
                                        <td>' . htmlspecialchars($row['plan']) . '</td>
                                        <td>' . (!empty($row['laboratory']) ? htmlspecialchars($row['laboratory']) : 'N/A') . '</td>
                                    </tr>';
                                }
                                echo '</tbody></table>';
                            } else {
                                echo '<div class="alert alert-info">No diagnosis records found for this patient.</div>';
                            }
                        }
                        ?>
<br>
<br>
                        <h4>Prescription Information</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Time to Take</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($prescriptions)): ?>
                                    <?php foreach ($prescriptions as $prescription): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($prescription['medicine_name']); ?></td>
                                            <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                                            <td><?php echo htmlspecialchars($prescription['frequency']); ?></td>
                                            <td><?php echo htmlspecialchars($prescription['time_to_take']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4">No prescriptions found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
<br>
<br>
                        <h4>Medical Records</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>File Path</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($medicalRecords)): ?>
                                    <?php foreach ($medicalRecords as $record): ?>
                                        <tr>
                                            <td><a href="<?php echo htmlspecialchars($record['file_path']); ?>" target="_self"><?php echo htmlspecialchars($record['file_path']); ?></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td>No medical records found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Print Button -->
                        <button id="printButton" onclick="window.print()" class="btn btn-primary">Print Report</button>

                    <?php else: ?>
                        <!-- Alert Message -->
                        <div class="alert alert-warning mt-4" id="alert">No patient found with the provided PID.</div>
                    <?php endif; ?>
                </div>
        <?php endif; ?>

    </div>

  </div>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery (local) -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI (local) -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 (local) -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS (local) -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline (local) -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap (local) -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart (local) -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker (local) -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 (local) -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote (local) -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars (local) -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App (local) -->
<script src="dist/js/adminlte.js"></script>
<script>
  function confirmLogout(event) {
    if (!confirm("Are you sure you want to log out?")) {
      event.preventDefault(); // Prevent the default action if they click "Cancel"
    }
  }
</script>

</body>
</html>

