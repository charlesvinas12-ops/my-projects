<?php
include("conn.php");
session_start();

if (!isset($_SESSION['judge_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['sign-out'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
// After the judge is authenticated, fetch the first and last names
$judge_id = intval($_SESSION['judge_id']);

// Query the database to get the first name and last name
$judge_sql = "SELECT fname, lname FROM judges WHERE id = ?";
$stmt = $conn->prepare($judge_sql);
$stmt->bind_param("i", $judge_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($fname, $lnamex);
$stmt->fetch();

// Store the first and last name in session
$_SESSION['judge_first_name'] = $fname;
$_SESSION['judge_last_name'] = $lnamex;

// Close the statement
$stmt->close();

// Check if the judge has already voted for each contestant
$sql = "SELECT contestants.*, scores.judge_id AS voted 
        FROM contestants 
        LEFT JOIN scores ON contestants.id = scores.contestant_id AND scores.judge_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $judge_id);
$stmt->execute();
$result = $stmt->get_result();

$contestants = [];
while ($row = $result->fetch_assoc()) {
    $contestants[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Judge Voting Interface</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #000;
            background-image: url('uploads/bg.jpg');
            background-size: cover;
            background-attachment: fixed;
            color: #FFD700;
            font-family: 'tungsten', sans-serif;
            font-weight: bold;

        }

        .navbar {
            background-color: black;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: #FFD700;
        }

        .container {
            max-width: 100%;
            padding: 0 15px;
        }

        .welcome-message {
            text-align: center;
            margin-top: 20px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .contestant-grid-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
    min-height: calc(100vh - 200px); /* Adjust based on your header and footer height */
}

.contestant-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 columns */
    gap: 50px;
    justify-items: center;
    align-items: stretch;
    justify-content: center;
    align-content: center;
}
        .contestant-card {
            width: 1700px;
            max-width: 400px;
            height: auto;
            padding: 20px;
            border: 1px solid #FFD700;
            border-radius: 15px;
            background-color: #333;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .contestant-img {
            width: 100%;
            max-width: 350px;
            height: auto;
            object-fit: contain;
            margin-bottom: 10px;
            border: 2px solid #FFD700;
            border-radius: 10px;
            cursor: pointer;
        }

        .contestant-details h5 {
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 1.2rem;
            color: #FFD700;
        }

        .form-group label {
            color: #FFD700;
        }

        .btn {
            background-color: #FFD700;
            color: black;
            border: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn:hover {
            background-color: #e6be33;
            color: black;
        }

        .modal-content {
            background-color: #2f3136;
            color: #FFD700;
        }

        .welcome-message {
            text-align: left;
            margin-top: 20px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .contestant-grid {
                grid-template-columns: repeat(2, 1fr); /* 2 columns on medium screens */
            }

            .contestant-card {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .contestant-grid {
                grid-template-columns: 1fr; /* 1 column on small screens */
            }

            .contestant-card {
                width: 100%;
            }
        }
        .bg-yellow {
    background-color: #FFD700; /* Yellow background */
}

.text-black {
    color: #000; /* Black text for readability */
}

.main-footer {
    padding: 1rem; /* Adds spacing for a clean layout */
    font-size: 0.9rem; /* Adjusts font size for a footer look */
    border-top: 1px solid #e8e8e8; /* Subtle border for separation */
}


    </style>
</head>
<body>
    <section class="content">
    <div class="container d-flex  align-items-center">
    <div class="flex-grow-1 text-center">
        <a class="navbar-brand" href="#" style="color: yellow; font-size: 2rem; font-weight: bold;">Pearls of the Orient</a>
    </div>
    
</div>
        <div class="container-fluid">
        <div class="welcome-message d-flex justify-content-between align-items-center">
    <span>
    Welcome, Judge <?php echo htmlspecialchars($_SESSION['judge_first_name'] . ' ' . $_SESSION['judge_last_name']); ?>!
    </span>
    <form action="" method="post" class="mb-0">
        <input type="submit" name="sign-out" value="Logout" class="btn btn-outline-warning">
    </form>
</div>
<div class="contestant-grid-container">
    <div class="contestant-grid">
        <?php foreach ($contestants as $contestant): ?>
            <div class="contestant-card">
                <form action="submit_vote.php" method="post">
                    <input type="hidden" name="judge_id" value="<?php echo $judge_id; ?>">
                    <input type="hidden" name="contestant_id" value="<?php echo htmlspecialchars($contestant['id']); ?>">
                    <?php $photoPath = "uploads/" . htmlspecialchars($contestant['photo']); ?>
                    <img src="<?php echo $photoPath; ?>" alt="<?php echo htmlspecialchars($contestant['fname'] . ' ' . $contestant['mname'] . ' ' . $contestant['lname']); ?>" class="contestant-img" data-toggle="modal" data-target="#imageModal" data-image="<?php echo $photoPath; ?>">
                    <div class="contestant-details">
                        <h5><?php echo htmlspecialchars($contestant['fname'] . ' ' . $contestant['mname'] . ' ' . $contestant['lname']); ?></h5>
                        <button type="button" class="btn btn-info mt-2" data-toggle="modal" data-target="#detailsModal-<?php echo $contestant['id']; ?>">View Details</button>

                        <?php 
    // Check if the judge has voted
    if ($contestant['voted'] == $judge_id):
        // Fetch the scores if the judge has already voted
        $score_sql = "SELECT score_criteria1, score_criteria2, score_criteria3 FROM scores WHERE judge_id = ? AND contestant_id = ?";
        $score_stmt = $conn->prepare($score_sql);
        $score_stmt->bind_param("ii", $judge_id, $contestant['id']);
        $score_stmt->execute();
        $score_stmt->store_result();
        $score_stmt->bind_result($score_criteria1, $score_criteria2, $score_criteria3);
        $score_stmt->fetch();
        $total_score = $score_criteria1 + $score_criteria2 + $score_criteria3;
        $score_stmt->close();
?>
        <p class="text-success mt-2">You have already voted for this contestant.</p>
        <div>
            <p><strong>Total Score:</strong> <?php echo $total_score; ?></p>
        </div>
<?php else: ?>
        <div class="form-group mt-2">
            <label>Score Criteria 1</label>
            <input type="number" name="score_criteria1" placeholder="Rate from 1-10" class="form-control" min="1" max="10" required>
        </div>
        <div class="form-group">
            <label>Score Criteria 2</label>
            <input type="number" name="score_criteria2" placeholder="Rate from 1-10" class="form-control" min="1" max="10" required>
        </div>
        <div class="form-group">
            <label>Score Criteria 3</label>
            <input type="number" name="score_criteria3" placeholder="Rate from 1-10" class="form-control" min="1" max="10" required>
        </div>
        <button type="submit" class="btn btn-success mt-2">Submit Vote</button>
<?php endif; ?>

                    </div>
                </form>
            </div>

            <!-- Contestant Details Modal -->
            <div class="modal fade" id="detailsModal-<?php echo $contestant['id']; ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo htmlspecialchars($contestant['fname'] . ' ' . $contestant['mname'] . ' ' . $contestant['lname']); ?></h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
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
    </section>

    <!-- Modal for Image -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="" alt="Contestant Photo" id="modalImage" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image Modal
        $('#imageModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var image = button.data('image');
            var modal = $(this);
            modal.find('#modalImage').attr('src', image);
        });
    </script>
</body>
</html>
