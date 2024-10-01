<?php
$min_weight = $_GET["min_weight"];
$max_weight = $_GET["max_weight"];
$min_height = $_GET["min_height"];
$max_height = $_GET["max_height"];
function bmi($weight, $height) {
  $height_m = $height / 100;
  $bmi = round($weight / ($height_m ** 2), 3);
  return $bmi;
}
echo "<table>";
echo "<tr>";
echo "<th>Weight/Height</th>";
for ($height = $min_height; $height <= $max_height; $height += 5) {
  echo "<th>$height cm</th>";
}
echo "</tr>";
for ($weight = $min_weight; $weight <= $max_weight; $weight += 5) {
  echo "<tr>";
  echo "<th>$weight kg</th>";
  for ($height = $min_height; $height <= $max_height; $height += 5) {
    $bmiVal = bmi($weight, $height);
    echo "<td>$bmiVal</td>";
  }
  echo "</tr>";
}
echo "</table>";

