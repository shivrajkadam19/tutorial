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

    // Get filters
    $courseID = $_GET['courseID'] ?? null;
    $subjectID = $_GET['subjectID'] ?? null;
    $topicID = $_GET['topicID'] ?? null;
    $quizID = $_GET['quizID'] ?? null;

    // Base query
    $sql = "
        SELECT 
            c.CourseName, s.SubjectName, t.TopicName, qz.QuizName, 
            q.QuestionText_EN, o.OptionText_EN, o.IsCorrect
        FROM course c
        LEFT JOIN subject s ON c.CourseID = s.CourseID
        LEFT JOIN topic t ON s.SubjectID = t.SubjectID
        LEFT JOIN quiz qz ON t.TopicID = qz.TopicID
        LEFT JOIN question q ON qz.QuizID = q.QuizID
        LEFT JOIN options o ON q.QuestionID = o.QuestionID
        WHERE c.IsActive = 1
    ";

    // Conditional filters
    if ($courseID) $sql .= " AND c.CourseID = :courseID";
    if ($subjectID) $sql .= " AND s.SubjectID = :subjectID";
    if ($topicID) $sql .= " AND t.TopicID = :topicID";
    if ($quizID) $sql .= " AND qz.QuizID = :quizID";

    $stmt = $conn->prepare($sql);

    // Bind parameters
    if ($courseID) $stmt->bindParam(':courseID', $courseID, PDO::PARAM_INT);
    if ($subjectID) $stmt->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
    if ($topicID) $stmt->bindParam(':topicID', $topicID, PDO::PARAM_INT);
    if ($quizID) $stmt->bindParam(':quizID', $quizID, PDO::PARAM_INT);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
