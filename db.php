<?php
// db.php — Database Connection
// Change credentials below if your XAMPP setup is different

$host   = "localhost";
$user   = "root";
$pass   = "";          // default XAMPP password is empty
$dbname = "fsd_lab4";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("<p style='color:red;'>Connection Failed: " . mysqli_connect_error() . "</p>");
}
?>
