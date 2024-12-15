<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['quizId'])) {
        $quizId = $conn->real_escape_string($data['quizId']);

        $query = "DELETE FROM `quiz` WHERE `QuizID` = '$quizId'";

        if ($conn->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Quiz deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete quiz.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
