<?php
session_start(); 

if (isset($_GET['userToGet'])) {
    $userID = $_GET['userToGet'];
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 'overview';  
    }
    
} else {
    header("location: ./analytics_landing_page.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Landing Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="stylesheets/user_analytics.css">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="js/individual_handler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://www.gstatic.com/charts/loader.js"></script>

</head>


<body>
    <?php
    // header handels the checking for login
    $currentPage = "analytics";
    include "../src/header.php";
    ?>

    <div class="d-flex">
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar">
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="?userToGet=<?php echo $_GET['userToGet'] ?? '' ?>&page=overview" class="nav-link <?php echo $page == "overview" ? "active" : "link-dark" ?>" aria-current="page">
                        <i class="bi bi-folder-fill"></i>
                        Overview
                    </a>
                </li>
                <li>
                    <a href="?userToGet=<?php echo $_GET['userToGet'] ?? '' ?>&page=tasks" class="nav-link <?php echo $page == "tasks" ? "active" : "link-dark" ?>">
                        <i class="bi bi-list-check"></i>
                        Tasks
                    </a>
                </li>
                <li>
                    <a href="?userToGet=<?php echo $_GET['userToGet']?>&page=progress" class="nav-link <?php echo $page == "progress" ? "active" : "link-dark" ?>">
                        <i class="bi bi-people-fill"></i>
                        Progression
                    </a>
                </li>
            </ul>
            <hr>
        </div>
        <div class="main-content-container" style="padding: 20px; width: 100%;">
            <?php if ($page == "overview") { ?>
            <script>
                fetchUserData(<?php echo $userID ?>, updateUserData);
            </script>
            <div class="col-md-9">
                <header class="mb-3">
                    <h1 class="sectionT" id="fullName"></h1>
                    <h2 id="role" class="subheadingT"></h2>
                </header>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="statBox bg-light-grey p-3">
                                        <div class="hours-info" id="overviewHoursSummary">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="statBox bg-light-grey p-3">
                                        <span class = "percentage-number">Current task completion: </span>
                                        <span id="overviewPercentageNumber" class="percentage-number"></span>
                                    </div>
                                </div>
                            </div>
                                
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="statBox bg-light-grey p-3">
                                        <div id="overviewTaskProjectInfo" class="taskProjectInfo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="pie-chart-box bg-light-grey p-3">
                                <canvas id="taskCompletionPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } elseif ($page == "tasks") { ?>
                <header class="main-content-header">
                    <h1 class = "sectionT">Tasks</h1>
                </header>
                <div class="task-container">
                    <?php
                    $servername = "localhost";
                    $username = "phpUser";
                    $password = "p455w0rD";
                    $dbname = "make_it_all";  
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT t.task_title, p.project_title, t.due_date, t.priority, t.est_length, t.completion_percentage 
                            FROM task t 
                            INNER JOIN project p ON t.project_ID = p.project_ID
                            WHERE t.user_ID = ?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        die('MySQL prepare error: ' . $conn->error);
                    }
                    $stmt->bind_param('i', $userID);
                    $stmt->execute();
                    $stmt->bind_result($taskName, $projectName, $dueDate, $priority, $estimatedLength, $completionPercentage);

                    while ($stmt->fetch()) {
                        ?>
                        <div class="task-box bg-light border rounded p-3 mb-2">
                            <div class="task-info">
                                <h5 class="task-name">Task: <?php echo htmlspecialchars($taskName); ?></h5>
                                <h5 class="project-name">Project: <?php echo htmlspecialchars($projectName); ?></h5>
                                <p class="task-due-date">Due Date: <?php echo htmlspecialchars($dueDate); ?></p>
                                <p class="task-priority">Priority: <?php echo htmlspecialchars($priority); ?></p>
                                <p class="task-length">Estimated Length: <?php echo htmlspecialchars($estimatedLength); ?> hours</p>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($completionPercentage); ?>%;" aria-valuenow="<?php echo htmlspecialchars($completionPercentage); ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?php echo htmlspecialchars($completionPercentage); ?>% Complete
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    $stmt->close();
                    $conn->close();
                    ?>
                </div>
            <?php } else if ($page == "progress") {?>
                <div id="progress_line_chart" style="width: 80%; height:400px"></div>
                <script>
                    fetchUserData(<?php echo $userID ?>, initProgressGraph);
                </script>
            <?php }?>
        </div>
    </div>
</body>
</html>

