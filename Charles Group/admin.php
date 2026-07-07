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

$sql = "SELECT * FROM judges";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize and validate input
  $fname = trim($_POST['judge_fname'] ?? '');
  $lname = trim($_POST['judge_lname'] ?? '');
  $username = trim($_POST['judge_username'] ?? '');
  $password = trim($_POST['judge_password'] ?? '');

  // Validation
  if (empty($fname)) $errors['judge_fname'] = "First Name is required.";
  if (empty($lname)) $errors['judge_lname'] = "Last Name is required.";
  if (empty($username)) $errors['judge_username'] = "Username is required.";
  if (empty($password)) $errors['judge_password'] = "Password is required.";

  // Process form if no errors
  if (empty($errors)) {
      // Prepare SQL statement
      $stmt = $conn->prepare("INSERT INTO judges (fname, lname, username, password) VALUES (?, ?, ?, ?)");
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash password

      $stmt->bind_param('ssss', $fname, $lname, $username, $hashedPassword);

      if ($stmt->execute()) {
          // Redirect to avoid form resubmission
          header("Location: admin.php?success=1");
          exit();
      } else {
          $errors['form'] = "Error inserting judge: " . $stmt->error;
      }
  }
}

// If redirected, check for success messages
if (isset($_GET['success']) && $_GET['success'] == 1) {
  $successMessage = "Judge registered successfully!";
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
  <title>Pageant Admin | Judges List</title>

  <!-- Google Font: Oswald -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <!-- Custom style -->
  <link rel="stylesheet" href="assets/plugins/bs-custom-file-input/bs-custom-file-input.min.css">
  <style>
   /* General Styling */
body, .wrapper, .main-sidebar, .navbar, .content-wrapper, .main-footer {
  background-color: #2f2f2f; /* Dark gray background */
  color: white; /* Gold text color */
  font-family: 'Tungsten Condensed', sans-serif;
  overflow-x: hidden;
  /* background-image: url('uploads/bg.jpg'); */ /* Uncomment if using a background image */
}

/* Brand Link Styling */
.brand-link {
  border-bottom: 1px solid white;
}

/* Navigation Styling */
.nav-pills .nav-link {
  color: white;
}
.nav-pills .nav-link.active {
  background-color: #FFD700; /* Gold background for active link */
  color: #000; /* Black text for active link */
}

/* Small Box Styling */
.small-box {
  background-color: #FFD700 !important; /* Gold background */
  color: #000 !important; /* Black text */
}

/* Breadcrumb Styling */
.breadcrumb-item a {
  color: #FFD700;
}
.breadcrumb-item.active {
  color: #FFD700;
}

/* Footer Styling */
.main-footer {
  border-top: 1px solid #FFD700;
}

/* Button Styling */
.btn.bg-danger {
  background-color: #FFD700 !important; /* Gold background for danger button */
  color: #000 !important; /* Black text */
}

/* Card Styling */
.card-header, .card-body {
  background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent black background */
  color: white; /* Gold text color */
}

/* Table Styling */
.table th, .table td {
  border-color: #FFD700; /* Gold border color */
}
.table thead th {
  background-color: #FFD700; /* Gold header background */
  color: #000; /* Black text color for header */
}
.table tbody tr:nth-child(even) {
  background-color: rgba(255, 215, 0, 0.2); /* Light gold for even rows */
}
.table tbody tr:nth-child(odd) {
  background-color: rgba(255, 215, 0, 0.1); /* Lighter gold for odd rows */
}
.table-hover tbody tr:hover {
  background-color: rgba(255, 215, 0, 0.3); /* Darker gold on hover */
}

/* Alert Styling */
.alert {
  color: #000; /* Black text */
  background-color: #FFD700; /* Gold background */
  border-color: #FFD700; /* Gold border */
}

/* Modal Styling */
.modal-content {
  background-color: #000; /* Black modal background */
  color: #FFD700; /* Gold text */
}
.modal-header, .modal-footer {
  border-color: #FFD700; /* Gold border for header and footer */
}

  </style>
</head>
<body class="hold-transition sidebar-mini ">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-black navbar-dark">
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
          <li class="nav-item menu-open">
            <a href="admin.php" class="nav-link active">
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
    <div class="row">
      <div class="col-12">
        <div class="card">
        <div class="card-header bg-dark d-flex justify-content-center align-items-center position-relative">
  <!-- Centered Heading -->
  <a href="#" class="h1 text-gold m-0"><b>Judges</b> List</a>

  <!-- Add Button -->
  <a href="#" 
     class="btn btn-warning btn-sm position-absolute " 
     style="right: 10px;" 
     data-toggle="modal" 
     data-target="#addInModal">
    Add
  </a>
</div>
          <div class="card-body bg-dark ">
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>


<?php if (!empty($errors)): ?>
    <div class="alert alert-danger mt-3">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

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
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class= 'align-middle text-center' >" . $row['fname'] . "</td>";
                        echo "<td class= 'align-middle text-center' >" . $row['lname'] . "</td>";
                        echo "<td class= 'align-middle text-center' >" . $row['username'] . "</td>";
                        echo '<td >
                              <a href="edit_judge.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                              </a>
                            </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No judges found.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  

<!-- Modal -->
<div class="modal fade" id="addInModal" tabindex="-1" role="dialog" aria-labelledby="addInModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="card-header text-center">
          <a href="#" class="h1"><b>Judge</b> Form</a>
        </div>
          <p class="login-box-msg">Register a new judge</p>
          <form action="" method="post">
              <div class="form-group">
                  <label for="judge_fname">First Name</label>
                  <input type="text" name="judge_fname" class="form-control" id="judge_fname" value="<?= htmlspecialchars($fname ?? '') ?>">
              </div>

              <div class="form-group">
                  <label for="judge_lname">Last Name</label>
                  <input type="text" name="judge_lname" class="form-control" id="judge_lname" value="<?= htmlspecialchars($lname ?? '') ?>">
              </div>

              <div class="form-group">
                  <label for="judge_username">Username</label>
                  <input type="text" name="judge_username" class="form-control" id="judge_username" value="<?= htmlspecialchars($username ?? '') ?>">
              </div>

              <div class="form-group">
                  <label for="judge_password">Password</label>
                  <input type="password" name="judge_password" class="form-control" id="judge_password">
              </div>

              <button type="submit" class="btn btn-primary">Register Judge</button>
          </form>
          <script>
window.onload = function () {
    const successMessage = document.querySelector('.alert-success');
    if (successMessage) {
        document.body.classList.add('blink-effect'); // Add the blinking effect to the entire page
        setTimeout(() => {
            document.body.classList.remove('blink-effect'); // Remove the effect after it completes
        }, 1500); // Match the duration of the CSS animation (0.5s * 3 loops)
    }
};
</script>
        </div>
      </div>
    </div>
  </div>
</div>

  
</div>

<!-- Scripts -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>
<script src="assets/dist/js/demo.js"></script>
<script>

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
