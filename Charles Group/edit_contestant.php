<?php
include("config.php");
session_start();

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$currentAdminId = $_SESSION['admin_id'];

// Handle sign-out
if (isset($_POST['sign-out'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Initialize variables
$errors = [];
$message = '';

// Update contestant information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contestant_id'])) {
    $id = intval($_POST['contestant_id']);
    $lname = trim($_POST['last_name']);
    $fname = trim($_POST['first_name']);
    $mname = trim($_POST['middle_initial']);
    $age = intval($_POST['age']);
    $civil_status = trim($_POST['civil_status']);
    $street_address = trim($_POST['street_address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $postal_code = trim($_POST['postal_code']);
    $country = trim($_POST['country']);
    $bio = trim($_POST['profile']);
    $achieve = trim($_POST['achievements']);
    $photo = $_POST['existing_photo'];

    // Validate inputs
    if (empty($lname)) $errors[] = "Last name is required.";
    if (empty($fname)) $errors[] = "First name is required.";
    if ($age <= 0) $errors[] = "Valid age is required.";
    if (empty($civil_status)) $errors[] = "Civil status is required.";
    if (empty($street_address)) $errors[] = "Street address is required.";
    if (empty($city)) $errors[] = "City is required.";
    if (empty($state)) $errors[] = "State is required.";
    if (empty($postal_code)) $errors[] = "Postal code is required.";
    if (empty($country)) $errors[] = "Country is required.";
    if (empty($bio)) $errors[] = "Profile is required.";
    if (empty($achieve)) $errors[] = "Achievements are required.";

    // Handle photo upload if a new photo is provided
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploadedPhoto = $_FILES['photo'];

        // Define upload directory and file name
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($uploadedPhoto["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type and size
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($uploadedPhoto["size"] > 500000) {
            $errors[] = "File size must be less than 500KB.";
        } elseif (move_uploaded_file($uploadedPhoto["tmp_name"], $target_file)) {
            $photo = htmlspecialchars(basename($uploadedPhoto["name"]));
        } else {
            $errors[] = "There was an error uploading your file.";
        }
    }

    // Update database if no errors
    if (empty($errors)) {
        $sql = "UPDATE contestants 
                SET lname = ?, fname = ?, mname = ?, age = ?, 
                    civil_status = ?, street_address = ?, city = ?, state = ?, 
                    postal_code = ?, country = ?, bio = ?, achievements = ?, photo = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssssssssi", $lname, $fname, $mname, $age, $civil_status, 
                          $street_address, $city, $state, $postal_code, $country, 
                          $bio, $achieve, $photo, $id);

        if ($stmt->execute()) {
            $message = "Record updated successfully.";
            header('Location: contestant.php');
            exit();
        } else {
            $errors[] = "Error updating record: " . $conn->error;
        }
    }
}

// Load contestant data for editing
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM contestants WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lname = $row['lname'];
        $fname = $row['fname'];
        $mname = $row['mname'];
        $age = $row['age'];
        $civil_status = $row['civil_status'];
        $street_address = $row['street_address'];
        $city = $row['city'];
        $state = $row['state'];
        $postal_code = $row['postal_code'];
        $country = $row['country'];
        $bio = $row['bio'];
        $achieve = $row['achievements'];
        $photo = $row['photo'];
    } else {
        echo "No contestant found with the given ID.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Admin details for sidebar
$admin_id = intval($_SESSION['admin_id']);
$admin_sql = "SELECT fname, lname FROM admins WHERE id = ?";
$stmt = $conn->prepare($admin_sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_fname, $admin_lname);
$stmt->fetch();
$_SESSION['admin_first_name'] = $admin_fname;
$_SESSION['admin_last_name'] = $admin_lname;
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Contestant</title>

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
      color: white;
      font-family: 'Tungsten Condensed', sans-serif;
      overflow-x: hidden;
      /*background-image: url('uploads/bg.jpg');*/
    }
    .navbar .nav-link, .brand-text {
      color: white !important;
    }
    .brand-link {
      border-bottom: 1px solid white;
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
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark bg-dark elevation-4">
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
          <li class="nav-item menu-open">
            <a href="contestant.php" class="nav-link active">
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
    <div class="col-4"></div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header text-center">
            <a href="#" class="h1"><b>Edit Contestant</b> Form</a>
          </div>
          <div class="card-body">
          <form id="edit-contestant-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="contestant_id" value="<?php echo htmlspecialchars($id); ?>">
    <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($photo); ?>">

    <!-- Last Name -->
    <div class="form-group mb-3">
        <label for="last_name">Last Name</label>
        <input 
            type="text" 
            name="last_name" 
            class="form-control" 
            value="<?php echo htmlspecialchars($lname); ?>" 
            required>
    </div>

    <!-- First Name -->
    <div class="form-group mb-3">
        <label for="first_name">First Name</label>
        <input 
            type="text" 
            name="first_name" 
            class="form-control" 
            value="<?php echo htmlspecialchars($fname); ?>" 
            required>
    </div>

    <!-- Middle Initial -->
    <div class="form-group mb-3">
        <label for="middle_initial">Middle Initial</label>
        <input 
            type="text" 
            name="middle_initial" 
            class="form-control" 
            value="<?php echo htmlspecialchars($mname); ?>">
    </div>

  
    <!-- Civil Status -->
    <div class="form-group mb-3">
        <label for="civil_status">Civil Status</label>
        <input 
            type="text" 
            name="civil_status" 
            class="form-control" 
            value="<?php echo htmlspecialchars($civil_status); ?>" 
            required>
    </div>

    <!-- Street Address -->
    <div class="form-group mb-3">
        <label for="street_address">Street Address</label>
        <input 
            type="text" 
            name="street_address" 
            class="form-control" 
            value="<?php echo htmlspecialchars($street_address); ?>" 
            required>
    </div>

    <!-- City -->
    <div class="form-group mb-3">
        <label for="city">City</label>
        <input 
            type="text" 
            name="city" 
            class="form-control" 
            value="<?php echo htmlspecialchars($city); ?>" 
            required>
    </div>

    <!-- State -->
    <div class="form-group mb-3">
        <label for="state">State</label>
        <input 
            type="text" 
            name="state" 
            class="form-control" 
            value="<?php echo htmlspecialchars($state); ?>" 
            required>
    </div>

    <!-- Postal Code -->
    <div class="form-group mb-3">
        <label for="postal_code">Postal Code</label>
        <input 
            type="text" 
            name="postal_code" 
            class="form-control" 
            value="<?php echo htmlspecialchars($postal_code); ?>" 
            required>
    </div>

    <!-- Country -->
    <div class="form-group mb-3">
        <label for="country">Country</label>
        <input 
            type="text" 
            name="country" 
            class="form-control" 
            value="<?php echo htmlspecialchars($country); ?>" 
            required>
    </div>

      <!-- Age -->
      <div class="form-group mb-3">
        <label for="age">Age</label>
        <input 
            type="number" 
            name="age" 
            class="form-control" 
            value="<?php echo htmlspecialchars($age); ?>" 
            required>
    </div>


    <!-- Profile -->
    <div class="form-group mb-3">
        <label for="profile">Brief Description/Profile</label>
        <textarea 
            name="profile" 
            class="form-control" 
            rows="4" 
            required><?php echo htmlspecialchars($bio); ?></textarea>
    </div>

    <!-- Achievements -->
    <div class="form-group mb-3">
        <label for="achievements">Achievements</label>
        <textarea 
            name="achievements" 
            class="form-control" 
            rows="4" 
            required><?php echo htmlspecialchars($achieve); ?></textarea>
    </div>

    <!-- Photo Upload -->
    <div class="form-group mb-3">
        <label for="photo">Upload Photo</label>
        <input 
            type="file" 
            name="photo" 
            id="photo" 
            class="form-control">
    </div>

    <!-- Display Existing Photo -->
    <?php if (!empty($photo)) : ?>
        <div class="mb-3">
            <img src="uploads/<?php echo htmlspecialchars($photo); ?>" alt="Contestant Photo" class="img-thumbnail" style="max-width: 150px;">
        </div>
    <?php endif; ?>

    <!-- Submit Button -->
    <div class="row">
        <div class="col-8"></div>
        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Update</button>
        </div>
    </div>
</form>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mt-3">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (!empty($message)): ?>
                <div class="alert alert-success mt-3">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
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
<script>
document.getElementById('edit-contestant-form').addEventListener('submit', function(e) {
    let valid = true;
    const requiredFields = ['contestant_name', 'contestant_status', 'contestant_profile', 'contestant_achievements'];
    requiredFields.forEach(function(field) {
        const fieldValue = document.getElementsByName(field)[0].value.trim();
        if (fieldValue === '') {
            valid = false;
            document.getElementsByName(field)[0].classList.add('is-invalid');
        } else {
            document.getElementsByName(field)[0].classList.remove('is-invalid');
        }
    });

    if (!valid) {
        e.preventDefault();
        alert('Please fill out all required fields.');
    }
});
</script>
</body>
</html>
