<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['quizId'], $data['questionText'], $data['options'], $data['correctOption'])) {
        $quizId = $conn->real_escape_string($data['quizId']);
        $questionText = $conn->real_escape_string($data['questionText']);
        $options = $data['options']; // Array of options
        $correctOption = $conn->real_escape_string($data['correctOption']);

        // Insert the question
        $query = "INSERT INTO Question (QuizID, QuestionText) VALUES ('$quizId', '$questionText')";
        if ($conn->query($query)) {
            $questionId = $conn->insert_id;

            // Insert the options
            $success = true;
            foreach ($options as $index => $optionText) {
                if ($optionText) {
                    $isCorrect = ($index + 1 == $correctOption) ? 1 : 0;
                    $optionQuery = "INSERT INTO Options (QuestionID, OptionText, IsCorrect) VALUES ('$questionId', '$optionText', '$isCorrect')";
                    if (!$conn->query($optionQuery)) {
                        $success = false;
                        break;
                    }
                }
            }

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Question added successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add options.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add question.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
