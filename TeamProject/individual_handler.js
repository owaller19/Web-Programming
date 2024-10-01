function fetchUserData(userID, callback) {
    fetch(`apis/getUserDetails.php?userID=${userID}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Parsed JSON data:', data);
            callback(data);
        })
        .catch(error => {
            console.error('Error fetching user data:', error);
        });
}

function initProgressGraph(userData) {
    google.charts.load('current', {'packages':['line']});
    google.charts.setOnLoadCallback(function () {drawProgressGraph(userData)});
}

function drawProgressGraph(userData) {
    var progressData = userData.data.progressLog
    // console.log(progressData)
    var data = new google.visualization.DataTable();
    data.addColumn('date', 'Date');
    data.addColumn('number', 'Day to Day');
    data.addColumn('number', 'Cumulative');

    var running_sum = 0
    processedDataArray = []
    progressData.forEach(row => {
        running_sum = running_sum + parseInt(row["hours_sum"])
        processedDataArray.push([new Date(row["date"]), parseInt(row["hours_sum"]), running_sum])
    })
    data.addRows(processedDataArray)

    var options = {
        chart: {
          title: 'User Progress Over Time',
          subtitle: 'in hours commited'
        },
        height: 500,
        vAxis: {
            minValue: 0, 
            viewWindow: {
                min:0
            }
        }
    };

    if (processedDataArray.length > 0) {
        var chart = new google.charts.Line(document.getElementById('progress_line_chart'));
        chart.draw(data, google.charts.Line.convertOptions(options));
    } else {
        document.getElementById('progress_line_chart').innerHTML = "There has been no progress logged for this user yet."
    }
}


function updateUserData(userData) {
    document.getElementById('fullName').innerText = "Overview - " + userData.data.userDetails.fullName;
    
    var roleText = '';
    switch (userData.data.userDetails.role) {
        case 'Mgr':
            roleText = 'Manager';
            break;
        case 'Emp':
            roleText = 'Employee';
            break;
        case 'TL':
            roleText = 'Team Leader';
            break;
        default:
            roleText = 'Role Undefined'; 
    }
    document.getElementById('role').innerText = roleText;

    var hoursCompleted = userData.data.statistics.hoursDone;
    var hoursRemaining = userData.data.statistics.hoursLeft;
    var hoursSummaryElement = document.getElementById('overviewHoursSummary');
    hoursSummaryElement.innerText = `${hoursCompleted} hours completed, ${hoursRemaining} hours remaining`;

    var completionPercentage = userData.data.statistics.overallCompletion;
    var percentageElement = document.getElementById('overviewPercentageNumber');
    percentageElement.innerText = completionPercentage + '%';
    percentageElement.style.fontWeight = 'bold';

    if (completionPercentage < 40) {
        percentageElement.style.color = 'red';
    } else if (completionPercentage >= 40 && completionPercentage <= 70) {
        percentageElement.style.color = 'orange';
    } else {
        percentageElement.style.color = 'green';
    }

    var taskCount = userData.data.statistics.taskCount;
    var projectCount = userData.data.statistics.projectCount;
    var taskProjectInfoElement = document.getElementById('overviewTaskProjectInfo');
    taskProjectInfoElement.innerText = `${taskCount} tasks assigned across ${projectCount} projects`;

    updatePieChart(hoursCompleted, hoursRemaining);
}

function updatePieChart(hoursCompleted, hoursRemaining) {
    var ctx = document.getElementById('taskCompletionPieChart').getContext('2d');
    var totalHours = hoursCompleted + hoursRemaining;
    var data = {
        labels: ['Hours Completed', 'Hours Remaining'],
        datasets: [{
            data: [hoursCompleted, hoursRemaining],
            backgroundColor: ['#4CAF50', '#FF0000'],
            borderColor: ['#fff'],
            borderWidth: 1
        }]
    };

    if (window.pieChart) {
        window.pieChart.data.datasets[0].data = [hoursCompleted, hoursRemaining];
        window.pieChart.update();
    } else {
        window.pieChart = new Chart(ctx, {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
}



