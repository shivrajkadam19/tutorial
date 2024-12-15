<?php
header('Content-Type: application/json'); // Set response type to JSON
header('Access-Control-Allow-Origin: *'); // Allow all origins (CORS)
header('Access-Control-Allow-Headers: Content-Type'); // Handle content type header
include '../partial/config.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (isset($data['subjectId'])) {
    $subjectId = $conn->real_escape_string($data['subjectId']); // Use SubjectID instead of SubjectName

    // Delete query using SubjectID
    $query = "DELETE FROM `subject` WHERE `SubjectID` = '$subjectId'";

    if ($conn->query($query) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Subject deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete subject.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
}
$conn->close();
?>
