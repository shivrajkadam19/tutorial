<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['topicName']) && isset($data['subjectId'])) {
        $topicName = $conn->real_escape_string($data['topicName']);
        $subjectId = $conn->real_escape_string($data['subjectId']);

        $query = "INSERT INTO `topic` (`TopicName`, `SubjectID`, `CreatedAt`, `UpdatedAt`) 
                  VALUES ('$topicName', '$subjectId', NOW(), NOW())";

        if ($conn->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Topic added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add topic.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
