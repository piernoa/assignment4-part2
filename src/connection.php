<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
// This is not the actual server connection used for the assignment. I didn't want to post my server credentials here.
$mysqli = new mysqli("localhost", "root", "root", "school");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
echo $mysqli->host_info . "\n";
?>
