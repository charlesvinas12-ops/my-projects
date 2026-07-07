<?php
include("config.php");
session_start();

$response = ['status' => 'error', 'message' => 'Unknown error occurred']; // Default error message

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        // Username not found, check if the password matches any user for "both incorrect" message
        $stmt = $conn->prepare("SELECT id FROM admins WHERE password = ?");
        $stmt->bind_param("s", $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result->fetch_assoc()) {
            $response['message'] = 'Both username and password are incorrect!';
        } else {
            $response['message'] = 'Username is incorrect!';
        }
    } elseif (password_verify($password, $row['password']) || $password === $row['password']) {
        // Successful login, hashed or plain-text password matched
        $_SESSION['admin_id'] = $row['id'];
        $response = ['status' => 'success'];

        // Redirect if not an AJAX request
        if (!isset($_POST['ajax'])) {
            header("Location: dashboard.php");
            exit();
        }
    } else {
        // Password is incorrect but username exists
        $response['message'] = 'Password is incorrect!';
    }
}

// Check if AJAX (for JSON response) or regular form submission (for redirect)
if (isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle non-AJAX login error
    echo '<script>alert("' . $response['message'] . '");</script>';
    header("Location: index.php");
}
exit();
?>
