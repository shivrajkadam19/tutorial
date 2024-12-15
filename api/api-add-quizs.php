<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['quizName']) && isset($data['topicId'])) {
        $quizName = $conn->real_escape_string($data['quizName']);
        $topicId = $conn->real_escape_string($data['topicId']);

        $query = "INSERT INTO `quiz` (`QuizName`, `TopicID`, `CreatedAt`, `UpdatedAt`) 
                  VALUES ('$quizName', '$topicId', NOW(), NOW())";

        if ($conn->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Quiz added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add quiz.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
