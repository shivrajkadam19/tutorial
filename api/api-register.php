<?php
require_once '../partial/config.php'; // Include database connection file

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract values from JSON
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $passwordConfirm = $data['password-confirm'] ?? '';


    // Input validation
    if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }

    if ($password !== $passwordConfirm) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    $sql = "INSERT INTO `admin` (`username`, `email`, `password`, `created_at`,`updated_at`) VALUES (?, ?, ?, NOW(),NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to register. Try again later']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
