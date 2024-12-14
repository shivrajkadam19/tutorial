<?php
header('Content-Type: application/json'); // Set response type to JSON
header('Access-Control-Allow-Origin: *'); // Allow all origins (CORS)
header('Access-Control-Allow-Headers: Content-Type'); // Handle content type header
include '../partial/config.php';

$data = json_decode(file_get_contents('php://input'), true);
$subjectName = $data['subjectName'];

$query = "DELETE FROM `subject` WHERE `SubjectName` = '$subjectName'";

if ($conn->query($query) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Subject deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete subject.']);
}
?>
