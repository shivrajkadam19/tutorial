<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT q.QuizName, q.QuizID, t.TopicName FROM quiz q 
              INNER JOIN topic t ON q.TopicID = t.TopicID";

    if ($result = $conn->query($query)) {
        $quizzes = [];
        while ($row = $result->fetch_assoc()) {
            $quizzes[] = $row;
        }
        echo json_encode(['success' => true, 'quizzes' => $quizzes]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch quizzes.', 'error' => $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
