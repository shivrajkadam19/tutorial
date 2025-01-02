<?php
require_once '../partial/config.php'; // Include database connection file

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Helper function for base64 URL encoding
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// Function to manually generate a JWT
function generate_jwt($payload, $secretKey) {
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);

    // Encode header and payload
    $encodedHeader = base64url_encode($header);
    $encodedPayload = base64url_encode(json_encode($payload));

    // Create the signature
    $signature = hash_hmac('sha256', "$encodedHeader.$encodedPayload", $secretKey, true);
    $encodedSignature = base64url_encode($signature);

    return "$encodedHeader.$encodedPayload.$encodedSignature";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract values from JSON
    $login = $data['login'] ?? ''; // Can be email or username
    $password = $data['password'] ?? '';

    // Input validation
    if (empty($login) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Login identifier and password are required']);
        exit;
    }

    // Check if the user exists (match email or username)
    $sql = "SELECT `UserID`, `UserName`, `Email`, `Password` 
            FROM `user` 
            WHERE `Email` = ? OR `UserName` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['Password'])) {
            // Generate JWT token manually
            $secretKey = "your_secret_key"; // Replace with a strong, secure key
            $payload = [
                'userId' => $user['UserID'],
                'username' => $user['UserName'],
                'email' => $user['Email'],
                'exp' => time() + (60 * 60 * 24) // Token expires in 1 day
            ];

            $jwt = generate_jwt($payload, $secretKey);

            // Return success response with JWT
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'token' => $jwt
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
