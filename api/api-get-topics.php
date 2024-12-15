<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT t.TopicName, t.TopicID, s.SubjectName FROM topic t 
              INNER JOIN subject s ON t.SubjectID = s.SubjectID";

    if ($result = $conn->query($query)) {
        $topics = [];
        while ($row = $result->fetch_assoc()) {
            $topics[] = $row;
        }
        echo json_encode(['success' => true, 'topics' => $topics]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch topics.', 'error' => $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
