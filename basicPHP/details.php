<?php
$servername = "sci-mysql";
$username = "coa123cycle";
$password = "bgt87awx!@2FD";
$dbname = "coa123cdb";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$date1 = $_GET['date_1'];
if (strtotime(str_replace('/', '-', $date1)) === false) {
    die("Invalid date entered. Please use the format DD/MM/YYYY");
}
$date1_formatted = date('Y-m-d', strtotime(str_replace('/', '-', $date1)));
$date2 = $_GET['date_2'];
if (strtotime(str_replace('/', '-', $date2)) === false) {
    die("Invalid date entered. Please use format dd/mm/yyyy.");
}
$date2_formatted = date('Y-m-d', strtotime(str_replace('/', '-', $date2)));

$sql = "SELECT name, iso_id, dob FROM cyclist WHERE dob BETWEEN '$date1_formatted' AND '$date2_formatted' ORDER BY dob DESC";
$result = mysqli_query($conn,$sql);

$names = array();
$country = array();
$dates = array();

while ($row = mysqli_fetch_assoc($result)) {
    $names[] = $row['name'];
    $country[] = $row['iso_id'];
    $dates[] = $row['dob'];
}

$name_iso_dob = array (
    'names' => $names,
    'country' => $country,
    'dates' => $dates
);

$json = json_encode($name_iso_dob);
header('Content-Type: application/json');
echo $json;

mysqli_close($conn);


