  <?php
  include("config.php");
  session_start();

  if (!isset($_SESSION['admin_id'])) {
      header("Location: index.php");
      exit();
  }

  $currentAdminId = $_SESSION['admin_id'];

  if (isset($_POST['sign-out'])) {
      session_unset();
      session_destroy();
      header("Location: index.php");
      exit();
  }

  if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']); // Clear the message after displaying
  }

  // Handle form submission for adding an admin
  $errors = [];
  $successMessage = "";

  // Handle form submission

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Sanitize and validate input
      $first_name = trim($_POST['admin_fname'] ?? '');
      $last_name = trim($_POST['admin_lname'] ?? '');
      $username = trim($_POST['admin_username'] ?? '');
      $password = trim($_POST['admin_password'] ?? '');
  
      // Validation
      if (!$first_name) $errors['admin_fname'] = "First Name is required.";
      if (!$last_name) $errors['admin_lname'] = "Last Name is required.";
      if (!$username) $errors['admin_username'] = "Username is required.";
      if (!$password) $errors['admin_password'] = "Password is required.";
  
      // Process form if no errors
      if (empty($errors)) {
          // Hash the password for security
          $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  
          $stmt = $conn->prepare("INSERT INTO admins (fname, lname, username, password) VALUES (?, ?, ?, ?)");
          $stmt->bind_param('ssss', $first_name, $last_name, $username, $hashed_password);
  
          if ($stmt->execute()) {
              $successMessage = "Admin registered successfully!";
              $first_name = $last_name = $username = $password = ""; // Clear form inputs
          } else {
              $errors['form'] = "Error: " . $stmt->error;
          }
      }
  }
  



  $sql = "SELECT * FROM admins";
  $result = $conn->query($sql);

  $errors = [];
  $success = '';
        
  if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
      $id = $_GET['id'];

      // Prevent the logged-in admin from deleting their own account
      if ($id != $currentAdminId) {
          // Delete the admin from the database
          $sql = "DELETE FROM admins WHERE id = $id";

          if ($conn->query($sql) === TRUE) {
              header('Location: addadmin.php'); // Redirect to the admins list page after successful deletion
              exit();
          } else {
              $errors[] = "Error deleting admin: " . $conn->error;
          }
      } else {
          $errors[] = "You cannot delete your own account.";
      }
  }

  $admin_id = intval($_SESSION['admin_id']);

  // Query to get the first name and last name from the 'admins' table
  $admin_sql = "SELECT fname, lname FROM admins WHERE id = ?";
  $stmt = $conn->prepare($admin_sql);
  $stmt->bind_param("i", $admin_id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($fname, $lnamex);
  $stmt->fetch();

  // Store the admin's first and last name in the session
  $_SESSION['admin_first_name'] = $fname;
  $_SESSION['admin_last_name'] = $lnamex;

  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | General Form Elements</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <style>
    .btn[disabled] {
      cursor: not-allowed;
      opacity: 0.65;
    }
    body, .wrapper, .main-sidebar, .navbar, .content-wrapper, .main-footer {
        background-color: #2f2f2f;
        color: #FFD700;
        font-family: 'Tungsten Condensed', sans-serif;
        overflow-x: hidden;
        /* background-image: url('uploads/bg.jpg'); */
      }
      .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      }
      .brand-link {
        border-bottom: 1px solid #FFD700;
      }
      .nav-pills .nav-link {
        color: white;
      }
      .nav-pills .nav-link.active {
        background-color: #FFD700;
        color: #000;
      }
      .small-box {
        background-color: #FFD700 !important;
        color: #000 !important;
      }
      .breadcrumb-item a {
        color: #FFD700;
      }
      .breadcrumb-item.active {
        color: #FFD700;
      }
      .main-footer {
        border-top: 1px solid #FFD700;
      }
      .btn.bg-danger {
        background-color: #FFD700 !important;
        color: #000 !important;
      }
      .card-header, .card-body {
        background-color: rgba(0, 0, 0, 0.7); /* semi-transparent black background */
        color: #FFD700; /* gold text color */
      }
      .table th, .table td {
        border-color: #FFD700; /* gold border color */
      }
      .table thead th {
        background-color: #FFD700; /* gold header background */
        color: #000; /* black text color for header */
      }
      .table tbody tr:nth-child(even) {
        background-color: rgba(255, 215, 0, 0.2); /* light gold for even rows */
      }
      .table tbody tr:nth-child(odd) {
        background-color: rgba(255, 215, 0, 0.1); /* lighter gold for odd rows */
      }
      .table-hover tbody tr:hover {
        background-color: rgba(255, 215, 0, 0.3); /* darker gold on hover */
      }
      .alert {
        color: #000;
        background-color: #FFD700;
        border-color: #FFD700;
      }
      .modal-content {
        background-color: #000;
        color: #FFD700;
      }
      .modal-header, .modal-footer {
        border-color: #FFD700;
      }
      .alert-success, .alert-danger {
        margin-bottom: 20px;
    }
  </style>

  </head>
  <body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-black navbar-dark">
      <!-- Left navbar links -->
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Logout Button -->
        <li class="nav-item">
          <form action="" method="post">
            <input type="submit" name="sign-out" value="Logout" class="nav-link btn" href="index.php">
          </form> 
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark bg-dark elevation-4">
      <!-- Brand Logo -->
      <a href="index3.html" class="brand-link">
        <span class="brand-text ">Pearls of the Qrient
      </a>
      <a href="index3.html" class="brand-link">
    <span class="brand-text">
      <?php echo $_SESSION['admin_first_name'] . " " . $_SESSION['admin_last_name']; ?>
    </span>
  </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="dashboard.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="admin.php" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>Add Judge</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="contestant.php" class="nav-link">
                <i class="nav-icon fas fa-user-friends"></i>
                <p>Add Contestant</p>
              </a>
            </li>
            <li class="nav-item menu-open">
              <a href="addadmin.php" class="nav-link active">
                <i class="nav-icon fas fa-user-shield"></i>
                <p>Add Admin</p>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="row">
        <div class="col-12">
          <div class="card">
          <div class="card-header bg-dark d-flex justify-content-center align-items-center position-relative">
  <!-- Centered Heading -->
  <a href="#" class="h1 text-gold m-0"><b>Admin</b> List</a>

  <!-- Add Button -->
  <a href="#" 
     class="btn btn-warning btn-sm position-absolute " 
     style="right: 10px;" 
     data-toggle="modal" 
     data-target="#addInModal">
    Add
  </a>
