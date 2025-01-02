<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = "localhost";
$dbname = "quizapp2";
$username = "root";
$password = "";

// Database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get parameters
    $type = $_GET['type'] ?? '';
    $parentID = $_GET['parentID'] ?? null;

    $sql = "";
    $params = [];

    switch ($type) {
        case 'course':
            $sql = "SELECT CourseID, CourseName FROM course WHERE IsActive = 1";
            break;

        case 'subject':
            $sql = "SELECT SubjectID, SubjectName FROM subject WHERE CourseID = :parentID";
            $params = ['parentID' => $parentID];
            break;

        case 'topic':
            $sql = "SELECT TopicID, TopicName FROM topic WHERE SubjectID = :parentID";
            $params = ['parentID' => $parentID];
            break;

        case 'quiz':
            $sql = "SELECT QuizID, QuizName FROM quiz WHERE TopicID = :parentID";
            $params = ['parentID' => $parentID];
            break;

        default:
            echo json_encode(["error" => "Invalid request type"]);
            exit();
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
