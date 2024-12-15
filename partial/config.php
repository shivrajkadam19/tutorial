<?php
include 'key.php';
define("DB_SERVER", $server);
define("DB_USERNAME", $username);
define("DB_PASSWORD", $password);
define("DB_NAME", $dbname);

# Connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

# Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}