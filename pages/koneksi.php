
<?php
$dbUser = "root";
$dbPass = "b15m1ll4h@123";
$dbName = "atlaz";
$dbHost = "localhost";

$con=mysqli_connect($dbHost, $dbUser, $dbPass);
mysqli_select_db($con,$dbName);
?>