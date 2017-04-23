<?php
/**
 * Date: 4/22/17
 * Time: 10:55 AM
 * yelp-fusion - scottfleming
 * db_name - hawk_fusion
 * db_user - hawk_fusion
 * db_pass - scootre
 * table - categories
 *
 */

$conn = mysqli_connect("localhost", "hawk_fusion", "scootre", "hawk_yfusion");
mysqli_select_db($conn, "hawk_yfusion") or die(mysql_error());

if(!$conn){echo "Error! Cant connect to DB"; }

$json_data = file_get_contents('categories.json');
$json_a = json_decode($json_data, true);

echo "<pre>";

foreach($json_a as $row){
    $alias = mysqli_real_escape_string($conn, $row['alias'] );
    $title = mysqli_real_escape_string($conn, $row['title']);
    $parent = mysqli_real_escape_string($conn, $row['parents'][0]);
    $query = "REPLACE INTO categories (`id`, `alias`, `title`, `parents`) VALUES
    (NULL, '". $alias. "', '". $title ."', '". $parent. "')";
    mysqli_query($conn, $query) or die(mysqli_error($conn));
    echo $query . PHP_EOL;

}