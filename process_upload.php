<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $questions = [];
        $header = fgetcsv($file); // Read the header row

        if (!$header || count($header) < 5) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSV format.']);
            exit;
        }

        while (($data = fgetcsv($file)) !== false) {
            if (count($data) >= 5) {
                $questions[] = [
                    'QuestionText_EN' => $data[0],
                    'QuestionText_HI' => $data[1],
                    'Option1_EN' => $data[2],
                    'Option1_HI' => $data[3],
                    'Option2_EN' => $data[4],
                    'Option2_HI' => $data[5],
                    'Option3_EN' => $data[6],
                    'Option3_HI' => $data[7],
                    'Option4_EN' => $data[8],
                    'Option4_HI' => $data[9],
                    'CorrectOption' => isset($data[10]) ? (int)$data[10] : 0,
                ];
            }
        }
        fclose($file);
        echo json_encode(['success' => true, 'questions' => $questions]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file.']);
    }
}
?>
