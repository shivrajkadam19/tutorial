<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "QuizApp";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the quiz ID from the request (e.g., through GET parameter)
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 1;

// Query to fetch quiz, questions, and options
$sql = "
    SELECT 
        qz.QuizName, 
        qs.QuestionID, 
        qs.QuestionText_EN, 
        qs.QuestionText_HI,
        opt.OptionID, 
        opt.OptionText_EN, 
        opt.OptionText_HI, 
        opt.IsCorrect
    FROM 
        Quiz qz
    INNER JOIN 
        Question qs ON qz.QuizID = qs.QuizID
    INNER JOIN 
        Options opt ON qs.QuestionID = opt.QuestionID
    WHERE 
        qz.QuizID = ?
    ORDER BY 
        qs.QuestionID, opt.OptionID
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

// Process results into a structured format
$quiz_data = [];
while ($row = $result->fetch_assoc()) {
    if (!isset($quiz_data['QuizName'])) {
        $quiz_data['QuizName'] = $row['QuizName'];
    }
    $quiz_data['Questions'][$row['QuestionID']]['QuestionText_EN'] = $row['QuestionText_EN'];
    $quiz_data['Questions'][$row['QuestionID']]['QuestionText_HI'] = $row['QuestionText_HI'];
    $quiz_data['Questions'][$row['QuestionID']]['Options'][] = [
        'OptionID' => $row['OptionID'],
        'OptionText_EN' => $row['OptionText_EN'],
        'OptionText_HI' => $row['OptionText_HI'],
        'IsCorrect' => $row['IsCorrect']
    ];
}

// Close connection
$stmt->close();
$conn->close();

// Display the quiz
if (isset($quiz_data['QuizName'])) {
    echo "<h1>Quiz: " . htmlspecialchars($quiz_data['QuizName']) . "</h1>";

    foreach ($quiz_data['Questions'] as $question_id => $question) {
        echo "<h3>Question: " . htmlspecialchars($question['QuestionText_EN']) . " (" . htmlspecialchars($question['QuestionText_HI']) . ")</h3>";
        echo "<ul>";
        foreach ($question['Options'] as $option) {
            echo "<li>" . htmlspecialchars($option['OptionText_EN']) . " (" . htmlspecialchars($option['OptionText_HI']) . ")";
            if ($option['IsCorrect']) {
                echo " <strong>(Correct)</strong>";
            }
            echo "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>No quiz found for the provided ID.</p>";
}
?>
