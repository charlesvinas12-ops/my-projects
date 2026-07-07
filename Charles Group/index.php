<?php
include("conn.php");


// Fetch contestant data
$sql = "SELECT id, fname, lname, mname, street_address, city, state, postal_code, country, age, civil_status, bio, achievements, photo FROM contestants";
$result = $conn->query($sql);

$contestants = array();
while ($row = $result->fetch_assoc()) {
    $contestants[] = $row;
}


// Fetch score data for each contestant
$scoreData = array();
foreach ($contestants as $contestant) {
    $contestantId = $contestant['id'];
    $sql = "SELECT SUM(total_score) AS total_score FROM scores WHERE contestant_id = $contestantId";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $scoreData[$contestantId] = $row['total_score'];
}

// Sort contestants by score in descending order
usort($contestants, function($a, $b) use ($scoreData) {
    return $scoreData[$b['id']] <=> $scoreData[$a['id']];
});

// Assign ranks to contestants
foreach ($contestants as $rank => $contestant) {
    $contestants[$rank]['rank'] = $rank + 1;
}


?>

<!DOCTYPE html>
<html lang="en">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pageant Voting App | Contestant List</title>

  <!-- Google Font: Source Sans Pro -->

  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <!-- Bootstrap 4 -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  
  <style>
    body {
      background-color: #000;
      background-image: url('uploads/bg.jpg');
      background-size: cover;
      background-attachment: fixed;
      color: #FFD700;
      font-family: 'tungsten';
      
    }
    h1 {
      text-align: center;
      margin-top: 20px;
      font-family: 'Bernard MT Condensed', Arial, sans-serif;
      font-size: 70px;
      margin-bottom: 10px;
      text-align: center;
      justify-content: center;
    }
    .navbar {
      background-color: #FFD700;
    }
    .navbar .nav-link, .navbar .navbar-brand {
      color: #000;
      transition: color 0.3s ease;
    }
    .navbar .nav-link:hover {
      color: #333;
    }
    .nav-link.small-text {
      font-size: 12px;
    }
    .content-header h1, .card-title {
      color: #FFD700;
    }
    .breadcrumb-item a {
      color: #FFD700;
    }
    .content {
      margin-top: 20px;
    }
    .contestant-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }
    .contestant-info {
      display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
      padding: 20px;
      border: 2px solid #FFD700;
      border-radius: 30px;
      background-color: #333;
      box-shadow: 0 6px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;

    }
    .contestant-details .btn {
  display: block;
  width: 100%; /* Makes the button span the container's width */
  height: 50px; /* Ensures consistent height for all buttons */
  margin-top: auto; /* Pushes the button to the bottom */
  text-align: center;
  background-color: #ffc107; /* Optional: Adjust button color */
  color: #000; /* Optional: Adjust text color */
  border: none;
  padding: 10px;
  font-weight: bold;
  font-size: 16px; /* Optional: Make text uniform */
  cursor: pointer;
}
.contestant-details {
  display: flex;
  flex-direction: column;
  justify-content: space-between; /* Ensure consistent spacing */
  align-items: center; /* Center-align the content */
  height: 100%; /* Ensure consistent height for all containers */
}
    .contestant-info:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .contestant-img {
      width: 100%;
      height: 300px;
      object-fit: contain;
      margin-bottom: 20px;
      border: 2px solid #FFD700;
      border-radius: 30px;
      cursor: pointer;
    }
    .contestant-details h5 {
      font-family: 'tungsten', sans-serif;
      color: #FFD700;
      font-size: 1.2rem;
      text-align: center;
    }
    .contestant-details p {
      color: #FFD700;
      margin: 5px 0;
      font-size: 1rem;
      text-align: center;
    }
    .btn {
      background-color: #FFD700;
      color: #000;
      border: none;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn:hover {
      background-color: #FFC700;
      color: #000;
    }
    .modal-content {
      background-color: #333;
      border: none;
    }
    .chart {
      background-color: #333;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .scrollable-container::-webkit-scrollbar {
      width: 8px;
    }
    .scrollable-container::-webkit-scrollbar-thumb {
      background-color: #FFD700;
      border-radius: 10px;
    }
    .scrollable-container::-webkit-scrollbar-track {
      background: #333;
    }
    .rank-1 {
      color: gold;
    }
    .rank-2 {
      color: silver;
    }
    .rank-3 {
      color: #cd7f32;
    }
    .card-body {
      background-color: transparent;
      color: #FFD700;
    }
    .transparent-background {
      background-color: transparent !important;
    }
    .card-title {
      font-family: 'Bernard MT Condensed', Arial, sans-serif;
      font-size: 32px;
      color: #FFD700;
      text-align: center;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }
    .contestant-card {
      background-color: #000;
      color: #FFD700;
      padding: 20px;
      border-radius: 0px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }
    .bar {
      size: 10;
    }
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      padding: 0 20px;
    }
    .card-title {
      margin: 0;
    }
    .small-text.btn {
      width: 100px;
      margin-left: auto;
    }
    .main-footer {
    display: flex;
    justify-content: space-between; /* Distributes content between left and right */
    align-items: center;
    padding: 1rem;
    background-color: #FFD700; /* Yellow background */
    color: #000; /* Black text */
}

