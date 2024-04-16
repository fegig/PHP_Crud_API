<?php
$host = 'srv496.hstgr.io';
$user = 'u751352931_tester';
$password = 's5]IgBJoiI';
$dbname = 'u751352931_tester'; 

   $conn = mysqli_connect($host, $user, $password, $dbname);
   if (!$conn){
    die("Database connection failed: " . mysqli_connect_error());
  
   }
    