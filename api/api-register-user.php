<?php
require_once '../partial/config.php'; // Include database connection file

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Helper function to create a base64-encoded JSON string
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// Generate a JWT manually
function generate_jwt($payload, $secretKey) {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    
    // Encode Header and Payload
    $encodedHeader = base64url_encode(json_encode($header));
    $encodedPayload = base64url_encode(json_encode($payload));

    // Create the Signature
    $signature = hash_hmac('sha256', "$encodedHeader.$encodedPayload", $secretKey, true);
    $encodedSignature = base64url_encode($signature);

    // Combine Header, Payload, and Signature
    return "$encodedHeader.$encodedPayload.$encodedSignature";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract values from JSON
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $passwordConfirm = $data['passwordConfirm'] ?? ''; 

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
    $sql = "INSERT INTO `user` (`UserName`, `Email`, `Password`, `CreatedAt`, `UpdatedAt`) VALUES (?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        $userId = $conn->insert_id; // Get the ID of the newly inserted user

        // Generate a manual JWT token
        $secretKey = "your_secret_key"; // Replace with a secure key
        $payload = [
            'userId' => $userId,
            'username' => $username,
            'email' => $email,
            'exp' => time() + (60 * 60 * 24) // Token expires in 1 day
        ];
        $jwt = generate_jwt($payload, $secretKey);

        // Return success response with JWT
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully',
            'userId' => $userId,
            'token' => $jwt
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to register. Try again later']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