.main-footer .text-start {
    text-align: left;
}

.main-footer a {
    text-decoration: none;
    color: inherit; /* Inherits black color */
}

  </style>
<body class="">
  <!-- Navbar -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble" src="uploads/bg.jpg" alt="AdminLTELogo" height="5000" width="5000">
  </div>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
      <h1>Pearls of the Orient</h1>
      <div class="header">
    <h3 class="card-title">Contestant</h3>
    <a class="small-text btn" data-toggle="modal" data-target="#signInModal">Sign In</a>
</div>
      </li>
    </ul>
  
 
  <!-- /.navbar -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
      <div class="col-12 col-lg-7 mb-3 transparent-background">
  <div class="card-body">
    <div class="form-group">
      <div class="input-group">
        <input type="text" class="form-control" id="searchInput" placeholder="Search...">
        <div class="input-group-append">
          <button type="button" id="searchButton" class="btn btn-warning" onclick="searchContestants()">Search</button>
        </div>
      </div>
    </div>
    <div class="scrollable-container">
      <div id="contestantList" class="contestant-grid">
        <?php foreach ($contestants as $contestant): ?>
          <div class="contestant-info">
  <?php
  $photoPath = "uploads/" . htmlspecialchars($contestant['photo']);
  if (file_exists($photoPath)):
  ?>
  <img src="<?php echo $photoPath; ?>" alt="<?php echo htmlspecialchars($contestant['fname']) . ' ' . htmlspecialchars($contestant['mname']) . ' ' . htmlspecialchars($contestant['lname']); ?>" class="contestant-img" data-toggle="modal" data-target="#photoModal-<?php echo $contestant['id']; ?>">
  <?php else: ?>
    <p>Photo not available</p>
  <?php endif; ?>
  <div class="contestant-details">
  <h5><?php echo htmlspecialchars($contestant['fname']) . ' ' . htmlspecialchars($contestant['mname']) . ' ' . htmlspecialchars($contestant['lname']); ?></h5>
    <p>Score: <?php echo isset($scoreData[$contestant['id']]) ? htmlspecialchars($scoreData[$contestant['id']]) : 0; ?></p>
    <p class="rank <?php echo ($contestant['rank'] == 1) ? 'rank-1' : (($contestant['rank'] == 2) ? 'rank-2' : (($contestant['rank'] == 3) ? 'rank-3' : '')); ?>">
      Rank: <?php echo $contestant['rank']; ?>
    </p>
    <button type="button" class="btn" data-toggle="modal" data-target="#detailsModal-<?php echo $contestant['id']; ?>">View Details</button>
  </div>
