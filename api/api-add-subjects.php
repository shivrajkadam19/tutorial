<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['subjectName']) && isset($data['courseId'])) {
        $subjectName = $conn->real_escape_string($data['subjectName']);
        $courseId = $conn->real_escape_string($data['courseId']);

        $query = "INSERT INTO `subject` (`SubjectName`, `CourseID`, `CreatedAt`, `UpdatedAt`) 
                  VALUES ('$subjectName', '$courseId', NOW(), NOW())";

        if ($conn->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Subject added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add subject.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
