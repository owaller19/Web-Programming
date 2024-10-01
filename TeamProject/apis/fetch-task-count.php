<?php
include_once(__DIR__.'/../src/db_connection.php');
$connection=mysqli_connect($servername,$username,$password,$dbname);
$statement=mysqli_stmt_init($connection);
$ID=$_GET['ID'];//variable for employee or project ID, depending on the type of the request
if($_GET['type']=="emp") {
    //Get the number of tasks for the selected employee
    mysqli_stmt_prepare($statement,"SELECT COUNT(task_id) AS taskCount FROM Task WHERE user_id=?");
    mysqli_stmt_bind_param($statement,"i",$ID);
    mysqli_stmt_execute($statement);
    $result=mysqli_stmt_get_result($statement);
    if($result!=false) {
        $resultArray=mysqli_fetch_array($result,MYSQLI_ASSOC);
    }
    echo json_encode($resultArray);
}
else if($_GET['type']=="proj") {
    //Get the number of tasks for the selected project
    mysqli_stmt_prepare($statement,"SELECT COUNT(task_id) AS taskCount FROM Task WHERE project_id=?");
    mysqli_stmt_bind_param($statement,"i",$ID);
    mysqli_stmt_execute($statement);
    $result=mysqli_stmt_get_result($statement);
    if($result!=false) {
        $resultArray=mysqli_fetch_array($result,MYSQLI_ASSOC);
    }
    echo json_encode($resultArray);
}
else {
    //Error - invalid request type
}
?>