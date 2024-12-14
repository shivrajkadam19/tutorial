<?php
require_once '../partial/config.php';

header('Content-Type: application/json'); // Set response type to JSON
header('Access-Control-Allow-Origin: *'); // Allow all origins (CORS)
header('Access-Control-Allow-Methods: POST'); // Allow POST methods
header('Access-Control-Allow-Headers: Content-Type'); // Handle content type header

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $courseName = $data['courseName'] ?? '';
    $description = $data['description'] ?? '';

    if (empty($courseName) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'Course name and description are required.']);
        exit;
    }

    $query = "INSERT INTO `course` (`CourseName`, `Description`, `CreatedAt`, `UpdatedAt`) 
              VALUES (?, ?, NOW(), NOW())";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ss", $courseName, $description);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Course added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add course.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL query.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
