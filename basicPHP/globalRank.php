<!DOCTYPE html>
<html>
<body>
<style>
    body{
        background-image: url("olympic_back.jpg");
        background-repeat: no-repeat;
        background-size: cover;
    }
    table{
        background-color: lightblue;
        width: 80%;
        margin: 0 auto;
        font-size: 35px;
        margin-top: 30px;
        color: red;
        border: 2px solid black;
    }
    th, td {
        padding: 8px;
        text-align: center;
    }
	.button-container {
		display: flex;
		justify-content: space-between;
		margin-top: 20px;
        flex-direction: row;
        align-items: center;
	}
    button {
        font-size: 25px;
        cursor: pointer;
    }
    button:hover{
        background-color: white;
    }
    .button_title {
        color: white;
        font-size: 35px;
    }
    .gold {
        background-color: gold;
    }
    .num {
        background-color: purple;
    }
    .age {
        background-color: red;
    }
</style>

<?php
$servername = "sci-mysql";
$username = "coa123cycle";
$password = "bgt87awx!@2FD";
$dbname = "coa123cdb";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$country1 = $_GET['country3'];
$country2 = $_GET['country4'];

function goldRank($conn,$country) {
    $rank = 0;
    $prevGold = null;
    $sqlgold = "SELECT iso_id, gold FROM country ORDER BY gold DESC";
    $resultGold = mysqli_query($conn,$sqlgold);
    while ($row = mysqli_fetch_assoc($resultGold)) {
        $gold = $row['gold'];
        if ($gold != $prevGold) {
            $rank++;
        }
        if ($row['iso_id'] == $country) {
            $countryRank = $rank;
        }
        $prevGold = $gold;
    }
    return($countryRank);
}

function numberCyclists($conn,$country) {
    $rank = 0;
    $prevCyclistCount = null;
    $sqlNumCyclists = "SELECT country.iso_id, COUNT(cyclist.iso_id) AS cyclist_count FROM country LEFT JOIN cyclist ON country.iso_id = cyclist.iso_id GROUP BY country.iso_id ORDER BY cyclist_count DESC";
    $resultNumCyclists = mysqli_query($conn,$sqlNumCyclists);
    while ($row = mysqli_fetch_assoc($resultNumCyclists)) {
        $cyclistCount = $row['cyclist_count'];
        if ($cyclistCount != $prevCyclistCount) {
            $rank++;
        }
        if ($row['iso_id'] == $country) {
            $countryRank = $rank;
        }
        $prevCyclistCount = $cyclistCount;
    }
    return($countryRank);
}

function ageRank($conn,$country) {
    $rank = 0;
    $prevAvg = null; 
    $sqlAvgAge = "SELECT country.iso_id, AVG(2012 - YEAR(cyclist.dob)) AS avg_age FROM country JOIN cyclist ON country.iso_id = cyclist.iso_id GROUP BY country.iso_id ORDER BY avg_age ASC";
    $ageResult = mysqli_query($conn, $sqlAvgAge);
    while ($row = mysqli_fetch_assoc($ageResult)) {
        $Avg = $row['avg_age'];
        if ($Avg != $prevAvg) {
            $rank++;
        }
        if ($row['iso_id'] == $country) {
            $countryRank = $rank;
        }
        $prevAvg = $Avg;
    }
    return($countryRank);
}

function getCountryData($conn, $country, $dataType) {
    $data = null;
    switch ($dataType) {
        case 'gold':
            $sql = "SELECT gold FROM country WHERE iso_id='$country'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $data = $row['gold'];
            break;
        case 'cyclist':
            $sql = "SELECT COUNT(*) AS cyclist_count FROM cyclist WHERE iso_id='$country'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $data = $row['cyclist_count'];
            break;
        case 'age':
            $sql = "SELECT AVG(2012 - YEAR(dob)) AS avg_age FROM cyclist WHERE iso_id='$country'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $data = $row['avg_age'];
            break;
    }
    return $data;
}
?>

<div class="button-container">
    <h2 class="button_title">Choose ranking criteria:</h2>
    <button class= "gold" onclick="switchRankType('Gold Rank')">Gold Rank</button>
    <button class = "num" onclick="switchRankType('Number Cyclists')">Number Cyclists</button>
    <button class = "age" onclick="switchRankType('Average Age')">Average Age</button>
</div>

<table id="myTable">
  <tr>
    <th>Country</th>
    <th id="rankType">Gold Medals Rank</th>
    <th id="dataType">Gold Medals</th>
  </tr>
  <tr>
    <td><?php echo $country1; ?></td>
    <td id="country1Rank"><?php echo goldRank($conn,$country1); ?></td>
    <td id="country1Data"><?php echo getCountryData($conn, $country1, 'gold'); ?></td>
  </tr>
  <tr>
    <td><?php echo $country2; ?></td>
    <td id="country2Rank"><?php echo goldRank($conn,$country2); ?></td>
    <td id="country2Data"><?php echo getCountryData($conn, $country2, 'gold'); ?></td>
  </tr>
</table>

<script>
function switchRankType(option) {
			var rankType = document.getElementById("rankType");
            var dataType = document.getElementById("dataType");
			var country1Rank = document.getElementById("country1Rank");
            var country1Data = document.getElementById("country1Data");
			var country2Rank = document.getElementById("country2Rank");
            var country2Data = document.getElementById("country2Data");

			if (option === "Gold Rank") {
				rankType.innerHTML = "Gold Medals Rank";
                dataType.innerHTML = "Gold Medals";
				country1Rank.innerHTML = "<?php echo goldRank($conn,$country1); ?>";
                country1Data.innerHTML = "<?php echo getCountryData($conn, $country1, 'gold'); ?>";
				country2Rank.innerHTML = "<?php echo goldRank($conn,$country2); ?>";
                country2Data.innerHTML = "<?php echo getCountryData($conn, $country2, 'gold'); ?>";
			} else if (option === "Number Cyclists") {
				rankType.innerHTML = "Number of Cyclists Rank";
                dataType.innerHTML = "Number of Cyclists";
				country1Rank.innerHTML = "<?php echo numberCyclists($conn,$country1); ?>";
                country1Data.innerHTML = "<?php echo getCountryData($conn, $country1, 'cyclist'); ?>";
				country2Rank.innerHTML = "<?php echo numberCyclists($conn,$country2); ?>";
                country2Data.innerHTML = "<?php echo getCountryData($conn, $country2, 'cyclist'); ?>";
			} else if (option === "Average Age") {
				rankType.innerHTML = "Average Age Rank";
                dataType.innerHTML = "Average Age of Cyclists";
				country1Rank.innerHTML = "<?php echo ageRank($conn,$country1); ?>";
                country1Data.innerHTML = "<?php echo getCountryData($conn, $country1, 'age'); ?>";
				country2Rank.innerHTML = "<?php echo ageRank($conn,$country2); ?>";
                country2Data.innerHTML = "<?php echo getCountryData($conn, $country2, 'age'); ?>";
			}
		}
</script>


</body>
</html>