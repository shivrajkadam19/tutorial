<?php
require_once '../partial/config.php'; // Include database connection file

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input from the request body
    $input = json_decode(file_get_contents('php://input'), true);
    $subjectId = isset($input['subject_id']) ? intval($input['subject_id']) : 0;

    if ($subjectId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Subject ID']);
        $conn->close();
        exit;
    }

    // Query to fetch all tests related to the subject
    $query = "
        SELECT t.TestID, t.TestName, s.SubjectName 
        FROM test t 
        INNER JOIN subject s ON t.SubjectID = s.SubjectID 
        WHERE s.SubjectID = ? AND t.IsActive = 1
    ";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $subjectId);
        $stmt->execute();
        $result = $stmt->get_result();

        $tests = [];
        while ($row = $result->fetch_assoc()) {
            $tests[] = $row;
        }

        echo json_encode(['success' => true, 'tests' => $tests]);
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch tests.', 'error' => $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
