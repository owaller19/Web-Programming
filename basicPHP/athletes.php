<?php
$servername = "sci-mysql";
$username = "coa123cycle";
$password = "bgt87awx!@2FD";
$dbname = "coa123cdb";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$country = $_GET['country_id'];
$part_name = $_GET['part_name'];

$sql1 = "SELECT name FROM cyclist WHERE iso_id = '$country' AND name LIKE '%$part_name%'";
$result1 = mysqli_query($conn,$sql1);
$sql2 = "SELECT COUNT(*) AS num_events FROM cyclist LEFT JOIN event ON cyclist.cyclist_id = event.cyclist_id WHERE name LIKE '%$part_name%'";
$result2 = mysqli_query($conn,$sql2);

if (mysqli_num_rows($result1) == 0) {
    echo "No results found.";
} else {
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Name</th>';
    echo '<th>Number of Events</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    while ($row1 = mysqli_fetch_assoc($result1) and $row2 = mysqli_fetch_assoc($result2)) {
        echo '<tr>';
        echo '<td>' . $row1['name'] . '</td>';
        echo '<td>' . $row2['num_events'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

mysqli_close($conn);
