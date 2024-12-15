<?php

include '../partial/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questions = json_decode($_POST['questions'], true);
    $quiz_id = 3; // Replace with actual quiz ID

    if (!$questions || !is_array($questions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid questions data.']);
        exit;
    }

    foreach ($questions as $question) {
        $stmt = $conn->prepare("INSERT INTO Question (QuizID, QuestionText_EN, QuestionText_HI) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $quiz_id, $question['QuestionText_EN'], $question['QuestionText_HI']);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to insert question.']);
            exit;
        }
        $question_id = $stmt->insert_id;

        $options_en = [$question['Option1_EN'], $question['Option2_EN'], $question['Option3_EN'], $question['Option4_EN']];
        $options_hi = [$question['Option1_HI'], $question['Option2_HI'], $question['Option3_HI'], $question['Option4_HI']];

        foreach ($options_en as $index => $option_en) {
            $is_correct = ($index + 1 == $question['CorrectOption']) ? 1 : 0;
            $stmt = $conn->prepare("INSERT INTO Options (QuestionID, OptionText_EN, OptionText_HI, IsCorrect) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $question_id, $option_en, $options_hi[$index], $is_correct);
            if (!$stmt->execute()) {
                echo json_encode(['success' => false, 'message' => 'Failed to insert options.']);
                exit;
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Questions saved successfully.']);
}
?>
