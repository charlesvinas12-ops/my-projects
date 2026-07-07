<?php
session_start();

if (!isset($_SESSION['judge_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pageant_voting";
$port = "3308";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$judge_id = intval($_SESSION['judge_id']);

// Validate judge_id exists in the judges table
$judge_check_sql = "SELECT id FROM judges WHERE id = ?";
$judge_check_stmt = $conn->prepare($judge_check_sql);
$judge_check_stmt->bind_param("i", $judge_id);
$judge_check_stmt->execute();
$judge_check_stmt->store_result();

if ($judge_check_stmt->num_rows === 0) {
    die("Invalid judge ID.");
}

$judge_check_stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contestant_id = intval($_POST['contestant_id']);
    $score_criteria1 = intval($_POST['score_criteria1']);
    $score_criteria2 = intval($_POST['score_criteria2']);
    $score_criteria3 = intval($_POST['score_criteria3']);

    // Validate scores are within the allowed range
    if ($score_criteria1 >= 1 && $score_criteria1 <= 10 && $score_criteria2 >= 1 && $score_criteria2 <= 10 && $score_criteria3 >= 1 && $score_criteria3 <= 10) {
        // Prepare and bind
        $sql = "INSERT INTO scores (judge_id, contestant_id, score_criteria1, score_criteria2, score_criteria3) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiii", $judge_id, $contestant_id, $score_criteria1, $score_criteria2, $score_criteria3);

        // Execute and check for success
        if ($stmt->execute()) {
            echo "Vote submitted successfully for contestant ID $contestant_id.";
            header('Location: judge.php'); // Redirect to the judges list page after successful submission
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Scores must be between 1 and 10.";
    }
}

$conn->close();
?>
