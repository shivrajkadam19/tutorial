<?php
require_once '../partial/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['topicId'])) { // Use TopicID instead of TopicName
        $topicId = $conn->real_escape_string($data['topicId']);

        $query = "DELETE FROM `topic` WHERE `TopicID` = '$topicId'";

        if ($conn->query($query)) {
            echo json_encode(['success' => true, 'message' => 'Topic deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete topic.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
