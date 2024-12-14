<?php
include './partial/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'];

    // Check if file is uploaded
    if (!isset($_FILES['questions_file']) || $_FILES['questions_file']['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading file.");
    }

    $file_tmp = $_FILES['questions_file']['tmp_name'];

    // Open the CSV file
    if (($handle = fopen($file_tmp, "r")) !== FALSE) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Skip the header row
            fgetcsv($handle);

            // Prepare statements
            $question_stmt = $conn->prepare("INSERT INTO `Question` (`QuizID`, `QuestionText_EN`, `QuestionText_HI`) VALUES (?, ?, ?)");
            $option_stmt = $conn->prepare("INSERT INTO `Options` (`QuestionID`, `OptionText_EN`, `OptionText_HI`, `IsCorrect`) VALUES (?, ?, ?, ?)");

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Parse question data
                $question_en = $data[1];
                $question_hi = $data[2];

                // Insert question
                $question_stmt->bind_param("iss", $quiz_id, $question_en, $question_hi);
                $question_stmt->execute();
                $question_id = $question_stmt->insert_id;

                // Parse options and correct option
                for ($i = 0; $i < 4; $i++) {
                    $option_en = $data[3 + $i * 2];
                    $option_hi = $data[4 + $i * 2];
                    $is_correct = ($i + 1 == $data[11]) ? 1 : 0;

                    // Insert option
                    $option_stmt->bind_param("issi", $question_id, $option_en, $option_hi, $is_correct);
                    $option_stmt->execute();
                }
            }

            // Commit transaction
            $conn->commit();
            echo "Questions uploaded successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }

        fclose($handle);
    } else {
        echo "Error opening file.";
    }
}

$conn->close();
?>
