<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT s.SubjectName, c.CourseName FROM subject s 
              INNER JOIN course c ON s.CourseID = c.CourseID";

    if ($result = $conn->query($query)) {
        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
        echo json_encode(['success' => true, 'subjects' => $subjects]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch subjects.', 'error' => $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
