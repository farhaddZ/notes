<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbName = "notes";

//creat connection
$db = mysqli_connect($servername, $username, $password, $dbName);
mysqli_query($db, 'SET NAMES utf8');

//check connection
if (!$db) {
  die("Connection failed: " . mysqli_connect_error());
}