<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT `CourseID`, `CourseName`,`Description` FROM `course`";

    if ($result = $conn->query($query)) {
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        echo json_encode(['success' => true, 'courses' => $courses]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch courses.']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
