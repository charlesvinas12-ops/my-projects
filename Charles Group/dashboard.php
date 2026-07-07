  <?php
  include("conn.php");
  session_start();

  if (!isset($_SESSION['admin_id'])) {
      header("Location: index.php");
      exit();
  }

  if(isset($_POST['sign-out'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
  }

  // Count the number of judges
  $stmt = $conn->prepare("SELECT COUNT(*) as totalJudges FROM judges");
  $stmt->execute();
  $result = $stmt->get_result();
  $totalJudges = $result->fetch_assoc()['totalJudges'];

  // Count the number of contestants
  $stmt = $conn->prepare("SELECT COUNT(*) as totalContestants FROM contestants");
  $stmt->execute();
  $result = $stmt->get_result();
  $totalContestants = $result->fetch_assoc()['totalContestants'];

  // Count the number of admins
  $stmt = $conn->prepare("SELECT COUNT(*) as totalAdmins FROM admins");
  $stmt->execute();
  $result = $stmt->get_result();
  $totalAdmins = $result->fetch_assoc()['totalAdmins'];
  
  // Get the admin's ID from the session
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

// Close the statement
$stmt->close();
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WeChoose</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <!-- Custom style -->
    <link rel="stylesheet" href="assets/plugins/bs-custom-file-input/bs-custom-file-input.min.css">
    <style>
      body, .wrapper, .main-sidebar, .navbar, .content-wrapper, .main-footer {
        background-color: #2f2f2f;
        color: #FFD700;
        font-family: 'Tungsten Condensed', sans-serif;
        /* background-image: url('uploads/bg.jpg'); */
      
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
    </style>
  </head>
  <body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-black navbar-dark">
      <!-- Left navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Logout Button -->
        <li class="nav-item">
          <form action="" method="post">
            <input type="submit" name="sign-out" value="Logout" class="nav-link btn ">
          </form>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark bg-dark elevation-4 ">
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
      <div class="sidebar ">
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item menu-open">
              <a href="dashboard.php" class="nav-link active">
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
            <li class="nav-item">
              <a href="addadmin.php" class="nav-link">
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
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Dashboard</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?php echo $totalJudges; ?></h3>
                  <p>Judges</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?php echo $totalContestants; ?></h3>
                  <p>Contestants</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?php echo $totalAdmins; ?></h3>
                  <p>Admins</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
  <script src="assets/dist/js/adminlte.min.js"></script>
  <script src="assets/dist/js/demo.js"></script>
  <script>
  $(function () {
    bsCustomFileInput.init();
  });

  function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this judge?")) {
      window.location.href = 'admin.php?id=' + id;
      return true;
    }
    return false;
  }
  </script>

  </body>
  </html>