</div>


          <!-- Photo Modal -->
          <div class="modal fade" id="photoModal-<?php echo $contestant['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel-<?php echo $contestant['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="photoModalLabel-<?php echo $contestant['id']; ?>"><?php echo htmlspecialchars($contestant['fname']) . ' ' . htmlspecialchars($contestant['mname']) . ' ' . htmlspecialchars($contestant['lname']); ?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body text-center">
                  <img src="<?php echo $photoPath; ?>" alt="<?php echo htmlspecialchars($contestant['fname']) . ' ' . htmlspecialchars($contestant['mname']) . ' ' . htmlspecialchars($contestant['lname']); ?>" class="img-fluid">
                </div>
              </div>
            </div>
          </div>

          <!-- Details Modal -->
          <div class="modal fade" id="detailsModal-<?php echo $contestant['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel-<?php echo $contestant['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel-<?php echo $contestant['id']; ?>">
                    <?php echo htmlspecialchars($contestant['fname']) . ' ' . htmlspecialchars($contestant['mname']) . ' ' . htmlspecialchars($contestant['lname']); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Civil Status:</strong> <?php echo htmlspecialchars($contestant['civil_status']); ?></p>
                <p><strong>Bio:</strong> <?php echo htmlspecialchars($contestant['bio']); ?></p>
                <p><strong>Achievements:</strong> <?php echo htmlspecialchars($contestant['achievements']); ?></p>
                <p><strong>Address:</strong> 
                    <?php
                    // Combine address fields into one string
                    $address = htmlspecialchars($contestant['street_address']) . ', ' . 
                              htmlspecialchars($contestant['city']) . ', ' . 
                              htmlspecialchars($contestant['state']) . ' ' . 
                              htmlspecialchars($contestant['postal_code']) . ', ' . 
                              htmlspecialchars($contestant['country']);
                    echo $address;
                    ?>
                </p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($contestant['age']); ?></p>
            </div>
        </div>
    </div>
</div>

        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<div class="col-12 col-lg-5 mb-3">
          <div class="card card-warning">
            <div class="card-header bg-dark">
            <h3 class="card-title">Voting Statistics</h3>  
              <!-- Each contestant's data will be dynamically injected here -->
            </div>
            <div id="contestantContainer">
            <canvas id="chartCanvas" style="display: none;"></canvas> <!-- Hidden canvas for Chart.js -->
            </div>
          </div>
        </div>
      </div>
      
    </div>
    
  </section>
  <!-- /.content -->

  <!-- Modal -->
  <div class="modal fade" id="signInModal" tabindex="-1" role="dialog" aria-labelledby="signInModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="signInModalLabel">Sign In</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#judgeLoginModal">Judge</a>
          <a href="#" class="btn btn-secondary btn-block" data-toggle="modal" data-target="#adminLoginModal">Admin</a>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

 <!-- Judge Login Modal -->
<div class="modal fade" id="judgeLoginModal" tabindex="-1" role="dialog" aria-labelledby="judgeLoginModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judgeLoginModalLabel">Judge Login</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="judgeLoginForm" method="POST" novalidate>
          <div class="form-group">
            <label for="judgeUsername">Username</label>
            <input type="text" class="form-control" id="judgeUsername" name="username" required>
            <div class="invalid-feedback">
              Please enter your username.
            </div>
          </div>
          <div class="form-group">
            <label for="judgePassword">Password</label>
            <input type="password" class="form-control" id="judgePassword" name="password" required>
            <div class="invalid-feedback">
              Please enter your password.
            </div>
          </div>
          <!-- Error message display area -->
          <div id="loginErrorMessage" class="text-danger mb-3" style="display: none;"></div>
          <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById("judgeLoginForm").addEventListener("submit", function(event) {
  event.preventDefault(); // Prevent the form from submitting normally
  const form = event.target;

  // Collect form data
  const formData = new FormData(form);

  // Send AJAX request to judge_login.php
  fetch("judge_login.php", {
    method: "POST",
    body: formData,
  })
  .then(response => response.json())
  .then(data => {
    const errorMessageDiv = document.getElementById("loginErrorMessage");
    if (data.status === "success") {
      window.location.href = "judge.php"; // Redirect on successful login
    } else {
      errorMessageDiv.textContent = data.message; // Display error message
      errorMessageDiv.style.display = "block";
    }
  })
  .catch(error => {
    console.error("Error:", error);
  });
});
</script>

