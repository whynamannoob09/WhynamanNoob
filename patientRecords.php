<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("connection.php");

// Function to sanitize input data
function sanitize_input($con, $data) {
    return mysqli_real_escape_string($con, htmlspecialchars(strip_tags($data)));
}

// Function to check if a patient already exists
function check_patient_exists($con, $name, $lastname, $phone_number, $pid = null) {
    $query = "SELECT * FROM patient_records WHERE name = '$name' AND lastname = '$lastname' AND phone_number = '$phone_number' ";
    if ($pid) {
        $query .= " AND pid != $pid";
    }
    $result = mysqli_query($con, $query);
    return mysqli_num_rows($result) > 0;
}

// Add/Update patient, Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($con, $_POST['name']);
    $lastname = sanitize_input($con, $_POST['lastname']);
    $address = sanitize_input($con, $_POST['address']);
    $birthday = sanitize_input($con, $_POST['birthday']);
    $phone_number = sanitize_input($con, $_POST['phone_number']);
    $gender = sanitize_input($con, $_POST['gender']);
    $pid = isset($_POST['pid']) ? intval($_POST['pid']) : null;

    // Initialize validation flag
    $validation_passed = true;

    // Validate phone number
    if (!ctype_digit($phone_number) || strlen($phone_number) != 11) {
        echo "<script>alert('Phone number must contain exactly 11 digits and no letters!');</script>";
        $validation_passed = false;
    }

    // Validate name and lastname uniqueness
    if (check_patient_exists($con, $name, $lastname, $phone_number, $pid)) {
        echo "<script>alert('Patient already exists!');</script>";
        $validation_passed = false;
    }

    if ($validation_passed) {
        // Calculate age automatically from birthday
        $birthdate = new DateTime($birthday);
        $today = new DateTime();
        $age = $today->diff($birthdate)->y;

        if (isset($_POST['add_patient'])) {
            // Insert into patients table
            $query = "INSERT INTO patient_records (name, lastname, address, age, birthday, phone_number, gender, status) 
                      VALUES ('$name', '$lastname', '$address', $age, '$birthday', '$phone_number', '$gender', 'Active')";
            if (mysqli_query($con, $query)) {
                // Get the last inserted PID
                $pid = mysqli_insert_id($con);

                // Insert into user table
                $hashed_password = password_hash($pid, PASSWORD_BCRYPT);
                $query_user = "INSERT INTO users (username, password, role) VALUES ('$pid', '$hashed_password', 'patient')";
                mysqli_query($con, $query_user);

                header("Location: patientRecords.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } elseif (isset($_POST['update_patient'])) {
            $query = "UPDATE patient_records SET 
                      name = '$name', lastname = '$lastname', address = '$address', 
                      age = $age, birthday = '$birthday', phone_number = '$phone_number', gender = '$gender' 
                      WHERE pid = $pid";
            if (mysqli_query($con, $query)) {
                header("Location: patientRecords.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($con);
            }
        }

        if (isset($_POST['update_status'])) {
            $status = sanitize_input($con, $_POST['status']);
            $query = "UPDATE patient_records SET status = '$status' WHERE pid = $pid";
            if (mysqli_query($con, $query)) {
                header("Location: patientRecords.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($con);
            }
        }
    } else {
        // If validation failed, stay on the form page
        echo "<script>window.history.back();</script>";
        exit();
    }
}

// Fetch all patient records
$query = "SELECT * FROM patient_records";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($con));
}

$patients = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard Patient Records</title>
        <!-- Font Awesome (local) -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">

    <!-- Tempus Dominus Bootstrap 4 (local) -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

    <!-- iCheck Bootstrap (local) -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <!-- JQVMap (local) -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">

    <!-- AdminLTE Theme (local) -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">

    <!-- OverlayScrollbars (local) -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

    <!-- Daterange Picker (local) -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">

    <!-- Summernote (local) -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <style>
        
        .content-wrapper{
            padding-left: 1%;
            padding-right: 1%;
        }
        .table-secondary {
            background-color: rgba(0, 0, 0, 0.1);
            color: white;
        }
        tr,th{
            text-align: center;
        }
        .nav-treeview .nav-item {
            padding-left: 3%;
        }
        .action-buttons{
            display: flex;
            justify-content: center;
            gap: 5px;
            font-size: 14px;             /* Adjust font size for readability */
            min-width: 80px;             /* Set a minimum width for buttons */
            text-align: center;          /* Center button text */
            display: inline-flex;
        }

    </style>

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
        <a href="index.php " class="nav-link">Home</a>
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
                <form class="form-inline" action="patientRecords.php" method="get">
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
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src=".//img/logo.png" alt="image Logo" class="brand-image img-circle elevation-4" style="opacity: 1">
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
                <a href="patientRecords.php" class="nav-link active">
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
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>


  <div class="content-wrapper">
        <div class="container mt-4">
            <h2 class="mb-4">Patient Records</h2>

            <div class="mb-3">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPatientModal">
                    Add Patient
                </button>
            </div>

            <!-- Patient Records Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>PID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Address</th>
                            <th>Birthdate</th>
                            <th>Age</th>
                            <th>Phone Number</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($patients)): ?>
                            <?php foreach ($patients as $patient): ?>
                                <tr class="<?php echo ($patient['status'] === 'Not Active') ? 'table-secondary' : ''; ?>">
                                    <td><?php echo htmlspecialchars($patient['pid']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['address']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['birthday']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['age']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['status']); ?></td>
                                    <td class="action-buttons">
                                        <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editPatientModal<?php echo $patient['pid']; ?>">Edit</a>
                                        <a href="viewPatient_Admin.php?pid=<?php echo $patient['pid']; ?>" class="btn btn-sm btn-info">View More</a>
                                        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#vitalSignModal<?php echo $patient['pid']; ?>">Vital Sign</a>
                                    </td>
                                </tr>

                                <!-- Edit Patient Modal -->
                                <div class="modal fade" id="editPatientModal<?php echo $patient['pid']; ?>" tabindex="-1" role="dialog" aria-labelledby="editPatientModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="patientRecords.php" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editPatientModalLabel">Edit Patient</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="pid" value="<?php echo $patient['pid']; ?>">
                                                    <div class="form-group">
                                                        <label for="edit-name">First Name</label>
                                                        <input type="text" class="form-control" id="edit-name" name="name" value="<?php echo $patient['name']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="edit-lastname">Last Name</label>
                                                        <input type="text" class="form-control" id="edit-lastname" name="lastname" value="<?php echo $patient['lastname']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="edit-address">Address</label>
                                                        <input type="text" class="form-control" id="edit-address" name="address" value="<?php echo $patient['address']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="edit-birthday">Birthdate</label>
                                                        <input type="date" class="form-control" id="edit-birthday" name="birthday" value="<?php echo $patient['birthday']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="edit-age">Age</label>
                                                        <input type="number" class="form-control" id="edit-age" name="age" value="<?php echo $patient['age']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="edit-phone-number">Phone Number</label>
                                                        <input type="tel" class="form-control" id="edit-phone-number" name="phone_number" value="<?php echo $patient['phone_number']; ?>" required pattern="\d{11}" maxlength="11" title="Please enter exactly 11 digits" oninput="this.value=this.value.replace(/[^0-9]/g,''); if (this.value.length > 11) { this.value = this.value.slice(0, 11); }">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="edit-gender">Gender</label>
                                                        <select class="form-control" id="edit-gender" name="gender" required>
                                                            <option value="Male" <?php echo ($patient['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                                            <option value="Female" <?php echo ($patient['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary" name="update_patient">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                                <!-- Vital Sign Modal -->
                                <div class="modal fade" id="vitalSignModal<?php echo $patient['pid']; ?>" tabindex="-1" role="dialog" aria-labelledby="vitalSignModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="vitalSign.php" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="vitalSignModalLabel">Add Vital Signs for <?php echo htmlspecialchars($patient['name']); ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="pid" value="<?php echo $patient['pid']; ?>">
                                                    <div class="form-group">
                                                        <label for="vital-date">Date</label>
                                                        <input type="date" class="form-control" id="vital-date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="vital-bp">Blood Pressure</label>
                                                        <input type="text" class="form-control" id="vital-bp" name="bp" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="vital-cr">Heart Rate</label>
                                                        <input type="text" class="form-control" id="vital-cr" name="cr" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="vital-rr">Respiratory Rate</label>
                                                        <input type="text" class="form-control" id="vital-rr" name="rr" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="vital-t">Temperature</label>
                                                        <input type="text" class="form-control" id="vital-t" name="t" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="vital-wt">Weight (kg)</label>
                                                        <input type="text" class="form-control" id="vital-wt" name="wt" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="vital-ht">Height (cm)</label>
                                                        <input type="text" class="form-control" id="vital-ht" name="ht" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Vital Signs</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">No patient records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Patient Modal -->
    <div class="modal fade" id="addPatientModal" tabindex="-1" role="dialog" aria-labelledby="addPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="patientRecords.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPatientModalLabel">Add New Patient</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">First Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="birthday">Birthdate</label>
                            <input type="date" class="form-control" id="birthday" name="birthday" required>
                        </div>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" class="form-control" id="age" name="age" required>
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" required pattern="\d{11}" maxlength="11" title="Please enter exactly 11 digits" oninput="this.value=this.value.replace(/[^0-9]/g,''); if (this.value.length > 11) { this.value = this.value.slice(0, 11); }">
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_patient">Add Patient</button>
                    </div>
                </form>
            </div>
        </div>
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
<script src="../wbhr_ms/Bday_Validation.js"></script>
<script>
  function confirmLogout(event) {
    if (!confirm("Are you sure you want to log out?")) {
      event.preventDefault(); // Prevent the default action if they click "Cancel"
    }
  }
</script>


</body>
</html>
  