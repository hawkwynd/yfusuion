<!DOCTYPE html>
<html>
<head>
    <style>
        body{margin: 14px; font-family: tahoma}
        pre{ font-family: tahoma; font-size: 16px;}
        input{padding:4px}
    </style>
    <title>Scott's Biz Project Search</title>
</head>
<body>
<h3>Business Category Listing from Yelp YFusion API</h3>
<p><a href="/">Back to Search</a></p>
<?php
/**
 * Date: 4/22/17
 * Time: 11:40 AM
 * yelp-fusion - scottfleming
 */


$mysqli = new mysqli("localhost", "hawk_fusion", "scootre", "hawk_yfusion");
// Oh no! A connect_errno exists so the connection attempt failed!
if ($mysqli->connect_errno) {
    // The connection failed. What do you want to do?
    // You could contact yourself (email?), log the error, show a nice page, etc.
    // You do not want to reveal sensitive information
    // Let's try this:
    echo "Sorry, this website is experiencing problems.";
    // Something you should not do on a public site, but this example will show you
    // anyways, is print out MySQL error related information -- you might log this
    echo "Error: Failed to make a MySQL connection, here is why: \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";

    // You might want to show them something nice, but we will simply exit
    exit;
}


$sql = "SELECT * from categories";

if (!$result = $mysqli->query($sql)) {
    // Oh no! The query failed.
    echo "Sorry, the website is experiencing problems. Big TIME";

    // Again, do not do this on a public site, but we'll show you how
    // to get the error information
    echo "Error: Our query failed to execute and here is why: \n";
    echo "Query: " . $sql . "\n";
    echo "Errno: " . $mysqli->errno . "\n";
    echo "Error: " . $mysqli->error . "\n";
    exit;
}

// Phew, we made it. We know our MySQL connection and query
// succeeded, but do we have a result?
if ($result->num_rows === 0) {
    // Oh, no rows! Sometimes that's expected and okay, sometimes
    // it is not. You decide. In this case, maybe actor_id was too
    // large?
    echo "We could not find a match sorry about that. Please try again.";
    exit;
}


echo "<ul>";

while($category = $result->fetch_assoc()){
    echo "<li>category title: " . $category['title'];
    echo  "<ul><li>alias: " . $category['alias'] ."</li>";
           echo "<li>parent category: " . $category['parents'] . "</li></ul>";
    echo "</li>";
}

echo "</ul>";
?>
</body>
</html>
