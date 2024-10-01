
<?php
include "../../src/db_connection.php";
// include "./db_connection.php";

try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
} catch (PDOException $e) {
    echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
}

if (!$conn) {
    echo json_encode(array("status" => "error", "message" => "failed to connect to db"));
    exit;
}

if (!isset($_GET["project_ID"])) {
    echo json_encode(array("status" => "error", "message" => "project ID not set"));
    exit;
}
$final_json = json_decode("{}");

// get the project details
$stmt = "select project.project_id, project_title, project.due_date, sum(est_length) as total_hours, avg(completion_percentage) as total_completion, count(task_id) as task_count
    from project left join task on project.project_id = task.project_id  
    where project.project_id = :project_id
    group by project.project_id";
    
$query = $conn->prepare($stmt);
$query->bindParam(":project_id", $_GET["project_ID"]);
$result = $query->execute();
if (!$result) {
    echo json_encode(array("status" => "error", "message" => "project query failed"));
    exit;
}
$final_json->project = $query->fetch();

// get the list of tasks in this project
$stmt = "select * from task where project_id = :project_id";
if (isset($_GET["search"])) {
    $search_string = $_GET["search"];
    $stmt = $stmt . " and task_title like '%$search_string%'";
}

if (isset($_GET["task_filter_milestone"]) && $_GET["task_filter_milestone"] == "true") {
    $stmt = $stmt . " and is_milestone = true";
}
if (isset($_GET["complete_filter"]) && $_GET["complete_filter"] == "true" && !((isset($_GET["incomplete_filter"]) && $_GET["incomplete_filter"] == "true"))) {
    $stmt = $stmt . " and completion_percentage = 100";
}
if (isset($_GET["incomplete_filter"]) && $_GET["incomplete_filter"] == "true" && !(isset($_GET["complete_filter"]) && $_GET["complete_filter"] == "true")) {
    $stmt = $stmt . " and completion_percentage < 100";
}

if (isset($_GET["sort_value"])) {
    if ($_GET["sort_value"] == "due_date") {
        $stmt = $stmt . " order by due_date "; 
    }
    else if ($_GET["sort_value"] == "priority") {
        $stmt = $stmt . " order by priority "; 
    }
    else if ($_GET["sort_value"] == "est_length") {
        $stmt = $stmt . " order by est_length "; 
    }
    else if ($_GET["sort_value"] == "completion_percentage") {
        $stmt = $stmt . " order by completion_percentage "; 
    }
} else {
    $stmt = $stmt . " order by due_date"; 
}
if (isset($_GET["sort_order"])) {
    if ($_GET["sort_order"] == "ASC" || $_GET["sort_order"] == "DESC") {
        $stmt = $stmt .  $_GET["sort_order"];
    } 
}

$query = $conn->prepare($stmt);
$query->bindParam(":project_id", $_GET["project_ID"]);
$result = $query->execute();
if (!$result) {
    echo json_encode(array("status" => "error", "message" => "task query failed"));
    exit;
}
$final_json->tasks = $query->fetchAll();

// get the user assignment for this project

$stmt = "select users.user_id, concat(first_name, ' ', surname) as full_name, task.task_id, task_title, task.est_length, (case when isnull(total_logged_hrs) then 0 else total_logged_hrs end) as total_logged_hrs  
from users left join task on task.user_id = users.user_id 
left join (select task_id, sum(hours_logged) as total_logged_hrs from task_progress_log group by task_id) as log2 on task.task_id = log2.task_id 
where task.project_id = :project_id";

if (isset($_GET["search"])) {
    $search_strings = preg_split('@ @', $_GET["search"], -1, PREG_SPLIT_NO_EMPTY);
    // print($search_strings);
    // print(count($search_strings));
    if (count($search_strings) > 0) {
        $stmt = $stmt . " and ("; 
        foreach ($search_strings as $ss) {
            if ($ss != "") {
                $stmt = $stmt . "concat(first_name, ' ', surname) like '%$ss%' or task_title like '%$ss%' or ";
            }
        }
        $stmt = rtrim($stmt, "or ") . ")"; 
    }
}

// print($stmt);
$query = $conn->prepare($stmt);
$query->bindParam(":project_id", $_GET["project_ID"]);
$result = $query->execute();
if (!$result) {
    echo json_encode(array("status" => "error", "message" => "user data query failed"));
    exit;
} 

$query_result_json = $query->fetchAll();
$final_json->user_assignment = json_decode("{}");

foreach ($query_result_json as $row) {
    $username = $row['full_name'];
    $final_json->user_assignment->$username->user_id = $row["user_id"];

    $currentTask = json_decode("{}");
    $currentTask->title=$row["task_title"];
    $currentTask->user_id=$row["user_id"];
    $currentTask->est_length=$row["est_length"];
    $currentTask->total_logged_hrs=$row["total_logged_hrs"];

    $taskID = $row["task_id"];
    $final_json->user_assignment->$username->$taskID = $currentTask;
}
// {user_id: {task_id: {}, task_id: {}}, user_id: {task_id: {}, ...}, ...}
// get the progress log
$stmt = "select date, sum(hours_logged) as hours_sum 
from task_progress_log left join task on task.task_id = task_progress_log.task_id
where project_id = :project_id  
group by date 
order by date";
$query = $conn->prepare($stmt);
$query->bindParam(":project_id", $_GET["project_ID"]);
$result = $query->execute();
if (!$result) {
    echo json_encode(array("status" => "error", "message" => "progress log query failed"));
    exit;
} 
$final_json->progress_log = $query->fetchAll();



echo json_encode(array("status" => "success", "message" => $final_json));
exit;



?>
