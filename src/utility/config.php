<?php
$host = 'localhost';
$user = 'username';
$password = '';
$dbname = 'api_auth'; 

   $conn = mysqli_connect($host, $user, $password, $dbname);
   if (!$conn){
    die("Database connection failed: " . mysqli_connect_error());
  
   }
    