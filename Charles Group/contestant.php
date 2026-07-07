<?php
include("conn.php");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  $id = $_GET['id'];

  // Check for related records in the scores table
  $checkSql = "SELECT COUNT(*) as count FROM scores WHERE contestant_id = $id";
  $checkResult = $conn->query($checkSql);
  $checkRow = $checkResult->fetch_assoc();

  if ($checkRow['count'] > 0) {
      // Delete related records in the scores table first
      $deleteScoresSql = "DELETE FROM scores WHERE contestant_id = $id";
      if ($conn->query($deleteScoresSql) !== TRUE) {
          $errors[] = "Error deleting related scores: " . $conn->error;
      }
  }

  // Now delete the contestant from the database
  $sql = "DELETE FROM contestants WHERE id = $id";
  
  if ($conn->query($sql) === TRUE) {
      header('Location: contestant.php');
      exit();
  } else {
      $errors[] = "Error deleting contestant: " . $conn->error;
  }
}


$errors = [];
$successMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $lname = trim($_POST['contestant_lname'] ?? '');
    $fname = trim($_POST['contestant_fname'] ?? '');
    $mname = trim($_POST['contestant_mname'] ?? '');
    $street_address = trim($_POST['contestant_street_address'] ?? '');
    $city = trim($_POST['contestant_city'] ?? '');
    $state = trim($_POST['contestant_state'] ?? '');
    $postal_code = trim($_POST['contestant_postal_code'] ?? '');
    $country = trim($_POST['contestant_country'] ?? '');
    $age = trim($_POST['contestant_age'] ?? '');
    $status = trim($_POST['contestant_status'] ?? '');
    $profile = trim($_POST['contestant_profile'] ?? '');
    $achievements = trim($_POST['contestant_achievements'] ?? '');
    $photo = $_FILES['contestant_photo'] ?? null;

    // Validation
    if (!$lname) $errors['contestant_lname'] = "Last Name is required.";
    if (!$fname) $errors['contestant_fname'] = "First Name is required.";
    if (!$street_address) $errors['contestant_street_address'] = "Street Address is required.";
    if (!$city) $errors['contestant_city'] = "City is required.";
    if (!$state) $errors['contestant_state'] = "State is required.";
    if (!$postal_code) $errors['contestant_postal_code'] = "Postal Code is required.";
    if (!$country) $errors['contestant_country'] = "Country is required.";
    if (!$age || !is_numeric($age) || $age <= 0) $errors['contestant_age'] = "Valid Age is required.";
    if (!$status) $errors['contestant_status'] = "Civil Status is required.";
    if (!$profile) $errors['contestant_profile'] = "Profile is required.";
    if (!$achievements) $errors['contestant_achievements'] = "Achievement is required.";

    // Validate photo upload if provided
    if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors['contestant_photo'] = "Invalid file type. Allowed: JPG, PNG, GIF.";
        } elseif ($photo['size'] > 2 * 1024 * 1024) { // Max 2MB
            $errors['contestant_photo'] = "File size must not exceed 2MB.";
        }
    }

    // Process form if no errors
    if (empty($errors)) {
        // Assume $conn is your database connection
        $stmt = $conn->prepare("INSERT INTO contestants (lname, fname, mname, street_address, city, state, postal_code, country, age, civil_status, bio, achievements, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Handle photo upload
        $photoName = $photo && $photo['error'] === UPLOAD_ERR_OK ? uniqid() . '.' . $fileExtension : null;
        if ($photoName) move_uploaded_file($photo['tmp_name'], "uploads/$photoName");

        $stmt->bind_param('ssssssssissss', $lname, $fname, $mname, $street_address, $city, $state, $postal_code, $country, $age, $status, $profile, $achievements, $photoName);
        if ($stmt->execute()) {
            $successMessage = "Contestant added successfully!";
            header("Refresh:0"); // Refresh page
            exit;
        } else {
            $errors['form'] = "Error: " . $stmt->error;
        }
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
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-black navbar-dark">
    <ul class="navbar-nav ml-auto">
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
  <a href="index3.html" class="brand-link">
      <span class="brand-text ">Pearls of the Qrient
    </a>
    <a href="index3.html" class="brand-link">
  <span class="brand-text">
    <?php echo $_SESSION['admin_first_name'] . " " . $_SESSION['admin_last_name']; ?>
  </span>
</a>


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
    <!-- Content Header (Page header) -->
    <div class="row">
      <div class="col-12">
        <div class="card">
        <div class="card-header bg-dark d-flex justify-content-center align-items-center position-relative">
  <!-- Centered Heading -->
  <a href="#" class="h1 text-gold m-0"><b>Contestant</b> List</a>

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
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                  <p><?php echo $error; ?></p>
                <?php endforeach; ?>
              </div>
            <?php elseif (isset($success)): ?>
              <div class="alert alert-success">
                <p><?php echo $success; ?></p>
              </div>
            <?php endif; ?>
            <table class="table table-bordered">
  <thead>
    <tr>
      <th>No.</th>
      <th>Name</th> <!-- Merged Last Name, First Name, and Middle Initial -->
      <th>Address</th>
      <th>Age</th>
      <th>Civil Status</th>
      <th>Profile</th>
      <th>Achievements</th>
      <th>Photo</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    if ($conn) {
        $sql = "SELECT id, lname, fname, mname, street_address, city, state, postal_code, country, age, civil_status, bio, achievements, photo FROM contestants";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $photo = isset($row['photo']) ? (is_array($row['photo']) ? implode('', $row['photo']) : $row['photo']) : 'default.jpg';

                echo "<tr>";
                echo "<td class='align-middle text-center'>" . htmlspecialchars($row['id']) . "</td>";
                
                // Merge Last Name, First Name, and Middle Initial
                $fullName = htmlspecialchars($row['lname']) . ', ' . htmlspecialchars($row['fname']) . ' ' . htmlspecialchars($row['mname']);
                echo "<td class='align-middle text-center'>" . $fullName . "</td>";
                
                // Combine Address Fields
                $fullAddress = htmlspecialchars($row['street_address']) . ', ' .
                               htmlspecialchars($row['city']) . ', ' .
                               htmlspecialchars($row['state']) . ' ' .
                               htmlspecialchars($row['postal_code']) . ', ' .
                               htmlspecialchars($row['country']);
                echo "<td class='align-middle text-center'>" . $fullAddress . "</td>";
                
                echo "<td class='align-middle text-center'>" . htmlspecialchars($row['age']) . "</td>";
                echo "<td class='align-middle text-center'>" . htmlspecialchars($row['civil_status']) . "</td>";
                echo "<td class='align-middle text-center'>" . htmlspecialchars($row['bio']) . "</td>";
                echo "<td class='align-middle text-center'>" . htmlspecialchars($row['achievements']) . "</td>";
                echo "<td class='align-middle text-center'><img src='uploads/" . htmlspecialchars($photo) . "' style='max-width: 100px; height: auto;'></td>";
                echo '<td>
                        <a href="edit_contestant.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-warning btn-sm">Edit</a>
                        <a href="#" class="btn btn-danger btn-sm" onclick="confirmDeletion(' . htmlspecialchars($row['id']) . '); return false;">Delete</a>
                      </td>';
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No contestants found.</td></tr>";
        }
    } else {
        echo "<tr><td colspan='9'>Error: Unable to connect to the database.</td></tr>";
    }
    ?>
</tbody>
</table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="addInModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Contestant</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="contestant_lname">Last Name</label>
        <input type="text" name="contestant_lname" class="form-control" id="contestant_lname" value="<?= htmlspecialchars($lname ?? '') ?>">
        <?php if (isset($errors['contestant_lname'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_lname"><?= $errors['contestant_lname'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_fname">First Name</label>
        <input type="text" name="contestant_fname" class="form-control" id="contestant_fname" value="<?= htmlspecialchars($fname ?? '') ?>">
        <?php if (isset($errors['contestant_fname'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_fname"><?= $errors['contestant_fname'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_mname">Middle Initial</label>
        <input type="text" name="contestant_mname" class="form-control" id="contestant_mname" value="<?= htmlspecialchars($mname ?? '') ?>">
    </div>

    <!-- Address Fields -->
    <div class="form-group">
        <label for="contestant_street_address">Street Address</label>
        <input type="text" name="contestant_street_address" class="form-control" id="contestant_street_address" value="<?= htmlspecialchars($street_address ?? '') ?>">
        <?php if (isset($errors['contestant_street_address'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_street_address"><?= $errors['contestant_street_address'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_city">City</label>
        <input type="text" name="contestant_city" class="form-control" id="contestant_city" value="<?= htmlspecialchars($city ?? '') ?>">
        <?php if (isset($errors['contestant_city'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_city"><?= $errors['contestant_city'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_state">State</label>
        <input type="text" name="contestant_state" class="form-control" id="contestant_state" value="<?= htmlspecialchars($state ?? '') ?>">
        <?php if (isset($errors['contestant_state'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_state"><?= $errors['contestant_state'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_postal_code">Postal Code</label>
        <input type="text" name="contestant_postal_code" class="form-control" id="contestant_postal_code" value="<?= htmlspecialchars($postal_code ?? '') ?>">
        <?php if (isset($errors['contestant_postal_code'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_postal_code"><?= $errors['contestant_postal_code'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_country">Country</label>
        <input type="text" name="contestant_country" class="form-control" id="contestant_country" value="<?= htmlspecialchars($country ?? '') ?>">
        <?php if (isset($errors['contestant_country'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_country"><?= $errors['contestant_country'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_age">Age</label>
        <input type="number" name="contestant_age" class="form-control" id="contestant_age" value="<?= htmlspecialchars($age ?? '') ?>">
        <?php if (isset($errors['contestant_age'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_age"><?= $errors['contestant_age'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_status">Civil Status</label>
        <input type="text" name="contestant_status" class="form-control" id="contestant_status" value="<?= htmlspecialchars($status ?? '') ?>">
        <?php if (isset($errors['contestant_status'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_status"><?= $errors['contestant_status'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_profile">Profile</label>
        <textarea name="contestant_profile" class="form-control" id="contestant_profile" rows="3"><?= htmlspecialchars($profile ?? '') ?></textarea>
        <?php if (isset($errors['contestant_profile'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_profile"><?= $errors['contestant_profile'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_achievements">Achievements</label>
        <textarea name="contestant_achievements" class="form-control" id="contestant_achievements" rows="3"><?= htmlspecialchars($achievements ?? '') ?></textarea>
        <?php if (isset($errors['contestant_achievements'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_achievements"><?= $errors['contestant_achievements'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="contestant_photo">Photo</label>
        <input type="file" name="contestant_photo" class="form-control-file" id="contestant_photo">
        <?php if (isset($errors['contestant_photo'])): ?>
            <div class="alert alert-danger mt-2" id="error-contestant_photo"><?= $errors['contestant_photo'] ?></div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Add Contestant</button>
</form>

<!-- Success Message -->
<?php if ($successMessage): ?>
    <div class="alert alert-success mt-3"><?= $successMessage ?></div>
<?php endif; ?>

<script>
    window.onload = function() {
        // Hide alert-danger messages after 1 second
        setTimeout(function() {
            const alertMessages = document.querySelectorAll('.alert.alert-danger');
            alertMessages.forEach(function (element) {
                element.style.display = 'none';
            });
        }, 1000); // 1 second timeout
    };
</script>

</div>

  </div>
</div>
<!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->


<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>
<script>
function confirmDeletion(id) {
    if (confirm("Are you sure you want to delete this contestant?")) {
        window.location.href = 'contestant.php?id=' + id; // Redirect to the deletion URL
    }
}
  document.getElementById('contestantForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var isValid = true;

    // Get form fields
    var contestantLname = document.getElementById('contestant_lname');
    var contestantFname = document.getElementById('contestant_fname');
    var contestantMname = document.getElementById('contestant_mname');
    var contestantAddress = document.getElementById('contestant_address');
    var contestantAge = document.getElementById('contestant_age');
    var contestantStatus = document.getElementById('contestant_status');
    var contestantProfile = document.getElementById('contestant_profile');
    var contestantAchievements = document.getElementById('contestant_achievements');
    var contestantPhoto = document.getElementById('contestant_photo');

    // Reset any previous validation classes
    contestantLname.classList.remove('is-invalid');
    contestantStatus.classList.remove('is-invalid');
    contestantProfile.classList.remove('is-invalid');
    contestantAchievements.classList.remove('is-invalid');
    contestantPhoto.classList.remove('is-invalid');

    // Check if fields are empty
    if (!contestantLname.value.trim()) {
        contestantLname.classList.add('is-invalid');
        isValid = false;
    }
    if (!contestantFname.value.trim()) {
        contestantFname.classList.add('is-invalid');
        isValid = false;
    }
    if (!contestantMname.value.trim()) {
        contestantMname.classList.add('is-invalid');
        isValid = false;
    }
    if (!contestantAddress.value.trim()) {
        contestantAddress.classList.add('is-invalid');
        isValid = false;
    }
    if (!contestantAge.value.trim()) {
        contestantAge.classList.add('is-invalid');
        isValid = false;
    }
    if (!contestantStatus.value.trim()) {
        contestantStatus.classList.add('is-invalid');
        isValid = false;
    }
    if (!contestantProfile.value.trim()) {
        contestantProfile.classList.add('is-invalid');
        isValid = false;
    }
    if (!contestantAchievements.value.trim()) {
        contestantAchievements.classList.add('is-invalid');
        isValid = false;
    }
    if (!contestantPhoto.value) {
        contestantPhoto.classList.add('is-invalid');
        isValid = false;
    }

    // If form is valid, submit it
    if (isValid) {
        this.submit();
    }
});
x 
</script>
</body>
</html>
