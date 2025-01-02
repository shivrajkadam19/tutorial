<?php
require_once '../partial/config.php'; // Ensure this path is correct

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Check if JSON decoding succeeded
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
        exit;
    }

    $login = $data['login'] ?? '';
    $password = $data['password'] ?? '';

    // Input validation
    if (empty($login) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Login and Password are required']);
        exit;
    }

    // Check database connection
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    // Prepare query to check user by email or username
    $sql = "SELECT * FROM `admin` WHERE `Email` = ? OR `UserName` = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ss", $login, $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if ($password==$user['Password']) {
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['AdminID'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['username'] = $user['UserName'];

            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// Close the connection
if (isset($conn)) {
    mysqli_close($conn);
}
?>