</div>
            <div class="card-body bg-dark">
  <!-- Success Message -->
  <?php if ($successMessage): ?>
      <div id="successAlert" class="alert alert-success mt-3">
          <?= htmlspecialchars($successMessage) ?>
      </div>
  <?php endif; ?>

  <!-- Error Messages -->
  <?php if (!empty($errors)): ?>
      <div id="errorAlert" class="alert alert-danger mt-3">
          <ul>
              <?php foreach ($errors as $error): ?>
                  <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
          </ul>
      </div>
  <?php endif; ?>

  <!-- Table Content -->
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td class='align-middle text-center'>" . htmlspecialchars($row['fname']) . "</td>";
              echo "<td class='align-middle text-center'>" . htmlspecialchars($row['lname']) . "</td>";
              echo "<td class='align-middle text-center'>" . htmlspecialchars($row['username']) . "</td>";
              echo '<td>';
              if ($row['id'] != $currentAdminId) {
                  echo '<a href="edit_admin.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                  echo '<a href="addadmin.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-danger btn-sm" onclick="return confirmDelete(' . htmlspecialchars($row['id']) . ');"><i class="fas fa-trash"></i> Delete</a>';
              } else {
                  echo '<a href="#" class="btn btn-warning btn-sm" disabled><i class="fas fa-edit"></i> Edit</a>';
                  echo '<a href="#" class="btn btn-danger btn-sm" disabled><i class="fas fa-trash"></i> Delete</a>';
              }
              echo "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='4'>No Admin found.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Success Alert
    const successAlert = document.getElementById("successAlert");
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.display = "none";
        }, 5000);
    }

    // Error Alert
    const errorAlert = document.getElementById("errorAlert");
    if (errorAlert) {
        setTimeout(() => {
            errorAlert.style.display = "none";
        }, 5000);
    }
});
</script>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>
      <!-- /.content -->
    </div>
    
    <div class="modal fade" id="addInModal" tabindex="-1" role="dialog" aria-labelledby="addInModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addInModalLabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="card-header text-center">
            <a href="#" class="h1"><b>Admin</b> Form</a>
          </div>
          <div class="card-body">
            <p class="login-box-msg">Register a new Admin</p>     
            <form action="" method="post">
      <div class="form-group">
          <label for="admin_fname">First Name</label>
          <input type="text" name="admin_fname" class="form-control" id="admin_fname" value="<?= htmlspecialchars($first_name ?? '') ?>">
      </div>

      <div class="form-group">
          <label for="admin_lname">Last Name</label>
          <input type="text" name="admin_lname" class="form-control" id="admin_lname" value="<?= htmlspecialchars($last_name ?? '') ?>">
      </div>

      <div class="form-group">
          <label for="admin_username">Username</label>
          <input type="text" name="admin_username" class="form-control" id="admin_username" value="<?= htmlspecialchars($username ?? '') ?>">
      </div>

      <div class="form-group">
          <label for="admin_password">Password</label>
          <input type="password" name="admin_password" class="form-control" id="admin_password">
      </div>

      <button type="submit" class="btn btn-primary">Register Admin</button>
  </form>


  <!-- JavaScript to hide alerts after 5 seconds -->

          </div>
        </div>
      </div>
    </div>
  </div>


  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="assets/plugins/chart.js/Chart.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="assets/dist/js/demo.js"></script>
  <!-- Page specific script -->
  <script>
  function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this Admin?")) {
      window.location.href = 'addadmin.php?id=' + id;
      return true;
    }
    return false;
  }
  </script>
  </body>
  </html>
