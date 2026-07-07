<?php
include("config.php");
session_start();

// Redirect to login if admin is not logged in
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

$errors = [];
$message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_id'])) {
    $id = intval($_POST['admin_id']);
    $fname = trim($_POST['admin_fname']);
    $lname = trim($_POST['admin_lname']);
    $username = trim($_POST['admin_username']);
    $password = trim($_POST['admin_password']);

    // Validate input
    if (empty($fname)) {
        $errors[] = "First name is required.";
    }
    if (empty($lname)) {
        $errors[] = "Last name is required.";
    }
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Update the admin information in the database
    if (empty($errors)) {
        $sql = "UPDATE admins SET fname=?, lname=?, username=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $fname, $lname, $username, $password, $id);

        if ($stmt->execute()) {
            $message = "Admin updated successfully";
            header('Location: addadmin.php'); // Redirect to the admin list page after successful update
            exit();
        } else {
            $errors[] = "Error updating admin: " . $stmt->error;
        }
    }
} else if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Retrieve admin information from the database
    $sql = "SELECT * FROM admins WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fname = $row['fname'];
        $lname = $row['lname'];
        $username = $row['username'];
        $password = $row['password'];
    } else {
        echo "No admin found with the given ID";
        exit();
    }
} else {
    echo "Invalid request";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Admin</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <style>
    /* Include the updated CSS from above */
    body, .wrapper, .main-sidebar, .navbar, .content-wrapper, .main-footer {
      background-color: #2f2f2f;
      color: #FFD700;
      font-family: 'Tungsten Condensed', sans-serif;
      overflow-x: hidden;
     /* background-image: url('uploads/bg.jpg');*/
    }
    .navbar .nav-link, .brand-text {
      color: white !important;
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
    
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-black navbar">
    <!-- Left navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Logout Button -->
      <li class="nav-item">
        <form action="" method="post">
          <input type="submit" name="sign-out" value="Logout" class="nav-link btn">
        </form>
      </li>
    </ul>
  </nav>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <span class="brand-text font-weight-light">Pageant Admin</span>
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
    <div class="col-4"></div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header text-center">
            <a href="#" class="h1"><b>Edit Admin</b> Form</a>
          </div>
          <div class="card-body">
          <form method="post">
    <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($id); ?>">

    <!-- First Name -->
    <div class="form-group mb-3">
        <label for="admin_fname">First Name</label>
        <div class="input-group">
            <input 
                type="text" 
                name="admin_fname" 
                id="admin_fname" 
                class="form-control" 
                placeholder="Enter first name" 
                value="<?php echo htmlspecialchars($fname); ?>">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Name -->
    <div class="form-group mb-3">
        <label for="admin_lname">Last Name</label>
        <div class="input-group">
            <input 
                type="text" 
                name="admin_lname" 
                id="admin_lname" 
                class="form-control" 
                placeholder="Enter last name" 
                value="<?php echo htmlspecialchars($lname); ?>">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Username -->
    <div class="form-group mb-3">
        <label for="admin_username">Username</label>
        <div class="input-group">
            <input 
                type="text" 
                name="admin_username" 
                id="admin_username" 
                class="form-control" 
                placeholder="Enter username" 
                value="<?php echo htmlspecialchars($username); ?>">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user-circle"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Password -->
    <div class="form-group mb-3">
        <label for="admin_password">New Password</label>
        <div class="input-group">
            <input 
                type="password" 
                name="admin_password" 
                id="admin_password" 
                class="form-control" 
                placeholder="Enter new password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row">
        <div class="col-8"></div>
        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Update</button>
        </div>
    </div>
</form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>