<!-- Admin Login Modal -->
<div class="modal fade" id="adminLoginModal" tabindex="-1" role="dialog" aria-labelledby="adminLoginModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adminLoginModalLabel">Admin Login</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="adminLoginForm" method="POST" novalidate>
          <div class="form-group">
            <label for="adminUsername">Username</label>
            <input type="text" class="form-control" id="adminUsername" name="username" required>
            <div class="invalid-feedback">
              Please enter your username.
            </div>
          </div>
          <div class="form-group">
            <label for="adminPassword">Password</label>
            <input type="password" class="form-control" id="adminPassword" name="password" required>
            <div class="invalid-feedback">
              Please enter your password.
            </div>
          </div>
          <!-- Admin login error message display area with unique ID -->
          <div id="adminLoginErrorMessage" class="text-danger mb-3" style="display: none;"></div>
          <button type="submit" class="btn btn-secondary btn-block" name="submit">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById("adminLoginForm").addEventListener("submit", function(event) {
  event.preventDefault(); // Prevent default form submission
  const form = event.target;

  // Collect form data
  const formData = new FormData(form);
  formData.append("ajax", true); // Indicate this is an AJAX request

  // Send AJAX request to admin_login.php
  fetch("admin_login.php", {
    method: "POST",
    body: formData,
  })
  .then(response => response.json())
  .then(data => {
    const errorMessageDiv = document.getElementById("adminLoginErrorMessage");
    if (data.status === "success") {
      window.location.href = "dashboard.php"; // Redirect on successful login
    } else {
      // Display error message if login fails
      errorMessageDiv.textContent = data.message;
      errorMessageDiv.style.display = "block";
    }
  })
  .catch(error => {
    console.error("Error:", error);
  });
});
</script>




  <!-- Control Sidebar -->
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<!-- jQuery -->
 <!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>
<!-- Chart.js --<!-- Custom Script -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function () {
  'use strict';
  window.addEventListener('load', function () {
    // Fetch all forms we want to apply custom Bootstrap validation to
    var forms = document.getElementsByTagName('form');
    // Loop over them and prevent submission if form is invalid
    Array.prototype.filter.call(forms, function (form) {
      form.addEventListener('submit', function (event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();

$(document).ready(function() {
    var contestants = <?php echo json_encode($contestants); ?>;
    var scoreData = <?php echo json_encode($scoreData); ?>;
    var container = $('#contestantContainer');
    
    // Maximum score assumed to be 100
    var maxScore = 100;

    // Loop through contestants and generate their details
    $(document).ready(function() {
    var contestants = <?php echo json_encode($contestants); ?>;
    var scoreData = <?php echo json_encode($scoreData); ?>;
    var container = $('#contestantContainer');
    
    // Maximum score assumed to be 100
    var maxScore = 100;

    // Loop through contestants and generate their details
    contestants.forEach(function(contestant, index) {
        var score = scoreData[contestant.id] || 0; // Ensure score is defined
        var fullname = contestant.fname + ' ' + contestant.mname + ' ' + contestant.lname; // Use fname, mname, lname
        var photo = contestant.photo ? 'uploads/' + contestant.photo : 'uploads/default.jpg';
        var percentage = (score / maxScore * 100).toFixed(2); // Calculate percentage

        // Create the contestant card
        var contestantCard = `
          <div class="contestant-card" style="display: flex; align-items: center; margin-bottom: 0px;">
            <img src="${photo}" alt="${fullname}" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 10px; margin-left: 10px">
            <div style="flex-grow: 1;">
              <h4>${fullname}</h4>
              <canvas id="chartCanvas-${index}" width="500px" height="30px"></canvas>
              <p>${percentage}%</p>
            </div>
          </div>
        `;
        
        // Append the card to the container
        container.append(contestantCard);
        
        // Draw the bar chart for this contestant
        var ctx = document.getElementById('chartCanvas-' + index).getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [''], // Placeholder label
                datasets: [{
                    data: [percentage], // Use percentage for the bar height
                    backgroundColor: 'rgba(255, 215, 0, 0.5)', // Gold color
                    borderColor: 'rgba(255, 215, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bar
                scales: {
                    x: { 
                        display: false,
                        max: 100 // Percentage is based on 100
                    }, 
                    y: { 
                        display: false // Hide y-axis
                    }
                },
                plugins: {
                    legend: { display: false }, // Hide legend
                    tooltip: { enabled: false } // Disable tooltips
                }
            }
        });
    });
});

    // Search functionality for contestants
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#contestantList .contestant-info').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Clear search text when search button is clicked
    $('#searchButton').on('click', function() {
        $('#searchInput').val('');  // Clear the search input field
    });
});

  </script>
 

</body>
</html>