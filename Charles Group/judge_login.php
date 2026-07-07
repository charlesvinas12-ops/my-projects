<?php
include("config.php");
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // First, check if the username exists
    $stmt = $conn->prepare("SELECT id, password FROM judges WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Check if the password matches, considering both hashed and plain-text cases
        if (password_verify($password, $row['password']) || $password === $row['password']) {
            $_SESSION['judge_id'] = $row['id'];
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Password is incorrect.']);
        }
    } else {
        // Check if any account matches the provided password, implying both are incorrect if not found
        $stmt = $conn->prepare("SELECT id FROM judges WHERE password = ?");
        $stmt->bind_param("s", $password);
        $stmt->execute();
        $passwordResult = $stmt->get_result();

        if (!$passwordResult->fetch_assoc()) {
            echo json_encode(['status' => 'error', 'message' => 'Both username and password are incorrect.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Username is incorrect.']);
        }
    }
    exit();
}
?>
