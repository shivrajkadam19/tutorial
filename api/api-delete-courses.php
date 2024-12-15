<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../partial/config.php'; // Ensure the config file path is correct

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['courseId']) || empty($data['courseId'])) {
    echo json_encode(['success' => false, 'message' => 'Course ID is required.']);
    exit;
}

$courseId = intval($data['courseId']); // Ensure CourseID is an integer

// Use prepared statements to prevent SQL injection
$query = $conn->prepare("DELETE FROM `course` WHERE `CourseID` = ?");
$query->bind_param('i', $courseId);

if ($query->execute()) {
    if ($query->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Course deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No course found with the provided ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete course.', 'error' => $query->error]);
}

$query->close();
$conn->close();
?>
