<?php
require_once '../partial/config.php'; // Include database connection file

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable detailed error reporting

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = json_decode(file_get_contents('php://input'), true);
    $testId = isset($input['test_id']) ? intval($input['test_id']) : 0;

    if ($testId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Test ID']);
        mysqli_close($conn);
        exit;
    }

    // Query to fetch questions and options for the test
    $query = "
        SELECT q.QuestionID, q.QuestionText_EN AS question_en, q.QuestionText_HI AS question_hi, 
               o.OptionID, o.OptionText_EN AS option_text_en, o.OptionText_HI AS option_text_hi, o.IsCorrect
        FROM question_test q
        JOIN options_test o ON q.QuestionID = o.QuestionID
        WHERE q.TestID = ? AND q.IsActive = 1 AND o.IsActive = 1
        ORDER BY q.QuestionID, o.OptionID
    ";

    try {
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $testId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $questions = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $questionId = $row['QuestionID'];
                if (!isset($questions[$questionId])) {
                    $questions[$questionId] = [
                        'question_id' => $questionId,
                        'question_text_en' => $row['question_en'],
                        'question_text_hi' => $row['question_hi'],
                        'options' => []
                    ];
                }

                $questions[$questionId]['options'][] = [
                    'option_id' => $row['OptionID'],
                    'option_text_en' => $row['option_text_en'],
                    'option_text_hi' => $row['option_text_hi'],
                    'is_correct' => (bool) $row['IsCorrect']
                ];
            }

            echo json_encode(['success' => true, 'data' => array_values($questions)]);
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
