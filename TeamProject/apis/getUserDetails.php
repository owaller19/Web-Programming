<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$userID = isset($_GET['userID']) ? intval($_GET['userID']) : null;
if (!$userID) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing userID']);
    exit;
}


$stmt = $conn->prepare("SELECT first_name, surname, role FROM users WHERE user_ID = ?");
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'MySQL prepare error: ' . $conn->error]);
    exit;
}
$stmt->bind_param('i', $userID);
$stmt->execute();
$stmt->bind_result($firstName, $surname, $role);

if ($stmt->fetch()) {
    $userDetails = [
        'fullName' => $firstName . " " . $surname,
        'role' => $role
    ];
} else {
    echo json_encode(['status' => 'error', 'message' => 'No user found with the specified userID']);
    exit;
}
$stmt->close();

$sql = "SELECT project_id, completion_percentage, est_length FROM task WHERE user_ID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'MySQL prepare error: ' . $conn->error]);
    exit;
}
$stmt->bind_param('i', $userID);
$stmt->execute();
$stmt->bind_result($projectID, $completionPercentage, $estimatedHours);

$projects = [];
while ($stmt->fetch()) {
    $projects[] = [
        'projectID' => $projectID,
        'completionPercentage' => $completionPercentage,
        'estimatedHours' => $estimatedHours
    ];
}
$stmt->close();

$completionPercentages = array_column($projects, 'completionPercentage');
$estimatedHoursArray = array_column($projects, 'estimatedHours');
$projectIDs = array_column($projects, 'projectID');

$completionSum = array_sum($completionPercentages);
$overallCompletion = count($completionPercentages) ? $completionSum / count($completionPercentages) : 0;

$hoursDone = 0;
foreach ($completionPercentages as $index => $percentage) {
    $hoursDone += $percentage * $estimatedHoursArray[$index] / 100;
}
$hoursLeft = array_sum($estimatedHoursArray) - $hoursDone;

$projectCount = count(array_unique($projectIDs));
$taskCount = count($projects);

// get the progress log
$sql = "SELECT date, sum(hours_logged) AS hours_sum 
FROM task_progress_log
WHERE user_id = ?   
GROUP BY date 
ORDER BY date";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(array("status" => "error", "message" => "progress log query failed"));
    exit;
}
$stmt->bind_param('i', $userID);
$stmt->execute();
$result = $stmt->get_result();
$progress_log = $result->fetch_all(MYSQLI_ASSOC);
// $stmt->close();


$conn->close();

echo json_encode([
    'status' => 'success',
    'data' => [
        'userDetails' => $userDetails,
        'projects' => $projects,
        'statistics' => [
            'overallCompletion' => $overallCompletion,
            'hoursDone' => $hoursDone,
            'hoursLeft' => $hoursLeft,
            'projectCount' => $projectCount,
            'taskCount' => $taskCount
        ],
        'progressLog' => $progress_log
    ]
]);
?>
